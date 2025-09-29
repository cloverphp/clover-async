<?php

declare(strict_types=1);

namespace Clover\Utils;

use Clover\Async\Promise;
use Clover\Async\EventLoop;

final class Timer {

    /**
     * setTimeout equivalent: executes once after $ms milliseconds
     */
    public static function setTimeout(callable $callback, int $ms): Promise {
        return new Promise(function ($resolve) use ($callback, $ms) {
            EventLoop::defer(function () use ($callback, $resolve, $ms) {
                usleep($ms * 1000); // sleep in microseconds
                $callback();
                $resolve(true);
            });
        });
    }

    /**
     * setInterval equivalent: executes repeatedly every $ms milliseconds
     * Returns a closure to cancel the interval
     */
    public static function setInterval(callable $callback, int $ms): callable {
        $running = true;

        EventLoop::defer(function () use (&$running, $callback, $ms) {
            while ($running) {
                usleep($ms * 1000);
                $callback();
            }
        });

        return fn() => $running = false; // cancel function
    }
}
