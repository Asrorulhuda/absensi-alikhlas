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
  </style>
</head>

<body class="bg-white text-slate-800 min-h-screen relative antialiased">

  <!-- Header Navigation -->
  <header class="w-full bg-white border-b border-slate-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      
      <!-- Logo / Brand Name -->
      <a href="index.php" class="flex items-center gap-3 font-extrabold text-2xl text-brand-primary">
        <div class="bg-blue-600 text-white p-2 rounded-xl shadow-md shadow-blue-500/20">
          <i class="material-icons-round text-base block">fingerprint</i>
        </div>
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

      <!-- Right Mockup UI Illustration -->
      <div class="relative w-full max-w-lg mx-auto flex items-center justify-center py-6 animate__animated animate__fadeInRight">
        
        <!-- Soft Blurry Circles -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 rounded-full bg-gradient-to-tr from-blue-200/50 to-indigo-200/50 blur-3xl opacity-60"></div>
        
        <!-- Main Mockup Card -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-2xl shadow-slate-200/60 p-6 w-full max-w-sm relative z-10 animate-float">
          
          <!-- Card Header -->
          <div class="border-b border-slate-100 pb-4 mb-4 text-center">
            <span class="font-extrabold text-slate-800 text-xs block mb-1">Rekap Absensi — Kelas X-A</span>
            <div class="flex items-center justify-center gap-2 mt-2">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Senin, 23 Juni 2025</span>
            </div>
          </div>

          <!-- Status stats boxes -->
          <div class="grid grid-cols-3 gap-2 text-center mb-6">
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 py-2 rounded-xl">
              <span class="block text-xs font-extrabold">25</span>
              <span class="block text-[8px] font-bold uppercase tracking-wider opacity-75">Hadir</span>
            </div>
            <div class="bg-amber-50 border border-amber-100 text-amber-600 py-2 rounded-xl">
              <span class="block text-xs font-extrabold">2</span>
              <span class="block text-[8px] font-bold uppercase tracking-wider opacity-75">Izin</span>
            </div>
            <div class="bg-rose-50 border border-rose-100 text-rose-600 py-2 rounded-xl">
              <span class="block text-xs font-extrabold">1</span>
              <span class="block text-[8px] font-bold uppercase tracking-wider opacity-75">Alpha</span>
            </div>
          </div>

          <!-- Student Rows -->
          <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-slate-50">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-bold text-slate-700">Andika Pratama</span>
              </div>
              <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[9px] font-extrabold">Hadir</span>
            </div>
            
            <div class="flex justify-between items-center py-2 border-b border-slate-50">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="text-xs font-bold text-slate-700">Salsabila Putri</span>
              </div>
              <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-[9px] font-extrabold">Izin Sakit</span>
            </div>

            <div class="flex justify-between items-center py-2">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-bold text-slate-700">Budi Santoso</span>
              </div>
              <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[9px] font-extrabold">Hadir</span>
            </div>
          </div>

          <!-- Bottom Tap Card box -->
          <div class="mt-6 p-4 bg-slate-50 border border-slate-100 rounded-2xl flex items-center gap-3">
            <div class="bg-brand-primary text-white p-2.5 rounded-xl shadow-md">
              <i class="material-icons-round text-sm block">contactless</i>
            </div>
            <div>
              <span class="text-[8px] text-slate-400 font-extrabold uppercase tracking-wider block">Tap Kartu RFID</span>
              <span class="text-xs font-bold text-slate-700 block">Tunjukkan kartu pelajar</span>
            </div>
          </div>

        </div>

        <!-- Floating Badge Top Right -->
        <div class="absolute -top-2 -right-2 bg-white rounded-2xl shadow-xl px-4 py-3 border border-slate-100 flex items-center gap-2 z-20 animate-pulse-slow">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
          <span class="text-[11px] font-extrabold text-slate-800">127 siswa</span>
          <span class="text-[11px] text-slate-400 font-medium">sudah absen hari ini</span>
        </div>

        <!-- Floating Badge Bottom Left -->
        <div class="absolute -bottom-2 -left-2 bg-white rounded-2xl shadow-xl px-4 py-3 border border-slate-100 flex items-center gap-2 z-20">
          <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] font-bold">
            <i class="material-icons-round text-xs">done</i>
          </div>
          <div>
            <span class="text-[10px] font-extrabold text-slate-800 block">Notif WA terkirim</span>
            <span class="text-[9px] text-slate-400 block leading-none mt-0.5">Detik ini ke orang tua</span>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- Statistics Counters Row -->
  <section class="border-t border-b border-slate-100 py-12 bg-white relative z-20">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8">
      
      <div class="text-center md:border-r border-slate-100 py-2">
        <span class="text-3xl sm:text-4xl font-extrabold text-[#0B2545]">1.200+</span>
        <span class="block text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mt-2">Total Siswa Terdaftar</span>
      </div>

      <div class="text-center md:border-r border-slate-100 py-2">
        <span class="text-3xl sm:text-4xl font-extrabold text-[#0B2545]">98%</span>
        <span class="block text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mt-2">Akurasi Data Absensi</span>
      </div>

      <div class="text-center md:border-r border-slate-100 py-2">
        <span class="text-3xl sm:text-4xl font-extrabold text-[#0B2545]">45+</span>
        <span class="block text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mt-2">Kelas Aktif</span>
      </div>

      <div class="text-center py-2">
        <span class="text-3xl sm:text-4xl font-extrabold text-[#0B2545]">3 Dtk</span>
        <span class="block text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mt-2">Kecepatan Notif WA</span>
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
          <span class="text-base font-bold text-slate-800 mt-1 block">1.200</span>
        </div>

        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm text-center flex flex-col items-center justify-center">
          <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
            <i class="material-icons-round text-xl">chat</i>
          </div>
          <span class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wider block">Notif Terkirim</span>
          <span class="text-base font-bold text-slate-800 mt-1 block">8.340</span>
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

  <!-- Wali Kelas / Guru Section -->
  <section class="max-w-7xl mx-auto px-6 py-20 bg-white">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      
      <!-- Text Left -->
      <div class="flex flex-col items-center lg:items-start text-center lg:text-left self-center">
        <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Wali Kelas</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 mb-4 leading-tight">Guru Berpengalaman Terkelola</h2>
        <p class="text-xs text-slate-400 font-light leading-relaxed mb-6">
          Setiap wali kelas punya akses mandiri untuk memantau dan mengelola absensi kelasnya masing-masing.
        </p>
        <a href="login.php" class="bg-[#0B2545] hover:bg-[#134074] text-white text-xs font-bold px-5 py-3 rounded-xl shadow-md transition-all hover:scale-[1.02] text-center inline-block">
          Lihat Semua Guru
        </a>
      </div>

      <!-- Right Columns of Cards -->
      <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">
        
        <!-- Card 1 -->
        <div class="bg-[#EEF2F6] border border-slate-100 p-5 rounded-2xl flex flex-col justify-between hover:shadow-md transition-all h-[180px]">
          <div>
            <span class="font-extrabold text-slate-800 text-sm block">Bu Andika Sari</span>
            <span class="text-[10px] text-slate-400 font-medium block mt-0.5">Wali Kelas X-A</span>
          </div>
          <div class="flex items-center gap-1 text-[10px] text-slate-600 font-bold mt-auto">
            <span>⭐</span>
            <span>4.9</span>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-[#EAF7F2] border border-slate-100 p-5 rounded-2xl flex flex-col justify-between hover:shadow-md transition-all h-[180px]">
          <div>
            <span class="font-extrabold text-slate-800 text-sm block">Pak Budi Santoso</span>
            <span class="text-[10px] text-slate-400 font-medium block mt-0.5">Wali Kelas XI-B</span>
          </div>
          <div class="flex items-center gap-1 text-[10px] text-slate-600 font-bold mt-auto">
            <span>⭐</span>
            <span>4.8</span>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-[#FEFCE8] border border-slate-100 p-5 rounded-2xl flex flex-col justify-between hover:shadow-md transition-all h-[180px]">
          <div>
            <span class="font-extrabold text-slate-800 text-sm block">Bu Melani Putri</span>
            <span class="text-[10px] text-slate-400 font-medium block mt-0.5">Wali Kelas XII-A</span>
          </div>
          <div class="flex items-center gap-1 text-[10px] text-slate-600 font-bold mt-auto">
            <span>⭐</span>
            <span>4.9</span>
          </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-[#FDF2F8] border border-slate-100 p-5 rounded-2xl flex flex-col justify-between hover:shadow-md transition-all h-[180px]">
          <div>
            <span class="font-extrabold text-slate-800 text-sm block">Pak Ridwan Hakim</span>
            <span class="text-[10px] text-slate-400 font-medium block mt-0.5">Wali Kelas X-B</span>
          </div>
          <div class="flex items-center gap-1 text-[10px] text-slate-600 font-bold mt-auto">
            <span>⭐</span>
            <span>4.7</span>
          </div>
        </div>

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
        <span class="font-semibold text-slate-400">Powered by Arducoding Corp</span>
      </div>

    </div>
  </footer>

  <script>
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