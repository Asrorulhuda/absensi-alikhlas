<?php
/**
 * Signed GitHub webhook receiver for automatic, fast-forward-only deployment.
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
if (!preg_match('/^[A-Za-z0-9._\/-]+$/', $branch)) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid deployment branch configuration']);
    exit;
}

if (($data['ref'] ?? '') !== 'refs/heads/' . $branch) {
    echo json_encode(['status' => 'ignored', 'message' => 'Not the deployment branch']);
    exit;
}

$lockHandle = fopen(__DIR__ . '/deploy.lock', 'c');
if ($lockHandle === false || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
    http_response_code(409);
    echo json_encode(['error' => 'Another deployment is running']);
    exit;
}

$remoteBranch = 'origin/' . $branch;
$phpCli = (string) app_config('php_cli', 'php');
if (!function_exists('exec')) {
    http_response_code(500);
    echo json_encode(['error' => 'PHP exec() is disabled on this server']);
    exit;
}
$commands = [
    'git -C ' . escapeshellarg(__DIR__) . ' fetch origin ' . escapeshellarg($branch) . ' 2>&1',
    'git -C ' . escapeshellarg(__DIR__) . ' merge --ff-only ' . escapeshellarg($remoteBranch) . ' 2>&1',
    escapeshellarg($phpCli) . ' ' . escapeshellarg(__DIR__ . '/scripts/migrate.php') . ' 2>&1',
];

$output = [];
$returnCode = 0;
exec(implode(' && ', $commands), $output, $returnCode);

$status = $returnCode === 0 ? 'SUCCESS' : 'FAILED';
$logMessage = sprintf(
    "%s | %s | branch=%s | code=%d\n%s\n",
    date('Y-m-d H:i:s'),
    $status,
    $branch,
    $returnCode,
    implode("\n", $output)
);
file_put_contents(__DIR__ . '/deploy.log', $logMessage, FILE_APPEND | LOCK_EX);

flock($lockHandle, LOCK_UN);
fclose($lockHandle);

http_response_code($returnCode === 0 ? 200 : 500);
echo json_encode([
    'status' => strtolower($status),
    'code' => $returnCode,
    'message' => $returnCode === 0 ? 'Deployment and migrations completed' : 'Deployment failed; check deploy.log',
]);
