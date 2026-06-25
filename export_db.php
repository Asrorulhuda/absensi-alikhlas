<?php
/**
 * Database Export Utility
 * Generates and downloads a complete SQL dump of the database.
 * Accessible only by logged-in Administrators.
 */

session_start();
date_default_timezone_set('Asia/Jakarta');

// Security check: Must be logged in as Admin
if (!isset($_SESSION['akses']) || $_SESSION['akses'] !== 'Admin') {
    http_response_code(403);
    die("Akses ditolak. Harap login sebagai Admin terlebih dahulu untuk mengunduh database.");
}

require_once "include/db_config.php";

// Set download headers
$filename = 'mialikhl_absensi_' . date('Ymd_His') . '.sql';
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// SQL Dump File Header
echo "-- phpMyAdmin SQL Dump / Custom Export\n";
echo "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
echo "-- Database: `" . $db_name . "`\n\n";

echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
echo "SET AUTOCOMMIT = 0;\n";
echo "SET FOREIGN_KEY_CHECKS = 0;\n";
echo "START TRANSACTION;\n";
echo "SET time_zone = \"+00:00\";\n\n";

// Fetch all tables in database
$tables = [];
$result = mysqli_query($link, "SHOW TABLES");
if ($result) {
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }
}

foreach ($tables as $table) {
    // Generate Drop Table statement
    echo "DROP TABLE IF EXISTS `" . $table . "`;\n";
    
    // Generate Create Table statement
    $create_res = mysqli_query($link, 'SHOW CREATE TABLE `' . $table . '`');
    if ($create_res) {
        $row_schema = mysqli_fetch_row($create_res);
        echo $row_schema[1] . ";\n\n";
    }
    
    // Fetch data and generate Insert statements
    $result_data = mysqli_query($link, 'SELECT * FROM `' . $table . '`');
    if ($result_data) {
        $num_fields = mysqli_num_fields($result_data);
        $rows_count = mysqli_num_rows($result_data);
        
        if ($rows_count > 0) {
            echo "INSERT INTO `" . $table . "` VALUES\n";
            $i = 0;
            while ($row_data = mysqli_fetch_row($result_data)) {
                echo "(";
                for ($j = 0; $j < $num_fields; $j++) {
                    if (isset($row_data[$j])) {
                        $val = mysqli_real_escape_string($link, $row_data[$j]);
                        
                        // Handle numeric vs string values
                        if (preg_match('/^[0-9]+$/', $row_data[$j]) && !preg_match('/^0[0-9]+/', $row_data[$j]) && strlen($row_data[$j]) <= 10) {
                            echo $val;
                        } else {
                            echo "'" . $val . "'";
                        }
                    } else {
                        echo "NULL";
                    }
                    
                    if ($j < ($num_fields - 1)) {
                        echo ",";
                    }
                }
                
                $i++;
                if ($i < $rows_count) {
                    echo "),\n";
                } else {
                    echo ");\n\n";
                }
            }
        }
    }
}

echo "SET FOREIGN_KEY_CHECKS = 1;\n";
echo "COMMIT;\n";
exit();
?>
