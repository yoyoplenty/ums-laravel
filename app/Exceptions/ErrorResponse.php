<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\CodeRenderer;

class ErrorResponse extends Exception {

    protected $code = 422;

    /**
     * Report the exception.
     */
    public function report(): void {
        // ...
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse {
        return new JsonResponse([
            "message" => $this->getMessage(),
            "status" => $this->code,
            "data" => null
        ], $this->code);
    }
}
