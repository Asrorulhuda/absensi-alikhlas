<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

if ($_SESSION['akses'] != 'Guru') {
    header('location:../../index'); 
    exit();
}

require_once "../../include/db_config.php";

$database = new Database();
$pdo = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $today = date('Y-m-d');
    $now = date('H:i:s');
    
    try {
        $pdo->beginTransaction();
        
        foreach ($_POST['status'] as $uid => $status) {
            // Validate status
            if (!in_array($status, ['HADIR', 'IZIN', 'SAKIT', 'ALPHA'])) {
                continue;
            }
            
            // Map status to DB values
            // Default values
            $keterangan = $status;
            $db_status = 'IN';
            $ket_masuk = 'Manual Wali Kelas';
            
            // Adjust based on status
            if ($status == 'HADIR') {
                $keterangan = 'HADIR'; // Ensure this matches report query
            } elseif ($status == 'IZIN') {
                $keterangan = 'IZIN';
            } elseif ($status == 'SAKIT') {
                $keterangan = 'SAKIT';
            } elseif ($status == 'ALPHA') {
                $keterangan = 'ALPHA';
            }
            
            // Check if record exists
            $stmt_check = $pdo->prepare("SELECT id FROM data_absen WHERE tanggal = ? AND uid = ?");
            $stmt_check->execute([$today, $uid]);
            $existing = $stmt_check->fetch();
            
            if ($existing) {
                // Update
                $stmt_update = $pdo->prepare("UPDATE data_absen SET keterangan = ?, ket_masuk = ? WHERE id = ?");
                $stmt_update->execute([$keterangan, $ket_masuk, $existing['id']]);
            } else {
                // Insert
                $stmt_insert = $pdo->prepare("INSERT INTO data_absen (tanggal, jam_masuk, jam_keluar, ket_masuk, ket_keluar, uid, status, keterangan, attendance_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                // jam_keluar 00:00:00, ket_keluar empty
                $stmt_insert->execute([$today, $now, '00:00:00', $ket_masuk, '', $uid, $db_status, $keterangan, 'KBM']);
            }
        }
        
        $pdo->commit();
        header("Location: dashboard_guru.php?att_msg=success");
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: dashboard_guru.php?att_msg=error");
        exit();
    }
} else {
    header("Location: dashboard_guru.php");
    exit();
}
?>