<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the inputs that are never flashed to the session on validation errors.
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
        $this->renderable(function (Throwable $e, Request $request) {
            // Handle API requests
            if ($request->expectsJson()) {
                return $this->handleApiException($e, $request);
            }

            // Handle web requests
            return $this->handleWebException($e, $request);
        });
    }

    /**
     * Handle API exceptions with JSON responses.
     */
    protected function handleApiException(Throwable $e, Request $request)
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        if ($e instanceof HttpException) {
            return response()->json([
                'error' => $e->getMessage() ?: 'An error occurred',
            ], $e->getStatusCode());
        }

        \Log::error('Unexpected error: ', ['exception' => $e]);

        return response()->json([
            'error' => 'An unexpected error occurred',
        ], 500);
    }

    /**
     * Handle web exceptions with error pages or redirects.
     */
    protected function handleWebException(Throwable $e, Request $request)
    {
        if ($e instanceof ValidationException) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", [
                    'exception' => $e,
                    'statusCode' => $statusCode,
                ], $statusCode);
            }
        }

        if (view()->exists('errors.error')) {
            return response()->view('errors.error', [
                'exception' => $e,
                'statusCode' => 500,
            ], 500);
        }

        return parent::prepareResponse($request, $e);
    }
}
