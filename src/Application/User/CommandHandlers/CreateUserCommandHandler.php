<?php

declare(strict_types=1);

namespace Application\User\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\User\Commands\CreateUserCommand;
use Domain\User\Repositories\UserRepositoryContract;

final class CreateUserCommandHandler extends CommandHandler
{
    public function __construct(private readonly UserRepositoryContract $userRepository)
    {
    }

    public function handle(CreateUserCommand $command): string
    {
        return $this->userRepository->save($command->userData);
    }
}
