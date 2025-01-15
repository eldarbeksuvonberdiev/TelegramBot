@extends('meal.main')

@section('title', 'Orders')

@section('content')
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered table-hover table-striped mt-5">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Created By</th>
                        <th>Delivered By</th>
                        <th>Delivery Time</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Meals</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->user->name }}</td>
                            <td>{{ $order->deliver->name }}</td>
                            <td>{{ $order->delivery_time }}</td>
                            @php
                                $locationData = json_decode($order->location, true);
                                $status = $order->status;
                                // broadcast(new \App\Events\OrderStatusEvent($order->status));
                                // broadcast(new \App\Events\OrderIdEvent($order->id));
                            @endphp
                            <td>
                                Latitude: {{ $locationData['latitude'] }}<br>
                                Longitude: {{ $locationData['longitude'] }}
                            </td>
                            <td id="statusOfOrder_{{ $order->id }}">
                                @if ($status == 0)
                                    <button class="btn btn-danger">Rejected</button>
                                @elseif($status == 1)
                                    <button class="btn btn-primary">Given</button>
                                @elseif($status == 2)
                                    <button class="btn btn-success">Given</button>
                                @endif
                            </td>
                            <td>
                                @foreach ($order->orderItems as $meal)
                                    {{ 'Nomi: ' . $meal->meal->name . ', ' }}
                                    {{ 'Soni: ' . $meal->quantity }}<br>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
