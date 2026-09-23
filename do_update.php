<?php
/**
 * Manual deployment endpoint for authenticated administrators.
 * Automatic deployments should use webhook_deploy.php.
 */
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (($_SESSION['akses'] ?? '') !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/include/runtime_config.php';

$branch = (string) app_config('deploy_branch', 'main');
if (!preg_match('/^[A-Za-z0-9._\/-]+$/', $branch)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Invalid deployment branch configuration']);
    exit;
}

$remoteBranch = 'origin/' . $branch;
$phpCli = (string) app_config('php_cli', 'php');
if (!function_exists('exec')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'PHP exec() is disabled on this server']);
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
$adminName = preg_replace('/[\r\n]+/', ' ', (string) ($_SESSION['name'] ?? 'Admin'));
$log = date('Y-m-d H:i:s') . " | MANUAL UPDATE | {$status} | admin={$adminName} | code={$returnCode}\n";
$log .= implode("\n", $output) . "\n";
file_put_contents(__DIR__ . '/deploy.log', $log, FILE_APPEND | LOCK_EX);

if ($returnCode === 0 && is_file(__DIR__ . '/update_available.json')) {
    unlink(__DIR__ . '/update_available.json');
}

http_response_code($returnCode === 0 ? 200 : 500);
echo json_encode([
    'success' => $returnCode === 0,
    'message' => $returnCode === 0
        ? 'Update dan migration berhasil.'
        : 'Update gagal. Periksa deploy.log pada server.',
]);
