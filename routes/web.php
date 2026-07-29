<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StaffUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::redirect('/blog', '/', 301);

Route::middleware('guest')->group(function () {
    Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])
        ->name('login');

    Route::get('/register', [AuthController::class, 'register'])
        ->name('register');
    Route::post('/register', [AuthController::class, 'storeRegistration'])
        ->name('register.store');
});

Route::prefix('admin')->name('admin.')->middleware('guest')->group(function () {
    Route::match(['get', 'post'], '/login', [AuthController::class, 'adminLogin'])
        ->name('login');
});

Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::post('/blog/{slug}/comments', [CommentController::class, 'store'])
    ->middleware(['auth', 'verified', 'frontend.user'])
    ->name('blog.comments.store');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::post('/admin/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('admin.logout');

Route::get('/set-language/{locale}', [AuthController::class, 'setLanguage'])->name('set-language');
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');
Route::get('/verify-email', [AuthController::class, 'verificationNotice'])
    ->name('verification.notice');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'blog.staff'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('change-password', [AuthController::class, 'changePassword'])->name('change-password');

        Route::get('posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::post('posts', [PostController::class, 'store'])->name('posts.store');
        Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
        Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

        Route::redirect('users', 'members', 301)->name('users.redirect');

        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::get('media/{media}', [MediaController::class, 'show'])->name('media.show');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'blog.admin'])
    ->group(function () {
        Route::get('members', [MemberController::class, 'index'])->name('members.index');
        Route::resource('staff', StaffUserController::class)->except(['show']);
    });
