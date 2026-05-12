<?php 
date_default_timezone_set('Asia/Jakarta');
session_start();

if ($_SESSION['akses'] != 'Admin') {
    header('location:../../index'); 
    exit();
}

$ses_name = $_SESSION['name'];
$_SESSION['pages'] = "Presensi";

require_once "../../include/db_config.php";
include "control/confignusers_data.php";

// Create semester_config table if not exists
$sql_create = "CREATE TABLE IF NOT EXISTS semester_config (
    id INT(11) NOT NULL AUTO_INCREMENT,
    academic_year VARCHAR(20) NOT NULL,
    semester VARCHAR(10) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active TINYINT(1) DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_sem (academic_year, semester)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($GLOBALS["___mysqli_ston"], $sql_create);

// Helper Functions
function indoMonth($m) {
    $m = str_pad($m, 2, '0', STR_PAD_LEFT);
    $months = [
        '01' => 'JAN', '02' => 'FEB', '03' => 'MAR',
        '04' => 'APR', '05' => 'MEI', '06' => 'JUN',
        '07' => 'JUL', '08' => 'AGU', '09' => 'SEP',
        '10' => 'OKT', '11' => 'NOV', '12' => 'DES'
    ];
    return $months[$m] ?? $m;
}

function countWorkingDaysInMonth($year, $month) {
    $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $workingDayCount = 0;
    
    for ($day = 1; $day <= $totalDays; $day++) {
        $date = new DateTime("$year-$month-$day");
        $dayOfWeek = $date->format('N');
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
            $workingDayCount++;
        }
    }
    return $workingDayCount;
}

function countNationalHolidaysInMonth($year, $month) {
    $url = "https://api-harilibur.vercel.app/api?year=$year&month=$month";
    $response = @file_get_contents($url);
    
    if ($response === false) return 0;
    
    $holidays = json_decode($response, true);
    $nationalHolidayCount = 0;
    
    foreach ($holidays as $holiday) {
        if (!empty($holiday['is_national_holiday'])) {
            $holidayDate = new DateTime($holiday['holiday_date']);
            $dayOfWeek = $holidayDate->format('N');
            if ($dayOfWeek != 6 && $dayOfWeek != 7) {
                $nationalHolidayCount++;
            }
        }
    }
    return $nationalHolidayCount;
}

// Initialize variables
$curY = intval(date('Y'));
$defaultYear = $curY . "/" . ($curY + 1);
$academic_year = $_POST['academic_year'] ?? $defaultYear;
$semester = $_POST['semester'] ?? 'GANJIL';
$kelas = $_POST['choices-kelas'] ?? '';
$jurusan = $_POST['choices-jurusan'] ?? '';
$msg_year = '';
$err_year = '';

// **TAMBAHKAN KODE INI** - Query untuk mengambil daftar tahun akademik dari database
$available_years = [];
$query_years = mysqli_query($GLOBALS["___mysqli_ston"], 
    "SELECT DISTINCT academic_year FROM semester_config ORDER BY academic_year DESC");

if ($query_years && mysqli_num_rows($query_years) > 0) {
    while ($row_year = mysqli_fetch_assoc($query_years)) {
        $available_years[] = $row_year['academic_year'];
    }
}

// Jika tidak ada data di database, tambahkan tahun default
if (count($available_years) == 0) {
    $available_years[] = $defaultYear;
    
    // Insert default year ke database
    $y1 = $curY;
    $y2 = $curY + 1;
    mysqli_query($GLOBALS["___mysqli_ston"], 
        "INSERT IGNORE INTO semester_config(academic_year, semester, start_date, end_date, is_active) 
         VALUES('$defaultYear','GANJIL','$y1-07-01','$y1-12-31', 0)");
    mysqli_query($GLOBALS["___mysqli_ston"], 
        "INSERT IGNORE INTO semester_config(academic_year, semester, start_date, end_date, is_active) 
         VALUES('$defaultYear','GENAP','$y2-01-01','$y2-06-30', 0)");
}

// Pastikan academic_year yang dipilih ada dalam daftar
if (!in_array($academic_year, $available_years)) {
    $academic_year = $available_years[0];
}


// Handle Save Semester Settings
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_save_semester'])) {
    $ay = mysqli_real_escape_string($GLOBALS['___mysqli_ston'], trim($_POST['academic_year']));
    $sem = mysqli_real_escape_string($GLOBALS['___mysqli_ston'], trim($_POST['semester']));
    $start_date_input = mysqli_real_escape_string($GLOBALS['___mysqli_ston'], trim($_POST['start_date']));
    $end_date_input = mysqli_real_escape_string($GLOBALS['___mysqli_ston'], trim($_POST['end_date']));
    
    $q = mysqli_query($GLOBALS["___mysqli_ston"], 
        "SELECT id FROM semester_config WHERE academic_year='$ay' AND semester='$sem' LIMIT 1");
    
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        $id = intval($row['id']);
        mysqli_query($GLOBALS["___mysqli_ston"], 
            "UPDATE semester_config SET start_date='$start_date_input', end_date='$end_date_input' WHERE id=$id");
        $msg_year = "Pengaturan semester berhasil diperbarui.";
    } else {
        mysqli_query($GLOBALS["___mysqli_ston"], 
            "INSERT INTO semester_config(academic_year, semester, start_date, end_date, is_active) 
             VALUES('$ay','$sem','$start_date_input','$end_date_input', 0)");
        $msg_year = "Pengaturan semester berhasil ditambahkan.";
    }
    
    $academic_year = $ay;
    $semester = $sem;
}
// Handle Add Academic Year
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_add_year'])) {
    $new_year = trim($_POST['new_academic_year']);
    
    if ($new_year) {
        $parts = explode('/', $new_year);
        
        if (count($parts) == 2) {
            $y1 = intval($parts[0]); 
            $y2 = intval($parts[1]);
            
            $exists = mysqli_query($GLOBALS["___mysqli_ston"], 
                "SELECT 1 FROM semester_config WHERE academic_year='$new_year' LIMIT 1");
            
            if (!$exists || mysqli_num_rows($exists) == 0) {
                $sql1 = "INSERT INTO semester_config(academic_year, semester, start_date, end_date, is_active) 
                         VALUES('$new_year','GANJIL','$y1-07-01','$y1-12-31', 0)";
                $sql2 = "INSERT INTO semester_config(academic_year, semester, start_date, end_date, is_active) 
                         VALUES('$new_year','GENAP','$y2-01-01','$y2-06-30', 0)";
                
                if (mysqli_query($GLOBALS["___mysqli_ston"], $sql1)) {
                    mysqli_query($GLOBALS["___mysqli_ston"], $sql2);
                    $msg_year = "Tahun akademik $new_year berhasil ditambahkan.";
                    
                    // **TAMBAHKAN: Reload page untuk refresh dropdown**
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = '" . htmlspecialchars($_SERVER["PHP_SELF"]) . "?academic_year=" . urlencode($new_year) . "&semester=" . urlencode($semester) . "';
                        }, 1500);
                    </script>";
                } else {
                    $err_year = "Gagal menambahkan tahun akademik.";
                }
            } else {
                $err_year = "Tahun akademik $new_year sudah ada.";
            }
        } else {
            $err_year = "Format tahun akademik harus seperti 2026/2027.";
        }
    } else {
        $err_year = "Isi tahun akademik terlebih dahulu.";
    }
}

// Resolve Semester Date Range
$start_date = '';
$end_date = '';
$semester_found = false;

$q = mysqli_query($GLOBALS["___mysqli_ston"], 
    "SELECT * FROM semester_config WHERE academic_year='$academic_year' AND semester='$semester' LIMIT 1");

if ($q && mysqli_num_rows($q) > 0) {
    $cfg = mysqli_fetch_assoc($q);
    $start_date = $cfg['start_date'];
    $end_date = $cfg['end_date'];
    $semester_found = true;
} else {
    $parts = explode('/', $academic_year);
    if (count($parts) == 2) {
        $y1 = intval($parts[0]); 
        $y2 = intval($parts[1]);
        
        if ($semester == 'GANJIL') {
            $start_date = "$y1-07-01";
            $end_date = "$y1-12-31";
        } else {
            $start_date = "$y2-01-01";
            $end_date = "$y2-06-30";
        }
    } else {
        $y1 = intval(date('Y'));
        $start_date = "$y1-01-01";
        $end_date = "$y1-12-31";
    }
}

// Calculate Months in Semester
$months_semester = [];
if (!empty($start_date) && !empty($end_date)) {
    try {
        $curM = new DateTime($start_date);
        $endM = new DateTime($end_date); 
        $endM->setTime(23, 59, 59);
        
        while ($curM <= $endM) {
            $months_semester[] = [
                'm' => $curM->format('m'),
                'y' => $curM->format('Y'),
                'start' => $curM->format('Y-m-01'),
                'end' => $curM->format('Y-m-t')
            ];
            $curM->modify('first day of next month');
        }
    } catch (Exception $e) {
        $err_year = "Error dalam menghitung bulan: " . $e->getMessage();
    }
}

// Build WHERE Filter
$where_siswa = "s_status='Aktif'";
if ($kelas !== '') {
    $where_siswa .= " AND s_kelas='" . mysqli_real_escape_string($GLOBALS['___mysqli_ston'], $kelas) . "'";
}
if ($jurusan !== '') {
    $where_siswa .= " AND s_jurusan='" . mysqli_real_escape_string($GLOBALS['___mysqli_ston'], $jurusan) . "'";
}

// Count Active Students/Teachers
$qStu = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) AS cnt FROM data_siswa WHERE $where_siswa");
$student_count = $qStu ? intval(mysqli_fetch_assoc($qStu)['cnt']) : 0;

$qGuruCount = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) AS cnt FROM data_guru");
$guru_count = $qGuruCount ? intval(mysqli_fetch_assoc($qGuruCount)['cnt']) : 0;

// Fetch People Data
$people = [];
if ($kelas === 'GURU') {
    $res_guru = mysqli_query($GLOBALS["___mysqli_ston"], 
        "SELECT g_uid AS uid, g_nama AS nama FROM data_guru ORDER BY g_nama");
    while ($res_guru && ($row = mysqli_fetch_assoc($res_guru))) {
        $people[] = ['uid' => $row['uid'], 'nama' => $row['nama']];
    }
} else {
    $res_siswa = mysqli_query($GLOBALS["___mysqli_ston"], 
        "SELECT s_uid AS uid, s_nama AS nama FROM data_siswa WHERE $where_siswa ORDER BY s_nama");
    while ($res_siswa && ($row = mysqli_fetch_assoc($res_siswa))) {
        $people[] = ['uid' => $row['uid'], 'nama' => $row['nama']];
    }
    
    if ($kelas === '') {
        $res_guru = mysqli_query($GLOBALS["___mysqli_ston"], 
            "SELECT g_uid AS uid, g_nama AS nama FROM data_guru ORDER BY g_nama");
        while ($res_guru && ($row = mysqli_fetch_assoc($res_guru))) {
            $people[] = ['uid' => $row['uid'], 'nama' => $row['nama']];
        }
    }
}

// Precompute Effective School Days
$effDaysMap = [];
$totalEffDays = 0;
foreach ($months_semester as $mm) {
    $wy = intval($mm['y']);
    $wm = intval($mm['m']);
    $workDays = countWorkingDaysInMonth($wy, $wm);
    $holidayDays = countNationalHolidaysInMonth($wy, $wm);
    $effDaysMap[$wy][$wm] = max(0, $workDays - $holidayDays);
    $totalEffDays += $effDaysMap[$wy][$wm];
}

// Aggregate Attendance Data
$recap_rows = [];
if (count($people) > 0 && count($months_semester) > 0) {
    $uids = array_map(function($p) { return $p['uid']; }, $people);
    $uids_in = "'" . implode("','", array_map(function($uid) {
        return mysqli_real_escape_string($GLOBALS['___mysqli_ston'], $uid);
    }, $uids)) . "'";
    
    $sqlAgg = "SELECT uid, YEAR(tanggal) AS y, MONTH(tanggal) AS m,
                  SUM(CASE WHEN (keterangan='HADIR' OR keterangan='COMPLETE') THEN 1 ELSE 0 END) AS masuk,
                  SUM(CASE WHEN (ket_masuk='Terlambat' OR ket_masuk='Sangat Terlambat') THEN 1 ELSE 0 END) AS terlambat,
                  SUM(CASE WHEN keterangan='IZIN' THEN 1 ELSE 0 END) AS izin,
                  SUM(CASE WHEN keterangan='SAKIT' THEN 1 ELSE 0 END) AS sakit
               FROM data_absen
               WHERE uid IN ($uids_in) AND tanggal BETWEEN '$start_date' AND '$end_date'
               GROUP BY uid, y, m";
    
    $aggRes = mysqli_query($GLOBALS["___mysqli_ston"], $sqlAgg);
    $map = [];
    
    if ($aggRes) {
        while ($row = mysqli_fetch_assoc($aggRes)) {
            $u = $row['uid'];
            $y = intval($row['y']);
            $m = intval($row['m']);
            
            if (!isset($map[$u])) $map[$u] = [];
            if (!isset($map[$u][$y])) $map[$u][$y] = [];
            
            $map[$u][$y][$m] = [
                'masuk' => intval($row['masuk']),
                'terlambat' => intval($row['terlambat']),
                'izin' => intval($row['izin']),
                'sakit' => intval($row['sakit'])
            ];
        }
    }
    
    foreach ($people as $p) {
        $rowMonths = [];
        $totM = $totT = $totI = $totS = $totA = 0;
        
        foreach ($months_semester as $mm) {
            $y = intval($mm['y']);
            $m = intval($mm['m']);
            $vals = $map[$p['uid']][$y][$m] ?? ['masuk' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0];
            $eff = $effDaysMap[$y][$m] ?? 0;
            $alpha = max(0, $eff - ($vals['masuk'] + $vals['izin'] + $vals['sakit']));
            
            $totM += $vals['masuk'];
            $totT += $vals['terlambat'];
            $totI += $vals['izin'];
            $totS += $vals['sakit'];
            $totA += $alpha;
            
            $rowMonths[] = [
                'm' => $vals['masuk'],
                't' => $vals['terlambat'],
                'i' => $vals['izin'],
                's' => $vals['sakit'],
                'a' => $alpha
            ];
        }
        
        // Calculate percentage
        $percentage = $totalEffDays > 0 ? round(($totM / $totalEffDays) * 100, 1) : 0;
        
        // Determine status
        $status = '';
        if ($percentage >= 95) {
            $status = 'Sangat Baik';
        } elseif ($percentage >= 85) {
            $status = 'Baik';
        } elseif ($percentage >= 75) {
            $status = 'Cukup';
        } else {
            $status = 'Kurang';
        }
        
        $recap_rows[] = [
            'nama' => $p['nama'],
            'months' => $rowMonths,
            'total' => ['m' => $totM, 't' => $totT, 'i' => $totI, 's' => $totS, 'a' => $totA],
            'percentage' => $percentage,
            'eff_days' => $totalEffDays,
            'status' => $status
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../../<?php echo $icon_bar; ?>">
    <title><?php echo $title_bar; ?></title>
    
    <!-- CSS -->
    <link href="../../assets/css/Roboto.css" rel="stylesheet" />
    <link href="../../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="../../assets/css/Material_icon.css" rel="stylesheet">
    <link href="../../assets/css/material-dashboard-pro.min.css?v=3.0.6" rel="stylesheet" />
    <link href="../../assets/vendor/DataTables_package/jquery.dataTables.min.css" rel="stylesheet" /> 
    <link href="../../assets/vendor/DataTables_package/buttons.dataTables.min.css" rel="stylesheet" />
    
    <script src="../../assets/js/kit.fontawesome.com_42d5adcbca.js" crossorigin="anonymous"></script>
    
    <style>
        :root {
            --color-masuk: #10b981;
            --color-masuk-light: #d1fae5;
            --color-terlambat: #f59e0b;
            --color-terlambat-light: #fef3c7;
            --color-izin: #3b82f6;
            --color-izin-light: #dbeafe;
            --color-sakit: #8b5cf6;
            --color-sakit-light: #ede9fe;
            --color-alpha: #ef4444;
            --color-alpha-light: #fee2e2;
        }

        .table-wrapper {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 0 12px;
        }

        .table-scroll-container {
            overflow-x: auto;
        }

        table.rekap-semester {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Roboto', sans-serif;
            font-size: 12px;
        }

        table.rekap-semester thead th {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
            color: #ffffff;
            font-weight: 600;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #cbd5e1;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        table.rekap-semester tbody td {
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        table.rekap-semester tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        table.rekap-semester tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* Sticky columns */
        table.rekap-semester th.sticky-no,
        table.rekap-semester td.sticky-no {
            position: sticky;
            left: 0;
            z-index: 11;
            background: #1e3a5f;
            color: white;
            font-weight: 600;
            width: 50px;
        }

        table.rekap-semester tbody td.sticky-no {
            background: #ffffff;
            color: #64748b;
        }

        table.rekap-semester tbody tr:nth-child(even) td.sticky-no {
            background: #f8fafc;
        }

        table.rekap-semester th.sticky-name,
        table.rekap-semester td.sticky-name {
            position: sticky;
            left: 50px;
            z-index: 11;
            background: #1e3a5f;
            color: white;
            min-width: 200px;
            text-align: left;
            padding-left: 16px;
        }

        table.rekap-semester tbody td.sticky-name {
            background: #ffffff;
            color: #1e293b;
            font-weight: 600;
            box-shadow: 4px 0 8px -2px rgba(0, 0, 0, 0.08);
        }

        table.rekap-semester tbody tr:nth-child(even) td.sticky-name {
            background: #f8fafc;
        }

        /* Month groups */
        .month-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Badges */
        .badge-compact {
            display: inline-block;
            min-width: 24px;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-m { background: var(--color-masuk-light); color: var(--color-masuk); }
        .badge-t { background: var(--color-terlambat-light); color: var(--color-terlambat); }
        .badge-i { background: var(--color-izin-light); color: var(--color-izin); }
        .badge-s { background: var(--color-sakit-light); color: var(--color-sakit); }
        .badge-a { background: var(--color-alpha-light); color: var(--color-alpha); }
        .badge-zero { background: #f1f5f9; color: #94a3b8; }

        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-sangat-baik { background: #d1fae5; color: #059669; }
        .status-baik { background: #dbeafe; color: #2563eb; }
        .status-cukup { background: #fef3c7; color: #d97706; }
        .status-kurang { background: #fee2e2; color: #dc2626; }

        /* Percentage */
        .percentage-cell {
            font-weight: 700;
            font-size: 13px;
        }

        /* Total column */
        .total-col {
            background: linear-gradient(180deg, #fafafa 0%, #f5f5f5 100%) !important;
            font-weight: 700;
        }

        /* Export buttons */
        .export-btn-group {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            padding: 16px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .export-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .export-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .export-btn.btn-pdf {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
        }

        .export-btn.btn-excel {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
        }

        .export-btn.btn-csv {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
        }

        @media (max-width: 768px) {
            table.rekap-semester {
                font-size: 10px;
            }
            
            table.rekap-semester th.sticky-name,
            table.rekap-semester td.sticky-name {
                min-width: 150px;
            }
        }

        /* Fix Dropdown Overlap */
        .choices__list--dropdown, 
        .choices__list[aria-expanded] {
            z-index: 100 !important;
        }
        
        .choices {
            overflow: visible !important;
        }
    </style>
</head>
<body class="g-sidenav-show bg-gray-200">
    <?php include 'view/part_sidenav.php'; ?>
    
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <?php include 'view/part_topnav.php'; ?>
        
        <div class="container-fluid py-4">
            <div class="row min-vh-80 mb-3">
                <div class="col-12">
                    <div class="card my-4 h-100" id="data_list">
                        <!-- Card Header -->
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <div class="d-flex justify-content-between align-items-center ps-3 pe-3">
                                    <div>
                                        <h6 class="text-white text-capitalize mb-0">📊 Rekap Presensi Per Semester</h6>
                                        <p class="text-white text-sm mb-0">Format Vertikal dengan Detail Persentase & Status</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="card-body px-0 pb-2">
                            <!-- Filter Form -->
                            <div class="p-4 bg-gray-100 border-radius-lg mb-4 mx-3">
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <div class="row">
                                        <!-- Tahun Akademik -->
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Tahun Akademik</label>
                                            <div class="input-group input-group-outline bg-white">
                                                <select class="form-control" name="academic_year" id="choices-academic-year">
                                                    <?php foreach ($available_years as $year): ?>
                                                        <option value="<?php echo htmlspecialchars($year); ?>" 
                                                                <?php echo ($academic_year == $year) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($year); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Semester -->
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Semester</label>
                                            <div class="input-group input-group-outline bg-white">
                                                <select class="form-control" name="semester" id="choices-semester">
                                                    <option value="GANJIL" <?php echo ($semester == 'GANJIL' ? 'selected' : ''); ?>>Ganjil</option>
                                                    <option value="GENAP" <?php echo ($semester == 'GENAP' ? 'selected' : ''); ?>>Genap</option>
                                                </select>
                                            </div>
                                        </div>
                                                                          
                                        <!-- Kelas -->
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Kelas</label>
                                            <div class="input-group input-group-outline bg-white">
                                                <select class="form-control" name="choices-kelas" id="choices-kelas">
                                                    <option value="">Semua Kelas</option>
                                                    <option value="GURU" <?php echo ($kelas == 'GURU' ? 'selected' : ''); ?>>GURU</option>
                                                    <?php
                                                    $sql_tingkat = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT tk_name FROM opsi_tk_kelas ORDER BY tk_name");
                                                    while ($data_tk = mysqli_fetch_assoc($sql_tingkat)) {
                                                        $k = $data_tk['tk_name'];
                                                        $selected = ($kelas == $k) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($k) . '" ' . $selected . '>' . htmlspecialchars($k) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Jurusan -->
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Jurusan</label>
                                            <div class="input-group input-group-outline bg-white">
                                                <select class="form-control" name="choices-jurusan" id="choices-jurusan">
                                                    <option value="">Semua Jurusan</option>
                                                    <?php
                                                    $sql_jurusan = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM opsi_jurusan");
                                                    while ($data_jurusan = mysqli_fetch_assoc($sql_jurusan)) {
                                                        $selected = ($jurusan == $data_jurusan['j_id']) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($data_jurusan['j_id']) . '" ' . $selected . '>' . htmlspecialchars($data_jurusan['j_short']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Action Buttons -->
                                        <div class="col-12 d-flex justify-content-end gap-2">
                                            <button type="submit" class="btn btn-primary btn-sm mb-0" name="btn_submit">
                                                <i class="material-icons text-sm">filter_alt</i> Tampilkan
                                            </button>
                                            <a href="javascript:;" class="btn btn-outline-secondary btn-sm mb-0" 
                                               data-bs-toggle="modal" data-bs-target="#modalSemester">
                                                <i class="material-icons text-sm">edit_calendar</i> Edit
                                            </a>
                                            <a href="javascript:;" class="btn btn-outline-success btn-sm mb-0" 
                                               data-bs-toggle="modal" data-bs-target="#modalAddYear">
                                                <i class="material-icons text-sm">add</i> Tambah
                                            </a>
                                        </div>
                                    </div>
                                </form>
                                
                                <!-- Messages -->
                                <?php if ($msg_year): ?>
                                    <div class="alert alert-success alert-dismissible fade show p-2 my-2" role="alert">
                                        <strong>✅ Sukses!</strong> <?php echo $msg_year; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($err_year): ?>
                                    <div class="alert alert-danger alert-dismissible fade show p-2 my-2" role="alert">
                                        <strong>❌ Error!</strong> <?php echo $err_year; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Info -->
                                <div class="mt-2 text-xs text-secondary">
                                    📅 <strong>Periode:</strong> <?php echo date('d M Y', strtotime($start_date)); ?> s/d <?php echo date('d M Y', strtotime($end_date)); ?>
                                    | 👥 <?php echo ($kelas === 'GURU' ? 'Guru: ' . $guru_count : 'Siswa: ' . $student_count); ?>
                                    | 📊 Bulan: <?php echo count($months_semester); ?>
                                    | 📆 Hari Efektif: <?php echo $totalEffDays; ?> hari
                                </div>
                            </div>
                            
                            <!-- Warning if no data -->
                            <?php if (count($months_semester) == 0): ?>
                                <div class="alert alert-warning mx-3">
                                    <strong>⚠️ Peringatan!</strong> Data bulan semester tidak ditemukan. 
                                    Silakan periksa pengaturan semester atau tambahkan data di menu <strong>Edit</strong> / <strong>Tambah</strong>.
                                </div>
                            <?php endif; ?>
                            
                            <!-- Table Section -->
                            <?php if (count($months_semester) > 0): ?>
                            <div class="row justify-content-center">
                                <div class="table-wrapper">
                                    <!-- Export Buttons -->
                                    <div class="export-btn-group">
                                        <button type="button" class="export-btn btn-csv" 
                                                onclick="$('#table_rekap_semester').DataTable().button(0).trigger()">
                                            <i class="material-icons" style="font-size:14px;">description</i> CSV
                                        </button>
                                        <button type="button" class="export-btn btn-excel" 
                                                onclick="$('#table_rekap_semester').DataTable().button(1).trigger()">
                                            <i class="material-icons" style="font-size:14px;">table_chart</i> Excel
                                        </button>
                                        <button type="button" class="export-btn btn-pdf" 
                                                onclick="$('#table_rekap_semester').DataTable().button(2).trigger()">
                                            <i class="material-icons" style="font-size:14px;">picture_as_pdf</i> PDF
                                        </button>
                                    </div>
                                    
                                    <!-- Table -->
                                    <div class="table-scroll-container">
                                        <table id="table_rekap_semester" class="rekap-semester" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th class="sticky-no" rowspan="2">No</th>
                                                    <th class="sticky-name" rowspan="2">Nama Siswa/Guru</th>
                                                    <?php foreach ($months_semester as $mm): ?>
                                                        <th class="month-header" colspan="5">
                                                            <?php echo indoMonth($mm['m']) . ' ' . $mm['y']; ?>
                                                        </th>
                                                    <?php endforeach; ?>
                                                    <th colspan="5" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">TOTAL SEMESTER</th>
                                                    <th rowspan="2" style="background: #8b5cf6; color: white;">%</th>
                                                    <th rowspan="2" style="background: #6366f1; color: white;">Hari Efektif</th>
                                                    <th rowspan="2" style="background: #ec4899; color: white;">Status</th>
                                                </tr>
                                                <tr>
                                                    <?php foreach ($months_semester as $mm): ?>
                                                        <th style="background: var(--color-masuk-light); color: var(--color-masuk); font-size: 10px;">M</th>
                                                        <th style="background: var(--color-terlambat-light); color: var(--color-terlambat); font-size: 10px;">T</th>
                                                        <th style="background: var(--color-izin-light); color: var(--color-izin); font-size: 10px;">I</th>
                                                        <th style="background: var(--color-sakit-light); color: var(--color-sakit); font-size: 10px;">S</th>
                                                        <th style="background: var(--color-alpha-light); color: var(--color-alpha); font-size: 10px;">A</th>
                                                    <?php endforeach; ?>
                                                    <th style="background: var(--color-masuk-light); color: var(--color-masuk); font-weight: 700; font-size: 10px;">M</th>
                                                    <th style="background: var(--color-terlambat-light); color: var(--color-terlambat); font-weight: 700; font-size: 10px;">T</th>
                                                    <th style="background: var(--color-izin-light); color: var(--color-izin); font-weight: 700; font-size: 10px;">I</th>
                                                    <th style="background: var(--color-sakit-light); color: var(--color-sakit); font-weight: 700; font-size: 10px;">S</th>
                                                    <th style="background: var(--color-alpha-light); color: var(--color-alpha); font-weight: 700; font-size: 10px;">A</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (count($recap_rows) > 0): ?>
                                                    <?php $no = 1; ?>
                                                    <?php foreach ($recap_rows as $r): ?>
                                                        <tr>
                                                            <td class="sticky-no"><?php echo $no++; ?></td>
                                                            <td class="sticky-name"><?php echo htmlspecialchars($r['nama']); ?></td>
                                                            
                                                            <!-- Data per Bulan -->
                                                            <?php foreach ($r['months'] as $mv): ?>
                                                                <td><span class="badge-compact badge-m <?php echo ($mv['m'] == 0 ? 'badge-zero' : ''); ?>"><?php echo $mv['m']; ?></span></td>
                                                                <td><span class="badge-compact badge-t <?php echo ($mv['t'] == 0 ? 'badge-zero' : ''); ?>"><?php echo $mv['t']; ?></span></td>
                                                                <td><span class="badge-compact badge-i <?php echo ($mv['i'] == 0 ? 'badge-zero' : ''); ?>"><?php echo $mv['i']; ?></span></td>
                                                                <td><span class="badge-compact badge-s <?php echo ($mv['s'] == 0 ? 'badge-zero' : ''); ?>"><?php echo $mv['s']; ?></span></td>
                                                                <td><span class="badge-compact badge-a <?php echo ($mv['a'] == 0 ? 'badge-zero' : ''); ?>"><?php echo $mv['a']; ?></span></td>
                                                            <?php endforeach; ?>
                                                            
                                                            <!-- Total Semester -->
                                                            <td class="total-col"><span class="badge-compact badge-m"><?php echo $r['total']['m']; ?></span></td>
                                                            <td class="total-col"><span class="badge-compact badge-t"><?php echo $r['total']['t']; ?></span></td>
                                                            <td class="total-col"><span class="badge-compact badge-i"><?php echo $r['total']['i']; ?></span></td>
                                                            <td class="total-col"><span class="badge-compact badge-s"><?php echo $r['total']['s']; ?></span></td>
                                                            <td class="total-col"><span class="badge-compact badge-a"><?php echo $r['total']['a']; ?></span></td>
                                                            
                                                            <!-- Persentase -->
                                                            <td class="percentage-cell" style="color: <?php 
                                                                echo $r['percentage'] >= 95 ? '#059669' : 
                                                                     ($r['percentage'] >= 85 ? '#2563eb' : 
                                                                     ($r['percentage'] >= 75 ? '#d97706' : '#dc2626')); 
                                                            ?>;">
                                                                <?php echo $r['percentage']; ?>%
                                                            </td>
                                                            
                                                            <!-- Hari Efektif -->
                                                            <td style="font-weight: 600;"><?php echo $r['eff_days']; ?></td>
                                                            
                                                            <!-- Status -->
                                                            <td>
                                                                <span class="status-badge status-<?php 
                                                                    echo strtolower(str_replace(' ', '-', $r['status'])); 
                                                                ?>">
                                                                    <?php echo $r['status']; ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="<?php echo (count($months_semester) * 5) + 10; ?>" class="text-center text-secondary py-5">
                                                            <i class="material-icons" style="font-size:48px;">inbox</i>
                                                            <p class="mb-0 mt-2">Tidak ada data untuk ditampilkan</p>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Legend -->
                                    <div style="display: flex; flex-wrap: wrap; gap: 16px; padding: 16px; background: #f8fafc; border-top: 1px solid #e2e8f0; justify-content: center; font-size: 11px;">
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span style="width: 12px; height: 12px; background: var(--color-masuk); border-radius: 3px;"></span>
                                            <span>M = Masuk</span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span style="width: 12px; height: 12px; background: var(--color-terlambat); border-radius: 3px;"></span>
                                            <span>T = Terlambat</span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span style="width: 12px; height: 12px; background: var(--color-izin); border-radius: 3px;"></span>
                                            <span>I = Izin</span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span style="width: 12px; height: 12px; background: var(--color-sakit); border-radius: 3px;"></span>
                                            <span>S = Sakit</span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span style="width: 12px; height: 12px; background: var(--color-alpha); border-radius: 3px;"></span>
                                            <span>A = Alpha</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
           <!-- Modal Edit Semester -->
<div class="modal fade" id="modalSemester" tabindex="-1" aria-labelledby="modalSemesterLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title font-weight-normal text-white">⚙️ Pengaturan Semester</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">×</button>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="modal-body">
                    <div class="container mt-2 mb-2">
                        <div class="row">
                            <div class="input-group input-group-static">
                                <label>Tahun Akademik</label>
                                <select class="form-control" name="academic_year" required>
                                    <?php foreach ($available_years as $year): ?>
                                        <option value="<?php echo htmlspecialchars($year); ?>" 
                                                <?php echo ($academic_year == $year) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($year); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="input-group input-group-static">
                                <label>Semester</label>
                                <select class="form-control" name="semester" required>
                                    <option value="GANJIL" <?php echo ($semester == 'GANJIL' ? 'selected' : ''); ?>>Ganjil</option>
                                    <option value="GENAP" <?php echo ($semester == 'GENAP' ? 'selected' : ''); ?>>Genap</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="input-group input-group-static">
                                <label>Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date" 
                                       value="<?php echo htmlspecialchars($start_date); ?>" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="input-group input-group-static">
                                <label>Tanggal Selesai</label>
                                <input type="date" class="form-control" name="end_date" 
                                       value="<?php echo htmlspecialchars($end_date); ?>" required>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3 p-2 text-xs mb-0">
                            <strong>💡 Info:</strong> Pastikan tanggal sudah benar untuk menghitung bulan semester dengan tepat.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn bg-gradient-success shadow-success" type="submit" name="btn_save_semester">
                        <i class="material-icons text-sm">save</i> Simpan
                    </button>
                    <button type="button" class="btn bg-gradient-secondary shadow-secondary" data-bs-dismiss="modal">
                        <i class="material-icons text-sm">close</i> Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>           
            <!-- Modal Tambah Tahun Akademik -->
            <div class="modal fade" id="modalAddYear" tabindex="-1" aria-labelledby="modalAddYearLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                        <div class="modal-header bg-success">
                            <h5 class="modal-title font-weight-normal text-white">➕ Tambah Tahun Akademik</h5>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">×</button>
                        </div>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="modal-body">
                                <div class="container mt-2 mb-2">
                                    <div class="row">
                                        <div class="input-group input-group-static">
                                            <label>Tahun Akademik Baru</label>
                                            <input type="text" class="form-control" name="new_academic_year" 
                                                   placeholder="2026/2027" required>
                                        </div>
                                    </div>
                                    <div class="alert alert-warning mt-3 p-2 text-xs mb-0">
                                        <strong>📌 Catatan:</strong> Sistem akan otomatis membuat:<br>
                                        • <strong>GANJIL</strong>: Juli - Desember<br>
                                        • <strong>GENAP</strong>: Januari - Juni
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn bg-gradient-success shadow-success" type="submit" name="btn_add_year">
                                    <i class="material-icons text-sm">add</i> Tambah
                                </button>
                                <button type="button" class="btn bg-gradient-secondary shadow-secondary" data-bs-dismiss="modal">
                                    <i class="material-icons text-sm">close</i> Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'view/part_theme_config.php'; ?>
    
    <!-- JavaScript -->
    <script src="../../assets/vendor/DataTables_package/jquery-3.5.1.js"></script>
    <script src="../../assets/vendor/DataTables_package/jquery.dataTables.min.js"></script>
    <script src="../../assets/vendor/DataTables_package/dataTables.buttons.min.js"></script>
    <script src="../../assets/vendor/DataTables_package/jszip.min.js"></script>
    <script src="../../assets/vendor/DataTables_package/pdfmake.min.js"></script>
    <script src="../../assets/vendor/DataTables_package/vfs_fonts.js"></script>
    <script src="../../assets/vendor/DataTables_package/buttons.html5.min.js"></script>
    <script src="../../assets/js/core/popper.min.js"></script>
    <script src="../../assets/js/core/bootstrap.min.js"></script>
    <script src="../../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../../assets/js/plugins/choices.min.js"></script>
    
    <script>
       $(document).ready(function() {
    // Initialize Choices.js untuk Tahun Akademik
    if (document.getElementById('choices-academic-year')) {
        new Choices(document.getElementById('choices-academic-year'), {
            searchEnabled: true,
            placeholder: true,
            placeholderValue: 'Pilih Tahun Akademik',
            itemSelectText: 'Tekan untuk memilih',
            shouldSort: false
        });
    }

    // Initialize Choices.js untuk Semester
    if (document.getElementById('choices-semester')) {
        new Choices(document.getElementById('choices-semester'), {
            searchEnabled: false,
            placeholder: true,
            placeholderValue: 'Pilih Semester',
            shouldSort: false,
            itemSelectText: 'Tekan untuk memilih'
        });
    }
    
    // Initialize Choices.js untuk Kelas
    if (document.getElementById('choices-kelas')) {
        new Choices(document.getElementById('choices-kelas'), {
            searchEnabled: true,
            placeholder: true,
            placeholderValue: 'Pilih Kelas'
        });
    }
    
    // Initialize Choices.js untuk Jurusan
    if (document.getElementById('choices-jurusan')) {
        new Choices(document.getElementById('choices-jurusan'), {
            searchEnabled: true,
            placeholder: true,
            placeholderValue: 'Pilih Jurusan'
        });
    }
                
            // Initialize DataTable
            var table = $('#table_rekap_semester').DataTable({
                ordering: false,
                dom: 'Blfrtip',
                searching: true,
                autoWidth: false,
                scrollX: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                language: {
                    search: "🔍 Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "⏮️",
                        last: "⏭️",
                        next: "▶️",
                        previous: "◀️"
                    },
                    emptyTable: "Tidak ada data untuk ditampilkan",
                    zeroRecords: "Tidak ada data yang cocok"
                },
                buttons: [
                    {
                        extend: 'csvHtml5',
                        title: 'Rekap_Presensi_Semester_<?php echo $academic_year . "_" . $semester; ?>',
                        filename: 'rekap_semester_<?php echo $academic_year . "_" . $semester . "_" . date("YmdHis"); ?>',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    return (data + '').replace(/<[^>]*>/g, '');
                                }
                            }
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'Rekap Presensi Semester <?php echo $academic_year . " - " . $semester; ?>',
                        filename: 'rekap_semester_<?php echo $academic_year . "_" . $semester . "_" . date("YmdHis"); ?>',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    return (data + '').replace(/<[^>]*>/g, '');
                                }
                            }
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Rekap Presensi Semester',
                        filename: 'rekap_semester_<?php echo $academic_year . "_" . $semester . "_" . date("YmdHis"); ?>',
                        orientation: 'landscape',
                        pageSize: 'A3',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    return (data + '').replace(/<[^>]*>/g, '');
                                }
                            }
                        },
                        customize: function(doc) {
                            // Header
                            doc.content.splice(0, 0, {
                                columns: [
                                    {
                                        width: 60,
                                        image: 'data:image/png;base64,<?php echo @base64_encode(@file_get_contents("../../" . ($logo_sekolah ?? ""))); ?>',
                                        fit: [50, 50]
                                    },
                                    {
                                        stack: [
                                            { text: '<?php echo addslashes($nama_sekolah ?? "SEKOLAH"); ?>', style: 'headerSchool', alignment: 'center' },
                                            { text: '<?php echo addslashes($alamat_sekolah ?? "-"); ?>', style: 'headerAddress', alignment: 'center' },
                                            { text: 'Telp: <?php echo addslashes($telp_sekolah ?? "-"); ?>', style: 'headerContact', alignment: 'center' }
                                        ],
                                        width: '*'
                                    }
                                ],
                                margin: [0, 0, 0, 10]
                            });
                            
                            doc.content.splice(1, 0, {
                                text: 'REKAP PRESENSI SEMESTER <?php echo strtoupper($semester); ?>',
                                style: 'reportTitle',
                                alignment: 'center',
                                margin: [0, 10, 0, 5]
                            });
                            
                            doc.content.splice(2, 0, {
                                text: 'Tahun Akademik: <?php echo $academic_year; ?> | Periode: <?php echo date("d M Y", strtotime($start_date)); ?> - <?php echo date("d M Y", strtotime($end_date)); ?>',
                                style: 'reportSubtitle',
                                alignment: 'center',
                                margin: [0, 0, 0, 10]
                            });
                            
                            // Styles
                            doc.styles.headerSchool = { fontSize: 14, bold: true, color: '#1e3a5f' };
                            doc.styles.headerAddress = { fontSize: 9, color: '#666666' };
                            doc.styles.headerContact = { fontSize: 8, color: '#888888' };
                            doc.styles.reportTitle = { fontSize: 12, bold: true, color: '#1e3a5f' };
                            doc.styles.reportSubtitle = { fontSize: 9, color: '#666666' };
                            
                                                       // Table styling
                            var objLayout = {};
                            objLayout['hLineWidth'] = function(i) { return 0.5; };
                            objLayout['vLineWidth'] = function(i) { return 0.5; };
                            objLayout['hLineColor'] = function(i) { return '#aaaaaa'; };
                            objLayout['vLineColor'] = function(i) { return '#aaaaaa'; };
                            objLayout['paddingLeft'] = function(i) { return 3; };
                            objLayout['paddingRight'] = function(i) { return 3; };
                            objLayout['paddingTop'] = function(i) { return 2; };
                            objLayout['paddingBottom'] = function(i) { return 2; };
                            doc.content[3].layout = objLayout;
                            
                            // Style table body
                            var tableNode = doc.content[3];
                            if (tableNode && tableNode.table && tableNode.table.body) {
                                var body = tableNode.table.body;
                                
                                // Header rows
                                for (var h = 0; h < Math.min(2, body.length); h++) {
                                    for (var c = 0; c < body[h].length; c++) {
                                        body[h][c].fillColor = '#1e3a5f';
                                        body[h][c].color = 'white';
                                        body[h][c].fontSize = 5;
                                        body[h][c].bold = true;
                                        body[h][c].alignment = 'center';
                                    }
                                }
                                
                                // Data rows
                                for (var i = 2; i < body.length; i++) {
                                    for (var j = 0; j < body[i].length; j++) {
                                        body[i][j].fontSize = 5;
                                        body[i][j].alignment = (j <= 1) ? 'left' : 'center';
                                        
                                        // Alternate row colors
                                        if (i % 2 === 0) {
                                            body[i][j].fillColor = '#f8fafc';
                                        }
                                        
                                        // Bold for no column
                                        if (j === 0) {
                                            body[i][j].bold = true;
                                        }
                                    }
                                }
                            }
                            
                            // Footer
                            doc['footer'] = function(currentPage, pageCount) {
                                return {
                                    columns: [
                                        {
                                            text: 'Dicetak: <?php echo date("d/m/Y H:i"); ?> WIB',
                                            alignment: 'left',
                                            fontSize: 7,
                                            margin: [40, 0]
                                        },
                                        {
                                            text: 'Halaman ' + currentPage.toString() + ' dari ' + pageCount,
                                            alignment: 'right',
                                            fontSize: 7,
                                            margin: [0, 0, 40, 0]
                                        }
                                    ],
                                    margin: [0, 10, 0, 0]
                                };
                            };
                            
                            // Page margins
                            doc.pageMargins = [15, 20, 15, 30];
                            
                            // Default style
                            doc.defaultStyle = {
                                fontSize: 6,
                                font: 'Roboto'
                            };
                        }
                    }
                ]
            });
        });
        
        // Perfect Scrollbar
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = { damping: '0.5' };
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
</body>
</html>
