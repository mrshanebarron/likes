<?php

use Illuminate\Support\Facades\Route;
use MrShaneBarron\Likes\Http\Controllers\ReactionsController;

Route::prefix('ld-likes')->middleware('web')->group(function () {
    Route::post('/react', [ReactionsController::class, 'react'])->name('ld-likes.react');
    Route::get('/status', [ReactionsController::class, 'status'])->name('ld-likes.status');
});
