<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::with('sport');
        // 1. Lọc theo môn thể thao
        if ($request->has('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }
        
        // 2. Tìm kiếm theo tên
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // 3. Sắp xếp
        // 3.1. Lấy giá trị từ request, nếu không có thì dùng giá trị mặc định
        $sortField = $request->get('sort_by', 'name'); // Mặc định sắp xếp theo 'name'
        $sortDirection = $request->get('sort_direction', 'asc'); // Mặc định 'asc' (tăng dần)

        // 3.2. Sắp xếp theo cột và hướng do người dùng chọn
        $query->orderBy($sortField, $sortDirection);
        
        // 4. Phân trang
        $perPage = $request->get('per_page', 15);
        $categories = $query->paginate($perPage);
        
        return response()->json(['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'sport_id' => 'required|exists:sports,id',
        ]);

        $category = Category::create($fields);

        return response()->json([
            'category' => $category->load('sport'),
            'message' => 'Tạo danh mục thành công'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json([
            'category' => $category->load('sport')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $fields = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'sport_id' => 'sometimes|required|exists:sports,id',
        ]);

        $category->update($fields);

        return response()->json([
            'category' => $category->fresh()->load('sport'),
            'message' => 'Cập nhật danh mục thành công'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Kiểm tra xem danh mục có sản phẩm không
        if ($category->products()->count() > 0) {
            return response()->json([
                'message' => 'Không thể xóa danh mục này vì có sản phẩm liên quan'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Xóa danh mục thành công'
        ]);
    }

    /**
     * Lấy danh sách sản phẩm thuộc danh mục
     */
    // public function products(Category $category, Request $request)
    // {
    //     $query = $category->products();
        
    //     // Lọc theo khoảng giá
    //     if ($request->has('min_price')) {
    //         $query->where('price', '>=', $request->min_price);
    //     }
        
    //     if ($request->has('max_price')) {
    //         $query->where('price', '<=', $request->max_price);
    //     }
        
    //     // Tìm kiếm theo tên
    //     if ($request->has('search')) {
    //         $query->where('name', 'like', '%' . $request->search . '%');
    //     }
        
    //     // Sắp xếp
    //     $sortField = $request->get('sort_by', 'created_at');
    //     $sortDirection = $request->get('sort_direction', 'desc');
    //     $query->orderBy($sortField, $sortDirection);
        
    //     // Phân trang
    //     $perPage = $request->get('per_page', 15);
        
    //     return response()->json($query->paginate($perPage));
    // }
}
