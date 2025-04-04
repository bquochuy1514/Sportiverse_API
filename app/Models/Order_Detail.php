<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_Detail extends Model
{
    /** @use HasFactory<\Database\Factories\OrderDetailsFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $table = 'order_details';

    /**
     * Lấy đơn hàng mà chi tiết này thuộc về
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Lấy sản phẩm trong chi tiết đơn hàng
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
