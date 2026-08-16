<?php

declare(strict_types=1);

use Application\Bus\HandlerRegistry;
use Application\User\CommandHandlers\CreateUserCommandHandler;
use Application\User\Commands\CreateUserCommand;
use Application\User\Queries\GetUserByIdQuery;
use Application\User\Queries\GetUserByIdQueryHandler;

it('discovers command and query handlers by attribute, without a cache file', function (): void {
    $registry = new HandlerRegistry(sourcePath: dirname(__DIR__, 4) . '/src');

    expect($registry->commandMap())->toHaveKey(CreateUserCommand::class, CreateUserCommandHandler::class);
    expect($registry->queryMap())->toHaveKey(GetUserByIdQuery::class, GetUserByIdQueryHandler::class);
});

it('warms and reuses a cache file', function (): void {
    $cacheFile = sys_get_temp_dir() . '/handlers-test-' . uniqid() . '.php';

    try {
        $registry = new HandlerRegistry(sourcePath: dirname(__DIR__, 4) . '/src', cacheFile: $cacheFile);
        $warmed = $registry->warm();

        expect($cacheFile)->toBeFile();
        expect($warmed['commands'])->toHaveKey(CreateUserCommand::class);

        // A fresh instance must read from the cache file rather than re-scanning.
        $cached = new HandlerRegistry(sourcePath: '/does/not/exist', cacheFile: $cacheFile);

        expect($cached->commandMap())->toHaveKey(CreateUserCommand::class, CreateUserCommandHandler::class);
    } finally {
        @unlink($cacheFile);
    }
});
