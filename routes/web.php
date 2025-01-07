<?php

use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/telegram',[TelegramController::class,'index']);
Route::post('/telegram',[TelegramController::class,'store'])->name('telegram.send');
