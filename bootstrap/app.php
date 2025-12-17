<?php

use App\DTOs\Commons\ErrorResponse;
use App\DTOs\Commons\ItemError;
use App\Exceptions\BaseException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);

        $middleware->alias([
            'teacher_role' => App\Http\Middleware\CheckTeacherRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                $details = [];
                foreach ($e->errors() as $field => $messages) {
                    foreach ($messages as $msg) {
                        $details[] = new ItemError($field, $msg);
                    }
                }
                return response()->json(
                    new ErrorResponse(code: '400', message: 'BAD_REQUEST', details: $details),
                    400
                );
            }
        });

        // 2. Xử lý Base Exception (Các lỗi logic custom: Unauthorized, Forbidden...)
        $exceptions->render(function (BaseException $e, Request $request) {
            return response()->json(
                new ErrorResponse(
                    code: $e->getErrorCode(),
                    message: $e->getMessage(),
                    details: $e->getDetails()
                ),
                $e->getStatusCode()
            );
        });

        // 3. Xử lý Authentication Exception (Lỗi chưa đăng nhập - 401)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(
                    new ErrorResponse(code: '401', message: 'UNAUTHORIZED'),
                    401
                );
            }
        });

        // 4. Xử lý HTTP Exception khác (404, 405, 500...)
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($request->is('api/*')) {
                $status = $e->getStatusCode();
                $message = match ($status) {
                    404 => 'NOT_FOUND',
                    405 => 'METHOD_NOT_ALLOWED',
                    403 => 'FORBIDDEN',
                    default => $e->getMessage() ?: 'HTTP_ERROR',
                };

                return response()->json(
                    new ErrorResponse(code: (string)$status, message: $message),
                    $status
                );
            }
        });
    })->create();
