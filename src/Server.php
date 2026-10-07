<?php namespace ClanCats\Station\PHPServer;

class Server {

    protected string $host;
    protected int $port;

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function listen(callable $callback): void
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        
        if (!socket_bind($socket, $this->host, $this->port)) {
            throw new \Exception("Could not bind to {$this->host}:{$this->port}");
        }

        socket_listen($socket);

        echo "Server läuft auf http://{$this->host}:{$this->port}\n";

        while (true) {
            $client = socket_accept($socket);
            $input = socket_read($client, 1024 * 4);

            if (empty($input)) {
                socket_close($client);
                continue;
            }

            try {
                $request = Request::parse($input);
                $response = $callback($request);
                
                socket_write($client, (string)$response, strlen((string)$response));
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }

            socket_close($client);
        }
    }
}
