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

//https://api.telegram.org/bot7552280930:AAHKxj0v2bVLh_mbHJLE66FjwI3mXkER9q4/setWebhook?url=https://5c06-188-113-247-181.ngrok-free.app/api/registration
//https://api.telegram.org/bot7552280930:AAHKxj0v2bVLh_mbHJLE66FjwI3mXkER9q4/getWebhookInfo