<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Domain\User\Repositories\UserRepositoryContract;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Infrastructure\User\Persistence\Models\UserModel;
use Infrastructure\User\Persistence\Policies\UserPolicy;
use Infrastructure\User\Persistence\Repositories\UserRepository;

class InfrastructureServiceProvider extends ServiceProvider
{
    /**
     * Automatically registered by the framework — see
     * Illuminate\Support\ServiceProvider's handling of $singletons.
     */
    public $singletons = [
        UserRepositoryContract::class => UserRepository::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(UserModel::class, UserPolicy::class);
    }
}
