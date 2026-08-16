<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Resources;

use Domain\User\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Shared\Domain\Paginated;

/**
 * @property Paginated<User> $paginated
 */
final class UserCollection extends ResourceCollection
{
    public $collects = UserResource::class;

    public function __construct(
        private readonly Paginated $paginated,
    ) {
        parent::__construct($paginated->items);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'page' => $this->paginated->page,
                'per_page' => $this->paginated->perPage,
                'total' => $this->paginated->total,
            ],
        ];
    }
}
