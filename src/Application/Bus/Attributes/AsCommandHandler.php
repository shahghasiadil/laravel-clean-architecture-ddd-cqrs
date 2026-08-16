<?php

declare(strict_types=1);

namespace Application\Bus\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class AsCommandHandler
{
    /**
     * @param  class-string  $command
     */
    public function __construct(
        public readonly string $command,
    ) {}
}
