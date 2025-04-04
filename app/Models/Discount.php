<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_value',
        'max_discount',
        'start_date',
        'end_date',
        'usage_limit',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Lấy các đơn hàng đã sử dụng mã giảm giá này
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_discounts')
            ->withPivot('discount_amount')
            ->withTimestamps();
    }

    /**
     * Kiểm tra xem mã giảm giá có hợp lệ không
     */
    public function isValid($orderValue = 0)
    {
        // Kiểm tra ngày hiệu lực
        if ($this->start_date && now() < $this->start_date) {
            return false;
        }

        // Kiểm tra ngày hết hạn
        if ($this->end_date && now() > $this->end_date) {
            return false;
        }

        // Kiểm tra số lần sử dụng
        if ($this->usage_limit !== null && $this->orders()->count() >= $this->usage_limit) {
            return false;
        }

        // Kiểm tra giá trị đơn hàng tối thiểu
        if ($orderValue < $this->min_order_value) {
            return false;
        }

        return true;
    }

    /**
     * Tính toán số tiền giảm giá
     */
    public function calculateDiscount($orderValue)
    {
        if (!$this->isValid($orderValue)) {
            return 0;
        }

        if ($this->discount_type === 'fixed') {
            return $this->discount_value;
        } else { // percent
            $discount = $orderValue * ($this->discount_value / 100);

            // Áp dụng giới hạn giảm giá tối đa nếu có
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                return $this->max_discount;
            }

            return $discount;
        }
    }
}
