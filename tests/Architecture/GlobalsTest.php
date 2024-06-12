<?php

declare(strict_types=1);

arch('globals')
    ->expect(['dd', 'dump', 'ray'])
    ->not->toBeUsed();

arch('app')
    ->expect(['Shared', 'Domain','Infrastructure','Application','Presentation'])
    ->toUseStrictTypes();

arch('Domain can be accessed by Infrastructure and Application Layers')
    ->expect('Domain')
    ->toOnlyBeUsedIn(['Infrastructure', 'Application']);

arch('Infrastructure should not access the Presentation Layer directly')
    ->expect('Infrastructure')
    ->not->toBeUsedIn(['Presentation','Domain', 'Shared']);

arch('Presentation should not be used in Domain Layer')
    ->expect('Presentation')
    ->not->toBeUsedIn(['Domain','Application', 'Shared', 'Infrastructure']);

arch('Shared resources should be accessible by all layers')
    ->expect('Shared')
    ->toOnlyBeUsedIn(['Domain', 'Application', 'Infrastructure', 'Presentation']);


