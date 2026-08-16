<?php

declare(strict_types=1);

namespace Shared\Criteria;

enum FilterOperator: string
{
    case Equal = '=';
    case NotEqual = '!=';
    case GreaterThan = '>';
    case GreaterOrEqual = '>=';
    case LessThan = '<';
    case LessOrEqual = '<=';
    case Contains = 'contains';
}

final readonly class Filter
{
    public function __construct(
        public string $field,
        public FilterOperator $operator,
        public mixed $value,
    ) {}
}
