<?php

declare(strict_types=1);

namespace Clover\Async;

enum PromiseState: int {
    case PENDING = 0;
    case FULFILLED = 1;
    case REJECTED = 2;
}
