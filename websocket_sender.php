<?php
// send-hi.php

$address = '127.0.0.1';
$port = 8080;

// Connect to the WebSocket server
$clientSocket = fsockopen("tcp://$address", $port, $errno, $errstr, 30);
if (!$clientSocket) {
    echo "Error: $errno - $errstr\n";
} else {
    // Send the "Hi" message
    $message = "Hi from team section!";
    fwrite($clientSocket, $message);
    echo "Sent message: $message\n";

    // Close the connection
    fclose($clientSocket);
}
?>
