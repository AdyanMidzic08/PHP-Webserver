<?php
require 'vendor/autoload.php';
use ClanCats\Station\PHPServer\Response;

function assertContains($needle, $haystack) {
    if (strpos($haystack, $needle) === false) {
        echo "FAIL: Expected '{$haystack}' to contain '{$needle}'\n";
        exit(1);
    }
}

// Test 200 OK
$resp200 = new Response("Hello", 200);
assertContains("HTTP/1.1 200 OK", (string)$resp200);

// Test 404 Not Found
$resp404 = new Response("Not Found", 404);
assertContains("HTTP/1.1 404 Not Found", (string)$resp404);

// Test 500 Internal Server Error
$resp500 = new Response("Error", 500);
assertContains("HTTP/1.1 500 Internal Server Error", (string)$resp500);

// Test Unknown Status
$respUnknown = new Response("Unknown", 999);
assertContains("HTTP/1.1 999 Unknown Status", (string)$respUnknown);

echo "All Step 1 tests passed!\n";
