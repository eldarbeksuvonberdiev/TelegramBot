<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        // foreach ($orders as $order) {
        //     dd(json_decode($order->location,true));
        // }
        $cart = session()->get('cart',[]);
        return view('order.index',compact('orders', 'cart'));
    }
}
