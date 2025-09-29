<?php

declare(strict_types=1);

namespace Clover\Async;

interface PromiseInterface {
    public function then(?callable $onFulfilled = null, ?callable $onRejected = null): PromiseInterface;
    public function catch(callable $onRejected): PromiseInterface;
    public function finally(callable $onFinally): PromiseInterface;
}
