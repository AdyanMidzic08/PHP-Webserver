<?php namespace ClanCats\Station\PHPServer;

class View {

    /**
     * Render the Nordic Minimalist Dashboard
     */
    public static function renderDashboard(Request $request, int $port = 8080): string
    {
        $method = htmlspecialchars($request->getMethod(), ENT_QUOTES, 'UTF-8');
        $uri = htmlspecialchars($request->getUri(), ENT_QUOTES, 'UTF-8');
        $headers = $request->getHeaders();
        $phpVersion = PHP_VERSION;
        $os = PHP_OS_FAMILY . ' (' . PHP_OS . ')';
        $serverTime = date('Y-m-d H:i:s');
        $serverStartTime = defined('SERVER_START_TIME') ? SERVER_START_TIME : time();

        $rows = '';
        foreach ($headers as $k => $v) {
            $sk = htmlspecialchars($k, ENT_QUOTES, 'UTF-8');
            $sv = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
            $rows .= "<tr><td class='code-key'>{$sk}</td><td class='code-val'>{$sv}</td></tr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="de" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Webserver · Overview</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>❄️</text></svg>">
</head>
<body>
    <div class="container">
        <!-- Navigation -->
        <header class="nordic-nav">
            <div class="brand-wrapper">
                <div class="brand-symbol">λ</div>
                <div class="brand-text">
                    <h1>PHP Socket Server</h1>
                    <p>Native HTTP/1.1 Protocol Daemon</p>
                </div>
            </div>
            <div class="nav-actions">
                <div class="status-pill">● 127.0.0.1:{$port} Online</div>
                <button class="theme-toggle-btn" onclick="toggleTheme()">
                    <span id="theme-icon">🌙</span>
                    <span id="theme-label">Dark Mode</span>
                </button>
            </div>
        </header>

        <!-- Stats Strip -->
        <div class="stats-strip">
            <div class="stat-card">
                <div class="stat-label">Server Uptime</div>
                <div class="stat-number" id="nordic-uptime">00:00:00</div>
                <div class="stat-caption">Gestartet um {$serverTime}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Environment</div>
                <div class="stat-number">PHP {$phpVersion}</div>
                <div class="stat-caption">{$os}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Socket Engine</div>
                <div class="stat-number">ext-sockets</div>
                <div class="stat-caption">Native TCP Stream</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Ping Latency</div>
                <div class="stat-number" id="nordic-ping">-- ms</div>
                <div class="stat-caption" id="nordic-ping-status">Prüfe Verbindung...</div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="content-layout">
            <!-- Headers Card -->
            <div class="section-box">
                <div class="section-box-header">
                    <span class="section-box-title">HTTP Request Headers</span>
                    <span style="font-family: var(--font-mono); font-size: 0.75rem; color: #0284c7; font-weight: 600;">{$method} {$uri}</span>
                </div>
                <div class="section-box-body" style="padding: 0;">
                    <div style="max-height: 380px; overflow-y: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Header</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$rows}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Route Explorer & Sandbox -->
            <div class="section-box">
                <div class="section-box-header">
                    <span class="section-box-title">Route Sandbox</span>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">HTTP/1.1</span>
                </div>
                <div class="section-box-body">
                    <p style="font-size: 0.825rem; color: var(--text-secondary); margin-bottom: 12px;">Schnellauswahl für Endpunkte & Statuscodes:</p>
                    <div class="route-grid">
                        <a href="/health" target="_blank" class="route-link">/health (JSON)</a>
                        <a href="/status/200" class="route-link">200 OK</a>
                        <a href="/status/201" class="route-link">201 Created</a>
                        <a href="/status/302" class="route-link">302 Found</a>
                        <a href="/status/400" class="route-link">400 Bad</a>
                        <a href="/status/404" class="route-link">404 Not Found</a>
                        <a href="/status/500" class="route-link">500 Server Err</a>
                    </div>

                    <div style="margin-top: 18px;">
                        <label style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 6px;">Asynchroner Routen-Test (Fetch)</label>
                        <div class="input-row">
                            <input type="text" id="routeTestInput" class="clean-input" value="/health">
                            <button class="clean-btn" onclick="runRouteTest()">Testen</button>
                        </div>
                        <pre id="cleanOutput" class="clean-output"></pre>
                    </div>
                </div>
            </div>
        </main>

        <footer class="clean-footer">
            PHP Raw Socket Server &bull; Native HTTP Server in PHP &bull; <a href="https://github.com/AdyanMidzic08/PHP-Webserver" target="_blank" style="color: #0284c7; text-decoration: none;">GitHub</a>
        </footer>
    </div>

    <script>
        const startTime = {$serverStartTime};
        function updateUptime() {
            const now = Math.floor(Date.now() / 1000);
            const diff = Math.max(0, now - startTime);
            const hrs = String(Math.floor(diff / 3600)).padStart(2, '0');
            const mins = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            const secs = String(diff % 60).padStart(2, '0');
            document.getElementById('nordic-uptime').textContent = hrs + ':' + mins + ':' + secs;
        }
        setInterval(updateUptime, 1000);
        updateUptime();

        async function ping() {
            const t0 = performance.now();
            try {
                const res = await fetch('/health?_t=' + Date.now());
                const ms = Math.round(performance.now() - t0);
                document.getElementById('nordic-ping').textContent = ms + ' ms';
                document.getElementById('nordic-ping-status').textContent = 'Gesund (HTTP ' + res.status + ')';
            } catch(e) {
                document.getElementById('nordic-ping').textContent = 'Fehler';
                document.getElementById('nordic-ping-status').textContent = 'Nicht erreichbar';
            }
        }
        setInterval(ping, 4000);
        ping();

        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme') || 'light';
            const next = current === 'light' ? 'dark' : 'light';
            html.setAttribute('data-theme', next);
            document.getElementById('theme-icon').textContent = next === 'light' ? '🌙' : '☀️';
            document.getElementById('theme-label').textContent = next === 'light' ? 'Dark Mode' : 'Light Mode';
        }

        async function runRouteTest() {
            const path = document.getElementById('routeTestInput').value.trim();
            const out = document.getElementById('cleanOutput');
            out.style.display = 'block';
            out.textContent = 'Lade ' + path + '...';
            try {
                const res = await fetch(path);
                const txt = await res.text();
                let result = 'HTTP ' + res.status + ' ' + res.statusText + '\\n';
                result += 'Content-Type: ' + (res.headers.get('content-type') || 'n/a') + '\\n\\n';
                try {
                    result += JSON.stringify(JSON.parse(txt), null, 2);
                } catch(e) {
                    result += txt.substring(0, 500);
                }
                out.textContent = result;
            } catch (err) {
                out.textContent = 'Fehler: ' + err.message;
            }
        }
    </script>
</body>
</html>
HTML;
    }

    /**
     * Render status page
     */
    public static function renderStatus(int $code, string $phrase): string
    {
        $safePhrase = htmlspecialchars($phrase, ENT_QUOTES, 'UTF-8');
        return <<<HTML
<!DOCTYPE html>
<html lang="de" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$code} {$safePhrase} · PHP Server</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body style="display:flex; align-items:center; justify-content:center; min-height:100vh;">
    <div style="background:var(--bg-surface); border:1px solid var(--border-color); border-radius:16px; padding:44px 36px; max-width:480px; text-align:center; box-shadow:var(--shadow-lg);">
        <div style="font-size:0.75rem; font-weight:700; color:#0284c7; letter-spacing:1px; margin-bottom:12px;">HTTP RESPONSE STATUS</div>
        <h1 style="font-size:64px; margin:0 0 4px; font-weight:800; color:var(--text-primary); font-family:var(--font-mono);">{$code}</h1>
        <h2 style="font-size:20px; font-weight:600; color:var(--text-secondary); margin:0 0 20px;">{$safePhrase}</h2>
        <a href="/" class="clean-btn" style="text-decoration:none; display:inline-block; margin-top:10px;">← Zurück zum Dashboard</a>
    </div>
</body>
</html>
HTML;
    }
}
