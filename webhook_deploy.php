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
$secretToken = 'absensi_alikhlas_2023';

// Path absolut ke direktori Git Anda di server.
$repoPath = __DIR__;

// Branch yang ingin di-pull.
$branch = 'main';

// File untuk menyimpan log.
$logFile = __DIR__ . '/deploy.log';

// --- PROSES ---

// Fungsi untuk mendapatkan semua header (karena getallheaders() tidak selalu ada)
function getRequestHeaders() {
    $headers = [];
    foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) <> 'HTTP_') {
            continue;
        }
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$header] = $value;
    }
    return $headers;
}

$headers = getRequestHeaders();
// Coba ambil signature dari berbagai kemungkinan key
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] 
             ?? $_SERVER['X_HUB_SIGNATURE_256'] 
             ?? $headers['X-Hub-Signature-256'] 
             ?? '';

// 1. Validasi Request
if (empty($signature)) {
    // Log headers untuk debug jika signature tidak ditemukan
    $debugHeaders = json_encode($headers);
    file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERROR: Signature missing. Headers received: $debugHeaders\n", FILE_APPEND);
    
    http_response_code(403);
    die("Signature not found. Check deploy.log for debug info.");
}

$payload = file_get_contents('php://input');
$hash = 'sha256=' . hash_hmac('sha256', $payload, $secretToken, false);

if (!hash_equals($hash, $signature)) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERROR: Signature mismatch.\n", FILE_APPEND);
    http_response_code(403);
    die("Signature mismatch.");
}

// 2. Jalankan Git Pull
// Pastikan folder repo bisa diakses oleh user web server (misal: www-data)
$command = "cd " . escapeshellarg($repoPath) . " && git pull origin " . escapeshellarg($branch) . " 2>&1";
exec($command, $output, $returnCode);

// 3. Simpan Log
$logMessage = date('Y-m-d H:i:s') . " | SUCCESS | Return Code: $returnCode | Output: " . implode("\n", $output) . "\n";
file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);

echo "Deployment finished with code: $returnCode. See deploy.log for details.";
?>