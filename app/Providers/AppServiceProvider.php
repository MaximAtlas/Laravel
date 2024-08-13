<?php

namespace App\Providers;

use App\Http\Resources\PostResource;
use App\Services\Post\PostService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('post', PostService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PostResource::withoutWrapping();
    }
}
