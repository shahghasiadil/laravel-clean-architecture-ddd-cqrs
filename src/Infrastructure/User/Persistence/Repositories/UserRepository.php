<?php

declare(strict_types=1);

namespace Infrastructure\User\Persistence\Repositories;

use Domain\User\Entities\User;
use Domain\User\Repositories\UserRepositoryContract;
use Domain\User\ValueObjects\EmailAddress;
use Domain\User\ValueObjects\UserId;
use Infrastructure\User\Persistence\Mappers\UserMapper;
use Infrastructure\User\Persistence\Models\UserModel;
use Shared\Domain\DomainEventCollector;
use Shared\Domain\Paginated;

final class UserRepository implements UserRepositoryContract
{
    public function __construct(
        private readonly UserMapper $mapper,
        private readonly DomainEventCollector $events,
    ) {}

    public function save(User $user): void
    {
        $model = $this->modelFor($user);
        $model->fill($this->mapper->toAttributes($user));
        $model->save();

        if ($model->wasRecentlyCreated) {
            $user->assignId(new UserId($model->id));
        }

        $this->events->push(...$user->releaseEvents());
    }

    public function findByEmail(EmailAddress $email): ?User
    {
        $model = UserModel::query()->where('email', $email->value)->first();

        return $model instanceof UserModel ? $this->mapper->toDomain($model) : null;
    }

    public function findById(UserId $id): ?User
    {
        $model = UserModel::query()->find($id->value);

        return $model instanceof UserModel ? $this->mapper->toDomain($model) : null;
    }

    public function paginate(int $page, int $perPage): Paginated
    {
        $paginator = UserModel::query()->paginate(perPage: $perPage, page: $page);

        return new Paginated(
            items: array_map(
                fn(UserModel $model): User => $this->mapper->toDomain($model),
                $paginator->items(),
            ),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            page: $paginator->currentPage(),
        );
    }

    private function modelFor(User $user): UserModel
    {
        if ( ! $user->hasId()) {
            return new UserModel();
        }

        return UserModel::query()->findOrFail($user->id()->value);
    }
}
