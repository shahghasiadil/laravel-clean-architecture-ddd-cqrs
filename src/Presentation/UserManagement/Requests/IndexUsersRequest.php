<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexUsersRequest extends FormRequest
{
    private const int MAX_PER_PAGE = 100;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:' . self::MAX_PER_PAGE],
        ];
    }

    public function page(): int
    {
        return (int) $this->input('page', 1);
    }

    public function perPage(): int
    {
        // Validated by rules() above (max: self::MAX_PER_PAGE) before this ever runs.
        return (int) $this->input('per_page', 15);
    }
}
