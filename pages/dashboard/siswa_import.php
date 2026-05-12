<?php
session_start();
if ($_SESSION['akses'] != 'Admin') {
    header('location:../../index');
    exit();
}

require_once "../../include/db_config.php";

// Helper to get Jurusan ID
$jurusan_map = [];
$sql_jurusan = "SELECT j_id, j_name, j_short FROM opsi_jurusan";
if ($result_jurusan = mysqli_query($GLOBALS["___mysqli_ston"], $sql_jurusan)) {
    while ($row = mysqli_fetch_assoc($result_jurusan)) {
        $jurusan_map[strtoupper($row['j_name'])] = $row['j_id'];
        $jurusan_map[strtoupper($row['j_short'])] = $row['j_id'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csv_file"])) {
    $file = $_FILES["csv_file"]["tmp_name"];
    
    if ($_FILES["csv_file"]["size"] > 0) {
        $handle = fopen($file, "r");
        
        // Skip header
        fgetcsv($handle, 1000, ",");
        
        $success_count = 0;
        $fail_count = 0;
        
        // Prepare PDO for safer inserts
        $dsn = "mysql:host=$db_server;dbname=$db_name;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        try {
            $pdo = new PDO($dsn, $db_user, $db_password, $options);
            // Check if column s_picture exists, if not remove from query. Assuming it exists based on siswa_registration.php
            // Default picture: ../../assets/img/user_pict/user_default.png
            
            $stmt = $pdo->prepare("INSERT INTO data_siswa (s_uid, s_nama, s_nis, s_kelamin, s_tgl_lahir, s_phone, s_alamat, s_nama_wali, s_kontak_wali, s_kelas, s_jurusan, s_status, s_created, s_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Aktif', CURDATE(), '../../assets/img/user_pict/user_default.png')");
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Map columns: UID,Nama,NIS,Kelamin,Tgl_Lahir,Phone,Alamat,Nama_Wali,Kontak_Wali,Kelas,Jurusan
                // Indexes: 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10
                
                if (count($data) < 11) {
                    $fail_count++;
                    continue;
                }

                $uid = trim($data[0]);
                $nama = trim($data[1]);
                $nis = trim($data[2]);
                $kelamin = trim($data[3]);
                $tgl_lahir = trim($data[4]); 
                $phone = trim($data[5]);
                $alamat = trim($data[6]);
                $nama_wali = trim($data[7]);
                $kontak_wali = trim($data[8]);
                $kelas = trim($data[9]);
                $jurusan_str = strtoupper(trim($data[10]));
                
                $jurusan_id = isset($jurusan_map[$jurusan_str]) ? $jurusan_map[$jurusan_str] : null;
                
                // Validate required fields
                if ($uid && $nama && $jurusan_id) {
                     try {
                        // Check date format, if empty set NULL or default?
                        if (empty($tgl_lahir)) $tgl_lahir = '2000-01-01';
                        
                        $stmt->execute([$uid, $nama, $nis, $kelamin, $tgl_lahir, $phone, $alamat, $nama_wali, $kontak_wali, $kelas, $jurusan_id]);
                        $newId = $pdo->lastInsertId();
                        $uname = strtolower(preg_replace('/\s+/', '', $nama)) . rand(100,999);
                        $upass = md5(($nis && trim($nis)!="") ? $nis : '123456');
                        $upict = '../../assets/img/operator_pict/user_default.png';
                        $stmtU = $pdo->prepare("INSERT INTO users (name,email,username,password,picture,level_akses,id_siswa) VALUES (?,?,?,?,?,?,?)");
                        $stmtU->execute([$nama,'',$uname,$upass,$upict,'User',$newId]);
                        mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE data_siswa SET user_stat=1 WHERE s_id='$newId'");
                        $success_count++;
                     } catch (Exception $e) {
                        $fail_count++;
                        // error_log("Import Error Row: " . implode(',', $data) . " - " . $e->getMessage());
                     }
                } else {
                    $fail_count++;
                }
            }
            
            fclose($handle);
            
            $msg = base64_encode("Import selesai. Berhasil: $success_count, Gagal: $fail_count");
            header("location: siswa?msg=".base64_encode("200")."&info=$msg");
            
        } catch (Exception $e) {
             $msg = base64_encode("Database Connection Error: " . $e->getMessage());
             header("location: siswa?msg=".base64_encode("300")."&info=$msg");
        }
    } else {
         $msg = base64_encode("File kosong");
         header("location: siswa?msg=".base64_encode("300")."&info=$msg");
    }
} else {
    header("location: siswa");
}
?>
