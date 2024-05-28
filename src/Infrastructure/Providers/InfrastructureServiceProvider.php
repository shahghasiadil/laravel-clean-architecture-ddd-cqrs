<?php

namespace Infrastructure\Providers;

use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Support\ServiceProvider;
use Infrastructure\User\Persistence\Repositories\UserRepository;

class InfrastructureServiceProvider extends ServiceProvider
{
    public $singletons = [
        UserRepositoryContract::class => UserRepository::class,
    ];

    public function register(): void
    {
        // Infrastructure services registration
    }

    public function boot(): void
    {
        // Infrastructure-specific bootstrapping
    }
}
