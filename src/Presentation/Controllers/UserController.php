<?php

namespace Presentation\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\User\Actions\UpdateUser;
use Application\User\Commands\CreateUserCommand;
use Application\User\DTOs\UserDTO;
use Application\User\Queries\GetUserByEmailQuery;
use Illuminate\Http\Request;
use Presentation\Requests\UserStoreFormRequest;

class UserController extends Controller
{
    public function __construct(
        protected CommandBusContract $commandBus,
        protected QueryBusContract $queryBus,
    ) {
    }

    public function store(UserStoreFormRequest $request)
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

    public function update($id, Request $request, UpdateUser $updateUser)
    {
        $updateUser($id, $request->name, $request->email);

        return $this->queryBus->ask(new GetUserByEmailQuery($request->email));
    }
}
