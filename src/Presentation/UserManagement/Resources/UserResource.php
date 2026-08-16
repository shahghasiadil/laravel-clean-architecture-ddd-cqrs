<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Resources;

use Domain\User\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property User $resource
 */
final class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id()->value,
            'name' => $this->resource->name()->value,
            'email' => $this->resource->email()->value,
            'status' => $this->resource->status()->value,
            'created_at' => $this->resource->createdAt()?->format(DATE_ATOM),
            'updated_at' => $this->resource->updatedAt()?->format(DATE_ATOM),
        ];
    }
}
