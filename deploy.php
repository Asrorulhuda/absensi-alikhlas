<?php
/**
 * GitHub Webhook Receiver
 * 
 * When GitHub sends a push event, this script saves a flag file
 * so the dashboard can show a "update available" notification.
 * 
 * SETUP:
 * 1. GitHub repo → Settings → Webhooks → Add webhook
 *    - Payload URL: https://yourdomain.com/deploy.php
 *    - Content type: application/json
 *    - Secret: (same as $secret below)
 *    - Events: Just the push event
 */

// ============================================
// CONFIGURATION
// ============================================
$secret = 'alikhlas-deploy-2026'; // Change this to match your GitHub webhook secret
$branch = 'main';
$flagFile = __DIR__ . '/update_available.json';
$logFile = __DIR__ . '/deploy.log';

// ============================================
// SECURITY VERIFICATION
// ============================================
$headers = getallheaders();
$hubSignature = isset($headers['X-Hub-Signature-256']) ? $headers['X-Hub-Signature-256'] : '';

if (empty($hubSignature)) {
    http_response_code(403);
    die('No signature provided.');
}

$payload = file_get_contents('php://input');
$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($hash, $hubSignature)) {
    http_response_code(403);
    die('Invalid signature.');
}

// Verify it's a push to the correct branch
$data = json_decode($payload, true);
$ref = isset($data['ref']) ? $data['ref'] : '';

if ($ref !== 'refs/heads/' . $branch) {
    die('Not the target branch. Ignoring.');
}

// ============================================
// SAVE UPDATE FLAG
// ============================================
$pusher = isset($data['pusher']['name']) ? $data['pusher']['name'] : 'Unknown';
$commitMsg = '';
$commitCount = 0;

if (isset($data['commits']) && is_array($data['commits'])) {
    $commitCount = count($data['commits']);
    $lastCommit = end($data['commits']);
    $commitMsg = isset($lastCommit['message']) ? $lastCommit['message'] : '';
}

$updateInfo = [
    'available' => true,
    'timestamp' => date('Y-m-d H:i:s'),
    'pusher' => $pusher,
    'branch' => $branch,
    'commit_message' => $commitMsg,
    'commit_count' => $commitCount,
    'compare_url' => isset($data['compare']) ? $data['compare'] : ''
];

file_put_contents($flagFile, json_encode($updateInfo, JSON_PRETTY_PRINT), LOCK_EX);

// Log
$log = date('Y-m-d H:i:s') . " | Push detected from: $pusher | Commits: $commitCount | Message: $commitMsg\n";
file_put_contents($logFile, $log, FILE_APPEND | LOCK_EX);

http_response_code(200);
echo json_encode(['status' => 'ok', 'message' => 'Update flag saved']);
