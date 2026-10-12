<?php namespace ClanCats\Station\PHPServer;

class Router {
    protected array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function resolve(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            return $handler($request, []);
        }

        foreach ($this->routes[$method] ?? [] as $routePath => $handler) {
            $pattern = "#^" . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routePath) . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Den ersten Treffer (Gesamtergebnis) entfernen
                return $handler($request, $matches); // Parameter übergeben
            }
        }

        // Falls nichts gefunden: 404
        return new Response("<h1>404 Not Found</h1><p>Die Seite wurde nicht gefunden.</p>", 404);
    }
}

