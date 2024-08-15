<?php

namespace App\Http\Middleware;

use App\Exceptions\Post\PostNotFoundException;
use App\Models\Post;
use Closure;
use Illuminate\Http\Request;

class DraftPostMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        /***
         * @var $post POST
         ***/
        $post = ($request->route('post'));

        if ($post->isDraft()) {
            throw new PostNotFoundException();
        }

        return $next($request);
    }
}
