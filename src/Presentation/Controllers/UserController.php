<?php

namespace Presentation\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Commands\User\CreateUserCommand;
use Application\DTOs\User\UserDTO;
use Application\Queries\User\GetUserByEmailQuery;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected CommandBusContract $commandBus,
        protected QueryBusContract $queryBus,
    ) {
    }

    public function __invoke(Request $request)
    {

        $email = $this->commandBus->dispatch(
            new CreateUserCommand(new UserDTO(fake()->name, fake()->email, fake()->password())),
        );

        $user = $this->queryBus->ask(
            new GetUserByEmailQuery($email)
        );

        return $user;
    }
}
