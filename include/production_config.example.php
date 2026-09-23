<?php
/**
 * Copy to production_config.php on the server. The copied file is ignored by
 * Git, so webhook deployments will not overwrite or publish these secrets.
 */
return [
    'app_env' => 'production',
    'webhook_secret' => 'GANTI_DENGAN_SECRET_GITHUB_YANG_PANJANG_DAN_ACAK',
    'device_api_key' => 'GANTI_DENGAN_API_KEY_MESIN_YANG_PANJANG_DAN_ACAK',
    'deploy_branch' => 'main',
    'cron_token' => 'GANTI_DENGAN_TOKEN_CRON_YANG_PANJANG_DAN_ACAK',
    // Sesuaikan bila executable PHP CLI hosting berada di path lain.
    'php_cli' => 'php',
    'registration_token' => 'GANTI_DENGAN_TOKEN_REGISTRASI_YANG_PANJANG_DAN_ACAK',
];
