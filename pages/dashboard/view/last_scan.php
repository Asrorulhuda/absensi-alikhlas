<?php
date_default_timezone_set('Asia/Jakarta');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
require_once '../../../include/db_config.php';

$conn = $conn ?? $link ?? $GLOBALS['___mysqli_ston'] ?? null;
$type = strtolower((string) ($_GET['type'] ?? 'all'));
if (!in_array($type, ['all', 'siswa', 'guru'], true)) {
    $type = 'all';
}

$where = ['a.tanggal = CURDATE()'];
if ($type === 'siswa') {
    $where[] = 's.s_uid IS NOT NULL';
} elseif ($type === 'guru') {
    $where[] = 'g.g_uid IS NOT NULL';
}

$sql = "SELECT a.id, a.uid, a.status, a.keterangan,
               COALESCE(s.s_nama, g.g_nama, a.uid) AS nama,
               s.s_kelas AS kelas,
               CASE WHEN g.g_uid IS NOT NULL THEN 'guru' ELSE 'siswa' END AS role,
               CASE WHEN a.status = 'OUT' THEN a.jam_keluar ELSE a.jam_masuk END AS jam
        FROM data_absen a
        LEFT JOIN data_siswa s ON s.s_uid = a.uid
        LEFT JOIN data_guru g ON g.g_uid = a.uid
        WHERE " . implode(' AND ', $where) . "
        ORDER BY jam DESC, a.id DESC
        LIMIT 5";
$result = $conn ? mysqli_query($conn, $sql) : false;

if (isset($_GET['meta']) && $_GET['meta'] === '1') {
    header('Content-Type: application/json; charset=UTF-8');
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        $status = strtoupper((string) $row['status']);
        $time = (string) ($row['jam'] ?? '');
        echo json_encode([
            'id' => (int) $row['id'],
            // Status dan waktu membuat perubahan IN -> OUT pada baris yang sama
            // tetap terbaca sebagai event baru.
            'event_key' => $row['id'] . ':' . $status . ':' . $time,
            'who' => (string) $row['nama'],
            'waktu' => $time,
            'status' => $status,
            'keterangan' => (string) ($row['keterangan'] ?? ''),
            'role' => (string) $row['role'],
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['id' => null, 'event_key' => null]);
    }
    exit;
}

if (!$result || mysqli_num_rows($result) === 0) {
    ?>
    <div class="text-center py-8 text-slate-400 text-xs">
      <span class="material-icons-round text-xl opacity-30 block">hourglass_empty</span>
      <span class="block mt-1">Belum ada presensi <?= $type === 'guru' ? 'guru' : ($type === 'siswa' ? 'siswa' : '') ?> hari ini.</span>
    </div>
    <?php
    exit;
}

$counter = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $counter++;
    $status = strtoupper((string) $row['status']);
    $description = strtoupper((string) $row['keterangan']);
    $displayStatus = in_array($description, ['SAKIT', 'IZIN'], true) ? $description : $status;
    $colors = [
        'IN' => '#16a34a', 'IN2' => '#2563eb', 'OUT' => '#f97316',
        'INVALID' => '#dc2626', 'LOCKED' => '#dc2626', 'IZIN' => '#d97706',
        'SAKIT' => '#dc2626', 'KEGIATAN' => '#9333ea',
    ];
    $color = $colors[$displayStatus] ?? '#2563eb';
    $subLabel = $row['role'] === 'guru'
        ? 'Guru'
        : 'Siswa' . (!empty($row['kelas']) ? ' · ' . $row['kelas'] : '');
    ?>
    <div class="last-item <?= $counter === 1 ? 'animate__animated animate__flash' : '' ?>">
      <div class="flex items-center justify-between gap-3 w-full">
        <div class="min-w-0">
          <div class="font-semibold text-slate-800 text-xs truncate">
            <?= htmlspecialchars((string) $row['nama'], ENT_QUOTES, 'UTF-8') ?>
          </div>
          <div class="text-[10px] text-slate-500 mt-0.5">
            <?= htmlspecialchars($subLabel, ENT_QUOTES, 'UTF-8') ?>
          </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          <span class="text-[10px] text-slate-500">
            <?= !empty($row['jam']) ? date('H:i', strtotime($row['jam'])) : '--:--' ?>
          </span>
          <span class="text-white px-2 py-0.5 rounded text-[9px] font-bold"
                style="background: <?= $color ?>">
            <?= htmlspecialchars($displayStatus, ENT_QUOTES, 'UTF-8') ?>
          </span>
        </div>
      </div>
    </div>
    <?php
}
