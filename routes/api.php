<?php

use App\Http\Controllers\TelegramController;
use App\Http\Controllers\TelegramRegistrationConstroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/send-reverse', [TelegramController::class, 'sendReverse']);
Route::post('/registration', [TelegramRegistrationConstroller::class, 'handle']);

