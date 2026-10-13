<?php namespace ClanCats\Station\PHPServer;

class View {

    /**
     * Render the main developer dashboard
     */
    public static function renderDashboard(Request $request, int $port = 8080): string
    {
        $method = htmlspecialchars($request->getMethod(), ENT_QUOTES, 'UTF-8');
        $uri = htmlspecialchars($request->getUri(), ENT_QUOTES, 'UTF-8');
        $headers = $request->getHeaders();
        $headersCount = count($headers);
        $phpVersion = PHP_VERSION;
        $os = PHP_OS_FAMILY . ' (' . PHP_OS . ')';
        $serverTime = date('Y-m-d H:i:s');
        $serverStartTime = defined('SERVER_START_TIME') ? SERVER_START_TIME : time();

        $headerRows = '';
        foreach ($headers as $key => $val) {
            $safeKey = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
            $safeVal = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
            $headerRows .= "<tr><td class='header-key'>{$safeKey}</td><td class='header-val'>{$safeVal}</td></tr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Raw Socket Server · Dashboard</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
</head>
<body>
    <div class="container">
        <!-- Top Navigation / Header -->
        <header class="top-header">
            <div class="brand">
                <div class="brand-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="brand-title">
                    <h1>PHP Socket Server</h1>
                    <span>Native HTTP/1.1 Engine · Zero Dependencies</span>
                </div>
            </div>
            <div class="header-badges">
                <div class="badge-live">
                    <span class="pulse-dot"></span>
                    <span>ONLINE</span>
                </div>
                <div class="badge-mono">127.0.0.1:{$port}</div>
            </div>
        </header>

        <!-- Metric Cards -->
        <section class="metrics-grid">
            <div class="metric-card">
                <div class="metric-header">
                    <span>Server Status</span>
                    <svg width="18" height="18" fill="none" stroke="var(--accent-emerald)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="metric-value" style="color: var(--accent-emerald);">LISTENING</div>
                <div class="metric-sub">
                    <span id="uptime-display">Uptime: 00:00:00</span>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span>PHP Runtime</span>
                    <svg width="18" height="18" fill="none" stroke="var(--accent-cyan)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </div>
                <div class="metric-value">v{$phpVersion}</div>
                <div class="metric-sub">{$os}</div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span>Socket Engine</span>
                    <svg width="18" height="18" fill="none" stroke="var(--accent-purple)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
                </div>
                <div class="metric-value">ext-sockets</div>
                <div class="metric-sub">TCP Stream Socket</div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <span>Health Ping</span>
                    <svg width="18" height="18" fill="none" stroke="var(--accent-blue)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div class="metric-value" id="ping-latency">-- ms</div>
                <div class="metric-sub" id="ping-status">Prüfe Verbindung...</div>
            </div>
        </section>

        <!-- Main Dashboard Split -->
        <main class="dashboard-grid">
            <!-- Left: Current Request Inspector -->
            <section class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Aktueller HTTP Request
                    </div>
                    <span class="badge-method {$method}">{$method}</span>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Request URI</span>
                        <span class="info-val">{$uri}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">HTTP Protocol</span>
                        <span class="info-val">HTTP/1.1</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Server Timestamp</span>
                        <span class="info-val">{$serverTime}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Anzahl Header</span>
                        <span class="info-val">{$headersCount} Header</span>
                    </div>

                    <div style="margin-top: 18px; display: flex; justify-content: space-between; align-items: center;">
                        <input type="text" id="headerSearch" class="search-box" placeholder="Header durchsuchen (z.B. User-Agent, Host)..." onkeyup="filterHeaders()">
                    </div>

                    <div class="headers-table-wrapper">
                        <table class="headers-table" id="headersTable">
                            <thead>
                                <tr>
                                    <th>Header Name</th>
                                    <th>Wert</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$headerRows}
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Right: Interactive Route Playground & API Tester -->
            <section class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Routen & Status Playground
                    </div>
                    <span class="badge-mono">REST / Testing</span>
                </div>
                <div class="card-body">
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 12px;">
                        Klicke auf eine Route oder teste benutzerdefinierte Pfade direkt im Browser:
                    </p>

                    <div class="route-chips">
                        <a href="/health" target="_blank" class="chip-btn success">/health (JSON)</a>
                        <a href="/status/200" class="chip-btn success">200 OK</a>
                        <a href="/status/201" class="chip-btn success">201 Created</a>
                        <a href="/status/302" class="chip-btn warning">302 Found</a>
                        <a href="/status/400" class="chip-btn danger">400 Bad Request</a>
                        <a href="/status/404" class="chip-btn danger">404 Not Found</a>
                        <a href="/status/500" class="chip-btn danger">500 Server Error</a>
                    </div>

                    <div style="margin-top: 20px;">
                        <label style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 6px;">LIVE ROUTE TESTER (AJAX)</label>
                        <div class="tester-bar">
                            <input type="text" id="routeInput" class="tester-input" value="/health" placeholder="/deine-route">
                            <button class="btn-primary" onclick="testRoute()">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                Senden
                            </button>
                        </div>
                        <pre id="testerOutput" class="tester-output"></pre>
                    </div>

                    <!-- Server Architecture Lifecycle -->
                    <div style="margin-top: 24px;">
                        <label style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 8px;">TCP SOCKET PIPELINE</label>
                        <div class="pipeline">
                            <div class="pipe-step">
                                <div class="step-num">01</div>
                                <div class="step-name">socket_bind()</div>
                                <div class="step-desc">Bindet TCP Port</div>
                            </div>
                            <div class="pipe-step">
                                <div class="step-num">02</div>
                                <div class="step-name">socket_listen()</div>
                                <div class="step-desc">Wartet auf Client</div>
                            </div>
                            <div class="pipe-step">
                                <div class="step-num">03</div>
                                <div class="step-name">Request::parse()</div>
                                <div class="step-desc">Parst HTTP Payload</div>
                            </div>
                            <div class="pipe-step">
                                <div class="step-num">04</div>
                                <div class="step-name">socket_write()</div>
                                <div class="step-desc">Sendet HTTP/1.1</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <p>PHP Raw Socket Web Server &bull; Entwickelt mit nativen PHP Socket-Funktionen (<code>ext-sockets</code>) &bull; <a href="https://github.com/AdyanMidzic08/PHP-Webserver" target="_blank">GitHub Repository</a></p>
        </footer>
    </div>

    <!-- Live Client Scripts -->
    <script>
        // Server Uptime Tracker
        const startTime = {$serverStartTime};
        function updateUptime() {
            const now = Math.floor(Date.now() / 1000);
            const diff = Math.max(0, now - startTime);
            const hrs = String(Math.floor(diff / 3600)).padStart(2, '0');
            const mins = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            const secs = String(diff % 60).padStart(2, '0');
            const el = document.getElementById('uptime-display');
            if (el) el.textContent = 'Uptime: ' + hrs + ':' + mins + ':' + secs;
        }
        setInterval(updateUptime, 1000);
        updateUptime();

        // Live Health Ping
        async function pingServer() {
            const t0 = performance.now();
            try {
                const res = await fetch('/health?_t=' + Date.now());
                const t1 = performance.now();
                const latency = Math.round(t1 - t0);
                document.getElementById('ping-latency').textContent = latency + ' ms';
                document.getElementById('ping-status').textContent = 'Gesund (' + res.status + ' OK)';
            } catch (err) {
                document.getElementById('ping-latency').textContent = 'Fehler';
                document.getElementById('ping-status').textContent = 'Server antwortet nicht';
            }
        }
        setInterval(pingServer, 4000);
        pingServer();

        // Headers Search Filter
        function filterHeaders() {
            const q = document.getElementById('headerSearch').value.toLowerCase();
            const rows = document.querySelectorAll('#headersTable tbody tr');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
        }

        // Live Route Tester AJAX
        async function testRoute() {
            const path = document.getElementById('routeInput').value.trim();
            const out = document.getElementById('testerOutput');
            out.style.display = 'block';
            out.textContent = 'Sende Anfrage an ' + path + '...';

            const start = performance.now();
            try {
                const res = await fetch(path);
                const duration = Math.round(performance.now() - start);
                const text = await res.text();
                
                let formatted = 'STATUS: ' + res.status + ' ' + res.statusText + ' (' + duration + ' ms)\\n';
                formatted += 'CONTENT-TYPE: ' + (res.headers.get('content-type') || 'unbekannt') + '\\n';
                formatted += '----------------------------------------\\n\\n';
                
                try {
                    const json = JSON.parse(text);
                    formatted += JSON.stringify(json, null, 2);
                } catch(e) {
                    formatted += text.substring(0, 800) + (text.length > 800 ? '... [gekürzt]' : '');
                }

                out.textContent = formatted;
            } catch (err) {
                out.textContent = 'FEHLER: ' + err.message;
            }
        }
    </script>
</body>
</html>
HTML;
    }

    /**
     * Render a styled HTTP Status Code page
     */
    public static function renderStatus(int $code, string $phrase): string
    {
        $statusClass = 'success';
        $statusColor = '#10b981';
        if ($code >= 300 && $code < 400) {
            $statusClass = 'warning';
            $statusColor = '#f59e0b';
        } elseif ($code >= 400) {
            $statusClass = 'danger';
            $statusColor = '#ef4444';
        }

        $safePhrase = htmlspecialchars($phrase, ENT_QUOTES, 'UTF-8');

        $explanations = [
            200 => 'Die HTTP-Anfrage wurde erfolgreich verarbeitet.',
            201 => 'Die Ressource wurde erfolgreich erstellt.',
            301 => 'Die angeforderte Ressource wurde dauerhaft verschoben.',
            302 => 'Die Ressource befindet sich vorübergehend an einer anderen Stelle.',
            400 => 'Der Client hat eine ungültige Anfrage gesendet (Bad Request).',
            401 => 'Für diese Ressource ist eine Authentifizierung erforderlich.',
            403 => 'Der Zugriff auf diese Ressource ist verboten (Forbidden).',
            404 => 'Die angeforderte Seite oder Ressource wurde nicht gefunden.',
            500 => 'Auf dem Server ist ein interner Fehler aufgetreten.',
            502 => 'Der Server hat als Gateway eine ungültige Antwort erhalten.',
            503 => 'Der Dienst ist vorübergehend nicht verfügbar.'
        ];

        $desc = $explanations[$code] ?? 'HTTP-Statuscode Antwort des PHP Raw Socket Webservers.';

        return <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$code} {$safePhrase} · PHP Socket Server</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        :root { --bg: #090d16; --card-bg: rgba(18, 24, 38, 0.85); --border: rgba(255,255,255,0.08); --text: #f8fafc; --text-muted: #94a3b8; }
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background:var(--bg); font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; color:var(--text); padding:20px; }
        .box { background:var(--card-bg); border:1px solid var(--border); border-radius:18px; padding:44px 36px; max-width:540px; width:100%; text-align:center; box-shadow:0 25px 50px rgba(0,0,0,0.5); backdrop-filter:blur(16px); }
        .badge { display:inline-block; font-size:12px; font-weight:700; color:{$statusColor}; background:rgba(255,255,255,0.05); border:1px solid {$statusColor}; padding:5px 14px; border-radius:99px; margin-bottom:18px; letter-spacing:1px; }
        h1 { font-size:64px; margin:0 0 4px; font-weight:800; color:{$statusColor}; font-family:monospace; }
        h2 { font-size:22px; margin:0 0 16px; font-weight:600; color:var(--text); }
        p { color:var(--text-muted); font-size:15px; margin:0 0 28px; line-height:1.6; }
        .actions { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }
        .btn { display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg, #3b82f6, #2563eb); color:#fff; text-decoration:none; font-weight:600; padding:12px 22px; border-radius:10px; transition:transform 0.2s, box-shadow 0.2s; box-shadow:0 4px 15px rgba(37,99,235,0.3); font-size:14px; }
        .btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(37,99,235,0.5); }
        .btn-ghost { background:rgba(255,255,255,0.06); color:var(--text); border:1px solid var(--border); box-shadow:none; }
        .btn-ghost:hover { background:rgba(255,255,255,0.1); transform:translateY(-2px); box-shadow:none; }
    </style>
</head>
<body>
    <div class="box">
        <div class="badge">HTTP STATUS RESPONSE</div>
        <h1>{$code}</h1>
        <h2>{$safePhrase}</h2>
        <p>{$desc}</p>
        <div class="actions">
            <a href="/" class="btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Zurück zum Dashboard
            </a>
            <a href="/health" class="btn btn-ghost">Health Check (JSON)</a>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
