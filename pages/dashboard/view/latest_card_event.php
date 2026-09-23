<?php
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require_once __DIR__ . '/../../../include/db_config.php';

$conn = $conn ?? $link ?? $GLOBALS['___mysqli_ston'] ?? null;
if (!$conn) {
    http_response_code(500);
    echo json_encode([
        'id' => null,
        'event_key' => null,
        'error' => 'Database connection unavailable',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "SELECT t.id, t.uid, t.jam, t.card_status,
               COALESCE(s.s_nama, g.g_nama, t.uid) AS nama,
               CASE
                   WHEN g.g_uid IS NOT NULL THEN 'guru'
                   WHEN s.s_uid IS NOT NULL THEN 'siswa'
                   ELSE 'unknown'
               END AS role
        FROM tmp_datacard t
        LEFT JOIN data_siswa s ON s.s_uid = t.uid
        LEFT JOIN data_guru g ON g.g_uid = t.uid
        ORDER BY t.id DESC
        LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result && ($row = mysqli_fetch_assoc($result))) {
    $status = strtoupper((string) $row['card_status']);
    $uid = (string) $row['uid'];
    $time = (string) $row['jam'];
    echo json_encode([
        'id' => (int) $row['id'],
        // UID, status, dan jam tetap membedakan event setelah tabel di-TRUNCATE.
        'event_key' => $row['id'] . ':' . $uid . ':' . $status . ':' . $time,
        'who' => (string) $row['nama'],
        'waktu' => $time,
        'status' => $status,
        'role' => (string) $row['role'],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['id' => null, 'event_key' => null], JSON_UNESCAPED_UNICODE);
