<?php

declare(strict_types=1);

arch('shared enums test')
    ->expect('Shared\Enums')
    ->toBeStringBackedEnums();
