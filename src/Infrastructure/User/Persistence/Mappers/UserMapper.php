<?php

declare(strict_types=1);

namespace Infrastructure\User\Persistence\Mappers;

use Domain\User\Entities\User;
use Domain\User\ValueObjects\EmailAddress;
use Domain\User\ValueObjects\HashedPassword;
use Domain\User\ValueObjects\UserId;
use Domain\User\ValueObjects\UserName;
use Infrastructure\User\Persistence\Models\UserModel;

final class UserMapper
{
    public function toDomain(UserModel $model): User
    {
        return User::reconstitute(
            new UserId($model->id),
            new UserName($model->name),
            new EmailAddress($model->email),
            new HashedPassword($model->password),
            $model->status,
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(User $user): array
    {
        return [
            'name' => $user->name()->value,
            'email' => $user->email()->value,
            'password' => $user->password()->value,
            'status' => $user->status(),
        ];
    }
}
