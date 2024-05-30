<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Support\ServiceProvider;
use Infrastructure\User\Persistence\Repositories\UserRepository;

class InfrastructureServiceProvider extends ServiceProvider
{
    public $singletons = [
        UserRepositoryContract::class => UserRepository::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        // Infrastructure-specific bootstrapping
    }


    /**
     * Register the bindings specified in the singletons array.
     */
    protected function registerSingletons(): void
    {
        foreach ($this->singletons as $interface => $implementation) {
            $this->app->singleton($interface, $implementation);
        }
    }
}
