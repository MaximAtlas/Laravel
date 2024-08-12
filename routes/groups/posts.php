<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::controller(PostController::class)
    ->prefix('posts')
    ->group(function () {
        Route::get('', 'index')->name('posts.index');
        Route::middleware('post.draft')->group(function () {
            Route::get('{post}', 'show')->name('posts.show');
        });

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
