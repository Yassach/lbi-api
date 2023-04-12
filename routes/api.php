<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MoviePeopleController;
use App\Http\Controllers\MovieTypeController;
use App\Http\Controllers\PeopleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Movies
Route::prefix('movies')->group(function (){
    Route::get('/', [MovieController::class, 'index']);
    Route::get('/{id}', [MovieController::class, 'show']);

    // Movie Types
    Route::get('/{id}/type', [MovieTypeController::class, 'show']);

    // Movie People
    Route::get('/{id}/people', [MoviePeopleController::class, 'show']);
});

// People
Route::prefix('people')->group(function (){
    Route::get('/', [PeopleController::class, 'index']);
    Route::get('/{id}', [PeopleController::class, 'show']);
});

// Protected API endpoints
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Movies
    Route::prefix('movies')->group(function (){
        Route::post('/', [MovieController::class, 'store']);
        Route::put('/{id}', [MovieController::class, 'update']);
        Route::delete('/{id}', [MovieController::class, 'destroy']);

        // Movie People
        Route::prefix('/{id}/people')->group(function (){
            Route::post('/', [MoviePeopleController::class, 'store']);
            Route::put('/{people}', [MoviePeopleController::class, 'update']);
            Route::delete('/{people}', [MoviePeopleController::class, 'destroy']);
        });

        // Movie Types
        Route::prefix('/{id}/type')->group(function (){
            Route::post('/', [MovieTypeController::class, 'store']);
            Route::put('/{type}', [MovieTypeController::class, 'update']);
            Route::delete('/{type}', [MovieTypeController::class, 'destroy']);
        });
    });

    // People
    Route::prefix('people')->group(function (){
        Route::post('/', [PeopleController::class, 'store']);
        Route::put('/{id}', [PeopleController::class, 'update']);
        Route::delete('/{id}', [PeopleController::class, 'destroy']);
    });
});

