<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::get('/{id}', [UserController::class, 'show'])->name('user.show');
        Route::post('/store', [UserController::class, 'store'])->name('user.store');
        Route::post('/{id}/update', [UserController::class, 'update'])->name('user.update');
        Route::delete('/{id}/destroy', [UserController::class, 'destroy'])->name('user.destroy');
    });

    Route::prefix('blogs')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('blog.index');
        Route::get('/{id}', [BlogController::class, 'show'])->name('blog.show');
        Route::post('/store', [BlogController::class, 'store'])->name('blog.store');
        Route::post('/{id}/update', [BlogController::class, 'update'])->name('blog.update');
        Route::delete('/{id}/destroy', [BlogController::class, 'destroy'])->name('blog.destroy');
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('product.index');
        Route::get('/{id}', [ProductController::class, 'show'])->name('product.show');
        Route::post('/store', [ProductController::class, 'store'])->name('product.store');
        Route::post('/{id}/update', [ProductController::class, 'update'])->name('product.update');
        Route::delete('/{id}/destroy', [ProductController::class, 'destroy'])->name('product.destroy');
    });

    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('addresse.index');
        Route::get('/{id}', [AddressController::class, 'show'])->name('addresse.show');
        Route::post('/store', [AddressController::class, 'store'])->name('addresse.store');
        Route::post('/{id}/update', [AddressController::class, 'update'])->name('addresse.update');
        Route::delete('/{id}/destroy', [AddressController::class, 'destroy'])->name('addresse.destroy');
    });
});
