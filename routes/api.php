<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('product.index');
    Route::get('/{id}', [ProductController::class, 'show'])->name('product.show');
    Route::post('/store', [ProductController::class, 'store'])->name('product.store');
    Route::post('/{id}/update', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/{id}/destroy', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::get('/category/{type}', [ProductController::class, 'getByCategory'])->name('product.get-by-category');
    Route::get('/search/{name}', [ProductController::class, 'getByName'])->name('product.get-by-name');
    Route::get('/limit/{limit}', [ProductController::class, 'getWithLimit'])->name('product.get-with-limit');
});

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::get('/{id}', [UserController::class, 'show'])->name('user.show');
        Route::get('/id/info', [UserController::class, 'userInfo'])->name('user.info');
        Route::post('/store', [UserController::class, 'store'])->name('user.store');
        Route::post('/{id}/update', [UserController::class, 'update'])->name('user.update');
        Route::post('/{id}/updateProfile', [UserController::class, 'updateProfile'])->name('user.updateProfile');
        Route::delete('/{id}/destroy', [UserController::class, 'destroy'])->name('user.destroy');
    });

    Route::prefix('blogs')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('blog.index');
        Route::get('/{id}', [BlogController::class, 'show'])->name('blog.show');
        Route::post('/store', [BlogController::class, 'store'])->name('blog.store');
        Route::post('/{id}/update', [BlogController::class, 'update'])->name('blog.update');
        Route::delete('/{id}/destroy', [BlogController::class, 'destroy'])->name('blog.destroy');
        Route::get('/search/{title}', [BlogController::class, 'getByTitle'])->name('blog.get-by-title');
        Route::get('/limit/{limit}', [BlogController::class, 'getWithLimit'])->name('blog.get-with-limit');
    });

    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('address.index');
        Route::get('/{id}', [AddressController::class, 'show'])->name('address.show');
        Route::get('/id/user', [AddressController::class, 'user'])->name('address.user');
        Route::get('/user/{userId}', [AddressController::class, 'getAddressByUserId'])->name('address.get-by-user-id');
        Route::post('/store', [AddressController::class, 'store'])->name('address.store');
        Route::post('/{id}/update', [AddressController::class, 'update'])->name('address.update');
        Route::delete('/{id}/destroy', [AddressController::class, 'destroy'])->name('address.destroy');
    });

    Route::prefix('banks')->group(function () {
        Route::get('/', [BankController::class, 'index'])->name('banks.index');
        Route::get('/{id}', [BankController::class, 'show'])->name('banks.show');
        Route::post('/store', [BankController::class, 'store'])->name('banks.store');
        Route::post('/{id}/update', [BankController::class, 'update'])->name('banks.update');
        Route::delete('/{id}/destroy', [BankController::class, 'destroy'])->name('banks.destroy');
    });

    Route::prefix('carts')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('carts.index');
        Route::get('/{id}', [CartController::class, 'show'])->name('carts.show');
        Route::post('/store', [CartController::class, 'store'])->name('carts.store');
        Route::post('/{id}/update', [CartController::class, 'update'])->name('carts.update');
        Route::delete('/{id}/destroy', [CartController::class, 'destroy'])->name('carts.destroy');
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
        Route::post('/checkout', [TransactionController::class, 'checkout'])->name('transaction.checkout');
        Route::post('/{id}/add-proof', [TransactionController::class, 'addProof'])->name('transactions.add-proof');
        Route::post('/{id}/update-status', [TransactionController::class, 'updateStatus'])->name('transactions.update-status');
        Route::delete('/{id}/destroy', [TransactionController::class, 'destroy'])->name('transaction.destroy');
    });

    Route::prefix('ratings')->group(function () {
        Route::get('/', [RatingController::class, 'index'])->name('ratings.index');
        Route::post('/store', [RatingController::class, 'store'])->name('ratings.store');
        Route::get('/{id}', [RatingController::class, 'show'])->name('ratings.show');
        Route::post('/{id}/update', [RatingController::class, 'update'])->name('ratings.update');
        Route::delete('/{id}/destroy', [RatingController::class, 'destroy'])->name('ratings.destroy');
        Route::get('/get-average/{product_id}', [RatingController::class, 'getAverage'])->name('ratings.get-average');
    });

    Route::prefix('wishlists')->group(function () {
        Route::get('/id/user', [WishlistController::class, 'user'])->name('wishlists.user');
        Route::post('/store', [WishlistController::class, 'store'])->name('wishlists.store');
        Route::delete('/{id}/destroy', [WishlistController::class, 'destroy'])->name('wishlists.destroy');
    });
});
