<?php

declare(strict_types=1);

namespace Application\Bus\Middleware;

use Application\Bus\Command;
use Closure;
use Illuminate\Database\ConnectionResolverInterface;

/**
 * Wraps command handling in a database transaction. Not applied to queries —
 * a read has nothing to roll back and paying for a transaction there is pure
 * overhead.
 */
final readonly class TransactionalMiddleware
{
    public function __construct(
        private ConnectionResolverInterface $connections,
    ) {}

    public function handle(Command $command, Closure $next): mixed
    {
        return $this->connections->connection()->transaction(fn () => $next($command));
    }
}
