<?php

declare(strict_types=1);

namespace Clover\Async;

use Fiber;
use Throwable;

final class Promise implements PromiseInterface {
    private PromiseState $state = PromiseState::PENDING;
    private mixed $result = null;
    private array $handlers = [];

    public function __construct(private readonly callable $executor) {
        try {
            $executor([$this, 'fulfill'], [$this, 'reject']);
        } catch (Throwable $e) {
            $this->reject($e);
        }
    }

    private function fulfill(mixed $value): void {
        if ($this->state !== PromiseState::PENDING) return;
        $this->state = PromiseState::FULFILLED;
        $this->result = $value;
        EventLoop::defer(fn() => $this->processHandlers());
    }

    private function reject(mixed $reason): void {
        if ($this->state !== PromiseState::PENDING) return;
        $this->state = PromiseState::REJECTED;
        $this->result = $reason;
        EventLoop::defer(fn() => $this->processHandlers());
    }

    private function processHandlers(): void {
        while (!empty($this->handlers)) {
            $entry = array_shift($this->handlers);
            $child = $entry['child'];
            try {
                match ($this->state) {
                    PromiseState::FULFILLED => $child->fulfill($entry['onFulfilled'] ? $entry['onFulfilled']($this->result) : $this->result),
                    PromiseState::REJECTED => $child->reject($entry['onRejected'] ? $entry['onRejected']($this->result) : $this->result),
                    default => null
                };
            } catch (Throwable $e) {
                $child->reject($e);
            }
        }
    }

    public function then(?callable $onFulfilled = null, ?callable $onRejected = null): PromiseInterface {
        $child = new self(fn() => null);
        $this->handlers[] = ['onFulfilled' => $onFulfilled, 'onRejected' => $onRejected, 'child' => $child];
        if ($this->state !== PromiseState::PENDING) $this->processHandlers();
        return $child;
    }

    public function catch(callable $onRejected): PromiseInterface {
        return $this->then(null, $onRejected);
    }

    public function finally(callable $onFinally): PromiseInterface {
        return $this->then(
            fn($v) => ($onFinally(), $v),
            fn($e) => throw ($onFinally(), $e)
        );
    }
}
