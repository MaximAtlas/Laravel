<?php

namespace App\Exceptions;

use App\Exceptions\Post\PostNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (PostNotFoundException $e) {
            return responseSimpleFail($e->getMessage(), $e->getCode());
        });
    }

    public function render($request, Throwable $e)
    {

        if ($e instanceof ModelNotFoundException) {
            return responseSimpleFail(__('messages.ModelNotFound'), 404);
        }

        if ($e instanceof NotFoundHttpException) {
            return responseSimpleFail(__('messages.NotFoundHttp'), 404);
        }

        if ($e instanceof AuthenticationException) {
            return responseSimpleFail(__('messages.Authentication'), 401);
        }

        return parent::render($request, $e);
    }
}
