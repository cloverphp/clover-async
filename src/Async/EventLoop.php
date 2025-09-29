<?php

declare(strict_types=1);

namespace Clover\Async;

use Fiber;

final class EventLoop {
    private static array $queue = [];

    public static function defer(callable $fn): void {
        self::$queue[] = $fn;
    }

    public static function run(): void {
        while (!empty(self::$queue)) {
            $task = array_shift(self::$queue);
            $fiber = new Fiber($task);
            $fiber->start();
        }
    }
}
