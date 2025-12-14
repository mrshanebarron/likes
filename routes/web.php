<?php

use Illuminate\Support\Facades\Route;
use MrShaneBarron\Likes\Http\Controllers\ReactionsController;

Route::prefix('sb-likes')->middleware('web')->group(function () {
    Route::post('/react', [ReactionsController::class, 'react'])->name('sb-likes.react');
    Route::get('/status', [ReactionsController::class, 'status'])->name('sb-likes.status');
});
