@extends('meal.main')

@section('title', 'Main')

@section('content')
    <div class="row">
        <div class="row">
            @if (empty($cart))
                <p>Your cart is currently empty.</p>
            @else
                <form action="{{ route('cart.update') }}" method="POST">
                    @csrf
                    <table style="width: 100%; border-collapse: collapse;" border="1">
                        <thead>
                            <tr>
                                <th style="padding: 8px; text-align: left;">Meal Name</th>
                                <th style="padding: 8px; text-align: center;">Quantity</th>
                                <th style="padding: 8px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart as $id => $meal)
                                <tr>
                                    <td style="padding: 8px;">{{ $meal['name'] }}</td>
                                    <td style="padding: 8px; text-align: center;">
                                        <input type="number" name="quantity[{{ $id }}]"
                                            value="{{ $meal['quantity'] }}" min="1"
                                            style="width: 50px; text-align: center;">
                                    </td>
                                    <td style="padding: 8px; text-align: center;">
                                        <form action="{{ route('cart.remove') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="meal_id" value="{{ $id }}">
                                            <button type="submit"
                                                style="background: red; color: white; border: none; padding: 5px 10px; cursor: pointer;">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Update Cart Button -->
                    <button type="submit"
                        style="margin-top: 15px; background: green; color: white; border: none; padding: 10px 20px; cursor: pointer;">Update
                        Cart</button>
                </form>

                <!-- Place Order Button -->
                <form action="{{ route('cart.placeOrder') }}" method="POST" style="margin-top: 15px;">
                    @csrf
                    <button type="submit"
                        style="background: blue; color: white; border: none; padding: 10px 20px; cursor: pointer;">Place
                        Order</button>
                </form>

                <!-- Clear Cart Button -->
                <form action="{{ route('cart.clear') }}" method="POST" style="margin-top: 15px;">
                    @csrf
                    <button type="submit"
                        style="background: orange; color: white; border: none; padding: 10px 20px; cursor: pointer;">Clear
                        Cart</button>
                </form>
            @endif
        </div>
    </div>
@endsection
