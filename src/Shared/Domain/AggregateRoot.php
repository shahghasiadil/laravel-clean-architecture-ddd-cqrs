<?php

declare(strict_types=1);

namespace Shared\Domain;

abstract class AggregateRoot
{
    /** @var list<DomainEvent> */
    private array $recordedEvents = [];

    /**
     * @return list<DomainEvent>
     */
    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    protected function recordThat(DomainEvent $event): void
    {
        $this->recordedEvents[] = $event;
    }
}
