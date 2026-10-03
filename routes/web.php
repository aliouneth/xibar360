<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\AdController as AdminAdController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SearchController as AdminSearchController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\StatsController as AdminStatsController;

// Public Routes
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/articles/{article}', [PublicController::class, 'show'])->name('articles.show');
Route::get('/category/{category}', [PublicController::class, 'category'])->name('category.show');
Route::get('/search', [PublicController::class, 'search'])->name('search.results');
Route::get('/{lang}/articles/{article}', [PublicController::class, 'localizedShow'])->where('lang', 'fr|en')->name('articles.show.lang');
Route::get('/{lang}/category/{category}', [PublicController::class, 'localizedCategory'])->where('lang', 'fr|en')->name('category.show.lang');

// Language Switcher
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['fr', 'en'])) {
        session(['locale' => $locale]);
    }
    $target = url()->previous();

    if ($target === url()->current()) {
        $target = url('/');
    }

    return redirect()->to($target);
})->name('lang.switch');

// Auth Routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('articles', AdminArticleController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('ads', AdminAdController::class);
    Route::resource('users', AdminUserController::class);
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/search', [AdminSearchController::class, 'index'])->name('search');
    Route::get('/stats', [AdminStatsController::class, 'index'])->name('stats');
    Route::post('/news/refresh', [AdminNewsController::class, 'refresh'])->name('news.refresh');
    Route::post('/articles/{article}/toggle-publish', [AdminArticleController::class, 'togglePublish'])->name('articles.toggle-publish');
    Route::post('/ads/{ad}/toggle', [AdminAdController::class, 'toggle'])->name('ads.toggle');
    Route::post('/users/{user}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');
});

// Admin Article Routes (for editors too)
Route::middleware(['auth', 'role:admin|editor'])->prefix('editor')->name('editor.')->group(function () {
    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
});