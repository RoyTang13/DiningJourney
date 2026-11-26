<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PlaceController as AdminPlaceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PlaceController::class, 'home'])->name('home');
Route::get('/search', [PlaceController::class, 'search'])->name('places.search');
Route::get('/attractions', [PlaceController::class, 'index'])->defaults('type', 'attraction')->name('places.attractions');
Route::get('/restaurants', [PlaceController::class, 'index'])->defaults('type', 'restaurant')->name('places.restaurants');
Route::get('/restaurants/compare', [PlaceController::class, 'compare'])->name('places.compare');
Route::get('/place/{place:slug}', [PlaceController::class, 'show'])->name('places.show');
Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile.edit');
    Route::patch('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/photo', [ProfilePhotoController::class, 'update'])->name('profile.photo');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
    Route::get('/preferences', [PreferenceController::class, 'edit'])->name('preferences.edit');
    Route::put('/preferences', [PreferenceController::class, 'update'])->name('preferences.update');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{place}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/places/{type}', [AdminPlaceController::class, 'index'])
        ->whereIn('type', ['attraction', 'restaurant'])
        ->name('places.index');
    Route::get('/places/{type}/create', [AdminPlaceController::class, 'create'])
        ->whereIn('type', ['attraction', 'restaurant'])
        ->name('places.create');
    Route::post('/places/{type}', [AdminPlaceController::class, 'store'])
        ->whereIn('type', ['attraction', 'restaurant'])
        ->name('places.store');
    Route::get('/places/{type}/{place}/edit', [AdminPlaceController::class, 'edit'])
        ->whereIn('type', ['attraction', 'restaurant'])
        ->whereNumber('place')
        ->name('places.edit');
    Route::put('/places/{type}/{place}', [AdminPlaceController::class, 'update'])
        ->whereIn('type', ['attraction', 'restaurant'])
        ->whereNumber('place')
        ->name('places.update');
    Route::delete('/places/{type}/{place}', [AdminPlaceController::class, 'destroy'])
        ->whereIn('type', ['attraction', 'restaurant'])
        ->whereNumber('place')
        ->name('places.destroy');
});
