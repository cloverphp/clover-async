<?php

declare(strict_types=1);

namespace Clover\Http;

final class HttpRequest {
    public function __construct(
        public readonly HttpMethod $method,
        public readonly string $url,
        public readonly array $headers = [],
        public readonly ?string $body = null
    ) {}
}
