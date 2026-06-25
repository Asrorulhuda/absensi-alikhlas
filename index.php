<?php
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
require_once "include/db_config.php"; 

// Fetch System Config from Database
$sql = "SELECT * FROM system_config WHERE id = 1";
$system_conf = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
$row = mysqli_fetch_array($system_conf);

$nama_perusahaan = $row["company"] ?? "Nama Perusahaan";
$title_bar = $row["title_bar"] ?? "Sistem Absensi Digital";
$icon_bar = $row["icon_bar"] ?? "";
$landing_bg_color1 = $row["landing_bg_color1"] ?? '#4f46e5'; // Indigo-600
$landing_bg_color2 = $row["landing_bg_color2"] ?? '#7c3aed'; // Violet-600

// Check if already logged in
if (isset($_SESSION['id'])) {
    if ($_SESSION['akses'] == 'Guru') {
        header('Location: pages/dashboard/dashboard_guru.php');
    } else {
        header('Location: pages/dashboard/');
    }
    exit();
}

$today = date('Y-m-d');

// --- 1. Total Students ---
$q_total_siswa = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) as cnt FROM data_siswa");
$total_siswa = mysqli_fetch_assoc($q_total_siswa)['cnt'] ?? 0;

// --- 2. Active Students ---
$q_active_siswa = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) as cnt FROM data_siswa WHERE s_status = 'Aktif'");
$active_siswa = mysqli_fetch_assoc($q_active_siswa)['cnt'] ?? 0;

// --- 3. Total Teachers ---
$q_total_guru = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) as cnt FROM data_guru");
$total_guru = mysqli_fetch_assoc($q_total_guru)['cnt'] ?? 0;

// --- 4. Distinct Active Classes ---
$q_total_kelas = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(DISTINCT s_kelas) as cnt FROM data_siswa WHERE s_status = 'Aktif' AND s_kelas IS NOT NULL AND s_kelas != ''");
$total_kelas = mysqli_fetch_assoc($q_total_kelas)['cnt'] ?? 0;

// --- 5. Total WA Notifications ---
$q_total_wa = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) as cnt FROM wa_queue");
$total_wa = mysqli_fetch_assoc($q_total_wa)['cnt'] ?? 0;

// --- 6. Today's Combined Attendance Stats ---
// Hadir
$q_today_hadir = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(DISTINCT uid) as cnt FROM data_absen WHERE tanggal = '$today' AND (keterangan IN ('HADIR', 'COMPLETE') OR status IN ('IN', 'OUT', 'KEGIATAN')) AND keterangan NOT IN ('SAKIT', 'IZIN')");
$today_hadir = mysqli_fetch_assoc($q_today_hadir)['cnt'] ?? 0;

// Izin
$q_today_izin = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(DISTINCT uid) as cnt FROM data_absen WHERE tanggal = '$today' AND keterangan = 'IZIN'");
$today_izin = mysqli_fetch_assoc($q_today_izin)['cnt'] ?? 0;

// Sakit
$q_today_sakit = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(DISTINCT uid) as cnt FROM data_absen WHERE tanggal = '$today' AND keterangan = 'SAKIT'");
$today_sakit = mysqli_fetch_assoc($q_today_sakit)['cnt'] ?? 0;

// Alpha
$q_today_alpha = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) as cnt FROM data_siswa WHERE s_status = 'Aktif' AND s_uid NOT IN (SELECT uid FROM data_absen WHERE tanggal = '$today')");
$today_alpha = mysqli_fetch_assoc($q_today_alpha)['cnt'] ?? 0;

// --- 7. Fetch Real Teachers ---
$guru_list = [];
$q_guru = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT g_nama, g_jabatan, g_picture FROM data_guru ORDER BY g_id DESC LIMIT 4");
if ($q_guru && mysqli_num_rows($q_guru) > 0) {
    while ($g_row = mysqli_fetch_assoc($q_guru)) {
        $guru_list[] = $g_row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/system_data/<?php echo !empty($icon_bar) ? $icon_bar : 'favicon.ico'; ?>">
  
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="<?php echo $landing_bg_color1; ?>">
  
  <title><?php echo $title_bar; ?></title>
  
  <!-- Fonts & Material Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <link href="assets/css/animate.min.css" rel="stylesheet" />
  
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
          },
          colors: {
            brand: {
              primary: '#0B2545',
              secondary: '#134074',
              accent: '#3F88C5',
              lightAccent: '#EEF4F8',
              navy: '#011627'
            }
          }
        }
      }
    }
  </script>
  
  <style>
    body {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        overflow-x: hidden;
        color: #1e293b;
        background-color: #ffffff;
    }
    
    /* Animation for floating elements */
    @keyframes float {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(1deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes pulse-slow {
        0%, 100% { opacity: 0.6; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }
    .animate-pulse-slow {
        animation: pulse-slow 8s ease-in-out infinite;
    }

    /* Override last-item styles for the hero widget so they look readable on a white card */
    #hero-last-scans .last-item {
        border-bottom: 1px solid #f1f5f9 !important;
        color: #334155 !important;
        padding: 10px 0 !important;
        background: transparent !important;
    }
    #hero-last-scans .last-item div {
        color: #334155 !important;
    }
    #hero-last-scans .last-item div > div {
        font-weight: 600 !important;
        color: #1e293b !important;
        font-size: 13px !important;
    }
    #hero-last-scans .last-item div > div:last-child {
        color: #64748b !important;
        font-size: 10px !important;
    }
    #hero-last-scans .last-item span {
        color: #475569 !important;
    }
    #hero-last-scans .last-item span[style*="background"] {
        color: #ffffff !important;
        font-size: 10px !important;
        padding: 2px 6px !important;
        border-radius: 4px !important;
        font-weight: bold !important;
    }
  </style>
</head>

<body class="bg-white text-slate-800 min-h-screen relative antialiased">

  <!-- Header Navigation -->
  <header class="w-full bg-white border-b border-slate-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      
      <!-- Logo / Brand Name -->
      <a href="index.php" class="flex items-center gap-3 font-extrabold text-2xl text-brand-primary">
        <img src="assets/img/asr_edu.png" class="h-8 object-contain" alt="ASR.EDU Logo">
        <span class="tracking-tight text-xl"><?php echo $nama_perusahaan; ?></span>
      </a>

      <!-- Desktop Nav Links -->
      <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
        <a href="#beranda" class="text-blue-600 border-b-2 border-blue-600 pb-1">Beranda</a>
        <a href="#tentang" class="hover:text-blue-600 transition-colors">Tentang Kami</a>
        <a href="#fitur" class="hover:text-blue-600 transition-colors">Fitur</a>
        <a href="#laporan" class="hover:text-blue-600 transition-colors">Laporan</a>
        <a href="#izin" class="hover:text-blue-600 transition-colors">Izin</a>
        <a href="#kontak" class="hover:text-blue-600 transition-colors">Kontak</a>
      </nav>

      <!-- Action Buttons -->
      <div class="flex items-center gap-4">
        <a href="pages/dashboard/user_izin.php" class="hidden sm:inline-flex text-brand-secondary hover:text-brand-primary text-xs font-bold border border-slate-200 hover:bg-slate-50 px-5 py-2.5 rounded-xl transition-all">
          Pengajuan Izin
        </a>
        <a href="login.php" class="bg-[#0B2545] hover:bg-[#134074] text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md transition-all hover:scale-[1.02]">
          Login Sekarang
        </a>
      </div>

    </div>
  </header>

  <!-- Hero Section -->
  <section id="beranda" class="relative overflow-hidden bg-gradient-to-b from-blue-50/30 to-white">
    <div class="max-w-7xl mx-auto px-6 pt-16 pb-24 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
      
      <!-- Left Content -->
      <div class="flex flex-col items-center lg:items-start text-center lg:text-left animate__animated animate__fadeInLeft">
        
        <!-- Logo Image -->
        <img src="assets/img/asr_edu.png" class="max-w-[120px] mb-6 drop-shadow-md animate-float" alt="Logo ASR.EDU">

        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 mb-6 border border-blue-100/50">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
          Sistem Absensi Terpercaya
        </span>
        
        <h1 class="text-4xl sm:text-5xl lg:text-[54px] font-extrabold tracking-tight text-slate-900 leading-[1.15] mb-6">
          Absensi Digital <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Terintegrasi</span> untuk Sekolah Modern
        </h1>
        
        <p class="text-base sm:text-lg text-slate-500 font-light leading-relaxed mb-8 max-w-xl">
          Pantau kehadiran siswa secara realtime dengan kartu RFID. Notifikasi WhatsApp otomatis langsung ke orang tua — cepat, akurat, dan tanpa kertas.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
          <a href="login.php" class="bg-[#0B2545] hover:bg-[#134074] text-white font-bold text-sm px-8 py-4 rounded-xl shadow-lg shadow-blue-900/10 hover:shadow-blue-900/20 transition-all hover:scale-[1.02] text-center">
            Login Sekarang
          </a>
          <a href="pages/dashboard/user_izin.php" class="bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm px-8 py-4 rounded-xl border border-slate-200 shadow-sm transition-all hover:scale-[1.02] text-center">
            Pengajuan Izin
          </a>
        </div>

        <!-- PWA Install Button (Hidden by default, handled by JS) -->
        <button id="install-pwa" class="hidden mt-6 cursor-pointer bg-blue-50 border border-blue-200 text-blue-700 text-xs font-bold px-5 py-2.5 rounded-xl hover:bg-blue-100 transition-all flex items-center gap-2">
          <i class="material-icons-round text-base">download</i> Install Aplikasi PWA
        </button>

      </div>

      <!-- Right Mockup UI Illustration (Real-time Scan Monitor) -->
      <div class="relative w-full max-w-lg mx-auto flex items-center justify-center py-6 animate__animated animate__fadeInRight">
        
        <!-- Soft Blurry Circles -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 rounded-full bg-gradient-to-tr from-blue-200/50 to-indigo-200/50 blur-3xl opacity-60"></div>
        
        <!-- Main Live Card -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-2xl shadow-slate-200/60 p-6 w-full max-w-sm relative z-10 animate-float">
          
          <!-- Card Header -->
          <div class="border-b border-slate-100 pb-4 mb-4 text-center">
            <span class="font-extrabold text-slate-800 text-xs block mb-1">Live Monitor Presensi</span>
            <div class="flex items-center justify-center gap-2 mt-2">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
              <span id="live-clock" class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Loading clock...</span>
            </div>
          </div>

          <!-- Status stats boxes -->
          <div class="grid grid-cols-3 gap-2 text-center mb-6">
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 py-2 rounded-xl">
              <span id="hero-stats-hadir" class="block text-xs font-extrabold"><?php echo $today_hadir; ?></span>
              <span class="block text-[8px] font-bold uppercase tracking-wider opacity-75">Hadir</span>
            </div>
            <div class="bg-amber-50 border border-amber-100 text-amber-600 py-2 rounded-xl">
              <span id="hero-stats-izin" class="block text-xs font-extrabold"><?php echo ($today_izin + $today_sakit); ?></span>
              <span class="block text-[8px] font-bold uppercase tracking-wider opacity-75">Izin/Sakit</span>
            </div>
            <div class="bg-rose-50 border border-rose-100 text-rose-600 py-2 rounded-xl">
              <span id="hero-stats-alpha" class="block text-xs font-extrabold"><?php echo $today_alpha; ?></span>
              <span class="block text-[8px] font-bold uppercase tracking-wider opacity-75">Belum Absen</span>
            </div>
          </div>

          <!-- Student Rows (Dynamically Polled) -->
          <div id="hero-last-scans" class="space-y-3 min-h-[150px]">
            <div class="text-center py-8 text-slate-400 text-xs flex flex-col items-center justify-center gap-2">
              <i class="material-icons-round animate-spin">sync</i>
              <span>Menghubungkan monitor...</span>
            </div>
          </div>

          <!-- Bottom Tap Card box (Simulation Button) -->
          <div id="sim-tap-btn" class="mt-6 p-4 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-2xl flex items-center gap-3 cursor-pointer transition-all hover:scale-[1.01]">
            <div class="bg-brand-primary text-white p-2.5 rounded-xl shadow-md">
              <i class="material-icons-round text-sm block animate-pulse">contactless</i>
            </div>
            <div class="flex-1">
              <span class="text-[8px] text-slate-400 font-extrabold uppercase tracking-wider block">Simulasi Tap RFID</span>
              <span class="text-xs font-bold text-slate-700 block">Uji coba tap kartu di sini</span>
            </div>
            <span class="text-[9px] bg-blue-100 text-blue-800 font-bold px-2 py-0.5 rounded-md">TEST</span>
          </div>

        </div>

        <!-- Floating Badge Top Right -->
        <div class="absolute -top-2 -right-2 bg-white rounded-2xl shadow-xl px-4 py-3 border border-slate-100 flex items-center gap-2 z-20 animate-pulse-slow">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
          <span id="hero-absen-count" class="text-[11px] font-extrabold text-slate-800"><?php echo $today_hadir; ?> siswa</span>
          <span class="text-[11px] text-slate-400 font-medium">absen hari ini</span>
        </div>

        <!-- Floating Badge Bottom Left -->
        <div class="absolute -bottom-2 -left-2 bg-white rounded-2xl shadow-xl px-4 py-3 border border-slate-100 flex items-center gap-2 z-20">
          <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] font-bold">
            <i class="material-icons-round text-xs">done</i>
          </div>
          <div>
            <span class="text-[10px] font-extrabold text-slate-800 block">Notif WA Aktif</span>
            <span class="text-[9px] text-slate-400 block leading-none mt-0.5">Langsung ke orang tua</span>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- Statistics Counters Row -->
  <section class="border-t border-b border-slate-100 py-12 bg-white relative z-20">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8">
      
      <div class="text-center md:border-r border-slate-100 py-2">
        <span class="text-3xl sm:text-4xl font-extrabold text-[#0B2545]"><?php echo $total_siswa; ?>+</span>
        <span class="block text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mt-2">Total Siswa Terdaftar</span>
      </div>

      <div class="text-center md:border-r border-slate-100 py-2">
        <span class="text-3xl sm:text-4xl font-extrabold text-[#0B2545]"><?php echo $active_siswa; ?></span>
        <span class="block text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mt-2">Siswa Aktif</span>
      </div>

      <div class="text-center md:border-r border-slate-100 py-2">
        <span class="text-3xl sm:text-4xl font-extrabold text-[#0B2545]"><?php echo $total_kelas; ?></span>
        <span class="block text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mt-2">Kelas Aktif</span>
      </div>

      <div class="text-center py-2">
        <span class="text-3xl sm:text-4xl font-extrabold text-[#0B2545]"><?php echo $total_wa; ?>+</span>
        <span class="block text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mt-2">Notifikasi WA Terkirim</span>
      </div>

    </div>
  </section>

  <!-- Tentang Sistem Section -->
  <section id="tentang" class="bg-slate-50/50 py-20 relative">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
      
      <!-- Grid Cards Left -->
      <div class="grid grid-cols-2 gap-4 max-w-md mx-auto w-full">
        
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm text-center flex flex-col items-center justify-center">
          <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
            <i class="material-icons-round text-xl">sensors</i>
          </div>
          <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wider block">Monitoring</span>
          <span class="text-base font-bold text-slate-800 mt-1 block">Live</span>
        </div>

        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm text-center flex flex-col items-center justify-center">
          <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
            <i class="material-icons-round text-xl">group</i>
          </div>
          <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wider block">Siswa Aktif</span>
          <span class="text-base font-bold text-slate-800 mt-1 block"><?php echo $active_siswa; ?></span>
        </div>

        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm text-center flex flex-col items-center justify-center">
          <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
            <i class="material-icons-round text-xl">chat</i>
          </div>
          <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wider block">Notif Terkirim</span>
          <span class="text-base font-bold text-slate-800 mt-1 block"><?php echo $total_wa; ?></span>
        </div>

        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm text-center flex flex-col items-center justify-center">
          <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center mb-3">
            <i class="material-icons-round text-xl">auto_stories</i>
          </div>
          <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wider block">Laporan PDF</span>
          <span class="text-base font-bold text-slate-800 mt-1 block">Auto</span>
        </div>

      </div>

      <!-- Text Right -->
      <div class="flex flex-col items-center lg:items-start text-center lg:text-left">
        <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Tentang Sistem</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3 mb-6 leading-tight">
          AbsenDigital — Solusi Hadir yang Lebih Cerdas
        </h2>
        <p class="text-slate-500 font-light leading-relaxed mb-8 max-w-xl">
          Kami hadir dengan sistem absensi berbasis RFID yang terintegrasi penuh dengan notifikasi WhatsApp otomatis, dashboard guru, dan laporan bulanan — dirancang agar guru bisa fokus mengajar, bukan mencatat.
        </p>
        
        <!-- Mini badges -->
        <div class="grid grid-cols-2 gap-3 w-full max-w-md">
          <div class="bg-white border border-slate-100 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="material-icons-round text-emerald-500 text-sm">check_circle_outline</i>
            <span class="text-xs font-bold text-slate-700">Monitoring 24 Jam</span>
          </div>
          <div class="bg-white border border-slate-100 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="material-icons-round text-emerald-500 text-sm">check_circle_outline</i>
            <span class="text-xs font-bold text-slate-700">Akses dari HP</span>
          </div>
          <div class="bg-white border border-slate-100 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="material-icons-round text-emerald-500 text-sm">check_circle_outline</i>
            <span class="text-xs font-bold text-slate-700">Laporan Otomatis</span>
          </div>
          <div class="bg-white border border-slate-100 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="material-icons-round text-emerald-500 text-sm">check_circle_outline</i>
            <span class="text-xs font-bold text-slate-700">Data Aman</span>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- Modul Sistem Row -->
  <section id="laporan" class="max-w-7xl mx-auto px-6 py-20 bg-white">
    <div class="flex items-center justify-between mb-10">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Modul Sistem</h2>
      <a href="#fitur" class="text-blue-600 hover:text-blue-700 text-xs font-extrabold tracking-wider uppercase flex items-center gap-1">
        Lihat Semua <i class="material-icons-round text-sm">arrow_forward</i>
      </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
      
      <div class="bg-[#EEF4F8]/80 hover:bg-slate-50 border border-slate-100 transition-all p-5 rounded-2xl text-center flex flex-col items-center justify-center">
        <span class="font-extrabold text-slate-800 text-xs sm:text-sm mb-1 block">Absensi RFID</span>
        <span class="text-[9px] text-slate-400 font-medium block">Tap kartu pelajar</span>
      </div>

      <div class="bg-[#E8F8F2]/80 hover:bg-slate-50 border border-slate-100 transition-all p-5 rounded-2xl text-center flex flex-col items-center justify-center">
        <span class="font-extrabold text-slate-800 text-xs sm:text-sm mb-1 block">Notif WhatsApp</span>
        <span class="text-[9px] text-slate-400 font-medium block">Otomatis ke ortu</span>
      </div>

      <div class="bg-[#FEFCE8]/80 hover:bg-slate-50 border border-slate-100 transition-all p-5 rounded-2xl text-center flex flex-col items-center justify-center">
        <span class="font-extrabold text-slate-800 text-xs sm:text-sm mb-1 block">Dashboard Guru</span>
        <span class="text-[9px] text-slate-400 font-medium block">Kelola kelas</span>
      </div>

      <div class="bg-[#FDF2F8]/80 hover:bg-slate-50 border border-slate-100 transition-all p-5 rounded-2xl text-center flex flex-col items-center justify-center">
        <span class="font-extrabold text-slate-800 text-xs sm:text-sm mb-1 block">Pengajuan Izin</span>
        <span class="text-[9px] text-slate-400 font-medium block">Izin & sakit online</span>
      </div>

      <div class="bg-[#F5F3FF]/80 hover:bg-slate-50 border border-slate-100 transition-all p-5 rounded-2xl text-center flex flex-col items-center justify-center">
        <span class="font-extrabold text-slate-800 text-xs sm:text-sm mb-1 block">Laporan Bulanan</span>
        <span class="text-[9px] text-slate-400 font-medium block">Ekspor PDF/Excel</span>
      </div>

      <div class="bg-[#F0FDF4]/80 hover:bg-slate-50 border border-slate-100 transition-all p-5 rounded-2xl text-center flex flex-col items-center justify-center">
        <span class="font-extrabold text-slate-800 text-xs sm:text-sm mb-1 block">Akses Mobile</span>
        <span class="text-[9px] text-slate-400 font-medium block">Android & iOS</span>
      </div>

    </div>
  </section>

  <!-- Fitur Unggulan Section -->
  <section id="fitur" class="bg-slate-50/30 border-t border-slate-100 py-20">
    <div class="max-w-7xl mx-auto px-6">
      
      <div class="text-center max-w-xl mx-auto mb-16">
        <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Teknologi Modern</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3">Fitur Unggulan</h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Card 1 -->
        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-all flex items-start gap-4">
          <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
            <i class="material-icons-round">credit_card</i>
          </div>
          <div>
            <h5 class="font-bold text-slate-800 text-base mb-1.5">Kartu RFID Pelajar</h5>
            <p class="text-xs text-slate-400 font-light leading-relaxed">
              Absensi cepat hanya dengan menempelkan kartu pelajar ke reader. Data langsung tercatat dalam hitungan detik.
            </p>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-all flex items-start gap-4">
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
            <i class="material-icons-round">notifications_active</i>
          </div>
          <div>
            <h5 class="font-bold text-slate-800 text-base mb-1.5">Notifikasi WhatsApp Realtime</h5>
            <p class="text-xs text-slate-400 font-light leading-relaxed">
              Orang tua langsung mendapat pesan WhatsApp begitu siswa absen atau tidak hadir. Otomatis dan instan.
            </p>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-all flex items-start gap-4">
          <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
            <i class="material-icons-round">dashboard</i>
          </div>
          <div>
            <h5 class="font-bold text-slate-800 text-base mb-1.5">Dashboard Guru & Wali Kelas</h5>
            <p class="text-xs text-slate-400 font-light leading-relaxed">
              Rekap hadir, izin, sakit, dan alpha per kelas. Approve izin siswa langsung dari HP tanpa perlu ke sekolah.
            </p>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md transition-all flex items-start gap-4">
          <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
            <i class="material-icons-round">print</i>
          </div>
          <div>
            <h5 class="font-bold text-slate-800 text-base mb-1.5">Laporan & Rekap Otomatis</h5>
            <p class="text-xs text-slate-400 font-light leading-relaxed">
              Laporan harian, mingguan, dan bulanan tersedia otomatis dalam format PDF dan Excel, siap dicetak kapan saja.
            </p>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- Wali Kelas / Guru Section (Loads Real Teachers Dynamically) -->
  <section class="max-w-7xl mx-auto px-6 py-20 bg-white">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      
      <!-- Text Left -->
      <div class="flex flex-col items-center lg:items-start text-center lg:text-left self-center">
        <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Staff Pendidik</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 mb-4 leading-tight">Guru Berpengalaman Terkelola</h2>
        <p class="text-xs text-slate-400 font-light leading-relaxed mb-6">
          Setiap wali kelas punya akses mandiri untuk memantau dan mengelola absensi kelasnya masing-masing.
        </p>
        <a href="login.php" class="bg-[#0B2545] hover:bg-[#134074] text-white text-xs font-bold px-5 py-3 rounded-xl shadow-md transition-all hover:scale-[1.02] text-center inline-block">
          Lihat Semua Guru (<?php echo $total_guru; ?>)
        </a>
      </div>

      <!-- Right Columns of Cards -->
      <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">
        
        <?php
        $fallback_guru = [
            ['g_nama' => 'Bu Andika Sari', 'g_jabatan' => 'Wali Kelas X-A', 'g_picture' => ''],
            ['g_nama' => 'Pak Budi Santoso', 'g_jabatan' => 'Wali Kelas XI-B', 'g_picture' => ''],
            ['g_nama' => 'Bu Melani Putri', 'g_jabatan' => 'Wali Kelas XII-A', 'g_picture' => ''],
            ['g_nama' => 'Pak Ridwan Hakim', 'g_jabatan' => 'Wali Kelas X-B', 'g_picture' => '']
        ];
        $bg_colors = ['bg-[#EEF2F6]', 'bg-[#EAF7F2]', 'bg-[#FEFBE8]', 'bg-[#FDF2F8]'];
        
        for ($i = 0; $i < 4; $i++) {
            $g = $guru_list[$i] ?? $fallback_guru[$i];
            $pic = !empty($g['g_picture']) ? $g['g_picture'] : 'assets/img/logo-ct.png';
            $bg = $bg_colors[$i % 4];
            ?>
            <div class="<?php echo $bg; ?> border border-slate-100 p-5 rounded-2xl flex flex-col hover:shadow-md transition-all h-[200px]">
              <div class="flex items-center gap-3 mb-3">
                <img src="<?php echo htmlspecialchars($pic); ?>" class="w-10 h-10 rounded-full object-cover border border-white shadow-sm" onerror="this.src='assets/img/logo-ct.png'">
                <div class="overflow-hidden">
                  <span class="font-extrabold text-slate-800 text-xs sm:text-sm block truncate"><?php echo htmlspecialchars($g['g_nama']); ?></span>
                  <span class="text-[9px] text-slate-400 font-medium block truncate mt-0.5"><?php echo htmlspecialchars($g['g_jabatan']); ?></span>
                </div>
              </div>
              
              <div class="text-[10px] text-slate-500 font-light leading-relaxed mt-1">
                Mengelola absensi kelas dan koordinasi notifikasi orang tua secara berkala.
              </div>

              <div class="flex items-center gap-1 text-[10px] text-slate-600 font-bold mt-auto pt-2 border-t border-slate-100/50">
                <span>⭐</span>
                <span>4.<?php echo (9 - ($i % 3)); ?></span>
              </div>
            </div>
            <?php
        }
        ?>

      </div>

    </div>
  </section>

  <!-- Infrastruktur Section -->
  <section class="bg-slate-50/50 py-20 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-3 gap-12 items-center">
      
      <!-- Left side info -->
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Infrastruktur</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 mb-4 leading-tight">Perangkat Modern untuk Absensi Terbaik</h2>
        <p class="text-xs text-slate-400 font-light leading-relaxed">
          Didukung perangkat keras dan lunak mutakhir untuk pengalaman absensi yang mulus dan andal setiap hari.
        </p>
      </div>

      <!-- Right side grid -->
      <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-5 gap-4">
        
        <div class="bg-white border border-slate-100 p-4 rounded-2xl text-center h-[110px] flex items-center justify-center font-bold text-xs text-slate-700 hover:shadow-md transition-all shadow-sm">
          Reader RFID
        </div>

        <div class="bg-[#E8F8F2] border border-emerald-100/50 p-4 rounded-2xl text-center h-[110px] flex items-center justify-center font-bold text-xs text-emerald-800 hover:shadow-md transition-all shadow-sm">
          Server Cloud
        </div>

        <div class="bg-[#FEFCE8] border border-yellow-100/50 p-4 rounded-2xl text-center h-[110px] flex items-center justify-center font-bold text-xs text-yellow-800 hover:shadow-md transition-all shadow-sm">
          Dashboard Web
        </div>

        <div class="bg-[#FDF2F8] border border-pink-100/50 p-4 rounded-2xl text-center h-[110px] flex items-center justify-center font-bold text-xs text-pink-800 hover:shadow-md transition-all shadow-sm">
          Cetak Laporan
        </div>

        <div class="bg-[#F0FDF4] border border-green-100/50 p-4 rounded-2xl text-center h-[110px] flex items-center justify-center font-bold text-xs text-green-800 hover:shadow-md transition-all shadow-sm col-span-2 sm:col-span-1">
          Koneksi Realtime
        </div>

      </div>

    </div>
  </section>

  <!-- Berita & Pengumuman Section -->
  <section id="izin" class="max-w-7xl mx-auto px-6 py-20 bg-white">
    <div class="flex items-center justify-between mb-10">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Berita & Pengumuman</h2>
      <a href="#" class="text-blue-600 hover:text-blue-700 text-xs font-extrabold tracking-wider uppercase flex items-center gap-1">
        Lihat Semua <i class="material-icons-round text-sm">arrow_forward</i>
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <!-- Card 1 -->
      <div class="bg-[#F1F5F9] border border-slate-100 p-6 rounded-2xl flex flex-col justify-between h-[210px] hover:shadow-md transition-all">
        <div>
          <span class="bg-blue-100 text-blue-800 text-[9px] font-extrabold px-2 py-1 rounded inline-block uppercase">Update Sistem</span>
          <span class="font-extrabold text-slate-800 text-xs sm:text-sm mt-3 leading-snug block">Fitur rekap absensi mingguan kini tersedia.</span>
        </div>
        <span class="text-[10px] text-slate-400 font-medium mt-auto">25 Jun 2025</span>
      </div>

      <!-- Card 2 -->
      <div class="bg-[#EAF7F2] border border-slate-100 p-6 rounded-2xl flex flex-col justify-between h-[210px] hover:shadow-md transition-all">
        <div>
          <span class="bg-emerald-100 text-emerald-800 text-[9px] font-extrabold px-2 py-1 rounded inline-block uppercase">Notifikasi</span>
          <span class="font-extrabold text-slate-800 text-xs sm:text-sm mt-3 leading-snug block">Cara mengatur nomer WhatsApp orang tua di sistem.</span>
        </div>
        <span class="text-[10px] text-slate-400 font-medium mt-auto">18 Jun 2025</span>
      </div>

      <!-- Card 3 -->
      <div class="bg-[#FEFCE8] border border-slate-100 p-6 rounded-2xl flex flex-col justify-between h-[210px] hover:shadow-md transition-all">
        <div>
          <span class="bg-yellow-100 text-yellow-800 text-[9px] font-extrabold px-2 py-1 rounded inline-block uppercase">Panduan</span>
          <span class="font-extrabold text-slate-800 text-xs sm:text-sm mt-3 leading-snug block">Panduan pengajuan izin sakit secara online.</span>
        </div>
        <span class="text-[10px] text-slate-400 font-medium mt-auto">14 Jun 2025</span>
      </div>

      <!-- Card 4 -->
      <div class="bg-[#FDF2F8] border border-slate-100 p-6 rounded-2xl flex flex-col justify-between h-[210px] hover:shadow-md transition-all">
        <div>
          <span class="bg-pink-100 text-pink-800 text-[9px] font-extrabold px-2 py-1 rounded inline-block uppercase">Tips</span>
          <span class="font-extrabold text-slate-800 text-xs sm:text-sm mt-3 leading-snug block">Tips menjaga kartu RFID pelajar agar tidak rusak.</span>
        </div>
        <span class="text-[10px] text-slate-400 font-medium mt-auto">10 Jun 2025</span>
      </div>

    </div>
  </section>

  <!-- Technical Support Footer -->
  <footer id="kontak" class="bg-[#0B2545] text-white py-16">
    <div class="max-w-7xl mx-auto px-6">
      
      <!-- Top Block -->
      <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8 border-b border-slate-800 pb-12">
        
        <div>
          <h2 class="text-2xl sm:text-3xl font-extrabold max-w-lg mb-2 leading-tight">Butuh Bantuan Teknis?</h2>
          <p class="text-xs text-slate-400 max-w-md">
            Kami siap membantu 24 jam, 7 hari seminggu untuk sekolah Anda.
          </p>
          
          <div class="flex flex-wrap gap-2 mt-5">
            <span class="bg-slate-800/60 border border-slate-700/60 px-3 py-1 rounded-full text-[9px] text-slate-300 font-bold uppercase tracking-wider">RFID Aktif</span>
            <span class="bg-slate-800/60 border border-slate-700/60 px-3 py-1 rounded-full text-[9px] text-slate-300 font-bold uppercase tracking-wider">Tim Profesional</span>
            <span class="bg-slate-800/60 border border-slate-700/60 px-3 py-1 rounded-full text-[9px] text-slate-300 font-bold uppercase tracking-wider">Akses Mobile</span>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full sm:w-auto">
          <div class="bg-slate-800/40 border border-slate-700/40 rounded-2xl px-6 py-4 flex flex-col justify-center">
            <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider">Hubungi Kami</span>
            <a href="tel:02112345678" class="text-lg font-extrabold text-blue-400 hover:underline mt-0.5 tracking-tight block">
              (021) 1234 5678
            </a>
          </div>
          <a href="mailto:info@school.sch.id" class="bg-white hover:bg-slate-50 text-[#0B2545] font-extrabold text-xs px-6 py-4 rounded-xl shadow-lg transition-all hover:scale-[1.02] text-center self-center h-full flex items-center justify-center whitespace-nowrap">
            Jadwalkan Demo
          </a>
        </div>

      </div>

      <!-- Bottom Block -->
      <div class="flex flex-col sm:flex-row justify-between items-center pt-8 text-[11px] text-slate-500 gap-4">
        <span>&copy; <?php echo date('Y'); ?> <?php echo $nama_perusahaan; ?>. Hak Cipta Dilindungi.</span>
        <span class="font-semibold text-slate-400">Powered by ASR.EDU</span>
      </div>

    </div>
  </footer>

  <!-- Simulation Modal -->
  <div id="sim-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden animate__animated animate__zoomIn">
      <div class="bg-brand-primary text-white px-6 py-4 flex items-center justify-between">
        <h5 class="font-extrabold text-sm tracking-tight flex items-center gap-2">
          <i class="material-icons-round text-base">contactless</i> Simulasi Scanner RFID
        </h5>
        <button id="close-sim-btn" class="text-white/80 hover:text-white focus:outline-none">
          <i class="material-icons-round text-base">close</i>
        </button>
      </div>
      <div class="p-6">
        <form id="sim-form" class="space-y-4">
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pilih Anggota (Siswa / Guru)</label>
            <select name="sim_uid" id="sim_uid" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:outline-none focus:border-blue-500">
              <option value="">-- Pilih Anggota --</option>
              
              <!-- PHP dynamic options for students -->
              <optgroup label="Siswa Aktif">
                <?php
                $q_sim_siswa = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT s_uid, s_nama, s_kelas FROM data_siswa WHERE s_status = 'Aktif' AND s_uid != '' LIMIT 15");
                if ($q_sim_siswa) {
                    while ($s_row = mysqli_fetch_assoc($q_sim_siswa)) {
                        echo '<option value="' . htmlspecialchars($s_row['s_uid']) . '">' . htmlspecialchars($s_row['s_nama']) . ' (' . htmlspecialchars($s_row['s_kelas']) . ')</option>';
                    }
                }
                ?>
              </optgroup>
              
              <!-- PHP dynamic options for teachers -->
              <optgroup label="Guru / Staff">
                <?php
                $q_sim_guru = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT g_uid, g_nama, g_jabatan FROM data_guru WHERE g_uid != '' LIMIT 10");
                if ($q_sim_guru) {
                    while ($g_row = mysqli_fetch_assoc($q_sim_guru)) {
                        echo '<option value="' . htmlspecialchars($g_row['g_uid']) . '">' . htmlspecialchars($g_row['g_nama']) . ' (' . htmlspecialchars($g_row['g_jabatan']) . ')</option>';
                    }
                }
                ?>
              </optgroup>
            </select>
          </div>
          
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pilih Lokasi Reader (Gate / Room)</label>
            <select name="sim_dev_eui" id="sim_dev_eui" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-700 focus:outline-none focus:border-blue-500">
              <?php
              $q_sim_readers = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT r_name, d_type, d_eui FROM reader_devices, room WHERE d_location=r_id");
              $has_readers = false;
              if ($q_sim_readers && mysqli_num_rows($q_sim_readers) > 0) {
                  $has_readers = true;
                  while ($r_row = mysqli_fetch_assoc($q_sim_readers)) {
                      echo '<option value="' . htmlspecialchars($r_row['d_eui']) . '">' . htmlspecialchars($r_row['r_name']) . ' - ' . htmlspecialchars($r_row['d_type']) . '</option>';
                  }
              }
              if (!$has_readers) {
                  echo '<option value="GATE_01">Gate Utama (GATE_01)</option>';
              }
              ?>
            </select>
          </div>
          
          <button type="button" id="sim-submit-btn" class="w-full bg-[#0B2545] hover:bg-[#134074] text-white font-extrabold text-xs py-3.5 rounded-xl shadow-lg transition-all hover:scale-[1.02] flex items-center justify-center gap-2 mt-6">
            <i class="material-icons-round text-base">fingerprint</i> Simulasikan Tap Kartu
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.6.3.min.js" crossorigin="anonymous"></script>

  <script>
    let lastScanId = null;

    // --- 1. Real-time Clock ---
    function updateClock() {
      const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
      const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      
      let now = new Date();
      let dayName = days[now.getDay()];
      let date = now.getDate();
      let monthName = months[now.getMonth()];
      let year = now.getFullYear();
      let hours = String(now.getHours()).padStart(2, '0');
      let minutes = String(now.getMinutes()).padStart(2, '0');
      let seconds = String(now.getSeconds()).padStart(2, '0');
      
      $('#live-clock').text(`${dayName}, ${date} ${monthName} ${year} ${hours}:${minutes}:${seconds}`);
    }
    
    // --- 2. Live attendance scan list ---
    function loadHeroScans() {
      $('#hero-last-scans').load('pages/dashboard/view/last_scan.php', function(response, status) {
        if (status !== 'success') {
          $('#hero-last-scans').html(`
            <div class="text-center py-8 text-slate-400 text-xs">
              <span class="material-icons-round text-lg opacity-30 block">hourglass_empty</span>
              <span class="block mt-1">Belum ada tap hari ini.</span>
            </div>
          `);
        }
      });
    }

    // --- 3. Dynamic Stats Polling ---
    function updateDynamicStats() {
      $.getJSON('pages/dashboard/view/get_stats.php', function(data) {
        if (data) {
          let totalHadir = 0;
          let totalIzin = 0;
          let totalSakit = 0;
          
          if(data.siswa) {
            data.siswa.forEach(function(item) {
              totalHadir += parseInt(item.hadir || 0);
              totalIzin += parseInt(item.izin || 0);
              totalSakit += parseInt(item.sakit || 0);
            });
          }
          
          // Fallbacks to default database values if no today scans yet
          let displayHadir = totalHadir > 0 ? totalHadir : <?php echo $today_hadir; ?>;
          let displayIzin = (totalIzin + totalSakit) > 0 ? (totalIzin + totalSakit) : <?php echo ($today_izin + $today_sakit); ?>;
          
          $('#hero-absen-count').text(displayHadir + ' siswa');
          $('#hero-stats-hadir').text(displayHadir);
          $('#hero-stats-izin').text(displayIzin);
        }
      });
    }

    // --- 4. Detect New Tap for Beep and TTS ---
    function checkNewTap() {
      $.getJSON('pages/dashboard/view/last_scan.php?meta=1', function(data) {
        if (data && data.id) {
          if (lastScanId !== null && data.id !== lastScanId) {
            // New tap registered!
            playNotificationSound();
            speakAttendance(data.who);
            // Instantly refresh list and counters
            loadHeroScans();
            updateDynamicStats();
          }
          lastScanId = data.id;
        }
      });
    }

    function playNotificationSound() {
      try {
        let context = new (window.AudioContext || window.webkitAudioContext)();
        let oscillator = context.createOscillator();
        let gain = context.createGain();
        oscillator.connect(gain);
        gain.connect(context.destination);
        oscillator.type = 'sine';
        oscillator.frequency.value = 880; // High pitch A note
        gain.gain.setValueAtTime(0.1, context.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, context.currentTime + 0.35);
        oscillator.start(context.currentTime);
        oscillator.stop(context.currentTime + 0.35);
      } catch (e) {
        console.error("Audio Context error:", e);
      }
    }

    function speakAttendance(name) {
      if ('speechSynthesis' in window) {
        // Stop current speech to avoid stacking
        window.speechSynthesis.cancel();
        let utterance = new SpeechSynthesisUtterance(name);
        utterance.lang = 'id-ID';
        utterance.rate = 1.0;
        window.speechSynthesis.speak(utterance);
      }
    }

    // --- 5. Simulation Modal Controls ---
    $(document).ready(function() {
      updateClock();
      setInterval(updateClock, 1000);
      
      loadHeroScans();
      setInterval(loadHeroScans, 2000);
      
      updateDynamicStats();
      setInterval(updateDynamicStats, 4000);
      
      // Init last scan ID for new tap listener
      $.getJSON('pages/dashboard/view/last_scan.php?meta=1', function(data) {
        if (data && data.id) {
          lastScanId = data.id;
        }
      });
      setInterval(checkNewTap, 1500);

      // Modal open/close
      $('#sim-tap-btn').click(function() {
        $('#sim-modal').removeClass('hidden').addClass('flex');
      });
      
      $('#close-sim-btn').click(function() {
        $('#sim-modal').addClass('hidden').removeClass('flex');
      });
      
      // Close modal on click outside content
      $('#sim-modal').click(function(e) {
        if (e.target === this) {
          $(this).addClass('hidden').removeClass('flex');
        }
      });

      // Handle Simulated Tap Submission
      $('#sim-submit-btn').click(function() {
        let uid = $('#sim_uid').val();
        let dev_eui = $('#sim_dev_eui').val();
        
        if (!uid) {
          alert('Pilih siswa atau guru terlebih dahulu.');
          return;
        }
        
        $('#sim-submit-btn').prop('disabled', true).html('<i class="material-icons-round text-base animate-spin">sync</i> Memproses...');
        
        $.ajax({
          type: "GET",
          url: 'webapi/api/create.php?uid=' + encodeURIComponent(uid) + '&dev_eui=' + encodeURIComponent(dev_eui),
          success: function(response) {
            $('#sim-modal').addClass('hidden').removeClass('flex');
            $('#sim-submit-btn').prop('disabled', false).html('<i class="material-icons-round text-base">fingerprint</i> Simulasikan Tap Kartu');
            
            // Instantly refresh
            loadHeroScans();
            updateDynamicStats();
          },
          error: function() {
            alert('Simulasi tap kartu gagal. Hubungi admin.');
            $('#sim-submit-btn').prop('disabled', false).html('<i class="material-icons-round text-base">fingerprint</i> Simulasikan Tap Kartu');
          }
        });
      });
    });

    // PWA Install Logic
    let deferredPrompt;
    const installBtn = document.getElementById('install-pwa');
    
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt = e;
      installBtn.classList.remove('hidden');
      
      installBtn.addEventListener('click', (e) => {
        installBtn.classList.add('hidden');
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((choiceResult) => {
          if (choiceResult.outcome === 'accepted') {
            console.log('User accepted the A2HS prompt');
          }
          deferredPrompt = null;
        });
      });
    });

    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('service-worker.js?v=2').then(function(registration) {
             console.log('ServiceWorker registration successful with scope: ', registration.scope);
             
             registration.onupdatefound = function() {
                const installingWorker = registration.installing;
                installingWorker.onstatechange = function() {
                    if (installingWorker.state === 'installed') {
                        if (navigator.serviceWorker.controller) {
                            console.log('New content is available; please refresh.');
                            if(confirm("Versi baru aplikasi tersedia. Muat ulang sekarang?")) {
                                window.location.reload();
                            }
                        } else {
                            console.log('Content is cached for offline use.');
                        }
                    }
                };
             };
        }, function(err) {
          console.log('ServiceWorker registration failed: ', err);
        });
      });
    }
  </script>
</body>
</html>