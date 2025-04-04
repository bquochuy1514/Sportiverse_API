<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get a current user's cart
     */
    public function index(Request $request)
    {
        $cartItems = Cart::where('user_id', $request->user()->id)
            ->with('product')
            ->get();
        
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
        
        return response()->json([
            'items' => $cartItems,
            'total_price' => $totalPrice
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Kiểm tra số lượng sản phẩm trong kho
        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Insufficient product quantity. Only ' . $product->stock . ' products left.'
            ], 422);
        }
        
        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $existingItem = Cart::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->first();
            
        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $request->quantity;
            
            // Kiểm tra lại số lượng mới có vượt quá số lượng trong kho không
            if ($newQuantity > $product->stock) {
                return response()->json([
                    'message' => 'Insufficient product quantity. Only ' . $product->stock . ' products left.'
                ], 422);
            }
            
            $existingItem->update(['quantity' => $newQuantity]);
            return response()->json([
                'item' => $existingItem->load('product'),
                'message' => 'Updated quantity of products in cart successfully'
            ]);
        }
        
        $cartItem = Cart::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);
        
        return response()->json([
            'item' => $cartItem->load('product'),
            'message' => 'Product added to cart successfully'
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        // Kiểm tra xem item giỏ hàng có thuộc về người dùng hiện tại không
        if ($cart->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }
        
        $request->validate([
            'quantity' => 'required|integer|min:1', // Chỉ cần yêu cầu số lượng
        ]);
        
        // Kiểm tra số lượng sản phẩm trong kho
        $product = $cart->product; // Lấy sản phẩm từ cart item
        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Số lượng sản phẩm không đủ. Hiện chỉ còn ' . $product->stock . ' sản phẩm.'
            ], 422);
        }
        
        // Cập nhật số lượng sản phẩm trong giỏ hàng
        $cart->update(['quantity' => $request->quantity]);
        
        return response()->json([
            'item' => $cart->load('product'),
            'message' => 'Cập nhật số lượng sản phẩm ' . $product->name . ' thành công'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Cart $cart)
    {
        // Kiểm tra xem item giỏ hàng có thuộc về người dùng hiện tại không
        if ($cart->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }
        
        $cart->delete();
        
        return response()->json([
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
        ]);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    // public function clear(Request $request)
    // {
    //     Cart::where('user_id', $request->user()->id)->delete();
        
    //     return response()->json([
    //         'message' => 'Đã xóa toàn bộ giỏ hàng'
    //     ]);
    // }
}
