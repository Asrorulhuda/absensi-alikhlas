<?php
/**
 * Runtime configuration loader.
 *
 * Production secrets can be supplied through environment variables or an
 * ignored include/production_config.php file. Environment variables win.
 */
function app_config(string $key, mixed $default = null): mixed
{
    static $config = null;

    if ($config === null) {
        $config = [];
        $privateConfig = __DIR__ . '/production_config.php';
        if (is_file($privateConfig)) {
            $loaded = require $privateConfig;
            if (is_array($loaded)) {
                $config = $loaded;
            }
        }

        $environmentMap = [
            'app_env' => 'APP_ENV',
            'webhook_secret' => 'WEBHOOK_SECRET',
            'device_api_key' => 'DEVICE_API_KEY',
            'deploy_branch' => 'DEPLOY_BRANCH',
            'cron_token' => 'CRON_TOKEN',
            'php_cli' => 'PHP_CLI',
            'registration_token' => 'REGISTRATION_TOKEN',
        ];

        foreach ($environmentMap as $configKey => $environmentName) {
            $value = getenv($environmentName);
            if ($value !== false && $value !== '') {
                $config[$configKey] = $value;
            }
        }
    }

    return array_key_exists($key, $config) ? $config[$key] : $default;
}

function verify_github_webhook(string $payload): bool
{
    $secret = (string) app_config('webhook_secret', '');
    $signature = (string) ($_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '');

    if ($secret === '' || $signature === '') {
        return false;
    }

    return hash_equals('sha256=' . hash_hmac('sha256', $payload, $secret), $signature);
}

function is_https_request(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }

    return strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
}

function set_remember_me_cookie(string $value, int $expires): void
{
    setcookie('remember_me', $value, [
        'expires' => $expires,
        'path' => '/',
        'secure' => is_https_request(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function has_registration_access(): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (($_SESSION['akses'] ?? '') === 'Admin') {
        return true;
    }

    $expected = (string) app_config('registration_token', '');
    if ($expected === '') {
        return true; // Backward-compatible local/test mode.
    }

    $provided = (string) (
        $_SERVER['HTTP_X_REGISTRATION_TOKEN']
        ?? $_GET['token']
        ?? $_POST['registration_token']
        ?? ''
    );
    if ($provided !== '' && hash_equals($expected, $provided)) {
        $_SESSION['registration_access'] = hash('sha256', $expected);
    }

    return isset($_SESSION['registration_access'])
        && hash_equals(hash('sha256', $expected), (string) $_SESSION['registration_access']);
}

function require_registration_access(bool $jsonResponse = false): void
{
    if (has_registration_access()) {
        return;
    }

    http_response_code(403);
    if ($jsonResponse) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'error', 'message' => 'Akses registrasi ditolak.']);
    } else {
        echo 'Akses registrasi ditolak.';
    }
    exit;
}

function require_device_api_key(): void
{
    $expected = (string) app_config('device_api_key', '');
    if ($expected === '') {
        return; // Backward-compatible local/test mode.
    }

    $provided = (string) ($_SERVER['HTTP_X_API_KEY'] ?? $_GET['key'] ?? '');
    if ($provided === '' || !hash_equals($expected, $provided)) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized device']);
        exit;
    }
}
