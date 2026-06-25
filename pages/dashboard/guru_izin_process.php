<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

if ($_SESSION['akses'] != 'Guru') {
    header('location:../../index');
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
        header('Location: guru_izin.php?msg=invalid');
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
        $new_filename = $uid . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $allowed_types = array('jpg', 'jpeg', 'png', 'pdf');
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES["bukti_foto"]["tmp_name"], $target_file)) {
                $bukti_foto = "assets/img/bukti_izin/" . $new_filename;
            } else {
                // Upload failed, but we can still process the text data
                // Or handle error
            }
        }
    }

    try {
        $sql = "INSERT INTO pengajuan_izin (uid, tipe_user, tanggal_mulai, tanggal_selesai, jenis_izin, keterangan, bukti_foto, status) 
                VALUES (?, 'Guru', ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$uid, $tanggal_mulai, $tanggal_selesai, $jenis_izin, $keterangan, $bukti_foto]);
        
        // --- NOTIFICATION SYSTEM ---
        // 1. Fetch Teacher Info
        $stmt_user = $pdo->prepare("SELECT id_guru FROM users WHERE id = ?");
        $stmt_user->execute([$uid]);
        $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

        $guru_nama = "Guru";
        $guru_jabatan = "Guru";
        $guru_uid = "";
        
        if ($user_data && !empty($user_data['id_guru'])) {
            $stmt_guru = $pdo->prepare("SELECT g_nama, g_jabatan, g_uid FROM data_guru WHERE g_id = ?");
            $stmt_guru->execute([$user_data['id_guru']]);
            $guru_data = $stmt_guru->fetch(PDO::FETCH_ASSOC);
            if ($guru_data) {
                $guru_nama = $guru_data['g_nama'];
                $guru_jabatan = $guru_data['g_jabatan'];
                $guru_uid = $guru_data['g_uid'];
            }
        }

        // 2. Fetch WA Config
        $stmt_wa = $pdo->prepare("SELECT * FROM wa_notification WHERE cnfg_id = 1 LIMIT 1");
        $stmt_wa->execute();
        $wa_conf = $stmt_wa->fetch(PDO::FETCH_ASSOC);

        if ($wa_conf && $wa_conf['cnfg_status'] == 1 && !empty($wa_conf['cnfg_no_kepsek']) && !empty($wa_conf['cnfg_template_guru_izin'])) {
            $target_number = $wa_conf['cnfg_no_kepsek'];
            $template = $wa_conf['cnfg_template_guru_izin'];
            
            // Replace tags
            $message = str_replace(
                ['{nama_guru}', '{jabatan}', '{jenis_izin}', '{tanggal_mulai}', '{tanggal_selesai}', '{alasan}'],
                [$guru_nama, $guru_jabatan, $jenis_izin, date('d-m-Y', strtotime($tanggal_mulai)), date('d-m-Y', strtotime($tanggal_selesai)), $keterangan],
                $template
            );

            // Append "Bukti Foto" if exists
            if (!empty($bukti_foto)) {
                 // Note: Sending media usually requires different API endpoint or parameters depending on gateway
                 // For now, we append text indicating proof is in system
                 $message .= "\n\n(Bukti foto terlampir di sistem)";
            }

            // Masukkan ke antrean WhatsApp (Queue)
            if (!empty($target_number)) {
                $meta = [
                    'siswa_uid'  => $guru_uid,
                    'siswa_nama' => $guru_nama,
                    'kelas'      => 'GURU',
                    'tipe'       => 'IZIN_GURU',
                    'target'     => 'KEPSEK',
                    'guru_nama'  => $guru_nama,
                ];
                enqueue_wa($target_number, $message, $meta);
            }
        }
        
        header('Location: guru_izin.php?msg=success');
    } catch (PDOException $e) {
        // Log error
        // echo $e->getMessage();
        header('Location: guru_izin.php?msg=error');
    }
} else {
    header('Location: guru_izin.php');
}
?>