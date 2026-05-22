```
src/
├── Core/
│   ├── Application
│   ├── Router
│   ├── Request
│   ├── Response
│   ├── Middleware
│   └── Dispatcher
│
├── Controllers/
├── Middlewares/
├── Routes/
└── Exceptions/
```

```php
<?php
class App
{
    private array $routes = [];

    public function get(string $path, callable $handler): void {
        $this->routes[] = [
            'method' => 'GET',
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function post(string $path, callable $handler): void {
        $this->routes[] = [
            'method' => 'POST',
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function run(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {

            if (
                $route['method'] === $requestMethod &&
                $route['path'] === $requestUri
            ) {
                $handler = $route['handler'];

                $handler();

                return;
            }
        }

        http_response_code(404);

        echo "404 - Route introuvable";
    }
}
```

```php
<?php
require 'App.php';

$app = new App();

$app->get('/', function () {
    echo "Accueil";
});

$app->get('/hello', function () {
    echo "Bonjour";
});

$app->run();
```
