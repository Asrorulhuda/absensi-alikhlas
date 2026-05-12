<?php
session_start();
require_once "include/db_config.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $uid = trim($_POST['uid']);
    $tipe = $_POST['tipe'];
    $nama = trim($_POST['nama']);
    $created = date("Y-m-d");
    
    if(empty($uid)){
         header("Location: registrasi_kartu.php?status=error&msg=UID tidak boleh kosong");
         exit();
    }

    $database = new Database();
    $pdo = $database->getConnection();

    // Check if UID exists in data_siswa
    $checkSiswa = $pdo->prepare("SELECT * FROM data_siswa WHERE s_uid = ?");
    $checkSiswa->execute([$uid]);
    if($checkSiswa->rowCount() > 0){
        header("Location: registrasi_kartu.php?status=error&msg=UID sudah terdaftar sebagai Siswa");
        exit();
    }

    // Check if UID exists in data_guru
    $checkGuru = $pdo->prepare("SELECT * FROM data_guru WHERE g_uid = ?");
    $checkGuru->execute([$uid]);
    if($checkGuru->rowCount() > 0){
        header("Location: registrasi_kartu.php?status=error&msg=UID sudah terdaftar sebagai Guru");
        exit();
    }

    if($tipe == "Siswa") {
        $nis = trim($_POST['nis']);
        $kontak_wali = trim($_POST['kontak_wali']);
        $nama_wali = trim($_POST['nama_wali']);
        $kelas = trim($_POST['kelas']);
        $jurusan = trim($_POST['jurusan']);
        
        // Use default values for missing fields to avoid SQL errors if strict mode
        $s_kelamin = "";
        $s_tgl_lahir = "0000-00-00";
        $s_phone = "";
        $s_alamat = "";
        $s_picture = "";
        $s_status = "Active";

        $sql = "INSERT INTO data_siswa (s_uid, s_nama, s_nis, s_kontak_wali, s_nama_wali, s_kelas, s_jurusan, s_created, s_kelamin, s_tgl_lahir, s_phone, s_alamat, s_picture, s_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([$uid, $nama, $nis, $kontak_wali, $nama_wali, $kelas, $jurusan, $created, $s_kelamin, $s_tgl_lahir, $s_phone, $s_alamat, $s_picture, $s_status])){
            // Remove from data_invalid if exists
            $delStmt = $pdo->prepare("DELETE FROM data_invalid WHERE uid = ?");
            $delStmt->execute([$uid]);
            $newId = $pdo->lastInsertId();
            $uname = strtolower(preg_replace('/\s+/', '', $nama)) . rand(100,999);
            $upass = md5(($nis && trim($nis)!="") ? $nis : '123456');
            $upict = '../../assets/img/operator_pict/user_default.png';
            $stmtU = $pdo->prepare("INSERT INTO users (name,email,username,password,picture,level_akses,id_siswa) VALUES (?,?,?,?,?,?,?)");
            $stmtU->execute([$nama,'',$uname,$upass,$upict,'User',$newId]);
            $pdo->prepare("UPDATE data_siswa SET user_stat=1 WHERE s_id=?")->execute([$newId]);
            header("Location: registrasi_kartu.php?status=success");
        } else {
            header("Location: registrasi_kartu.php?status=error&msg=Gagal menyimpan data siswa");
        }

    } else if($tipe == "Guru") {
        $nip = trim($_POST['nip']);
        $kontak_guru = trim($_POST['kontak_guru']);
        $tugas_tambahan = trim($_POST['tugas_tambahan']);

        $sql = "INSERT INTO data_guru (g_uid, g_nama, g_nip, g_contact, g_tgs_tambahan) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([$uid, $nama, $nip, $kontak_guru, $tugas_tambahan])){
             // Remove from data_invalid if exists
             $delStmt = $pdo->prepare("DELETE FROM data_invalid WHERE uid = ?");
             $delStmt->execute([$uid]);
            header("Location: registrasi_kartu.php?status=success");
        } else {
            header("Location: registrasi_kartu.php?status=error&msg=Gagal menyimpan data guru");
        }
    }
} else {
    header("Location: registrasi_kartu.php");
}
?>
