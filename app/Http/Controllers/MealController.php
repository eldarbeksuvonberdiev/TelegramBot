<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $meals = Meal::all();
        $cart = session()->get('cart', []);
        return view('meal.meal', compact('meals', 'cart'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cart = session()->get('cart', []);
        return view('meal.create', compact('cart'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
        ]);
        Meal::create($data);
        return redirect()->route('meal');
    }

    public function addToCart(Meal $meal)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$meal->id])) {

            $cart[$meal->id]['quantity'] = $cart[$meal->id]['quantity'] + 1;
        } else {

            $cart[$meal->id] = [
                'name' => $meal->name,
                'quantity' => 1
            ];
        }
        session()->put('cart', $cart);
        return back();
    }

    public function cart()
    {
        $cart = session()->get('cart', []);
        $users = User::where('role', '!=', 'admin')->get();
        return view('meal.cart', compact('cart', 'users'));
    }

    public function clearCart(Request $request)
    {
        $request->session()->forget('cart');
        return redirect()->route('meal')->with('success', 'Cart cleared successfully.');
    }


    public function update(Request $request)
    {
        $quantities = $request->input('quantity', []);

        $cart = $request->session()->get('cart', []);

        foreach ($quantities as $mealId => $quantity) {
            if (isset($cart[$mealId])) {
                $cart[$mealId]['quantity'] = max(1, (int)$quantity);
            }
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('meal.cart')->with('success', 'Cart updated successfully.');
    }

    public function remove(Meal $meal)
    {

        $cart = session()->get('cart', []);
        unset($cart[$meal->id]);
        if (count($cart)) {
            session()->put('cart', $cart);
        } else {
            session()->forget('cart');
        }

        return back();
    }

    public function placeOrder(Request $request)
    {
        $data = $request->validate([
            'deliver' => 'required',
            'dalivery_time' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('meal.cart')->with('error', 'Your cart is empty. Add items before placing an order.');
        }

        $order = Order::create([
            'admin_id' => Auth::user()->id,
            'deliver_id' => $data['deliver'],
            'location' => json_encode([
                'longitude' => $data['longitude'],
                'latitude' => $data['latitude']
            ]),

        ]);

        foreach ($cart as $id => $meal) {
            $order->orderItems->create([
                'meal_id' => $id,
                'quantity' => $cart[$id]['quantity'],
            ]);
        }



        $request->session()->forget('cart');

        return redirect()->route('meal.cart')->with('success', 'Order placed successfully! Your order ID is #' . $order->id);
    }
}
