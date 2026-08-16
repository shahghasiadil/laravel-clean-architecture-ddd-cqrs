<?php

declare(strict_types=1);

namespace Application\Bus\Exceptions;

use RuntimeException;

final class HandlerNotFoundException extends RuntimeException
{
    /**
     * @param  class-string  $commandClass
     */
    public static function forCommand(string $commandClass): self
    {
        return new self("No handler is registered for command \"{$commandClass}\". Run \"php artisan bus:cache\" after adding a new #[AsCommandHandler] handler.");
    }

    /**
     * @param  class-string  $queryClass
     */
    public static function forQuery(string $queryClass): self
    {
        return new self("No handler is registered for query \"{$queryClass}\". Run \"php artisan bus:cache\" after adding a new #[AsQueryHandler] handler.");
    }
}
