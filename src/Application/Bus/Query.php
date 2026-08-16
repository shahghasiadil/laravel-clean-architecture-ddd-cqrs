<?php

declare(strict_types=1);

namespace Application\Bus;

/**
 * Marker interface for a read-intent message dispatched through the query bus.
 *
 * @template TResult
 */
interface Query {}
