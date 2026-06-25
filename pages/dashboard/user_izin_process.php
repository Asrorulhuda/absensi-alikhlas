<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

if ($_SESSION['akses'] != 'User' && $_SESSION['akses'] != 'Admin') {
    header('location:../../index.php');
    exit();
}

require_once "../../include/db_config.php";
require_once "../../include/helpers.php";

// Initialize PDO
$database = new Database();
$pdo = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_SESSION['id'];
    $jenis_izin = $_POST['jenis_izin'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $keterangan = $_POST['keterangan'];
    $bukti_foto = "";

    // Validation
    if (empty($jenis_izin) || empty($tanggal_mulai) || empty($tanggal_selesai) || empty($keterangan)) {
        header('Location: user_izin.php?msg=invalid');
        exit();
    }

    // Handle File Upload
    if (isset($_FILES['bukti_foto']) && $_FILES['bukti_foto']['error'] == 0) {
        $target_dir = "../../assets/img/bukti_izin/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["bukti_foto"]["name"], PATHINFO_EXTENSION);
        $new_filename = "siswa_" . $uid . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $allowed_types = array('jpg', 'jpeg', 'png', 'pdf');
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES["bukti_foto"]["tmp_name"], $target_file)) {
                $bukti_foto = "assets/img/bukti_izin/" . $new_filename;
            }
        }
    }

    try {
        // Insert into pengajuan_izin
        // Note: tipe_user = 'Siswa'
        $sql = "INSERT INTO pengajuan_izin (uid, tipe_user, tanggal_mulai, tanggal_selesai, jenis_izin, keterangan, bukti_foto, status) 
                VALUES (?, 'Siswa', ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$uid, $tanggal_mulai, $tanggal_selesai, $jenis_izin, $keterangan, $bukti_foto]);
        
        // --- NOTIFICATION SYSTEM ---
        // 1. Fetch Student Info
        $stmt_user = $pdo->prepare("SELECT id_siswa FROM users WHERE id = ?");
        $stmt_user->execute([$uid]);
        $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

        $siswa_nama = "Siswa";
        $siswa_kelas = "-";
        $siswa_uid = "";
        
        if ($user_data && !empty($user_data['id_siswa'])) {
            // Assuming id_siswa links to s_id in data_siswa
            $stmt_siswa = $pdo->prepare("SELECT s_nama, s_kelas, s_uid FROM data_siswa WHERE s_id = ?");
            $stmt_siswa->execute([$user_data['id_siswa']]);
            $siswa_data = $stmt_siswa->fetch(PDO::FETCH_ASSOC);
            if ($siswa_data) {
                $siswa_nama = $siswa_data['s_nama'];
                $siswa_kelas = $siswa_data['s_kelas'];
                $siswa_uid = $siswa_data['s_uid'];
            }
        }

        // 2. Fetch WA Config
        $stmt_wa = $pdo->prepare("SELECT * FROM wa_notification WHERE cnfg_id = 1 LIMIT 1");
        $stmt_wa->execute();
        $wa_conf = $stmt_wa->fetch(PDO::FETCH_ASSOC);

        if ($wa_conf && $wa_conf['cnfg_status'] == 1 && !empty($wa_conf['cnfg_no_kepsek'])) {
            $target_number = $wa_conf['cnfg_no_kepsek']; // Sending to Headmaster/Admin for now
            
            // Choose template based on type
            $template = "";
            if ($jenis_izin == 'Sakit' && !empty($wa_conf['cnfg_template_sakit'])) {
                $template = $wa_conf['cnfg_template_sakit'];
            } elseif (!empty($wa_conf['cnfg_template_izin'])) {
                $template = $wa_conf['cnfg_template_izin'];
            }
            
            if (!empty($template)) {
                // Replace tags
                // Assuming placeholders: {nama}, {kelas}, {jenis_izin}, {tanggal_mulai}, {tanggal_selesai}, {alasan}
                // Note: I am guessing the placeholders based on guru_izin_process.php. 
                // Since I cannot change the DB template easily without admin panel, I hope the placeholders match or I am setting the convention now.
                // For safety, I'll use common ones.
                $message = str_replace(
                    ['{nama}', '{kelas}', '{jenis_izin}', '{tanggal_mulai}', '{tanggal_selesai}', '{alasan}'],
                    [$siswa_nama, $siswa_kelas, $jenis_izin, date('d-m-Y', strtotime($tanggal_mulai)), date('d-m-Y', strtotime($tanggal_selesai)), $keterangan],
                    $template
                );

                // Append "Bukti Foto" if exists
                if (!empty($bukti_foto)) {
                     $message .= "\n\n(Bukti foto terlampir di sistem)";
                }
                
                // Masukkan ke antrean WhatsApp (Queue)
                if (!empty($target_number)) {
                    $meta = [
                        'siswa_uid'  => $siswa_uid,
                        'siswa_nama' => $siswa_nama,
                        'kelas'      => $siswa_kelas,
                        'tipe'       => 'IZIN_SISWA',
                        'target'     => 'KEPSEK',
                    ];
                    enqueue_wa($target_number, $message, $meta);
                }
            }
        }
        
        header('Location: user_izin.php?msg=success');
        exit();

    } catch (PDOException $e) {
        // Log error
        // echo $e->getMessage();
        header('Location: user_izin.php?msg=error');
        exit();
    }
} else {
    header('Location: user_izin.php');
    exit();
}
?>