<?php
require_once "include/db_config.php";

// Use the existing connection from db_config.php
// $link is mysqli, $pdo is PDO (if initialized)

// We need to ensure the password column is long enough for Bcrypt (60 chars). 
// MD5 is 32 chars. Safe to upgrade to 255.

$database = new Database();
$pdo = $database->getConnection();

try {
    echo "Updating 'users' table password column length...\n";
    $sql = "ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL";
    $pdo->exec($sql);
    echo "Success: Password column updated to VARCHAR(255).\n";
} catch (PDOException $e) {
    echo "Error updating password column: " . $e->getMessage() . "\n";
}

// Security: Prevent directory listing in uploads
$upload_dirs = [
    "assets/img/bukti_izin/",
    "assets/img/user/",
    "assets/img/guru/",
    "assets/img/siswa/"
];

foreach ($upload_dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    $htaccess = $dir . ".htaccess";
    $content = "Options -Indexes\n<FilesMatch \"\.(php|php5|phtml)$\">\nOrder Deny,Allow\nDeny from all\n</FilesMatch>";
    file_put_contents($htaccess, $content);
    echo "Secured directory: $dir\n";
}

echo "Database and Directory Security Update Completed.\n";
?>