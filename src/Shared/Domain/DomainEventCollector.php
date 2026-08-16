<?php

declare(strict_types=1);

namespace Shared\Domain;

/**
 * Request-scoped bag that repositories push released aggregate events into
 * after a successful save, so a bus middleware can publish them once the
 * surrounding transaction has committed. Lives in Shared, not Application,
 * so an Infrastructure repository can depend on it without reaching into
 * the Application layer.
 */
final class DomainEventCollector
{
    /** @var list<DomainEvent> */
    private array $events = [];

    public function push(DomainEvent ...$events): void
    {
        array_push($this->events, ...$events);
    }

    /**
     * @return list<DomainEvent>
     */
    public function release(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
