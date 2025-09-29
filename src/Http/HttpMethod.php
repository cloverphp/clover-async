<?php

declare(strict_types=1);

namespace Clover\Http;

enum HttpMethod: string {
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
    case OPTION = 'OPTION';
    case HEAD = 'HEAD';
}
