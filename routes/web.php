<?php

use Illuminate\Support\Facades\Route;

// === CONTROLLERS ===
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController as PublicCategoryController;
use App\Http\Controllers\TagController as PublicTagController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Author\ArticleController as AuthorArticleController;

// ========= 1. ROUTE CÔNG KHAI =========
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/bai-viet/{slug}', [ArticleController::class, 'show'])
    ->name('article.show');

Route::get('/danh-muc/{slug}', [PublicCategoryController::class, 'show'])
    ->name('category.show');

Route::get('/tag/{slug}', [PublicTagController::class, 'show'])
    ->name('tag.show');


// ========= 2. REDIRECT SAU ĐĂNG NHẬP =========
Route::get('/redirect', function () {
    $user = auth()->user();

    return match ((int) $user->role) {
        1 => redirect()->route('admin.dashboard'),
        2 => redirect()->route('author.articles.index'),
        default => redirect()->route('home'),
    };
})->middleware('auth')->name('redirect');


// ========= 3. ADMIN ROUTES (role = 1) =========
Route::middleware(['auth', 'role:1'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Quản lý nội dung
        Route::resource('articles', AdminArticleController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('tags', TagController::class);
        Route::resource('games', GameController::class);
        Route::resource('users', UserController::class);
    });


// ========= 4. AUTHOR ROUTES (role = 2) =========
Route::middleware(['auth', 'role:2'])
    ->prefix('author')
    ->name('author.')
    ->group(function () {
        Route::resource('articles', AuthorArticleController::class);
    });


// ========= 5. HỒ SƠ NGƯỜI DÙNG =========
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


// ========= 6. AUTH ROUTES (Laravel Breeze / Fortify) =========
require __DIR__.'/auth.php';