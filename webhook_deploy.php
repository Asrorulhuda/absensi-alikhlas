<?php
/**
 * Webhook Receiver for Automatic Git Deployment
 * 
 * This script listens for a POST request from a Git provider (like GitHub or GitLab)
 * and automatically pulls the latest changes for a specified branch.
 */

// --- KONFIGURASI ---
// Ganti dengan secret token yang Anda atur di GitHub/GitLab webhook.
// Ini sangat penting untuk keamanan.
$secretToken = 'GANTI_DENGAN_SECRET_TOKEN_ANDA';

// Path absolut ke direktori Git Anda di server.
$repoPath = __DIR__;

// Branch yang ingin di-pull.
$branch = 'main';

// File untuk menyimpan log.
$logFile = __DIR__ . '/deploy.log';

// --- PROSES ---

// 1. Validasi Request
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
if (empty($signature)) {
    http_response_code(403);
    die("Signature not found.");
}

$payload = file_get_contents('php://input');
$hash = 'sha256=' . hash_hmac('sha256', $payload, $secretToken, false);

if (!hash_equals($hash, $signature)) {
    http_response_code(403);
    die("Signature mismatch.");
}

// 2. Jalankan Git Pull
$command = "cd " . escapeshellarg($repoPath) . " && git pull origin " . escapeshellarg($branch) . " 2>&1";
exec($command, $output, $returnCode);

// 3. Simpan Log
$logMessage = date('Y-m-d H:i:s') . " | Return Code: $returnCode | Output: " . implode("\n", $output) . "\n";
file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);

echo "Deployment finished with code: $returnCode. See deploy.log for details.";
?>