<?php
session_start();
require_once "include/runtime_config.php";
require_registration_access(true);
require_once "include/db_config.php";

header('Content-Type: application/json');

if(isset($_POST['uid']) && isset($_POST['nama']) && isset($_POST['nomor']) && isset($_POST['role'])) {
    $uid = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['uid']);
    $nama = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['nama']);
    $nomor = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['nomor']);
    $role = $_POST['role'];
    
    // Check if UID already exists
    $check_siswa = "SELECT s_id FROM data_siswa WHERE s_uid = '$uid'";
    $check_guru = "SELECT g_id FROM data_guru WHERE g_uid = '$uid'";
    
    $result_siswa = mysqli_query($GLOBALS["___mysqli_ston"], $check_siswa);
    $result_guru = mysqli_query($GLOBALS["___mysqli_ston"], $check_guru);
    
    if(mysqli_num_rows($result_siswa) > 0 || mysqli_num_rows($result_guru) > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'UID kartu ini sudah terdaftar!'
        ]);
        exit;
    }
    
    // Insert new data
    if($role == 'Siswa') {
        // Check if NIS already exists
        $check_nis = "SELECT s_id FROM data_siswa WHERE s_nis = '$nomor'";
        $result_nis = mysqli_query($GLOBALS["___mysqli_ston"], $check_nis);
        
        if(mysqli_num_rows($result_nis) > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'NIS sudah terdaftar!'
            ]);
            exit;
        }
        
        $sql = "INSERT INTO data_siswa (s_nama, s_nis, s_uid) VALUES ('$nama', '$nomor', '$uid')";
    } else if ($role == 'Guru') {
        // Check if NIP already exists
        $check_nip = "SELECT g_id FROM data_guru WHERE g_nip = '$nomor'";
        $result_nip = mysqli_query($GLOBALS["___mysqli_ston"], $check_nip);
        
        if(mysqli_num_rows($result_nip) > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'NIP sudah terdaftar!'
            ]);
            exit;
        }
        
        $sql = "INSERT INTO data_guru
                (g_nama, g_nip, g_uid, g_tgl_lahir, g_kelamin, g_jabatan,
                 g_mail, g_contact, g_kompetensi, g_picture, g_tgs_tambahan, g_alamat)
                VALUES ('$nama', '$nomor', '$uid', '1970-01-01', '-', 'Guru',
                        '', '', '-', '../../assets/img/operator_pict/user_default.png', '-', '-')";
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Role tidak valid.'
        ]);
        exit;
    }
    
    if(mysqli_query($GLOBALS["___mysqli_ston"], $sql)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Registrasi berhasil! Data dan kartu telah tersimpan.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan data: ' . mysqli_error($GLOBALS["___mysqli_ston"])
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak lengkap'
    ]);
}
?>
