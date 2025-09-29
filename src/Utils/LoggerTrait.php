<?php

declare(strict_types=1);

namespace Clover\Utils;

trait LoggerTrait {
    public function log(string $message): void {
        $time = (new \DateTimeImmutable())->format('Y-m-d H:i:s.u');
        echo "[LOG {$time}] {$message}" . PHP_EOL;
    }

    public function error(string $message): void {
        $time = (new \DateTimeImmutable())->format('Y-m-d H:i:s.u');
        fwrite(STDERR, "[ERROR {$time}] {$message}" . PHP_EOL);
    }
}
