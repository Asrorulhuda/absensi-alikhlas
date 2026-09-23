<?php
session_start();
if (($_SESSION['akses'] ?? '') !== 'Admin') {
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}
require_once "../../../include/db_config.php";
header("Content-Type: application/json; charset=UTF-8");

$sql = "SELECT * FROM tmp_datacard ORDER BY id DESC LIMIT 1";
$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);

if ($row = mysqli_fetch_assoc($result)) {
    $uid = strtoupper(trim((string) $row['uid']));
    $placeholderUids = ['UID_KARTU', 'KARTU_UID', 'YOUR_UID'];

    if ($uid === '' || in_array($uid, $placeholderUids, true)) {
        echo json_encode([
            'status' => 'invalid',
            'message' => 'Tempelkan kartu asli, bukan nilai contoh UID_KARTU.'
        ]);
        exit;
    }

    $stmt = mysqli_prepare(
        $GLOBALS["___mysqli_ston"],
        "SELECT 'siswa' AS jenis, s_nama AS nama FROM data_siswa WHERE s_uid = ?
         UNION ALL
         SELECT 'guru' AS jenis, g_nama AS nama FROM data_guru WHERE g_uid = ?
         LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, "ss", $uid, $uid);
    mysqli_stmt_execute($stmt);
    $registered = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($registered) {
        echo json_encode([
            'status' => 'registered',
            'uid' => $uid,
            'message' => 'Kartu sudah dipakai oleh ' . $registered['jenis'] . ' ' . $registered['nama'] . '.'
        ]);
        exit;
    }

    echo json_encode(['status' => 'success', 'uid' => $uid, 'time' => $row['jam']]);
} else {
    echo json_encode(['status' => 'empty']);
}
?>
