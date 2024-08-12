<?php

namespace App\Http\Middleware;

use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DraftPostMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /***
         * @var $post POST
         ***/
        $post = ($request->route('post'));

        if ($post->isDraft()) {
            return response()->json([
                'message' => 'DraftPost',
            ], 403);
        }

        return $next($request);
    }
}
