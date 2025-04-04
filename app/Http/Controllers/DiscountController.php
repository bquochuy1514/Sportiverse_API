<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Order_Discount;
use App\Models\OrderDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    /**
     * Admin: Lấy danh sách mã giảm giá
     */
    public function index(Request $request)
    {
        $query = Discount::query();
        
        // Lọc theo trạng thái (active/expired)
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                });
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', now());
            }
        }
        
        // Tìm kiếm theo mã code
        if ($request->has('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }
        
        // Sắp xếp
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        return response()->json($query->paginate(15));
    }

    /**
     * Admin: Tạo mã giảm giá mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:discounts',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $discount = Discount::create($request->all());

        return response()->json([
            'discount' => $discount,
            'message' => 'Tạo mã giảm giá thành công'
        ], 201);
    }

    /**
     * Admin: Lấy thông tin chi tiết mã giảm giá
     */
    public function show(Discount $discount)
    {
        return response()->json([
            'discount' => $discount,
            'usage_count' => $discount->orders()->count(),
        ]);
    }

    /**
     * Admin: Cập nhật mã giảm giá
     */
    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'code' => 'sometimes|string|max:50|unique:discounts,code,' . $discount->id,
            'discount_type' => 'sometimes|in:percent,fixed',
            'discount_value' => 'sometimes|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $discount->update($request->all());

        return response()->json([
            'discount' => $discount,
            'message' => 'Cập nhật mã giảm giá thành công'
        ]);
    }

    /**
     * Admin: Xóa mã giảm giá
     */
    public function destroy(Discount $discount)
    {
        // Kiểm tra xem mã giảm giá đã được sử dụng chưa
        if ($discount->orders()->count() > 0) {
            return response()->json([
                'message' => 'Không thể xóa mã giảm giá này vì đã được sử dụng'
            ], 422);
        }

        $discount->delete();

        return response()->json([
            'message' => 'Xóa mã giảm giá thành công'
        ]);
    }

    /**
     * Kiểm tra tính hợp lệ của mã giảm giá
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:discounts,code',
            'order_value' => 'required|numeric|min:0',
        ]);

        $discount = Discount::where('code', $request->code)->first();

        if (!$discount) {
            return response()->json([
                'valid' => false,
                'message' => 'Mã giảm giá không tồn tại'
            ]);
        }

        if (!$discount->isValid($request->order_value)) {
            return response()->json([
                'valid' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
            ]);
        }

        $discountAmount = $discount->calculateDiscount($request->order_value);

        return response()->json([
            'valid' => true,
            'discount' => $discount,
            'discount_amount' => $discountAmount,
            'final_price' => $request->order_value - $discountAmount,
            'message' => 'Mã giảm giá hợp lệ'
        ]);
    }

    /**
     * Áp dụng mã giảm giá vào đơn hàng
     */
    public function applyToOrder(Request $request, Order $order)
    {
        $request->validate([
            'discount_code' => 'required|string',
        ]);

        // Kiểm tra quyền truy cập
        if ($order->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        // Kiểm tra xem đơn hàng đã có mã giảm giá chưa
        if ($order->discounts()->count() > 0) {
            return response()->json([
                'message' => 'Đơn hàng này đã áp dụng mã giảm giá'
            ], 422);
        }

        $discount = Discount::where('code', $request->discount_code)->first();

        if (!$discount) {
            return response()->json([
                'message' => 'Mã giảm giá không tồn tại'
            ], 404);
        }

        if (!$discount->isValid($order->total_price)) {
            return response()->json([
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
            ], 422);
        }

        $discountAmount = $discount->calculateDiscount($order->total_price);

        DB::beginTransaction();

        try {
            // Tạo liên kết giữa đơn hàng và mã giảm giá
            Order_Discount::create([
                'order_id' => $order->id,
                'discount_id' => $discount->id,
                'discount_amount' => $discountAmount,
            ]);

            DB::commit();

            return response()->json([
                'order' => $order->fresh()->load('orderDetails.product', 'discounts'),
                'discount_amount' => $discountAmount,
                'final_price' => $order->total_price - $discountAmount,
                'message' => 'Áp dụng mã giảm giá thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Đã xảy ra lỗi khi áp dụng mã giảm giá: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Hủy áp dụng mã giảm giá cho đơn hàng
     */
    public function removeFromOrder(Request $request, Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        // Kiểm tra xem đơn hàng có mã giảm giá không
        if ($order->discounts()->count() === 0) {
            return response()->json([
                'message' => 'Đơn hàng này chưa áp dụng mã giảm giá'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Xóa tất cả liên kết giữa đơn hàng và mã giảm giá
            Order_Discount::where('order_id', $order->id)->delete();

            DB::commit();

            return response()->json([
                'order' => $order->fresh()->load('orderDetails.product'),
                'message' => 'Hủy áp dụng mã giảm giá thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Đã xảy ra lỗi khi hủy áp dụng mã giảm giá: ' . $e->getMessage()
            ], 500);
        }
    }
}
