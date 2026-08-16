<?php

declare(strict_types=1);

namespace Application\Bus;

use Application\Bus\Contracts\QueryBusContract;
use Application\Bus\Exceptions\HandlerNotFoundException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Pipeline\Pipeline;

/**
 * Deliberately does not share Illuminate\Bus\Dispatcher with the command
 * bus: a query has no business being queueable or passing through command
 * middleware such as database transactions.
 */
final readonly class IlluminateQueryBus implements QueryBusContract
{
    /**
     * @param  list<class-string>  $middleware
     */
    public function __construct(
        private Container $container,
        private HandlerRegistry $registry,
        private array $middleware = [],
    ) {}

    /**
     * @template TResult
     *
     * @param  Query<TResult>  $query
     * @return TResult
     */
    public function ask(Query $query): mixed
    {
        $handlerClass = $this->registry->queryMap()[$query::class]
            ?? throw HandlerNotFoundException::forQuery($query::class);

        return (new Pipeline($this->container))
            ->send($query)
            ->through($this->middleware)
            ->then(function (Query $query) use ($handlerClass): mixed {
                $handler = $this->container->make($handlerClass);

                // Handlers implement handle() by convention rather than by
                // interface method: a single QueryHandler#handle() signature
                // can't accept every concrete Query subtype (PHP forbids
                // narrowing parameter types across an interface boundary).
                return $handler->handle($query);
            });
    }
}
