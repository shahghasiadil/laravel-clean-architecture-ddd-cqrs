<?php

declare(strict_types=1);

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Presentation\Http\ProblemDetails;
use Shared\Domain\Exceptions\ConcurrencyException;
use Shared\Domain\Exceptions\DomainValidationException;
use Shared\Domain\Exceptions\EntityNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [__DIR__ . '/../src/Presentation/UserManagement/routes/web.php'],
        api: [__DIR__ . '/../src/Presentation/UserManagement/routes/api.php'],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__ . '/../src/Presentation/Console',
    ])
    ->withMiddleware(function (Middleware $middleware): void {})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(fn (EntityNotFoundException $e) => ProblemDetails::response($e, 404, 'Not Found'));
        $exceptions->render(fn (DomainValidationException $e) => ProblemDetails::response($e, 422, 'Unprocessable Entity'));
        $exceptions->render(fn (ConcurrencyException $e) => ProblemDetails::response($e, 409, 'Conflict'));
        $exceptions->render(fn (AuthorizationException $e) => ProblemDetails::response($e, 403, 'Forbidden'));
    })->create();
