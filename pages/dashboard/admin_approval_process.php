<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

if ($_SESSION['akses'] != 'Admin') {
    header('location:../../index');
    exit();
}

require_once "../../include/db_config.php";

// Initialize PDO
$database = new Database();
$pdo = $database->getConnection();

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    $admin_id = $_SESSION['id'];
    
    $status = '';
    if ($action == 'approve') {
        $status = 'Disetujui';
    } elseif ($action == 'reject') {
        $status = 'Ditolak';
    } else {
        header('Location: admin_approval.php?msg=error');
        exit();
    }
    
    try {
        $sql = "UPDATE pengajuan_izin SET status = ?, approved_by = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status, $admin_id, $id]);
        
        if ($action == 'approve') {
            header('Location: admin_approval.php?msg=approved');
        } else {
            header('Location: admin_approval.php?msg=rejected');
        }
    } catch (PDOException $e) {
        header('Location: admin_approval.php?msg=error');
    }
} else {
    header('Location: admin_approval.php');
}
?>