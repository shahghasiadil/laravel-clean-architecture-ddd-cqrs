<?php

declare(strict_types=1);

namespace Shared\Providers;

use Illuminate\Support\ServiceProvider;
use Shared\Domain\DomainEventCollector;

class SharedServiceProvider extends ServiceProvider
{
    public $singletons = [
        DomainEventCollector::class => DomainEventCollector::class,
    ];

    public function register(): void {}

    public function boot(): void {}
}
