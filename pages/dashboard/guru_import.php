<?php
session_start();
if ($_SESSION['akses'] != 'Admin') {
    header('location:../../index');
    exit();
}

require_once "../../include/db_config.php";

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
            
            $stmt = $pdo->prepare("INSERT INTO data_guru (g_nip, g_uid, g_nama, g_tgl_lahir, g_kelamin, g_jabatan, g_mail, g_contact, g_kompetensi, g_picture, g_tgs_tambahan, g_alamat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, '../../assets/img/user_pict/user_default.png', ?, ?)");
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Map columns: NIP,UID,Nama,Tgl_Lahir,Kelamin,Jabatan,Email,Kontak,Kompetensi,Tugas_Tambahan,Alamat
                // Indexes: 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10
                
                if (count($data) < 11) {
                    $fail_count++;
                    continue;
                }

                $nip = trim($data[0]);
                $uid = trim($data[1]);
                $nama = trim($data[2]);
                $tgl_lahir = trim($data[3]);
                $kelamin = trim($data[4]);
                $jabatan = trim($data[5]);
                $email = trim($data[6]);
                $kontak = trim($data[7]);
                $kompetensi = trim($data[8]);
                $tgs_tambahan = trim($data[9]);
                $alamat = trim($data[10]);
                
                // Validate required fields
                if ($nip && $nama) {
                     try {
                        if (empty($tgl_lahir)) $tgl_lahir = '1980-01-01';
                        
                        $stmt->execute([$nip, $uid, $nama, $tgl_lahir, $kelamin, $jabatan, $email, $kontak, $kompetensi, $tgs_tambahan, $alamat]);
                        $success_count++;
                     } catch (Exception $e) {
                        $fail_count++;
                     }
                } else {
                    $fail_count++;
                }
            }
            
            fclose($handle);
            
            $msg = base64_encode("Import selesai. Berhasil: $success_count, Gagal: $fail_count");
            header("location: guru?msg=".base64_encode("200")."&info=$msg");
            
        } catch (Exception $e) {
             $msg = base64_encode("Database Connection Error: " . $e->getMessage());
             header("location: guru?msg=".base64_encode("300")."&info=$msg");
        }
    } else {
         $msg = base64_encode("File kosong");
         header("location: guru?msg=".base64_encode("300")."&info=$msg");
    }
} else {
    header("location: guru");
}
?>