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

arch('Infrastructure should only access Domain and Shared Resources layers')
    ->expect('Infrastructure')
    ->toOnlyBeUsedIn(['Domain', 'Shared']);

arch('Infrastructure should not access the Presentation Layer directly')
    ->expect('Infrastructure')
    ->not->toBeUsedIn(['Presentation']);

arch('Presentation should only access Application and Shared Resources layers')
    ->expect('Presentation')
    ->toOnlyBeUsedIn(['Application', 'Shared']);

arch('Presentation should not access Domain Layer directly')
    ->expect('Presentation')
    ->not->toBeUsedIn(['Domain']);

arch('Shared resources should be accessible by all layers')
    ->expect('Shared')
    ->toOnlyBeUsedIn(['Domain', 'Application', 'Infrastructure', 'Presentation']);
