<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);


Route::get('products/before', [ProductController::class, 'indexBefore']);
Route::get('products/after', [ProductController::class, 'indexAfter']);
Route::get('products/{id}', [ProductController::class, 'displayOneProduct']);

Route::get('daily-report-before', [ReportController::class, 'generateDailyReport']);

Route::get('daily-report-after', [ReportController::class, 'generateDailyReportWithJob']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [UserController::class, 'logout']);


Route::middleware(['auth:sanctum', 'throttle:10,1'])->group(function () {

    Route::post('cart/add', [OrderController::class, 'addToOrder']);
    Route::put('cart/update/{id}', [OrderController::class, 'updateOrder']);
    Route::delete('cart/remove/{id}', [OrderController::class, 'cancelOrder']);
    Route::get('cart', [OrderController::class, 'getUserOrders']);
    Route::post('checkout-no-lock', [OrderController::class, 'confirmOrderWithoutLock']);
    Route::post('checkout', [OrderController::class, 'confirmOrder']);
    Route::post('checkout-async', [OrderController::class, 'confirmOrderAsync']);
    Route::post('checkout-sync',[OrderController::class, 'confirmOrderSync']);

});

});
