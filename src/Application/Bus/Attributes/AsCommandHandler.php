<?php

declare(strict_types=1);

namespace Application\Bus\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class AsCommandHandler
{
    /**
     * @param  class-string  $command
     */
    public function __construct(
        public string $command,
    ) {}
}
