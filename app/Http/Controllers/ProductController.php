<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::all();
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
            'product' => $product
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'message' => 'Product retrieved successfully',
            'product' => $product
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
            'product' => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
          'message' => 'Product deleted successfully'
        ], 200);
    }
}
