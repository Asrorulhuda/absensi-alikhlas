<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only.');
}

require_once __DIR__ . '/../include/db_config.php';

$pdo = new PDO(
    "mysql:host={$db_server};dbname={$db_name};charset=utf8mb4",
    $db_user,
    $db_password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => true,
    ]
);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS schema_migrations (
        id int NOT NULL AUTO_INCREMENT,
        migration varchar(255) NOT NULL,
        applied_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY migration (migration)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

$migrationFiles = glob(__DIR__ . '/../database/migrations/*.sql') ?: [];
sort($migrationFiles, SORT_STRING);

$isApplied = $pdo->prepare('SELECT 1 FROM schema_migrations WHERE migration = ? LIMIT 1');
$markApplied = $pdo->prepare('INSERT INTO schema_migrations (migration) VALUES (?)');

foreach ($migrationFiles as $migrationFile) {
    $migrationName = basename($migrationFile);
    $isApplied->execute([$migrationName]);
    if ($isApplied->fetchColumn()) {
        echo "SKIP {$migrationName}\n";
        continue;
    }

    $sqlContents = file_get_contents($migrationFile);
    if ($sqlContents === false) {
        throw new RuntimeException("Cannot read migration: {$migrationName}");
    }

    // Execute statements one by one. This also works on hosting providers that
    // disable PDO MySQL multi-statements for security.
    $sqlLines = preg_split('/\R/', $sqlContents) ?: [];
    $sqlLines = array_filter(
        $sqlLines,
        static fn(string $line): bool => !preg_match('/^\s*--/', $line)
    );
    $statements = explode(';', implode("\n", $sqlLines));
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if ($statement !== '') {
            $pdo->exec($statement);
        }
    }

    $markApplied->execute([$migrationName]);
    echo "APPLIED {$migrationName}\n";
}

echo "Database migrations complete.\n";
