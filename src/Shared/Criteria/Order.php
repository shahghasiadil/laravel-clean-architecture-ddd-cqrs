<?php

declare(strict_types=1);

namespace Shared\Criteria;

enum OrderDirection: string
{
    case Asc = 'asc';
    case Desc = 'desc';
}

final readonly class Order
{
    public function __construct(
        public string $field,
        public OrderDirection $direction = OrderDirection::Asc,
    ) {}
}
