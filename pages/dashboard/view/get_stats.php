<?php
require_once "../../../include/db_config.php";
if (!isset($conn) && isset($link)) {
    $conn = $link;
}

date_default_timezone_set('Asia/Jakarta');

$response = [
    'siswa' => [],
    'guru' => []
];

// --- 1. STATISTICS PER KELAS (SISWA) ---

// Get list of distinct classes
$kelas_list = [];
// Get classes from settings AND existing students to ensure coverage
$sql_kelas = "SELECT tk_name as s_kelas FROM opsi_tk_kelas 
              UNION 
              SELECT DISTINCT s_kelas FROM data_siswa WHERE s_kelas != '' 
              ORDER BY s_kelas";
$q_kelas = mysqli_query($conn, $sql_kelas);

if($q_kelas) {
    while($row = mysqli_fetch_assoc($q_kelas)) {
        if(!empty($row['s_kelas'])) {
            $kelas_list[] = $row['s_kelas'];
        }
    }
} else {
    // Fallback if query fails (e.g. table doesn't exist), though unlikely
    $q_kelas_backup = mysqli_query($conn, "SELECT DISTINCT s_kelas FROM data_siswa WHERE s_kelas != '' ORDER BY s_kelas");
    while($row = mysqli_fetch_assoc($q_kelas_backup)) {
        $kelas_list[] = $row['s_kelas'];
    }
}

foreach($kelas_list as $kelas) {
    // Total Students in this class
    $q_total = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM data_siswa WHERE s_kelas = '$kelas'");
    $total = mysqli_fetch_assoc($q_total)['cnt'];

    // Hadir (IN, OUT, COMPLETE, HADIR) - Exclude SAKIT and IZIN explicitly
    $q_hadir = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM data_absen a 
                                    JOIN data_siswa s ON a.uid = s.s_uid 
                                    WHERE s.s_kelas = '$kelas' 
                                    AND (a.keterangan='HADIR' OR a.keterangan='COMPLETE' OR a.status='IN' OR a.status='OUT') 
                                    AND (a.keterangan NOT IN ('SAKIT', 'IZIN'))
                                    AND a.tanggal=CURDATE()");
    $hadir = mysqli_fetch_assoc($q_hadir)['cnt'];

    // Pulang (OUT)
    $q_pulang = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM data_absen a 
                                     JOIN data_siswa s ON a.uid = s.s_uid 
                                     WHERE s.s_kelas = '$kelas' 
                                     AND a.status='OUT' 
                                     AND a.tanggal=CURDATE()");
    $pulang = mysqli_fetch_assoc($q_pulang)['cnt'];

    // Izin (IZIN)
    $q_izin = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM data_absen a 
                                   JOIN data_siswa s ON a.uid = s.s_uid 
                                   WHERE s.s_kelas = '$kelas' 
                                   AND a.keterangan='IZIN' 
                                   AND a.tanggal=CURDATE()");
    $izin = mysqli_fetch_assoc($q_izin)['cnt'];

    // Sakit (SAKIT)
    $q_sakit = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM data_absen a 
                                    JOIN data_siswa s ON a.uid = s.s_uid 
                                    WHERE s.s_kelas = '$kelas' 
                                    AND a.keterangan='SAKIT' 
                                    AND a.tanggal=CURDATE()");
    $sakit = mysqli_fetch_assoc($q_sakit)['cnt'];

    $response['siswa'][] = [
        'kelas' => $kelas,
        'total' => $total,
        'hadir' => $hadir,
        'pulang' => $pulang,
        'izin' => $izin,
        'sakit' => $sakit
    ];
}

// --- 2. STATISTICS GURU ---

// Total Guru
$q_total_guru = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM data_guru");
$total_guru = mysqli_fetch_assoc($q_total_guru)['cnt'];

// Hadir
$q_guru_hadir = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM data_absen a 
                                     JOIN data_guru g ON a.uid = g.g_uid 
                                     WHERE (a.keterangan='HADIR' OR a.keterangan='COMPLETE' OR a.status='IN' OR a.status='OUT') 
                                     AND (a.keterangan NOT IN ('SAKIT', 'IZIN'))
                                     AND a.tanggal=CURDATE()");
$guru_hadir = mysqli_fetch_assoc($q_guru_hadir)['cnt'];

// Pulang
$q_guru_pulang = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM data_absen a 
                                      JOIN data_guru g ON a.uid = g.g_uid 
                                      WHERE a.status='OUT' 
                                      AND a.tanggal=CURDATE()");
$guru_pulang = mysqli_fetch_assoc($q_guru_pulang)['cnt'];

// Izin
$q_guru_izin = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM data_absen a 
                                    JOIN data_guru g ON a.uid = g.g_uid 
                                    WHERE a.keterangan='IZIN' 
                                    AND a.tanggal=CURDATE()");
$guru_izin = mysqli_fetch_assoc($q_guru_izin)['cnt'];

// Sakit
$q_guru_sakit = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM data_absen a 
                                     JOIN data_guru g ON a.uid = g.g_uid 
                                     WHERE a.keterangan='SAKIT' 
                                     AND a.tanggal=CURDATE()");
$guru_sakit = mysqli_fetch_assoc($q_guru_sakit)['cnt'];

$response['guru'] = [
    'label' => 'Guru',
    'total' => $total_guru,
    'hadir' => $guru_hadir,
    'pulang' => $guru_pulang,
    'izin' => $guru_izin,
    'sakit' => $guru_sakit
];

header('Content-Type: application/json');
echo json_encode($response);
?>