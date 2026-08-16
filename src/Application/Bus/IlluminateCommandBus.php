<?php

declare(strict_types=1);

namespace Application\Bus;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Exceptions\HandlerNotFoundException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Pipeline\Pipeline;

final readonly class IlluminateCommandBus implements CommandBusContract
{
    /**
     * @param  list<class-string>  $middleware
     */
    public function __construct(
        private Container $container,
        private HandlerRegistry $registry,
        private array $middleware = [],
    ) {}

    public function dispatch(Command $command): mixed
    {
        $handlerClass = $this->registry->commandMap()[$command::class]
            ?? throw HandlerNotFoundException::forCommand($command::class);

        return (new Pipeline($this->container))
            ->send($command)
            ->through($this->middleware)
            ->then(function (Command $command) use ($handlerClass): mixed {
                $handler = $this->container->make($handlerClass);

                // Handlers implement handle() by convention rather than by
                // interface method: a single CommandHandler#handle() signature
                // can't accept every concrete Command subtype (PHP forbids
                // narrowing parameter types across an interface boundary).
                return $handler->handle($command); // @phpstan-ignore-line
            });
    }
}
