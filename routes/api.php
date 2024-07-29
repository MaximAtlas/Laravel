<?php

use App\Http\Controllers\PostController;
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

Route::controller(PostController::class)
    ->prefix('posts')
    ->group(function () {
        Route::get('', 'index')->name('posts.index');
        Route::get('{post}', 'show')->name('posts.show');
        Route::post('', 'store')->name('posts.store');
        Route::post('{post}/comment', 'comment')->name('posts.add.comment');
        Route::put('{post}', 'updatePut')->name('posts.put');
        Route::patch('{post}', 'updatePatch')->name('posts.patch');
        Route::delete('{post}', 'delete')->name('posts.delete');
    });
