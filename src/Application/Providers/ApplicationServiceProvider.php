<?php

declare(strict_types=1);

namespace Application\Providers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Bus\HandlerRegistry;
use Application\Bus\IlluminateCommandBus;
use Application\Bus\IlluminateQueryBus;
use Application\Bus\Middleware\DispatchesDomainEventsMiddleware;
use Application\Bus\Middleware\IdempotencyMiddleware;
use Application\Bus\Middleware\LogsExecutionMiddleware;
use Application\Bus\Middleware\TransactionalMiddleware;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HandlerRegistry::class, fn (): HandlerRegistry => new HandlerRegistry(
            sourcePath: base_path('src'),
            cacheFile: base_path('bootstrap/cache/handlers.php'),
        ));

        $this->app->singleton(CommandBusContract::class, fn (Container $app): IlluminateCommandBus => new IlluminateCommandBus(
            container: $app,
            registry: $app->make(HandlerRegistry::class),
            middleware: [
                LogsExecutionMiddleware::class,
                DispatchesDomainEventsMiddleware::class,
                IdempotencyMiddleware::class,
                TransactionalMiddleware::class,
            ],
        ));

        $this->app->singleton(QueryBusContract::class, fn (Container $app): IlluminateQueryBus => new IlluminateQueryBus(
            container: $app,
            registry: $app->make(HandlerRegistry::class),
            middleware: [
                LogsExecutionMiddleware::class,
            ],
        ));
    }

    public function boot(): void
    {
        $this->optimizes(
            optimize: 'bus:cache',
            clear: 'bus:clear',
            key: 'bus',
        );
    }
}
