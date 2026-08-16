<?php

declare(strict_types=1);

namespace Shared\Domain;

/**
 * A page of domain entities returned by a repository, framework-free so it
 * can cross the Domain boundary without leaking Eloquent's paginator.
 *
 * @template TItem
 */
final readonly class Paginated
{
    /**
     * @param  list<TItem>  $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $perPage,
        public int $page,
    ) {}
}
