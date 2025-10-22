<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Autenticación ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Categorías ---
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});

// --- Productos ---
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
});

// --- Órdenes ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders/stats', [OrderController::class, 'stats']); // 👈 debe ir primero
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/user', [OrderController::class, 'userOrders']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
});



Route::post('/orders', [OrderController::class, 'store']);
Route::post('/orders/verify', [OrderController::class, 'verify']);


Route::post('/orders/send-code', [OrderController::class, 'sendCode']);
Route::post('/orders/confirm', [OrderController::class, 'confirm']);


// Hasta aquí llega lo que sugieres, a continuación está otro código que ya estaba, hay que validar si es necesario mantenerlo 
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    
    // CRUD Categories
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{category}', [CategoryController::class, 'update']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']); 

    // CRUD Products
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);

    // Orders
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/user', [OrderController::class, 'userOrders']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);

    // Admin only
    Route::middleware('admin')->group(function () {
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{category}', [CategoryController::class, 'update']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy']); 

        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{product}', [ProductController::class, 'update']);
        Route::delete('products/{product}', [ProductController::class, 'destroy']);

        Route::get('orders', [OrderController::class, 'index']);

    });

    return $request->user();
});


