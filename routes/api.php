<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

//Route::apiResource('posts', PostController::class);

Route::controller(PostController::class)
    ->prefix('posts')
    ->group(function () {
        Route::get('', 'index')->name('posts.index');
        Route::get('{post}', 'show')->name('posts.show');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('{post}/comment', 'comment')->name('posts.add.comment');

            Route::middleware('admin')->group(function () {
                Route::post('', 'store')->name('posts.store');

                Route::put('{post}', 'update')->name('posts.put');
                Route::patch('{post}', 'update')->name('posts.patch');
                Route::delete('{post}', 'destroy')->name('posts.destroy');
            });
        });
    });

Route::controller(UserController::class)->group(function () {
    Route::post('login', 'login')->name('login');
});
