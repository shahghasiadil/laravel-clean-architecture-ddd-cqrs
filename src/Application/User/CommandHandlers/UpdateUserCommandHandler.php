<?php

declare(strict_types=1);

namespace Application\User\CommandHandlers;

use Application\Bus\Attributes\AsCommandHandler;
use Application\Bus\CommandHandler;
use Application\User\Commands\UpdateUserCommand;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Repositories\UserRepositoryContract;
use Domain\User\ValueObjects\EmailAddress;
use Domain\User\ValueObjects\HashedPassword;
use Domain\User\ValueObjects\UserName;
use Illuminate\Support\Facades\Hash;
use Spatie\LaravelData\Optional;

#[AsCommandHandler(UpdateUserCommand::class)]
final readonly class UpdateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserRepositoryContract $userRepository,
    ) {}

    public function handle(UpdateUserCommand $command): void
    {
        $user = $this->userRepository->findById($command->id)
            ?? throw UserNotFoundException::withId($command->id->value);

        $data = $command->userData;

        $user->rename(new UserName($data->name));
        $user->changeEmail(new EmailAddress($data->email));
        $user->changeStatus($data->status);

        if (! $data->password instanceof Optional) {
            $user->changePassword(new HashedPassword(Hash::make($data->password)));
        }

        $this->userRepository->save($user);
    }
}
