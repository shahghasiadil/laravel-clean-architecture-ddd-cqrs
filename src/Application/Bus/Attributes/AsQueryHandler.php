<?php

declare(strict_types=1);

namespace Application\Bus\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class AsQueryHandler
{
    /**
     * @param  class-string  $query
     */
    public function __construct(
        public string $query,
    ) {}
}
