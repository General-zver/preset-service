<?php

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, \Throwable $e) =>
                $request->is('api/*') || $request->expectsJson()
        );
        $exceptions->render(function (Throwable $e, Request $request) {
        if (! $request->is('api/*')) {
            return null;
        }

        $errors = null;

        if ($e instanceof ValidationException) {
            $status = 422;
            $title = 'Ошибка валидации';
            $detail = 'Переданные данные не прошли проверку.';
            $errors = $e->errors();
        } elseif ($e instanceof AuthenticationException) {
            $status = 401;
            $title = 'Не авторизован';
            $detail = 'Для выполнения запроса необходим Bearer Token.';
        } elseif ($e instanceof AuthorizationException) {
            $status = 403;
            $title = 'Доступ запрещён';
            $detail = 'Недостаточно прав для выполнения операции.';
        } elseif ($e instanceof ModelNotFoundException) {
            $status = 404;
            $title = 'Ресурс не найден';
            $detail = 'Запрашиваемый ресурс не существует.';
        } elseif ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            $title = Response::$statusTexts[$status] ?? 'HTTP error';
            $detail = $e->getMessage() ?: $title;
        } else {
            $status = 500;
            $title = 'Внутренняя ошибка сервера';
            $detail = 'Не удалось выполнить запрос.';
        }

        $body = [
            'type' => 'about:blank',
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'instance' => $request->getRequestUri(),
        ];

        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        $headers = [
            'Content-Type' => 'application/problem+json',
        ];

        if ($status === 401) {
            $headers['WWW-Authenticate'] = 'Bearer';
        }

        return response()->json($body, $status, $headers);
    });
    })->create();
