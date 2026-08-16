<?php

declare(strict_types=1);

namespace Shared\Criteria;

/**
 * Persistence-agnostic query specification passed from the Application layer
 * to a repository, so repository contracts do not grow one findByX() method
 * per queryable field.
 */
final readonly class Criteria
{
    /**
     * @param  list<Filter>  $filters
     * @param  list<Order>  $orders
     */
    public function __construct(
        public array $filters = [],
        public array $orders = [],
        public int $page = 1,
        public int $perPage = 15,
    ) {}
}
