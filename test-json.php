<?php
// test-json.php - Simple JSON test
header('Content-Type: application/json; charset=utf-8');

// Check what's being sent
$debug = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'post_data' => $_POST,
    'get_data' => $_GET,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
];

// Simple response
echo json_encode([
    'success' => true,
    'message' => 'JSON test successful!',
    'debug' => $debug,
    'note' => 'If you see this, JSON is working correctly.'
], JSON_PRETTY_PRINT);
?>