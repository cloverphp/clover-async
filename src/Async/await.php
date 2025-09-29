<?php

declare(strict_types=1);

namespace Clover\Async;

use Fiber;

function await(Promise $promise): mixed {
    $fiber = Fiber::getCurrent();
    if (!$fiber) throw new \RuntimeException("await() must be called inside async()");

    $result = null;
    $error = null;

    $promise->then(
        fn($v) => ($result = $v, $fiber->resume()),
        fn($e) => ($error = $e, $fiber->throw($e))
    );

    Fiber::suspend();

    if ($error) throw $error;
    return $result;
}
