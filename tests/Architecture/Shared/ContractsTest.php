<?php

declare(strict_types=1);

arch('shared interfaces/contracts test')
    ->expect('Shared\Contracts')
    ->toBeInterfaces();
