<?php
// Legacy compatibility wrapper. All API routes now share the application's
// private database configuration instead of keeping credentials in Git.
require_once __DIR__ . '/../../include/db_config.php';
