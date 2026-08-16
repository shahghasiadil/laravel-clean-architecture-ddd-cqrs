<?php

declare(strict_types=1);

namespace Presentation\Http;

use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Renders an RFC 9457 (application/problem+json) response for a Domain
 * exception, so a missing aggregate or a broken invariant reaches the
 * client as a structured 4xx instead of an unhandled 500.
 */
final class ProblemDetails
{
    public static function response(Throwable $exception, int $status, string $title): JsonResponse
    {
        return new JsonResponse(
            [
                'type' => 'about:blank',
                'title' => $title,
                'status' => $status,
                'detail' => $exception->getMessage(),
            ],
            $status,
            ['Content-Type' => 'application/problem+json'],
        );
    }
}
