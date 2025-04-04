<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
    ];

    /**
     * Lấy người dùng đặt đơn hàng
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy chi tiết đơn hàng
     */
    public function orderDetails()
    {
        return $this->hasMany(Order_Detail::class);
    }

    /**
     * Lấy thanh toán của đơn hàng
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'order_discounts')
            ->withPivot('discount_amount')
            ->withTimestamps();
    }

    /**
    * Lấy tổng số tiền giảm giá
    */
    public function getTotalDiscountAttribute()
    {
        return $this->discounts()->sum('discount_amount');
    }

    /**
    * Lấy giá trị đơn hàng sau khi áp dụng giảm giá
    */
    public function getFinalPriceAttribute()
    {
        return $this->total_price - $this->getTotalDiscountAttribute();
    }
}
