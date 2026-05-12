<?php
require_once "include/db_config.php";

try {
    $conn = new Database();
    $pdo = $conn->getConnection();
    if ($pdo) {
        echo "Database connection successful!\n";
        // Test a simple query
        $stmt = $pdo->query("SELECT 1");
        if ($stmt) {
            echo "Query execution successful!\n";
        } else {
            echo "Query execution failed!\n";
        }
    } else {
        echo "Database connection failed!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
