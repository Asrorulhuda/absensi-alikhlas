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
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/system_data/<?php echo !empty($icon_bar) ? $icon_bar : 'favicon.ico'; ?>">
  
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="<?php echo $landing_bg_color1; ?>">
  
  <title><?php echo $title_bar; ?></title>
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="assets/css/animate.min.css" rel="stylesheet" />
  
  <script src="https://cdn.tailwindcss.com"></script>
  
  <style>
    body {
        font-family: 'Inter', sans-serif;
        overflow-x: hidden;
    }
    
    .hero-bg {
        background: linear-gradient(135deg, <?php echo $landing_bg_color1; ?> 0%, <?php echo $landing_bg_color2; ?> 100%);
    }

    /* Glassmorphism Utilities */
    .glass-panel {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }
    
    .glass-card:hover {
        transform: translateY(-8px);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .feature-icon-gradient {
        background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .blob {
        position: absolute;
        filter: blur(60px);
        z-index: 0;
        opacity: 0.6;
    }
    
    /* Animation for floating elements */
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
    .animate-float {
        animation: float 5s ease-in-out infinite;
    }
  </style>
</head>

<body class="hero-bg min-h-screen text-white relative">
  <div class="bg-yellow-400 text-black text-center py-2 font-bold fixed top-0 w-full z-50 shadow-md text-sm">
      🚀 TEST AUTO-DEPLOY BERHASIL! mantap 🚀
  </div>
  
  <div class="blob bg-purple-500 w-96 h-96 rounded-full top-10 left-10"></div>
  <div class="blob bg-blue-400 w-80 h-80 rounded-full bottom-10 right-10"></div>
  
  <div class="container mx-auto px-6 pt-24 pb-12 min-h-screen flex items-center relative z-10">
    <div class="flex flex-col lg:flex-row items-center justify-between w-full gap-12">
      
      <div class="w-full lg:w-1/2 flex flex-col items-center lg:items-start text-center lg:text-left animate__animated animate__fadeInLeft">
          <img src="assets/img/arducoding_corp.png" class="max-w-[150px] mb-6 drop-shadow-xl animate-float" alt="Logo Sekolah" onerror="this.src='assets/img/logo-ct.png'">
          
          <h1 class="text-4xl lg:text-6xl font-extrabold tracking-tight mb-4 drop-shadow-md">
            <?php echo $title_bar; ?>
          </h1>
          
          <p class="text-lg text-indigo-100 mb-8 max-w-lg font-light leading-relaxed">
            Sistem Absensi Digital Terintegrasi dengan Notifikasi WhatsApp Realtime. Memudahkan Guru, Siswa, dan Orang Tua dalam memantau kehadiran harian secara otomatis.
          </p>
          
          <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
              <?php if (isset($_SESSION['id'])) { ?>
                  <a href="<?php echo ($_SESSION['akses'] == 'Guru') ? 'pages/dashboard/dashboard_guru.php' : 'pages/dashboard/'; ?>" class="bg-white text-indigo-700 hover:bg-indigo-50 font-bold py-3 px-8 rounded-full shadow-lg transition-transform hover:scale-105 flex items-center justify-center gap-2">
                    <i class="fas fa-columns"></i> Buka Dashboard
                  </a>
              <?php } else { ?>
                  <a href="login.php" class="bg-white text-indigo-700 hover:bg-indigo-50 font-bold py-3 px-8 rounded-full shadow-lg transition-transform hover:scale-105 flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i> Login Sekarang
                  </a>
              <?php } ?>
              
              <a href="pages/dashboard/user_izin.php" class="glass-panel text-white hover:bg-white/20 font-bold py-3 px-8 rounded-full transition-all flex items-center justify-center gap-2">
                 <i class="material-icons text-xl">assignment</i> Pengajuan Izin
              </a>
          </div>
          
          <div id="install-pwa" class="hidden mt-8 cursor-pointer glass-panel px-6 py-3 rounded-xl hover:bg-white/20 transition-all inline-flex items-center gap-2">
              <i class="material-icons">download</i> Install Aplikasi PWA
          </div>
      </div>
      
      <div class="w-full lg:w-1/2 relative animate__animated animate__fadeInRight">
          <div class="grid grid-cols-2 gap-4 lg:gap-6">
              
              <div class="glass-card rounded-2xl p-6 text-center mt-0 lg:mt-8">
                  <i class="material-icons text-5xl mb-4 feature-icon-gradient">notifications_active</i>
                  <h5 class="font-bold text-lg mb-2">Notifikasi WA</h5>
                  <p class="text-sm text-indigo-100 font-light">Kirim pesan otomatis ke Orang Tua saat siswa tapping kartu.</p>
              </div>
              
              <div class="glass-card rounded-2xl p-6 text-center">
                  <i class="material-icons text-5xl mb-4 feature-icon-gradient">dashboard</i>
                  <h5 class="font-bold text-lg mb-2">Dashboard Guru</h5>
                  <p class="text-sm text-indigo-100 font-light">Kelola izin, sakit, dan absensi kelas dengan rekapitulasi mudah.</p>
              </div>
              
              <div class="glass-card rounded-2xl p-6 text-center mt-0 lg:mt-8">
                  <i class="material-icons text-5xl mb-4 feature-icon-gradient">credit_card</i>
                  <h5 class="font-bold text-lg mb-2">Kartu RFID</h5>
                  <p class="text-sm text-indigo-100 font-light">Absensi cepat dan akurat menggunakan kartu pelajar UID.</p>
              </div>
              
              <div class="glass-card rounded-2xl p-6 text-center">
                  <i class="material-icons text-5xl mb-4 feature-icon-gradient">phone_iphone</i>
                  <h5 class="font-bold text-lg mb-2">Akses Mobile</h5>
                  <p class="text-sm text-indigo-100 font-light">Desain responsif, lancar diakses dari HP (Android/iOS).</p>
              </div>
              
          </div>
      </div>
      
    </div>
  </div>

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