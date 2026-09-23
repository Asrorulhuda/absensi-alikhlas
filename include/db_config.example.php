<?php
/**
 * Database Configuration File
 * Sistem Presensi Siswa
 * 
 * INSTRUCTIONS: Copy this file to db_config.php and fill in your credentials.
 */

date_default_timezone_set("Asia/Jakarta");

// PHP 8.1+ enables strict MySQLi exceptions by default. The application was
// written around the legacy false-return checks, so retain those semantics.
mysqli_report(MYSQLI_REPORT_OFF);

// Database credentials
$db_server = 'localhost';
$db_name = 'your_database_name';
$db_user = 'your_database_user';
$db_password = 'your_database_password';

// Application settings
$no_of_records_per_page = 10;
$appname = 'Sistem Presensi Siswa';

// MySQLi Connection
$link = mysqli_connect($db_server, $db_user, $db_password, $db_name);

if (!$link) {
    error_log("MySQLi Connection Error: " . mysqli_connect_error());
    die("Database connection failed. Please contact administrator.");
}

// Set character encoding
$query = "SHOW VARIABLES LIKE 'character_set_database'";
if ($result = mysqli_query($link, $query)) {
    while ($row = mysqli_fetch_row($result)) {
        if (!$link->set_charset($row[1])) {
            error_log("Error loading character set {$row[1]}: " . $link->error);
        }
    }
    mysqli_free_result($result);
}

// Global MySQLi connection (legacy support)
$GLOBALS["___mysqli_ston"] = mysqli_connect($db_server, $db_user, $db_password);
if ($GLOBALS["___mysqli_ston"]) {
    mysqli_select_db($GLOBALS["___mysqli_ston"], $db_name);
}

/**
 * PDO Database Connection Class
 */
class Database
{
    public $conn;

    public function getConnection()
    {
        global $db_server, $db_name, $db_user, $db_password;
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$db_server};dbname={$db_name};charset=utf8mb4",
                $db_user,
                $db_password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $exception) {
            error_log("Database Connection Error: " . $exception->getMessage());
            die("Database connection failed. Please contact administrator.");
        }
        return $this->conn;
    }
}
