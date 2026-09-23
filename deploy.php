<?php
/**
 * GitHub webhook receiver for the dashboard's "update available" flag.
 * Use webhook_deploy.php instead when deployment must happen automatically.
 */
require_once __DIR__ . '/include/runtime_config.php';

header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$payload = file_get_contents('php://input');
if ($payload === false || !verify_github_webhook($payload)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid signature']);
    exit;
}

$data = json_decode($payload, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload']);
    exit;
}

$branch = (string) app_config('deploy_branch', 'main');
if (($data['ref'] ?? '') !== 'refs/heads/' . $branch) {
    echo json_encode(['status' => 'ignored', 'message' => 'Not the deployment branch']);
    exit;
}

$commits = is_array($data['commits'] ?? null) ? $data['commits'] : [];
$lastCommit = $commits ? end($commits) : [];
$updateInfo = [
    'available' => true,
    'timestamp' => date('Y-m-d H:i:s'),
    'pusher' => (string) ($data['pusher']['name'] ?? 'Unknown'),
    'branch' => $branch,
    'commit_message' => (string) ($lastCommit['message'] ?? ''),
    'commit_count' => count($commits),
    'compare_url' => (string) ($data['compare'] ?? ''),
];

$encoded = json_encode($updateInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
if ($encoded === false || file_put_contents(__DIR__ . '/update_available.json', $encoded, LOCK_EX) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Cannot write update flag']);
    exit;
}

http_response_code(200);
echo json_encode(['status' => 'ok', 'message' => 'Update flag saved']);
