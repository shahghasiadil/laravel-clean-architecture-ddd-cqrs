<?php

namespace App\Providers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Bus\IlluminateCommandBus;
use Application\Bus\IlluminateQueryBus;
use Application\User\Commands\CreateUserCommand;
use Application\User\CommandHandlers\CreateUserCommandHandler;
use Application\User\Queries\GetUserByEmailQuery;
use Application\User\Queries\GetUserByEmailQueryHandler;
use Domain\Repositories\UserRepositoryContract;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Persistence\Repositories\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        CommandBusContract::class => IlluminateCommandBus::class,
        QueryBusContract::class => IlluminateQueryBus::class,
        UserRepositoryContract::class => UserRepository::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerCommandHandlers();
        $this->registerQueryHandlers();
    }

    /**
     * Register command handlers.
     */
    protected function registerCommandHandlers(): void
    {
        $commandBus = app(CommandBusContract::class);

        $commandBus->register([
            CreateUserCommand::class => CreateUserCommandHandler::class,
        ]);
    }

    /**
     * Register query handlers.
     */
    protected function registerQueryHandlers(): void
    {
        $queryBus = app(QueryBusContract::class);

        $queryBus->register([
            GetUserByEmailQuery::class => GetUserByEmailQueryHandler::class,
        ]);
    }
}
