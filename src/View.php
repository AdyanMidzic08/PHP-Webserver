<?php namespace ClanCats\Station\PHPServer;

class View {

    /**
     * Render the Cyberpunk Terminal HUD Dashboard
     */
    public static function renderDashboard(Request $request, int $port = 8080): string
    {
        $method = htmlspecialchars($request->getMethod(), ENT_QUOTES, 'UTF-8');
        $uri = htmlspecialchars($request->getUri(), ENT_QUOTES, 'UTF-8');
        $headers = $request->getHeaders();
        $headersJson = htmlspecialchars(json_encode($headers, JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8');
        $phpVersion = PHP_VERSION;
        $os = PHP_OS_FAMILY . ' (' . PHP_OS . ')';
        $serverTime = date('Y-m-d H:i:s');
        $serverStartTime = defined('SERVER_START_TIME') ? SERVER_START_TIME : time();

        return <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP TERMINAL HUD [PORT: {$port}]</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📟</text></svg>">
</head>
<body class="scanlines">
    <div class="terminal-container">
        <!-- Top Status Line -->
        <div class="hud-bar">
            <div class="hud-title">
                <span>[■]</span>
                <span>PHP RAW SOCKET ENGINE // CLI INTERFACE</span>
            </div>
            <div class="hud-tools">
                <span id="term-clock">{$serverTime}</span>
                <button class="hud-btn" onclick="toggleScanlines()">SCANLINES: <span id="scanline-status">ON</span></button>
                <button class="hud-btn" onclick="executeCmd('matrix')">MATRIX MODE</button>
            </div>
        </div>

        <!-- ASCII Banner -->
        <pre class="ascii-banner">
 ██████╗ ██╗  ██╗██████╗     ███████╗███████╗██████╗ ██╗   ██╗███████╗██████╗ 
 ██╔══██╗██║  ██║██╔══██╗    ██╔════╝██╔════╝██╔══██╗██║   ██║██╔════╝██╔══██╗
 ██████╔╝███████║██████╔╝    ███████╗█████╗  ██████╔╝██║   ██║█████╗  ██████╔╝
 ██╔═══╝ ██╔══██║██╔═══╝     ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██╔══╝  ██╔══██╗
 ██║     ██║  ██║██║         ███████║███████╗██║  ██║ ╚████╔╝ ███████╗██║  ██║
        </pre>

        <!-- Main HUD Layout -->
        <div class="hud-grid">
            <!-- Left: Interactive Shell Terminal -->
            <div class="hud-panel">
                <div class="panel-header">
                    <span>>_ INTERACTIVE SYSTEM SHELL</span>
                    <span>SESSION: TTY1</span>
                </div>
                <div class="panel-body terminal-screen" id="termScreen">
                    <div class="terminal-logs" id="terminalLogs">
                        <div class="log-line success">[INIT] PHP Raw Socket HTTP/1.1 Engine initialisiert auf 127.0.0.1:{$port}</div>
                        <div class="log-line system">[KERNEL] ext-sockets geladen · Non-blocking event loop aktiv</div>
                        <div class="log-line cyan">[INCOMING] {$method} {$uri} verarbeitet um {$serverTime}</div>
                        <div class="log-line system">[INFO] Tippe 'help' für eine Liste der verfügbaren Terminal-Befehle ein.</div>
                        <div class="log-line warn">--------------------------------------------------</div>
                    </div>
                    <div class="terminal-prompt-row">
                        <span class="prompt-symbol">root@socket-engine:~#</span>
                        <input type="text" id="termInput" class="terminal-input" autofocus autocomplete="off" spellcheck="false" placeholder="Befehl eingeben (z.B. help, health, ping, routes)..." onkeydown="handleKeyDown(event)">
                    </div>
                </div>
            </div>

            <!-- Right: System Telemetry & Quick Commands -->
            <div class="hud-panel">
                <div class="panel-header">
                    <span>TELEMETRIE & STATUS</span>
                    <span>ONLINE</span>
                </div>
                <div class="panel-body">
                    <div class="stat-block">
                        <div class="stat-tag">UPTIME MONITOR</div>
                        <div class="stat-val" id="hud-uptime">00:00:00</div>
                    </div>
                    <div class="stat-block">
                        <div class="stat-tag">RUNTIME ENVIRONMENT</div>
                        <div class="stat-val" style="font-size: 15px;">PHP {$phpVersion}</div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top:2px;">{$os}</div>
                    </div>
                    <div class="stat-block">
                        <div class="stat-tag">HEALTH PROBE</div>
                        <div class="stat-val" id="hud-ping" style="font-size: 15px;">-- ms</div>
                    </div>

                    <div style="margin-top: 18px;">
                        <div class="stat-tag" style="margin-bottom: 8px;">SCHNELL-BEFEHLE (KLICKBAR)</div>
                        <div class="quick-cmds">
                            <button class="quick-cmd-btn" onclick="executeCmd('help')">help</button>
                            <button class="quick-cmd-btn" onclick="executeCmd('health')">health</button>
                            <button class="quick-cmd-btn" onclick="executeCmd('headers')">headers</button>
                            <button class="quick-cmd-btn" onclick="executeCmd('routes')">routes</button>
                            <button class="quick-cmd-btn" onclick="executeCmd('status 200')">status 200</button>
                            <button class="quick-cmd-btn" onclick="executeCmd('status 404')">status 404</button>
                            <button class="quick-cmd-btn" onclick="executeCmd('status 500')">status 500</button>
                            <button class="quick-cmd-btn" onclick="executeCmd('clear')">clear</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terminal Logic -->
    <script>
        const startTime = {$serverStartTime};
        const headersData = {$headersJson};

        function updateHUD() {
            const now = Math.floor(Date.now() / 1000);
            const diff = Math.max(0, now - startTime);
            const hrs = String(Math.floor(diff / 3600)).padStart(2, '0');
            const mins = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            const secs = String(diff % 60).padStart(2, '0');
            document.getElementById('hud-uptime').textContent = hrs + ':' + mins + ':' + secs;
        }
        setInterval(updateHUD, 1000);
        updateHUD();

        async function pingLoop() {
            const t0 = performance.now();
            try {
                const res = await fetch('/health?_t=' + Date.now());
                const t1 = performance.now();
                document.getElementById('hud-ping').textContent = Math.round(t1 - t0) + ' ms (ONLINE)';
            } catch (e) {
                document.getElementById('hud-ping').textContent = 'OFFLINE';
            }
        }
        setInterval(pingLoop, 4000);
        pingLoop();

        function toggleScanlines() {
            document.body.classList.toggle('scanlines');
            document.getElementById('scanline-status').textContent = document.body.classList.contains('scanlines') ? 'ON' : 'OFF';
        }

        function appendLog(text, type = 'system') {
            const logs = document.getElementById('terminalLogs');
            const div = document.createElement('div');
            div.className = 'log-line ' + type;
            div.textContent = text;
            logs.appendChild(div);
            const screen = document.getElementById('termScreen');
            screen.scrollTop = screen.scrollHeight;
        }

        function handleKeyDown(e) {
            if (e.key === 'Enter') {
                const val = e.target.value.trim();
                e.target.value = '';
                if (val) executeCmd(val);
            }
        }

        async function executeCmd(cmd) {
            appendLog('root@socket-engine:~# ' + cmd, 'cyan');
            const parts = cmd.toLowerCase().split(' ');
            const action = parts[0];

            switch(action) {
                case 'help':
                    appendLog('Verfügbare Befehle:', 'warn');
                    appendLog('  health       - Ruft die /health Route des Servers ab');
                    appendLog('  headers      - Zeigt die HTTP-Request-Header dieser Sitzung');
                    appendLog('  routes       - Listet registrierte Endpunkte auf');
                    appendLog('  status <code>- Testet einen HTTP Statuscode (z.B. status 404)');
                    appendLog('  curl <url>   - Sendet einen Raw GET Request an einen Pfad');
                    appendLog('  clear        - Bereinigt den Terminalbildschirm');
                    appendLog('  matrix       - Startet/stoppt animierten Matrix-Effekt');
                    break;
                case 'health':
                    appendLog('Sende GET /health...', 'system');
                    try {
                        const res = await fetch('/health');
                        const data = await res.json();
                        appendLog(JSON.stringify(data, null, 2), 'success');
                    } catch(e) { appendLog('Fehler: ' + e.message, 'error'); }
                    break;
                case 'headers':
                    appendLog('Aktuelle HTTP Headers:', 'warn');
                    for (const [k, v] of Object.entries(headersData)) {
                        appendLog('  ' + k + ': ' + v, 'system');
                    }
                    break;
                case 'routes':
                    appendLog('Registrierte Server-Routen:', 'warn');
                    appendLog('  GET /             -> Terminal Dashboard', 'system');
                    appendLog('  GET /health       -> JSON Telemetrie', 'system');
                    appendLog('  GET /status/{code}-> HTTP Status-Code Simulator', 'system');
                    appendLog('  GET /style.css    -> Static Asset', 'system');
                    break;
                case 'status':
                    const code = parts[1] || '200';
                    window.open('/status/' + code, '_blank');
                    appendLog('Status ' + code + ' in neuem Tab geöffnet.', 'success');
                    break;
                case 'curl':
                    const target = parts[1] || '/';
                    appendLog('GET ' + target + '...', 'system');
                    try {
                        const res = await fetch(target);
                        const body = await res.text();
                        appendLog('HTTP ' + res.status + ' ' + res.statusText, 'success');
                        appendLog(body.substring(0, 300) + (body.length > 300 ? '...' : ''), 'system');
                    } catch(e) { appendLog('Fehler: ' + e.message, 'error'); }
                    break;
                case 'clear':
                    document.getElementById('terminalLogs').innerHTML = '';
                    break;
                case 'matrix':
                    appendLog('Matrix-Regen aktiviert...', 'success');
                    break;
                default:
                    appendLog('Befehl nicht gefunden: ' + action + '. Tippe "help".', 'error');
            }
        }
    </script>
</body>
</html>
HTML;
    }

    /**
     * Render Status code page
     */
    public static function renderStatus(int $code, string $phrase): string
    {
        $safePhrase = htmlspecialchars($phrase, ENT_QUOTES, 'UTF-8');
        return <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STATUS {$code} // PHP TERMINAL</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body class="scanlines" style="display:flex; align-items:center; justify-content:center; min-height:100vh;">
    <div style="border: 2px solid var(--term-green); background: rgba(10,18,12,0.95); padding: 40px; max-width: 500px; text-align: center; box-shadow: 0 0 20px rgba(0,255,102,0.3);">
        <div style="color: var(--term-green); font-size: 14px; letter-spacing: 2px; margin-bottom: 10px;">[HTTP PROTOCOL PACKET]</div>
        <h1 style="color: var(--term-green); font-size: 56px; margin: 0; text-shadow: 0 0 10px rgba(0,255,102,0.5);">{$code}</h1>
        <h2 style="color: var(--text-primary); font-size: 20px; margin: 10px 0 20px;">{$safePhrase}</h2>
        <div style="margin-top: 24px;">
            <a href="/" class="hud-btn" style="padding: 10px 20px; text-decoration: none; display: inline-block;">ZURÜCK ZUR KONSOLE</a>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
