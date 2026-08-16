<?php

declare(strict_types=1);

namespace Application\Bus\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class AsQueryHandler
{
    /**
     * @param  class-string  $query
     */
    public function __construct(
        public readonly string $query,
    ) {}
}
