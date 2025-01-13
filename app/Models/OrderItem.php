<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'meal_id',
        'quantity',
        'location',
    ];

    protected $casts = [
        'location' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class, 'meal_id');
    }
}
