<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Order_Detail;
use App\Models\Order_Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('orderDetails.product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Lấy giỏ hàng của người dùng
        $cartItems = Cart::where('user_id', $request->user()->id)
            ->with('product')
            ->get();
            
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Giỏ hàng trống'], 422);
        }
        
        // Tính tổng giá trị đơn hàng
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
        
        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();
        
        try {
            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);
            
            // Tạo chi tiết đơn hàng và cập nhật số lượng sản phẩm
            foreach ($cartItems as $item) {
                $product = Product::find($item->product_id);
                
                // Kiểm tra lại số lượng sản phẩm trong kho
                if ($product->stock < $item->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Sản phẩm ' . $product->name . ' chỉ còn ' . $product->stock . ' sản phẩm.'
                    ], 422);
                }
                
                // Tạo chi tiết đơn hàng
                Order_Detail::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $product->price,
                ]);
                
                // Cập nhật số lượng sản phẩm trong kho
                $product->update([
                    'stock' => $product->stock - $item->quantity
                ]);
            }

            // Kiểm tra mã giảm giá nếu có
            if ($request->has('discount_code')) {
                $discount = Discount::where('code', $request->discount_code)->first();

                if ($discount) {
                    if ($discount->isValid($totalPrice)) {
                        $discountAmount = $discount->calculateDiscount($totalPrice);
                        
                        // Tạo liên kết giữa đơn hàng và mã giảm giá
                        Order_Discount::create([
                            'order_id' => $order->id,
                            'discount_id' => $discount->id,
                            'discount_amount' => $discountAmount,
                        ]);

                        // Cập nhật tổng giá trị đơn hàng sau khi áp dụng mã giảm giá
                        $totalPrice -= $discountAmount;
                        $order->update(['total_price' => $totalPrice]);
                    } else {
                        return response()->json(['message' => 'Mã giảm giá không hợp lệ'], 422);
                    }
                } else {
                    return response()->json(['message' => 'Mã giảm giá không tồn tại'], 404);
                }
            }

            // Xóa giỏ hàng sau khi đặt hàng thành công
            Cart::where('user_id', $request->user()->id)->delete();
            
            // Commit transaction
            DB::commit();
            
            return response()->json([
                'order' => $order->load('orderDetails.product'),
                'message' => 'Đặt hàng thành công'
            ], 201);
            
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();
            
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi đặt hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Order $order)
    {
        // Kiểm tra xem đơn hàng có thuộc về người dùng hiện tại không
        if ($order->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }
        
        return response()->json([
            'order' => $order->load('orderDetails.product', 'user')
        ]);
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel(Request $request, Order $order)
    {
        // Kiểm tra xem đơn hàng có thuộc về người dùng hiện tại không
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }
        
        // Chỉ có thể hủy đơn hàng ở trạng thái pending
        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Không thể hủy đơn hàng ở trạng thái hiện tại'
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            // Cập nhật trạng thái đơn hàng
            $order->update(['status' => 'canceled']);
            
            // Hoàn lại số lượng sản phẩm vào kho
            foreach ($order->orderDetails as $detail) {
                $product = $detail->product;
                $product->update([
                    'stock' => $product->stock + $detail->quantity
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'order' => $order->fresh()->load('orderDetails.product'),
                'message' => 'Hủy đơn hàng thành công'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi hủy đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Lấy tất cả đơn hàng
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with('user', 'orderDetails.product');
        
        // Lọc theo trạng thái
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Lọc theo ngày
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sắp xếp
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        return response()->json($query->paginate(15));
    }

    /**
     * Admin: Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,shipped,completed,canceled',
        ]);
        
        $oldStatus = $order->status;
        $newStatus = $request->status;
        
        DB::beginTransaction();
        
        try {
            // Cập nhật trạng thái đơn hàng
            $order->update(['status' => $newStatus]);
            
            // Nếu chuyển từ trạng thái khác sang canceled, hoàn lại số lượng sản phẩm vào kho
            if ($newStatus === 'canceled' && $oldStatus !== 'canceled') {
                foreach ($order->orderDetails as $detail) {
                    $product = $detail->product;
                    $product->update([
                        'stock' => $product->stock + $detail->quantity
                    ]);
                }
            }
            
            // Nếu chuyển từ canceled sang trạng thái khác, trừ lại số lượng sản phẩm trong kho
            if ($oldStatus === 'canceled' && $newStatus !== 'canceled') {
                foreach ($order->orderDetails as $detail) {
                    $product = $detail->product;
                    
                    // Kiểm tra lại số lượng sản phẩm trong kho
                    if ($product->stock < $detail->quantity) {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Sản phẩm ' . $product->name . ' chỉ còn ' . $product->stock . ' sản phẩm.'
                        ], 422);
                    }
                    
                    $product->update([
                        'stock' => $product->stock - $detail->quantity
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'order' => $order->fresh()->load('orderDetails.product', 'user'),
                'message' => 'Cập nhật trạng thái đơn hàng thành công'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi cập nhật trạng thái đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }
}
