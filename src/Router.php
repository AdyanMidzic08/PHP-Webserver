<?php namespace ClanCats\Station\PHPServer;

class Router {
    protected array $routes = [];

    // Registriert eine GET-Route
    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function resolve(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        // Prüfen, ob die Route existiert
        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            return $handler($request);
        }

        return new Response("<h1>404 Not Found</h1><p>Die Seite wurde nicht gefunden.</p>", 404);
    }
}
