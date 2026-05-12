<?php
// pages/dashboard/view/last_scan.php
date_default_timezone_set('Asia/Jakarta');

require_once "../../../include/db_config.php";

// Fix: konsistensi koneksi
if (!isset($conn) && isset($link)) {
    $conn = $link;
}

// Fix SQL: hapus markdown syntax, gunakan a.id biasa
$sql = "SELECT a.id, a.uid, a.status, a.keterangan,
        COALESCE(s.s_nama, g.g_nama) AS nama, 
        s.s_kelas AS kelas, 
        COALESCE(s.s_picture, g.g_picture) AS picture,
        CASE WHEN a.status = 'OUT' THEN a.jam_keluar ELSE a.jam_masuk END AS jam
        FROM data_absen a
        LEFT JOIN data_siswa s ON s.s_uid = a.uid
        LEFT JOIN data_guru g ON g.g_uid = a.uid
        WHERE a.tanggal = CURDATE()
        ORDER BY jam DESC
        LIMIT 5";

$res = mysqli_query($conn, $sql);

// Jika dipanggil dengan ?meta=1 → kembalikan JSON ringan untuk deteksi perubahan
if (isset($_GET['meta']) && $_GET['meta'] == 1) {
    if ($res && mysqli_num_rows($res) > 0) {
        $r    = mysqli_fetch_assoc($res);
        $who  = $r['nama'] ? $r['nama'] : $r['uid'];
        echo json_encode([
            'id'    => $r['id'],
            'who'   => $who,
            'waktu' => $r['jam']
        ]);
    } else {
        echo json_encode(['id' => null]);
    }
    exit;
}

// ── Output HTML untuk panel "Terakhir Tap" ──────────────────────────────────
if ($res && mysqli_num_rows($res) > 0) {

    $counter = 0;

    while ($r = mysqli_fetch_assoc($res)) {
        $counter++;

        $id          = $r['id'];
        $uid         = $r['uid'];
        $jam         = $r['jam'];
        $status      = $r['status'];
        $keterangan  = $r['keterangan'];

        // Tampilkan keterangan SAKIT / IZIN lebih prioritas
        $display_status = $status;
        if ($keterangan == 'SAKIT' || $keterangan == 'IZIN') {
            $display_status = $keterangan;
        }

        $nama    = $r['nama']    ? $r['nama']    : 'Tidak terdaftar';
        $kelas   = $r['kelas']   ? $r['kelas']   : '-';
        $picture = $r['picture'] ? $r['picture'] : '../../assets/img/avatar-default.png';

        // Warna badge berdasarkan status
        $color = '#2196f3'; // biru default
        switch (strtoupper($display_status)) {
            case 'IN':       $color = '#4caf50'; break; // hijau
            case 'IN2':      $color = '#2196f3'; break; // biru info
            case 'OUT':      $color = '#ff9800'; break; // oranye
            case 'INVALID':  $color = '#f44336'; break; // merah
            case 'LOCKED':   $color = '#f44336'; break; // merah
            case 'IZIN':     $color = '#ffb300'; break; // amber
            case 'SAKIT':    $color = '#e53935'; break; // merah tua
            case 'KEGIATAN': $color = '#9c27b0'; break; // ungu
        }

        // Animasi flash hanya untuk item terbaru (pertama)
        $animClass = ($counter == 1) ? 'animate__animated animate__flash' : '';

        // Label tipe: Siswa / Guru
        $type_label = $r['kelas'] ? 'Siswa · ' . htmlspecialchars($kelas) : 'Guru';
        ?>
        <div class="last-item <?php echo $animClass; ?>"
             style="padding:10px;
                    border-bottom:1px solid rgba(255,255,255,0.1);
                    margin-bottom:0;
                    border-radius:0;
                    background:transparent;">

            <div style="display:flex; align-items:center; justify-content:space-between; width:100%;">

                <!-- Nama + label kelas/guru -->
                <div>
                    <div style="font-weight:500; font-size:14px;">
                        <?php echo htmlspecialchars($nama); ?>
                    </div>
                    <div style="font-size:11px; color:rgba(255,255,255,0.5); margin-top:2px;">
                        <?php echo $type_label; ?>
                    </div>
                </div>

                <!-- Jam + badge status -->
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:12px; color:rgba(255,255,255,0.6);">
                        <?php echo $jam ? date("H:i", strtotime($jam)) : '--:--'; ?>
                    </span>
                    <span style="background:<?php echo $color; ?>;
                                 color:white;
                                 padding:2px 8px;
                                 border-radius:4px;
                                 font-size:11px;
                                 font-weight:bold;
                                 min-width:40px;
                                 text-align:center;">
                        <?php echo htmlspecialchars($display_status); ?>
                    </span>
                </div>

            </div>
        </div>
        <?php
    }

} else {
    ?>
    <div style="text-align:center; padding:20px; color:rgba(255,255,255,0.4);">
        <span class="material-icons" style="font-size:32px; opacity:0.3;">hourglass_empty</span>
        <div style="margin-top:8px; font-size:13px;">Belum ada tap hari ini.</div>
    </div>
    <?php
}
?>