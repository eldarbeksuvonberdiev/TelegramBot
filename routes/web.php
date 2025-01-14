<?php

use App\Http\Controllers\MealController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/meal', [MealController::class, 'index'])->middleware(['auth', 'verified'])->name('meal');
Route::get('/meal/create', [MealController::class, 'create'])->middleware(['auth', 'verified'])->name('meal.create');
Route::post('/meal/create', [MealController::class, 'store'])->middleware(['auth', 'verified'])->name('meal.store');


Route::get('/meal-toCart/{meal}', [MealController::class, 'addToCart'])->middleware(['auth', 'verified'])->name('meal.addToCart');
Route::get('/meal-cart', [MealController::class, 'cart'])->middleware(['auth', 'verified'])->name('meal.cart');
Route::get('/cart-remove/{meal}', [MealController::class, 'remove'])->middleware(['auth', 'verified'])->name('cart.remove');
Route::post('/cart-clear', [MealController::class, 'clearCart'])->middleware(['auth', 'verified'])->name('cart.clear');
Route::post('/cart-update', [MealController::class, 'update'])->middleware(['auth', 'verified'])->name('cart.update');
Route::post('/cart-placeOrder', [MealController::class, 'clearCart'])->middleware(['auth', 'verified'])->name('cart.placeOrder');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/telegram/message', [TelegramController::class, 'index']);
Route::get('/telegram/file', [TelegramController::class, 'indexFile']);
Route::get('/telegram/selection', [TelegramController::class, 'indexSelection']);


Route::post('/telegram/message', [TelegramController::class, 'store'])->name('telegram.sendMessage');
Route::post('/telegram/file', [TelegramController::class, 'sendMessageWithFile'])->name('telegram.sendMessageWithFile');
Route::post('/telegram/selection', [TelegramController::class, 'sendMessageBySelecting'])->name('telegram.sendMessageBySelected');


require __DIR__ . '/auth.php';
