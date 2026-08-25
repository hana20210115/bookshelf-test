<?php

use App\Http\Controllers\Api\V1\BookApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/books', [BookApiController::class, 'index']);
    Route::get('/books/{book}', [BookApiController::class, 'show']);
    
    Route::middleware('auth:sanctum')->group(function(){
        Route::Post('/books', [BookApiController::class, 'store']);
        Route::put('/books/{book}', [BookApiController::class, 'update']);
        Route::delete('/books/{book}', [BookApiController::class, 'destroy']);
    });
});
