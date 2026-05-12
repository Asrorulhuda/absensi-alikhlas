<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

date_default_timezone_set('Asia/Jakarta');
include_once '../../include/db_config.php';
include_once '../class/absensi.php';

// ============================================================
// Ambil konfigurasi WA Notification
// ============================================================
$cnfg_status         = 0;
$template_message    = "";
$cnfg_intro_kbm      = "";
$cnfg_intro_eskul    = "";
$cnfg_intro_kegiatan = "";
$cnfg_intro_guru     = ""; // BARU: template khusus guru (opsional)
$cnfg_token          = "";
$cnfg_sender         = "";
$tipe_presensi       = "";
$status_presensi     = "";
$ket_tambahan        = "";

$sql_cek_wa_service = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM wa_notification WHERE cnfg_id=1;");
while ($data_wa_service = mysqli_fetch_assoc($sql_cek_wa_service)) {
    $cnfg_status         = $data_wa_service['cnfg_status'];
    $template_message    = $data_wa_service['cnfg_intro'];
    $cnfg_intro_kbm      = $data_wa_service['cnfg_intro_kbm'];
    $cnfg_intro_eskul    = $data_wa_service['cnfg_intro_eskul'];
    $cnfg_intro_kegiatan = $data_wa_service['cnfg_intro_kegiatan'];
    $cnfg_token          = $data_wa_service['cnfg_token'];
    $cnfg_sender         = $data_wa_service['cnfg_sender'];

    // Ambil template guru jika kolom ada di tabel
    // Jika belum ada kolom ini, tidak error karena pakai isset
    $cnfg_intro_guru = isset($data_wa_service['cnfg_intro_guru'])
        ? $data_wa_service['cnfg_intro_guru']
        : "";
}

// ============================================================
// Proses Absensi
// ============================================================
$database = new Database();
$db       = $database->getConnection();

$item      = new Absensi($db);
$item->uid = isset($_GET['uid']) ? $_GET['uid'] : die('wrong structure!');

if ($item->createData()) {

    // Kirim response ke RFID reader
    $data_arr = array(
        "waktu"      => $item->waktu,
        "nama"       => $item->nama,
        "uid"        => $item->uid,
        "status"     => $item->status,
        "keterangan" => $item->ket_absen,
        "role"       => $item->is_guru ? "guru" : "siswa", // info tambahan
    );
    http_response_code(200);
    echo json_encode($data_arr);

    // ============================================================
    // Kirim Notifikasi WA (jika aktif)
    // ============================================================
    if ($cnfg_status == 1) {

        $waktu      = $item->waktu;
        $nama       = $item->nama;
        $status     = $item->status;
        $keterangan = $item->ket_absen;
        $ket_masuk  = $item->ket_masuk;
        $ket_keluar = $item->ket_keluar;
        $jam_masuk  = $item->jm_masuk;
        $jam_pulang = $item->jm_pulang;
        $is_guru    = $item->is_guru;

        // --------------------------------------------------------
        // Tentukan nomor tujuan WA
        // - Guru  : kirim ke nomor guru sendiri (wali_kontak = g_contact)
        // - Siswa : kirim ke nomor wali murid
        // --------------------------------------------------------
        $kontak_tujuan = $item->wali_kontak;
        $nama_tujuan   = $item->wali_nama;

        // --------------------------------------------------------
        // Susun tipe & status presensi
        // --------------------------------------------------------
        if ($status == "IN") {
            $tipe_presensi = $is_guru ? "ABSENSI MASUK GURU" : "ABSENSI MASUK";
            if (!empty($ket_masuk)) {
                $status_presensi = $ket_masuk;
                $ket_tambahan    = "Untuk diketahui bahwa jadwal Absensi Masuk adalah pukul *" . $jam_masuk . "*";
            } else {
                $status_presensi = "Tepat Waktu";
                $ket_tambahan    = "";
            }
        }

        if ($status == "OUT") {
            $tipe_presensi = $is_guru ? "ABSENSI PULANG GURU" : "ABSENSI PULANG";
            if (!empty($ket_keluar)) {
                $status_presensi = $ket_keluar;
                $ket_tambahan    = "Untuk diketahui bahwa jadwal Absensi Pulang adalah pukul *" . $jam_pulang . "*";
            } else {
                $status_presensi = "Tepat Waktu";
                $ket_tambahan    = "";
            }
        }

        if ($status == "KEGIATAN") {
            $tipe_presensi   = "ABSENSI KEGIATAN";
            $status_presensi = "HADIR";
            $ket_tambahan    = $keterangan;
        }

        // --------------------------------------------------------
        // Hanya kirim WA untuk status yang relevan
        // --------------------------------------------------------
        if (in_array($status, ["IN", "OUT", "KEGIATAN"])) {

            // --- Pilih Template ---
            if ($is_guru) {
                // Guru: gunakan template guru jika ada,
                // fallback ke template KBM, fallback ke template umum
                if (!empty($cnfg_intro_guru)) {
                    $template_message = $cnfg_intro_guru;
                } elseif (!empty($cnfg_intro_kbm)) {
                    $template_message = $cnfg_intro_kbm;
                }
                // template_message tetap pakai nilai awal (cnfg_intro) jika keduanya kosong
            } else {
                // Siswa: prioritaskan KBM sebagai default
                if (!empty($cnfg_intro_kbm)) {
                    $template_message = $cnfg_intro_kbm;
                }
                // Override jika tipe spesifik
                if ($item->attendance_type == "ESKUL" && !empty($cnfg_intro_eskul)) {
                    $template_message = $cnfg_intro_eskul;
                } elseif ($item->attendance_type == "KEGIATAN" && !empty($cnfg_intro_kegiatan)) {
                    $template_message = $cnfg_intro_kegiatan;
                }
            }

            // --- Render pesan dengan placeholder ---
            // Placeholder: {nama_siswa} dipakai juga untuk nama guru
            $wa_message = str_replace(
                ['{nama_siswa}', '{nama_guru}', '{tipe_presensi}', '{waktu}', '{status_presensi}', '{ket_tambahan}'],
                [$nama,          $nama,         $tipe_presensi,    $waktu,    $status_presensi,    $ket_tambahan],
                $template_message
            );

            // --- Anti-Banned Strategy ---
            // 1. Flush output agar RFID reader tidak menunggu delay
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            } else {
                ob_flush();
                flush();
            }

            // 2. Random delay 1-3 detik untuk menghindari spam burst
            sleep(rand(1, 3));

            // 3. Unique footer untuk menghindari duplikat pesan
            $ref_id      = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 5);
            $wa_message .= "\n\nRef: " . $ref_id . " | " . date('d-m-Y H:i:s');
            // ----------------------------

            // Jangan kirim jika nomor tujuan kosong
            if (!empty($kontak_tujuan)) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL            => 'https://gateway.asr-desain.my.id/send-message',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING       => '',
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_TIMEOUT        => 5,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => 'POST',
                    CURLOPT_POSTFIELDS     => array(
                        'api_key' => $cnfg_token,
                        'sender'  => $cnfg_sender,
                        'number'  => $kontak_tujuan,
                        'message' => $wa_message,
                    )
                ));
                $response = curl_exec($curl);
                curl_close($curl);
            }
        }
    }

} else {
    http_response_code(404);
    echo json_encode("Failed!");
}
?>