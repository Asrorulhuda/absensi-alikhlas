<?php
/**
 * GitHub Webhook Auto-Deploy Script
 * 
 * This script is triggered by GitHub webhooks whenever you push to the repo.
 * It pulls the latest changes from GitHub to your hosting server.
 * 
 * SETUP INSTRUCTIONS:
 * 1. Upload this file to your hosting server
 * 2. Set your secret key below (change DEPLOY_SECRET)
 * 3. In GitHub repo → Settings → Webhooks → Add webhook:
 *    - Payload URL: https://yourdomain.com/deploy.php
 *    - Content type: application/json
 *    - Secret: (same as DEPLOY_SECRET below)
 *    - Events: Just the push event
 * 4. Make sure Git is available on your hosting server
 * 5. Make sure the web root is a git repository (run `git clone` once on server)
 */

// ============================================
// CONFIGURATION - CHANGE THESE VALUES
// ============================================
$secret = 'alikhlas-deploy-2026'; // Change this to a strong secret key
$branch = 'main';
$logFile = __DIR__ . '/deploy.log';

// ============================================
// SECURITY VERIFICATION
// ============================================
// Verify the request is from GitHub
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
// DEPLOY
// ============================================
$output = [];
$returnCode = 0;

// Navigate to web root and pull latest changes
$commands = [
    'cd ' . escapeshellarg(__DIR__) . ' 2>&1',
    'git fetch origin ' . $branch . ' 2>&1',
    'git reset --hard origin/' . $branch . ' 2>&1',
];

$fullCommand = implode(' && ', $commands);
exec($fullCommand, $output, $returnCode);

// Log the deployment
$log = date('Y-m-d H:i:s') . " | Branch: $branch | Status: " . ($returnCode === 0 ? 'SUCCESS' : 'FAILED') . "\n";
$log .= "Output: " . implode("\n", $output) . "\n";
$log .= str_repeat('-', 50) . "\n";
file_put_contents($logFile, $log, FILE_APPEND | LOCK_EX);

// Response
if ($returnCode === 0) {
    http_response_code(200);
    echo "Deploy successful!\n" . implode("\n", $output);
} else {
    http_response_code(500);
    echo "Deploy failed!\n" . implode("\n", $output);
}
