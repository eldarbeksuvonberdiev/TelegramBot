<?php

use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/telegram/message',[TelegramController::class,'index']);
Route::get('/telegram/file',[TelegramController::class,'indexFile']);
Route::post('/telegram/message',[TelegramController::class,'store'])->name('telegram.sendMessage');
Route::post('/telegram/file',[TelegramController::class,'sendMessageWithFile'])->name('telegram.sendMessageWithFile');
