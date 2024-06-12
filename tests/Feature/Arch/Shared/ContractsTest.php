<?php

arch('shared interfaces/contracts test')
    ->expect('src\Shared\Contracts')
    ->toBeInterfaces();
