<?php

declare(strict_types=1);

namespace Application\Components\Response;

class HttpResponseFactory
{
    /**
     * @param string $type
     * @return string
     */
    public static function get(string $type = "application/json"): string
    {
        return match ($type) {
            "application/x-msgpack" => MessagePackResponse::class,
            default => JsonResponse::class,
        };
    }
}
