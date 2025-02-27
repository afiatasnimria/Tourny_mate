<?php
// server.php

$address = '127.0.0.1';
$port = 8080;
$clients = [];

// Create the server socket
$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($server, $address, $port);
socket_listen($server);

echo "Server started at $address:$port\n";

while (true) {
    // Accept new client connections
    $client = socket_accept($server);
    $clients[] = $client;

    // Handle incoming message
    $data = socket_read($client, 1024);
    if ($data) {
        echo "Received message: $data\n";
        // Broadcast message to all connected clients
        foreach ($clients as $cli) {
            socket_write($cli, "Hi from server: $data", strlen("Hi from server: $data"));
        }
    }
}
?>
