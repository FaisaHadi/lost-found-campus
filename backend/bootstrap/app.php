<?php

use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $apiError = function (
            string $code,
            string $message,
            int $status,
            ?array $errors = null
        ) {
            $payload = [
                'success' => false,
                'message' => $message,
                'data' => null,
                'errors' => $errors,
                'error' => [
                    'code' => $code,
                    'message' => $message,
                ],
                'meta' => [],
            ];

            if ($errors !== null) {
                $payload['error']['details'] = $errors;
            }

            return response()->json($payload, $status);
        };

        $exceptions->render(function (ValidationException $exception, Request $request) use ($apiError) {
            if (! $request->is('api/*')) {
                return null;
            }

            $message = collect($exception->errors())->flatten()->first()
                ?? 'The submitted data is invalid.';

            return $apiError('VALIDATION_ERROR', $message, 422, $exception->errors());
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) use ($apiError) {
            if (! $request->is('api/*')) {
                return null;
            }

            return $apiError('UNAUTHENTICATED', 'Authentication token is missing or invalid.', 401);
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) use ($apiError) {
            if (! $request->is('api/*')) {
                return null;
            }

            return $apiError('FORBIDDEN', 'You are not allowed to perform this action.', 403);
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) use ($apiError) {
            if (! $request->is('api/*')) {
                return null;
            }

            return $apiError('NOT_FOUND', 'The requested API resource was not found.', 404);
        });

        $exceptions->render(function (ModelNotFoundException $exception, Request $request) use ($apiError) {
            if (! $request->is('api/*')) {
                return null;
            }

            return $apiError('NOT_FOUND', 'The requested resource was not found.', 404);
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) use ($apiError) {
            if (! $request->is('api/*')) {
                return null;
            }

            $status = $exception->getStatusCode();

            if ($status === 403) {
                return $apiError('FORBIDDEN', 'You are not allowed to perform this action.', 403);
            }

            if (in_array($status, [401, 404, 422], true)) {
                return null;
            }

            return $apiError(
                'HTTP_ERROR',
                $exception->getMessage() ?: 'The request could not be completed.',
                $status
            );
        });
    })->create();
