<?php

declare(strict_types=1);

arch('globals')
    ->expect(['dd', 'dump', 'ray'])
    ->not->toBeUsed();

arch('app')
    ->expect(['Shared', 'Domain', 'Infrastructure', 'Application', 'Presentation'])
    ->toUseStrictTypes();

arch('Domain is framework-free: it must not depend on Illuminate, Application, Infrastructure, or Presentation')
    ->expect('Domain')
    ->not->toUse(['Illuminate', 'Application', 'Infrastructure', 'Presentation']);

arch('Domain can be accessed by Infrastructure, Application, and Presentation Layers')
    ->expect('Domain')
    ->toOnlyBeUsedIn(['Infrastructure', 'Application', 'Presentation']);

arch('Infrastructure should only be accessed by the Application layer, or by database seeders/factories')
    ->expect('Infrastructure')
    ->toOnlyBeUsedIn(['Application', 'Database']);

arch('Application should only accessed by Presentation, Infrastructure and Domain Layer')
    ->expect('Application')
    ->toOnlyBeUsedIn(['Presentation', 'Infrastructure', 'Domain']);

arch('Presentation should not be used in Domain, Application, Shared, Infrastructure Layer')
    ->expect('Presentation')
    ->not->toBeUsedIn(['Domain', 'Application', 'Shared', 'Infrastructure']);

arch('Shared resources should be accessible by all layers')
    ->expect('Shared')
    ->toOnlyBeUsedIn(['Domain', 'Application', 'Infrastructure', 'Presentation']);

arch('Shared\Domain and Shared\Criteria are framework-free: they must not depend on Illuminate')
    ->expect(['Shared\Domain', 'Shared\Criteria'])
    ->not->toUse('Illuminate');

arch('Commands are immutable messages')
    ->expect('Application\User\Commands')
    ->toBeFinal()
    ->toBeReadonly();

arch('Command and query handlers are final')
    ->expect(['Application\User\CommandHandlers', 'Application\User\Queries'])
    ->toBeFinal();
