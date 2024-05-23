<?php

namespace Presentation\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Commands\User\CreateUserCommand;
use Application\DTOs\User\UserDTO;
use Application\Queries\User\GetUserByEmailQuery;
use Presentation\Requests\UserStoreFormRequest;

class UserController extends Controller
{
    public function __construct(
        protected CommandBusContract $commandBus,
        protected QueryBusContract $queryBus,
    ) {
    }

    public function __invoke(UserStoreFormRequest $request)
    {

        $userDto = new UserDTO($request->safe()->name, $request->safe()->email, $request->safe()->password);

        $email = $this->commandBus->dispatch(
            new CreateUserCommand($userDto),
        );

        $user = $this->queryBus->ask(
            new GetUserByEmailQuery($email)
        );

        return $user;
    }
}
