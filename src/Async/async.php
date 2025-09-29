<?php

declare(strict_types=1);

namespace Clover\Async;

use Fiber;

function async(callable $fn): callable {
    return function (...$args): Promise {
        return new Promise(function ($resolve, $reject) use ($fn, $args) {
            $fiber = new Fiber(function () use ($fn, $args, $resolve, $reject) {
                try {
                    $result = $fn(...$args);
                    if ($result instanceof Promise) {
                        $result->then($resolve, $reject);
                    } else {
                        $resolve($result);
                    }
                } catch (\Throwable $e) {
                    $reject($e);
                }
            });
            $fiber->start();
        });
    };
}
