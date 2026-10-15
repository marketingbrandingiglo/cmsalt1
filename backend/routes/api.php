<?php

use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\HomeController;
use Illuminate\Support\Facades\Route;

// Public, read-only About Us content. No auth/token — see CLAUDE.md.
Route::prefix('about-us')->group(function () {
    Route::get('/', [AboutUsController::class, 'aboutUs']);
    Route::get('/milestones', [AboutUsController::class, 'milestones']);
    Route::get('/partners', [AboutUsController::class, 'partners']);
    Route::get('/client-categories', [AboutUsController::class, 'clientCategories']);
});

// Public, read-only Home page content. No auth/token — see CLAUDE.md.
Route::prefix('home')->group(function () {
    Route::get('/', [HomeController::class, 'home']);
    Route::get('/hero-slides', [HomeController::class, 'heroSlides']);
});
