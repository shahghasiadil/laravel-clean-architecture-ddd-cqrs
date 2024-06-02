<?php

declare(strict_types=1);

namespace Presentation\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\User\Actions\UpdateUser;
use Application\User\Commands\CreateUserCommand;
use Application\User\DTOs\CreateUserDTO;
use Application\User\Queries\GetUserByEmailQuery;
use Domain\User\Entities\User;
use Illuminate\Http\Request;
use Presentation\Requests\UserStoreFormRequest;

final class UserController extends Controller
{
    public function __construct(
        protected CommandBusContract $commandBus,
        protected QueryBusContract $queryBus,
    ) {}

    public function store(UserStoreFormRequest $request): User
    {

        $CreateUserDTO = new CreateUserDTO(...$request->validated());

        $email = $this->commandBus->dispatch(
            new CreateUserCommand($CreateUserDTO),
        );

        $user = $this->queryBus->ask(
            new GetUserByEmailQuery($email),
        );

        return $user;
    }

    public function update(int $id, Request $request, UpdateUser $updateUser): User
    {
        $updateUser($id, $request->name, $request->email);

        return $this->queryBus->ask(new GetUserByEmailQuery($request->email));
    }
}
