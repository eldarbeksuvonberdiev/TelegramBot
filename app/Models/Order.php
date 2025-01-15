<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['admin_id', 'deliver_id', 'location', 'delivery_time', 'status'];

    // protected $casts = ['location' => 'array'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function deliver()
    {
        return $this->belongsTo(User::class, 'deliver_id');
    }
}
