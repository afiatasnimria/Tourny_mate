<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// In a real application, this would fetch from a database
$notifications = [
    [
        'message' => 'New tournament "Summer League" is now open for registration',
        'timestamp' => date('Y-m-d H:i:s')
    ],
    [
        'message' => 'Your team "Wildcats" has been invited to a match',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-1 day'))
    ]
];

echo json_encode($notifications);