<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
require_once "../../include/db_config.php";

echo "<h2>🔍 DIAGNOSTIC PRESENSI SYSTEM</h2>";
echo "<hr>";

// 1. Cek Timezone
echo "<h3>1️⃣ Timezone Check</h3>";
echo "PHP Timezone: <b>" . date_default_timezone_get() . "</b><br>";
echo "PHP Current Time: <b>" . date("Y-m-d H:i:s") . "</b><br>";

$sql_time = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT NOW() as mysql_time, CURDATE() as mysql_date");
$mysql_time = mysqli_fetch_assoc($sql_time);
echo "MySQL Current Time: <b>" . $mysql_time['mysql_time'] . "</b><br>";
echo "MySQL Current Date: <b>" . $mysql_time['mysql_date'] . "</b><br>";
echo "<hr>";

// 2. Cek Total Data Presensi Hari Ini
echo "<h3>2️⃣ Total Data Presensi Hari Ini</h3>";
$today = date("Y-m-d");
$sql_count = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) as total FROM data_absen WHERE DATE(tanggal) = '$today'");
$count_data = mysqli_fetch_assoc($sql_count);
echo "Total data presensi untuk tanggal <b>$today</b>: <b style='color:red; font-size:20px;'>{$count_data['total']}</b><br>";

if ($count_data['total'] == 0) {
    echo "<p style='color:red;'><b>⚠️ TIDAK ADA DATA!</b> Data tidak tersimpan di database.</p>";
} else {
    echo "<p style='color:green;'><b>✅ DATA ADA!</b> Masalah ada di query atau JOIN.</p>";
}
echo "<hr>";

// 3. Cek Data Mentah (Tanpa JOIN)
echo "<h3>3️⃣ Data Mentah dari table data_absen (Hari Ini)</h3>";
$sql_raw = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM data_absen WHERE DATE(tanggal) = '$today' LIMIT 5");
if (mysqli_num_rows($sql_raw) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    echo "<tr><th>ID</th><th>UID</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Keluar</th><th>Keterangan</th></tr>";
    while ($row = mysqli_fetch_assoc($sql_raw)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['uid']}</td>";
        echo "<td>{$row['tanggal']}</td>";
        echo "<td>{$row['jam_masuk']}</td>";
        echo "<td>{$row['jam_keluar']}</td>";
        echo "<td>{$row['keterangan']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'><b>❌ TIDAK ADA DATA!</b></p>";
}
echo "<hr>";

// 4. Cek UID Match dengan data_siswa
echo "<h3>4️⃣ Cek UID Match</h3>";
$sql_uid_check = mysqli_query($GLOBALS["___mysqli_ston"], "
    SELECT 
        da.uid,
        da.tanggal,
        ds.s_uid,
        ds.s_nama
    FROM data_absen da
    LEFT JOIN data_siswa ds ON da.uid = ds.s_uid
    WHERE DATE(da.tanggal) = '$today'
    LIMIT 5
");

if (mysqli_num_rows($sql_uid_check) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    echo "<tr><th>UID (absen)</th><th>Tanggal</th><th>UID (siswa)</th><th>Nama Siswa</th><th>Status</th></tr>";
    while ($row = mysqli_fetch_assoc($sql_uid_check)) {
        $status = $row['s_uid'] ? "<span style='color:green;'>✅ MATCH</span>" : "<span style='color:red;'>❌ TIDAK MATCH</span>";
        echo "<tr>";
        echo "<td>{$row['uid']}</td>";
        echo "<td>{$row['tanggal']}</td>";
        echo "<td>" . ($row['s_uid'] ?: 'NULL') . "</td>";
        echo "<td>" . ($row['s_nama'] ?: 'NULL') . "</td>";
        echo "<td>{$status}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'><b>❌ TIDAK ADA DATA!</b></p>";
}
echo "<hr>";

// 5. Test Query Lengkap (Seperti di presensi_harian.php)
echo "<h3>5️⃣ Test Query Lengkap dengan JOIN</h3>";
$sql_full = "SELECT 
    da.id, 
    DATE(da.tanggal) as tanggal, 
    da.jam_masuk, 
    da.jam_keluar, 
    COALESCE(ds.s_nama, 'Data Siswa Tidak Ditemukan') as s_nama,
    da.ket_masuk, 
    da.ket_keluar, 
    da.keterangan, 
    da.status, 
    COALESCE(ds.s_picture, '../../assets/img/avatar_profile.png') as s_picture,
    COALESCE(ds.s_kelas, '-') as s_kelas,
    COALESCE(oj.j_short, '-') as j_short
FROM data_absen da
LEFT JOIN data_siswa ds ON da.uid = ds.s_uid
LEFT JOIN opsi_jurusan oj ON ds.s_jurusan = oj.j_id
WHERE DATE(da.tanggal) = '$today'
ORDER BY da.jam_masuk DESC
LIMIT 5";

echo "<b>Query:</b><br><pre>" . htmlspecialchars($sql_full) . "</pre>";

$result_full = mysqli_query($GLOBALS["___mysqli_ston"], $sql_full);

if (!$result_full) {
    echo "<p style='color:red;'><b>❌ QUERY ERROR:</b> " . mysqli_error($GLOBALS["___mysqli_ston"]) . "</p>";
} else {
    $count = mysqli_num_rows($result_full);
    echo "<p><b>Jumlah hasil:</b> <span style='color:blue; font-size:18px;'>{$count}</span></p>";
    
    if ($count > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Tanggal</th><th>Nama</th><th>Kelas</th><th>Jurusan</th><th>Jam Masuk</th><th>Keterangan</th></tr>";
        while ($row = mysqli_fetch_assoc($result_full)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['tanggal']}</td>";
            echo "<td>{$row['s_nama']}</td>";
            echo "<td>{$row['s_kelas']}</td>";
            echo "<td>{$row['j_short']}</td>";
            echo "<td>{$row['jam_masuk']}</td>";
            echo "<td>{$row['keterangan']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p style='color:green;'><b>✅ QUERY BERHASIL!</b> Data seharusnya muncul di halaman utama.</p>";
    } else {
        echo "<p style='color:orange;'><b>⚠️ QUERY BERHASIL TAPI TIDAK ADA HASIL</b></p>";
    }
}
echo "<hr>";

// 6. Cek Sample Data dari create.php (5 terakhir)
echo "<h3>6️⃣ 5 Data Presensi Terakhir (Semua Tanggal)</h3>";
$sql_last = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM data_absen ORDER BY id DESC LIMIT 5");
if (mysqli_num_rows($sql_last) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    echo "<tr><th>ID</th><th>UID</th><th>Tanggal</th><th>Jam Masuk</th><th>Status</th></tr>";
    while ($row = mysqli_fetch_assoc($sql_last)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['uid']}</td>";
        echo "<td style='background:" . ($row['tanggal'] == $today ? 'lightgreen' : 'white') . ";'>{$row['tanggal']}</td>";
        echo "<td>{$row['jam_masuk']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'><b>❌ TIDAK ADA DATA SAMA SEKALI!</b></p>";
}
echo "<hr>";

echo "<h3>📝 KESIMPULAN & NEXT STEPS</h3>";
echo "<ol>";
echo "<li>Jika <b>Total data hari ini = 0</b> → Masalah ada di <code>create.php</code>, data tidak tersimpan</li>";
echo "<li>Jika <b>Total data hari ini > 0</b> tapi <b>Query lengkap = 0</b> → Masalah ada di JOIN atau kondisi WHERE</li>";
echo "<li>Jika <b>UID tidak match</b> → UID yang dikirim dari API berbeda dengan UID di table data_siswa</li>";
echo "<li>Jika <b>Timezone berbeda</b> → MySQL menyimpan tanggal dengan timezone yang berbeda</li>";
echo "</ol>";
?>
