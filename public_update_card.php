<?php
require_once "include/db_config.php";

header('Content-Type: application/json');

if(isset($_POST['id']) && isset($_POST['type']) && isset($_POST['uid'])) {
    $id = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['id']);
    $type = $_POST['type'];
    $uid = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['uid']);
    
    // Check if UID is already used
    $check_siswa = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM data_siswa WHERE s_uid='$uid'");
    $check_guru = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM data_guru WHERE g_uid='$uid'");
    
    if(mysqli_num_rows($check_siswa) > 0 || mysqli_num_rows($check_guru) > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'UID Kartu sudah terdaftar di sistem. Mohon gunakan kartu lain.'
        ]);
        exit;
    }
    
    if($type == 'siswa') {
        $sql = "UPDATE data_siswa SET s_uid='$uid' WHERE s_id='$id'";
        if(mysqli_query($GLOBALS["___mysqli_ston"], $sql)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Kartu siswa berhasil diupdate.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal update kartu siswa.'
            ]);
        }
    } else if($type == 'guru') {
        $sql = "UPDATE data_guru SET g_uid='$uid' WHERE g_id='$id'";
        if(mysqli_query($GLOBALS["___mysqli_ston"], $sql)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Kartu guru berhasil diupdate.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal update kartu guru.'
            ]);
        }
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak lengkap'
    ]);
}
?>