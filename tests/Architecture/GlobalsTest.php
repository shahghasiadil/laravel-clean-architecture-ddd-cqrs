<?php

declare(strict_types=1);

arch('globals')
    ->expect(['dd', 'dump', 'ray'])
    ->not->toBeUsed();

arch('app')
    ->expect(['Shared', 'Domain','Infrastructure','Application','Presentation'])
    ->toUseStrictTypes();
