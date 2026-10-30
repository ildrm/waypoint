<?php

use App\Http\Middleware\PlatformContext;
use App\Http\Middleware\RequireCapability;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', api: __DIR__.'/../routes/api.php', commands: __DIR__.'/../routes/console.php')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [AuthenticateSession::class, PlatformContext::class]);
        $middleware->alias(['staff.capability' => RequireCapability::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['password', 'password_confirmation', 'token', 'resume_token']);
        $exceptions->render(function (Throwable $error, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }$status = $error instanceof ValidationException ? 422 : ($error instanceof HttpExceptionInterface ? $error->getStatusCode() : ($error instanceof AuthenticationException ? 401 : ($error instanceof ModelNotFoundException ? 404 : 500)));
            $titles = [401 => 'Authentication required', 403 => 'Action denied', 404 => 'Resource unavailable', 409 => 'Request conflict', 412 => 'Stale revision', 413 => 'Request too large', 422 => 'Validation failed', 428 => 'If-Match required', 429 => 'Rate limited'];
            $correlation = (string) Str::uuid();

            return response()->json(['type' => 'about:blank', 'title' => $titles[$status] ?? 'Request failed', 'status' => $status, 'detail' => $status === 404 ? 'Resource unavailable.' : ($status === 500 ? 'An internal error occurred. Retry with the same operation key.' : ($error instanceof ValidationException ? collect($error->errors())->flatten()->first() : ($error->getMessage() ?: 'The request could not be completed.'))), 'instance' => $request->path(), 'correlationId' => $correlation], $status, array_merge($error instanceof HttpExceptionInterface ? $error->getHeaders() : [], ['Content-Type' => 'application/problem+json', 'X-Correlation-ID' => $correlation]));
        });

    })
    ->create();
