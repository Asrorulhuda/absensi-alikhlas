<?php
$db_config_path = "c:\\xampp\\htdocs\\absensirfid\\include\\db_config.php";
if (!file_exists($db_config_path)) {
    die("File not found: " . $db_config_path);
}
require_once $db_config_path;

$dsn = "mysql:host=$db_server;dbname=$db_name;charset=utf8mb4";
$options = [
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_password, $options);
    
    $columns = ['cnfg_kbm', 'cnfg_eskul', 'cnfg_kegiatan'];
    
    foreach ($columns as $col) {
        // Check if column exists
        $sql = "SHOW COLUMNS FROM wa_notification LIKE '$col'";
        $stmt = $pdo->query($sql);
        
        if ($stmt->rowCount() == 0) {
            // Add column
            $sql = "ALTER TABLE wa_notification ADD COLUMN $col TINYINT(1) DEFAULT 0";
            $pdo->exec($sql);
            echo "Added column $col.\n";
        } else {
            echo "Column $col already exists.\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>