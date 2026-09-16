<?php

use App\Http\Controllers\Api\AboutUsController;
use Illuminate\Support\Facades\Route;

// Public, read-only About Us content. No auth/token — see CLAUDE.md.
Route::prefix('about-us')->group(function () {
    Route::get('/', [AboutUsController::class, 'aboutUs']);
    Route::get('/milestones', [AboutUsController::class, 'milestones']);
    Route::get('/partners', [AboutUsController::class, 'partners']);
    Route::get('/client-categories', [AboutUsController::class, 'clientCategories']);
});
