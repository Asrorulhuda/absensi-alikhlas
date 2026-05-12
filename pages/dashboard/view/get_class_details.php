<?php
require_once "../../../include/db_config.php";
if (!isset($conn) && isset($link)) {
    $conn = $link;
}

date_default_timezone_set('Asia/Jakarta');

$kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'siswa'; // siswa or guru

if (empty($kelas) && $type != 'guru') {
    echo '<div class="text-center p-3">Kelas tidak valid.</div>';
    exit;
}

// Prepare query based on type
if ($type == 'guru') {
    // For Guru
    $query = "SELECT g.g_nama as nama, a.keterangan, a.status, a.jam_masuk, a.jam_keluar 
              FROM data_guru g 
              LEFT JOIN data_absen a ON g.g_uid = a.uid AND a.tanggal = CURDATE()
              ORDER BY CASE WHEN a.jam_masuk IS NOT NULL THEN 0 ELSE 1 END, a.jam_masuk DESC, g.g_nama ASC";
} else {
    // For Siswa
    $kelas = mysqli_real_escape_string($conn, $kelas);
    $query = "SELECT s.s_nama as nama, a.keterangan, a.status, a.jam_masuk, a.jam_keluar 
              FROM data_siswa s 
              LEFT JOIN data_absen a ON s.s_uid = a.uid AND a.tanggal = CURDATE()
              WHERE s.s_kelas = '$kelas' 
              ORDER BY CASE WHEN a.jam_masuk IS NOT NULL THEN 0 ELSE 1 END, a.jam_masuk DESC, s.s_nama ASC";
}

$result = mysqli_query($conn, $query);

if (!$result) {
    echo '<div class="text-center p-3 text-danger">Error: ' . mysqli_error($conn) . '</div>';
    exit;
}

if (mysqli_num_rows($result) > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-sm text-white">';
    echo '<thead><tr><th>Nama</th><th>Status</th><th>Masuk</th><th>Ket</th></tr></thead>';
    echo '<tbody>';
    
    while ($row = mysqli_fetch_assoc($result)) {
        $status_color = 'text-white-50'; // Default for Alpha
        $status_text = 'Alpha';
        $jam_masuk = '-';
        $jam_keluar = '';
        
        // Jika ada record absensi
        if (!empty($row['status']) || !empty($row['keterangan']) || !empty($row['jam_masuk'])) {
            
            $jam_masuk = ($row['jam_masuk'] && $row['jam_masuk'] != '00:00:00' ? substr($row['jam_masuk'], 0, 5) : '-');
            $jam_keluar = ($row['jam_keluar'] && $row['jam_keluar'] != '00:00:00' ? 'Plg: ' . substr($row['jam_keluar'], 0, 5) : '');
            
            // PRIORITAS 1: Cek apakah Izin / Sakit (Tidak ada jam masuk/keluar)
            if ($row['keterangan'] == 'SAKIT') {
                $status_color = 'text-danger';
                $status_text = 'Sakit';
            } elseif ($row['keterangan'] == 'IZIN') {
                $status_color = 'text-warning'; // Di Bootstrap text-warning warnanya kuning/orange
                $status_text = 'Izin';
            } 
            // PRIORITAS 2: Cek apakah SUDAH PULANG (Jam keluar sudah terisi atau status COMPLETE/OUT)
            elseif ($jam_keluar !== '' || $row['status'] == 'OUT' || $row['keterangan'] == 'COMPLETE') {
                $status_color = 'text-warning'; // Warna orange untuk Pulang, seperti di gambar
                $status_text = 'Pulang';
            } 
            // PRIORITAS 3: Jika belum pulang, berarti masih HADIR/IN
            elseif ($jam_masuk !== '-' || $row['status'] == 'IN' || $row['keterangan'] == 'HADIR') {
                $status_color = 'text-success'; // Warna hijau untuk Hadir
                $status_text = 'Hadir';
            } 
            // FALLBACK
            else {
                $status_color = 'text-white';
                $status_text = !empty($row['keterangan']) ? $row['keterangan'] : $row['status'];
            }
        }
        
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
        echo '<td class="'.$status_color.' font-weight-bold">' . $status_text . '</td>';
        echo '<td>' . $jam_masuk . '</td>';
        echo '<td>' . $jam_keluar . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
} else {
    // Jika data kosong
    echo '<div class="text-center p-3 text-white-50">Tidak ada data.</div>';
}
?>