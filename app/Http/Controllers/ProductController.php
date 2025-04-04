<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category.sport');

        // Lọc theo danh mục
        if($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo môn thể thao
        if($request->has('sport_id')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('sport_id', $request->sport_id);
            });
        }

        // Lọc theo khoảng giá
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Tìm kiếm theo tên
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sắp xếp
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Phân trang
        $perPage = $request->get('per_page', 15);

        return response()->json($query->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'category_id' => 'required|exists:categories,id', 
            'name' => 'required|string|max:255', 
            'price' => 'required|numeric|min:0', 
            'description' => 'required|string', 
            'stock' => 'required|integer|min:0', 
            'image' => 'nullable|string'
        ]);
        
        $product = Product::create($fields);
        
        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load('category.sport')
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'message' => 'Product retrieved successfully',
            'product' => $product->load('category.sport')
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $fields = $request->validate([
            'category_id' => 'exists:categories,id',
            'name' => 'string|max:255',
            'price' => 'integer|min:0', 
            'description' => 'string',
            'stock' => 'integer|min:0',
            'image' => 'nullable|string'
        ]);

        $product->update($fields);

        return response()->json([
           'message' => 'Product updated successfully',
            'product' => $product->load('category.sport')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Kiểm tra xem sản phẩm có trong đơn hàng nào không
        // if ($product->orderDetails()->count() > 0) {
        //     return response()->json([
        //         'message' => 'This product cannot be deleted because it is already in the order details!'
        //     ], 422);
        // }

        $product->delete();

        return response()->json([
          'message' => 'Product deleted successfully'
        ], 200);
    }
}
