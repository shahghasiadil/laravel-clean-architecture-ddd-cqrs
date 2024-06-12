<?php

declare(strict_types=1);

arch('shared enums test')
    ->expect('src\Shared\Enums')
    ->toBeStringBackedEnums();
