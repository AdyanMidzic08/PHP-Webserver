<?php namespace ClanCats\Station\PHPServer;

class Server {

    public function listen(int $port): void
        {
            echo "Server läuft auf http://localhost:{$port}\n";
        }

}

