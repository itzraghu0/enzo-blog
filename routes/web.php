<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/', [AuthController::class, 'login'])
    ->middleware('guest')
    ->name('login');

Route::get('register', [AuthController::class, 'register'])
    ->middleware('guest')
    ->name('register');
Route::post('register', [AuthController::class, 'storeRegistration'])
    ->middleware('guest')
    ->name('register.store');

Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::post('blog/{slug}/comments', [\App\Http\Controllers\CommentController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('blog.comments.store');
Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('set-language/{locale}', [AuthController::class, 'setLanguage'])->name('set-language');
Route::get('verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');
Route::get('verify-email', [AuthController::class, 'verificationNotice'])
    ->name('verification.notice');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [PostController::class, 'index'])->name('dashboard');
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('change-password');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::get('media/{media}', [MediaController::class, 'show'])->name('media.show');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
        Route::post('posts', [PostController::class, 'store'])->name('posts.store');
        Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
        Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
});
