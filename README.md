## Clover Async


[![Code Coverage](https://github.com/cloverphp/clover/actions/workflows/code-coverage.yml/badge.svg?branch=main)](https://github.com/cloverphp/clover/actions/workflows/code-coverage.yml)
[![Tests](https://github.com/cloverphp/clover/actions/workflows/tests.yml/badge.svg)](https://github.com/cloverphp/clover/actions/workflows/tests.yml)
![Packagist Version](https://img.shields.io/packagist/v/cloverphp/async?style=flat&logo=composer&logoColor=%23fff)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cloverphp/async/php?style=flat&logo=php&logoColor=blue&label=PHP&color=blue)
![Packagist License](https://img.shields.io/packagist/l/cloverphp/async?style=flat&label=License&color=blue)
![Packagist Downloads](https://img.shields.io/packagist/dt/cloverphp/async?style=flat&logo=packagist&label=Downloads&color=blue)


---

### Example 1
Time based async task

```php

use Clover\Utils\Timer;
use Clover\Utils\LoggerTrait;
use function Clover\Async\async;

class Demo {
    use LoggerTrait;

    public function run(): void {
        $this->log("Starting demo...");

        Timer::setTimeout(fn() => $this->log("Timeout fired after 1s"), 1000);

        $cancel = Timer::setInterval(fn() => $this->log("Interval tick every 500ms"), 500);

        // cancel interval after 3 seconds
        Timer::setTimeout(fn() => $cancel(), 3000);
    }
}

$demo = new Demo();
$demo->run();

```

---

### Example 2
Promise based HTTP calling.

```php

use Clover\Http\HttpClient;
use function Clover\Async\{async, await};

$client = new HttpClient();

$router->get('/users', async(function ($req, $res) use ($client) {
    try {
        $response = await($client->fetch("https://jsonplaceholder.typicode.com/users/1"));
        $res->json($response->json());
    } catch (\Throwable $e) {
        $res->status(500)->send("Error: " . $e->getMessage());
    } finally {
        log("Route /users finished");
    }
}));

```

---

### Example 3 
Async/Await Promise


```php

use function Clover\Async\{async, await};
use Clover\Async\Http\HttpClient;

$client = new HttpClient();

$router->get('/users', async(function ($req, $res) use ($client) {
    try {
        $json = await($client->fetch("https://jsonplaceholder.typicode.com/users/1"));
        $res->json(json_decode($json, true));
    } catch (\Throwable $e) {
        $res->status(500)->send("Error: " . $e->getMessage());
    } finally {
        log("Route /users finished");
    }
}));

```

---
