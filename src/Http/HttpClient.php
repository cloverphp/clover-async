<?php

declare(strict_types=1);

namespace Clover\Http;

use Clover\Async\Promise;

final class HttpClient {

    public function send(HttpRequest $request): Promise {
        return new Promise(function ($resolve, $reject) use ($request) {
            try {
                $options = [
                    'http' => [
                        'method' => $request->method->value,
                        'header' => $this->buildHeaders($request->headers),
                        'content' => $request->body
                    ]
                ];
                $context = stream_context_create($options);
                $body = file_get_contents($request->url, false, $context);

                if ($body === false) {
                    throw new \RuntimeException("Failed to fetch {$request->url}");
                }

                $status = $this->parseStatus($http_response_header ?? []);
                $response = new HttpResponse($status, $this->parseHeaders($http_response_header ?? []), $body);

                $resolve($response);
            } catch (\Throwable $e) {
                $reject($e);
            }
        });
    }

    private function buildHeaders(array $headers): string {
        $lines = [];
        foreach ($headers as $k => $v) {
            $lines[] = "{$k}: {$v}";
        }
        return implode("\r\n", $lines);
    }

    private function parseHeaders(array $lines): array {
        $headers = [];
        foreach ($lines as $line) {
            if (str_contains($line, ':')) {
                [$k, $v] = explode(':', $line, 2);
                $headers[trim($k)] = trim($v);
            }
        }
        return $headers;
    }

    private function parseStatus(array $lines): int {
        if (isset($lines[0])) {
            if (preg_match('#HTTP/\d+\.\d+\s+(\d+)#', $lines[0], $matches)) {
                return (int)$matches[1];
            }
        }
        return 0;
    }

    // helper shortcut
    public function fetch(string $url): Promise {
        return $this->send(new HttpRequest(HttpMethod::GET, $url));
    }
}
