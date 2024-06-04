<?php

declare(strict_types=1);

namespace Application\User\CommandHandlers;

use Application\Bus\CommandHandler;
use Application\User\Commands\CreateUserCommand;
use Domain\User\Repositories\UserRepositoryContract;

final class CreateUserCommandHandler extends CommandHandler
{
    private $userRepository;

    public function __construct(UserRepositoryContract $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(CreateUserCommand $command): string
    {
        return $this->userRepository->save($command->userData);
    }
}
