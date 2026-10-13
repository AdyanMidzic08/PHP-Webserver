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
        $path = parse_url($uri, PHP_URL_PATH) ?? $uri;

        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            return $handler($request, []);
        }

        foreach ($this->routes[$method] ?? [] as $routePath => $handler) {
            $pattern = "#^" . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routePath) . "$#";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Den ersten Treffer entfernen
                return $handler($request, $matches); // Parameter übergeben
            }
        }

        // Statische Dateien aus public/ Verzeichnis prüfen und ausliefern
        $publicDir = realpath(__DIR__ . '/../public');
        if ($publicDir) {
            $safePath = realpath($publicDir . '/' . ltrim($path, '/'));
            if ($safePath && str_starts_with($safePath, $publicDir) && is_file($safePath)) {
                $ext = strtolower(pathinfo($safePath, PATHINFO_EXTENSION));
                $mimeTypes = [
                    'css'  => 'text/css; charset=UTF-8',
                    'js'   => 'application/javascript; charset=UTF-8',
                    'json' => 'application/json; charset=UTF-8',
                    'png'  => 'image/png',
                    'jpg'  => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif'  => 'image/gif',
                    'svg'  => 'image/svg+xml',
                    'ico'  => 'image/x-icon',
                    'txt'  => 'text/plain; charset=UTF-8',
                ];
                $contentType = $mimeTypes[$ext] ?? 'text/plain';
                return new Response(file_get_contents($safePath), 200, ['Content-Type' => $contentType]);
            }
        }

        // Falls nichts gefunden: Schöne 404-Seite
        return new Response(self::renderNotFound($path), 404);
    }

    public static function renderNotFound(string $path): string
    {
        $safePath = htmlspecialchars($path, ENT_QUOTES, 'UTF-8');
        return <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Seite nicht gefunden | PHP Socket Server</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        :root { --bg: #090d16; --card-bg: rgba(22, 29, 45, 0.7); --border: rgba(255,255,255,0.08); --text: #f8fafc; --text-muted: #94a3b8; --accent: #ef4444; }
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background:var(--bg); font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; color:var(--text); padding:20px; }
        .box { background:var(--card-bg); border:1px solid var(--border); border-radius:18px; padding:40px; max-width:480px; width:100%; text-align:center; box-shadow:0 20px 40px rgba(0,0,0,0.5); backdrop-filter:blur(16px); }
        .badge { display:inline-block; font-size:12px; font-weight:700; color:#ef4444; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); padding:4px 12px; border-radius:99px; margin-bottom:16px; letter-spacing:1px; }
        h1 { font-size:56px; margin:0 0 10px; font-weight:800; background:linear-gradient(135deg, #ef4444, #f97316); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
        p { color:var(--text-muted); font-size:15px; margin:0 0 24px; line-height:1.6; }
        code { background:rgba(255,255,255,0.06); padding:3px 8px; border-radius:6px; color:#fca5a5; font-family:monospace; }
        .btn { display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg, #3b82f6, #2563eb); color:#fff; text-decoration:none; font-weight:600; padding:12px 24px; border-radius:10px; transition:transform 0.2s, box-shadow 0.2s; box-shadow:0 4px 15px rgba(37,99,235,0.4); }
        .btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(37,99,235,0.6); }
    </style>
</head>
<body>
    <div class="box">
        <div class="badge">HTTP 404 ERROR</div>
        <h1>404</h1>
        <p>Die angeforderte Route <code>{$safePath}</code> konnte auf diesem PHP Raw-Socket-Server nicht gefunden werden.</p>
        <a href="/" class="btn">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Zurück zum Dashboard
        </a>
    </div>
</body>
</html>
HTML;
    }
}

