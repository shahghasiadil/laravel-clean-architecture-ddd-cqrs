<?php

declare(strict_types=1);

namespace Application\User\CommandHandlers;

use Application\Bus\Attributes\AsCommandHandler;
use Application\Bus\CommandHandler;
use Application\User\Commands\CreateUserCommand;
use Domain\User\Entities\User;
use Domain\User\Repositories\UserRepositoryContract;
use Domain\User\ValueObjects\EmailAddress;
use Domain\User\ValueObjects\HashedPassword;
use Domain\User\ValueObjects\UserId;
use Domain\User\ValueObjects\UserName;
use Illuminate\Support\Facades\Hash;
use Shared\Domain\Exceptions\DomainValidationException;
use Spatie\LaravelData\Optional;

#[AsCommandHandler(CreateUserCommand::class)]
final readonly class CreateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserRepositoryContract $userRepository,
    ) {}

    public function handle(CreateUserCommand $command): UserId
    {
        $data = $command->userData;

        if ($data->password instanceof Optional) {
            throw new DomainValidationException('A password is required to register a user.');
        }

        $user = User::register(
            new UserName($data->name),
            new EmailAddress($data->email),
            new HashedPassword(Hash::make($data->password)),
            $data->status,
        );

        $this->userRepository->save($user);

        return $user->id();
    }
}
