<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_Discount extends Model
{
    /** @use HasFactory<\Database\Factories\OrderDiscountFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'discount_id',
        'discount_amount',
    ];

    /**
     * Lấy đơn hàng mà mã giảm giá áp dụng cho
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Lấy mã giảm giá được áp dụng
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
