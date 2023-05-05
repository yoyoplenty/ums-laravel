<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\ItemNotFoundException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler {
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
    public function register(): void {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (ItemNotFoundException $e, $request) {
            return response()->json(['message' => 'Data not found'], 404);
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            return response()->json(['message' => 'Data not found'], 404);
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json(['message' => 'Data not found.'], 404);
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return response()->json(['message' => 'Incorrect Http Verb.'], 403);
        });

        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        });
    }
}