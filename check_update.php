<?php
/**
 * Check Update Endpoint
 * Called via AJAX from the dashboard to check if an update is available.
 */
session_start();
header('Content-Type: application/json');

// Only admin can check for updates
if (!isset($_SESSION['akses']) || $_SESSION['akses'] !== 'Admin') {
    echo json_encode(['available' => false]);
    exit;
}

$flagFile = __DIR__ . '/update_available.json';

if (file_exists($flagFile)) {
    $data = json_decode(file_get_contents($flagFile), true);
    if ($data && isset($data['available']) && $data['available'] === true) {
        echo json_encode($data);
        exit;
    }
}

echo json_encode(['available' => false]);
