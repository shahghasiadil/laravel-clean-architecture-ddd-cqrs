<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\User\Commands\CreateUserCommand;
use Application\User\Commands\UpdateUserCommand;
use Application\User\Data\UserData;
use Application\User\Queries\GetUserByIdQuery;
use Application\User\Queries\GetUsersQuery;
use Domain\User\Entities\User;
use Domain\User\ValueObjects\UserId;
use Illuminate\Http\JsonResponse;
use Presentation\Controller;
use Presentation\UserManagement\Requests\IndexUsersRequest;
use Presentation\UserManagement\Requests\StoreUserRequest;
use Presentation\UserManagement\Requests\UpdateUserRequest;
use Presentation\UserManagement\Resources\UserCollection;
use Presentation\UserManagement\Resources\UserResource;
use Shared\Domain\Paginated;

final class UserController extends Controller
{
    public function __construct(
        private readonly CommandBusContract $commandBus,
        private readonly QueryBusContract $queryBus,
    ) {}

    public function store(StoreUserRequest $request): JsonResponse
    {
        $userData = UserData::from($request->validated());

        /** @var UserId $id */
        $id = $this->commandBus->dispatch(new CreateUserCommand($userData));

        /** @var User $user */
        $user = $this->queryBus->ask(new GetUserByIdQuery($id));

        return new UserResource($user)->response()->setStatusCode(201);
    }

    public function update(int $id, UpdateUserRequest $request): UserResource
    {
        $userId = new UserId($id);
        $userData = UserData::from($request->validated());

        $this->commandBus->dispatch(new UpdateUserCommand($userId, $userData));

        /** @var User $user */
        $user = $this->queryBus->ask(new GetUserByIdQuery($userId));

        return new UserResource($user);
    }

    public function index(IndexUsersRequest $request): UserCollection
    {
        /** @var Paginated<User> $paginated */
        $paginated = $this->queryBus->ask(new GetUsersQuery(
            page: $request->page(),
            perPage: $request->perPage(),
        ));

        return new UserCollection($paginated);
    }

    public function show(int $id): UserResource
    {
        /** @var User $user */
        $user = $this->queryBus->ask(new GetUserByIdQuery(new UserId($id)));

        return new UserResource($user);
    }
}
