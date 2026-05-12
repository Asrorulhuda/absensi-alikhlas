<?php
require_once "include/db_config.php";

header('Content-Type: application/json');

if(isset($_POST['uid'])) {
    $uid = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['uid']);
    
    // Check in siswa table
    $sql_siswa = "SELECT s_id, s_nama, s_nis, s_uid FROM data_siswa WHERE s_uid = '$uid' LIMIT 1";
    $result_siswa = mysqli_query($GLOBALS["___mysqli_ston"], $sql_siswa);
    
    if(mysqli_num_rows($result_siswa) > 0) {
        $data = mysqli_fetch_assoc($result_siswa);
        echo json_encode([
            'status' => 'registered',
            'type' => 'siswa',
            'data' => [
                'nama' => $data['s_nama'],
                'nomor' => $data['s_nis'],
                'role' => 'Siswa',
                'uid' => $data['s_uid']
            ]
        ]);
        exit;
    }
    
    // Check in guru table
    $sql_guru = "SELECT g_id, g_nama, g_nip, g_uid FROM data_guru WHERE g_uid = '$uid' LIMIT 1";
    $result_guru = mysqli_query($GLOBALS["___mysqli_ston"], $sql_guru);
    
    if(mysqli_num_rows($result_guru) > 0) {
        $data = mysqli_fetch_assoc($result_guru);
        echo json_encode([
            'status' => 'registered',
            'type' => 'guru',
            'data' => [
                'nama' => $data['g_nama'],
                'nomor' => $data['g_nip'],
                'role' => 'Guru',
                'uid' => $data['g_uid']
            ]
        ]);
        exit;
    }
    
    // Card not registered
    echo json_encode([
        'status' => 'not_registered',
        'uid' => $uid
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'UID tidak ditemukan'
    ]);
}
?>