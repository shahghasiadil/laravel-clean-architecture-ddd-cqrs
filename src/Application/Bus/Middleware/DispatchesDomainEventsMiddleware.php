<?php

declare(strict_types=1);

namespace Application\Bus\Middleware;

use Application\Bus\Command;
use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Shared\Domain\DomainEventCollector;
use Throwable;

/**
 * Flushes whatever domain events the handler's repository collected while
 * saving an aggregate. Placed outside TransactionalMiddleware in the
 * pipeline so events are only published once the transaction has committed —
 * a subscriber acting on a UserRegistered event should never observe a user
 * that a rolled-back transaction made disappear again.
 */
final readonly class DispatchesDomainEventsMiddleware
{
    public function __construct(
        private DomainEventCollector $collector,
        private Dispatcher $events,
    ) {}

    public function handle(Command $command, Closure $next): mixed
    {
        try {
            $result = $next($command);
        } catch (Throwable $exception) {
            // Discard rather than dispatch: the transaction that recorded
            // these events was rolled back, so they never really happened.
            $this->collector->release();

            throw $exception;
        }

        foreach ($this->collector->release() as $event) {
            $this->events->dispatch($event);
        }

        return $result;
    }
}
