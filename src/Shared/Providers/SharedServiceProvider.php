<?php

declare(strict_types=1);

namespace Shared\Providers;

use Illuminate\Support\ServiceProvider;
use Shared\Domain\DomainEventCollector;

class SharedServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // scoped(), not singleton(): under Octane a singleton would survive
        // across requests, so a leftover collector from one request's failed
        // command could get flushed into the next request's success path.
        // Octane resets scoped bindings between requests; on classic
        // PHP-FPM this behaves the same as a singleton anyway.
        $this->app->scoped(DomainEventCollector::class);
    }

    public function boot(): void {}
}
