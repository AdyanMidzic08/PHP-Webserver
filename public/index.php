<?php
require __DIR__ . '/../vendor/autoload.php';

use ClanCats\Station\PHPServer\Request;
use ClanCats\Station\PHPServer\View;

if (!defined('SERVER_START_TIME')) {
    define('SERVER_START_TIME', time());
}

$request = new Request(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/',
    function_exists('getallheaders') ? (getallheaders() ?: []) : []
);

$port = (int)($_SERVER['SERVER_PORT'] ?? 8080);
echo View::renderDashboard($request, $port);