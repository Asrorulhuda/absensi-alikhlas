<?php
/**
 * Execute Update (git pull)
 * Called when admin clicks "Update" button from the dashboard notification.
 */
session_start();
header('Content-Type: application/json');

// Only admin can execute updates
if (!isset($_SESSION['akses']) || $_SESSION['akses'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$branch = 'main';
$flagFile = __DIR__ . '/update_available.json';
$logFile = __DIR__ . '/deploy.log';

// Execute git pull
$output = [];
$returnCode = 0;

$commands = [
    'cd ' . escapeshellarg(__DIR__) . ' 2>&1',
    'git fetch origin ' . $branch . ' 2>&1',
    'git reset --hard origin/' . $branch . ' 2>&1',
];

$fullCommand = implode(' && ', $commands);
exec($fullCommand, $output, $returnCode);

if ($returnCode === 0) {
    // Remove the update flag
    if (file_exists($flagFile)) {
        unlink($flagFile);
    }

    // Log success
    $log = date('Y-m-d H:i:s') . " | UPDATE EXECUTED by: " . $_SESSION['name'] . " | Status: SUCCESS\n";
    $log .= "Output: " . implode("\n", $output) . "\n";
    $log .= str_repeat('-', 50) . "\n";
    file_put_contents($logFile, $log, FILE_APPEND | LOCK_EX);

    echo json_encode([
        'success' => true,
        'message' => 'Update berhasil! Aplikasi telah diperbarui.',
        'output' => implode("\n", $output)
    ]);
} else {
    // Log failure
    $log = date('Y-m-d H:i:s') . " | UPDATE EXECUTED by: " . $_SESSION['name'] . " | Status: FAILED\n";
    $log .= "Output: " . implode("\n", $output) . "\n";
    $log .= str_repeat('-', 50) . "\n";
    file_put_contents($logFile, $log, FILE_APPEND | LOCK_EX);

    echo json_encode([
        'success' => false,
        'message' => 'Update gagal! Periksa log untuk detail.',
        'output' => implode("\n", $output)
    ]);
}
