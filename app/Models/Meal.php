<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $fillable = ['name'];

    public function orderItem() 
    {
        return $this->hasMany(OrderItem::class, 'meal_id');
    }
}
