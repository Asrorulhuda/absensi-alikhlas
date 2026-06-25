<?php
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
require_once "include/db_config.php";

// Fetch system config
$sql = "SELECT * FROM system_config WHERE id =1";
$system_conf = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
$row = mysqli_fetch_array($system_conf);
$nama_perusahaan = $row["company"];
$title_bar = $row["title_bar"];
$icon_bar = $row["icon_bar"];
$landing_bg_color1 = $row["landing_bg_color1"] ?? '#667eea';
$landing_bg_color2 = $row["landing_bg_color2"] ?? '#764ba2';
$card_bg_color = $row["card_bg_color"] ?? 'rgba(255, 255, 255, 0.1)';

// Check if already logged in
// if (isset($_SESSION['id'])) {
//     if ($_SESSION['akses'] == 'Guru') {
//         header('Location: pages/dashboard/dashboard_guru.php');
//     } else {
//         header('Location: pages/dashboard/');
//     }
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/system_data/favicon.ico">
  
  <!-- PWA Manifest & Meta -->
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#e91e63">
  
  <title><?php echo $title_bar; ?></title>
  
  <!-- Fonts and icons -->
  <link href="assets/css/Roboto.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="assets/js/kit.fontawesome.com_42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="assets/css/Material_icon.css" rel="stylesheet">
  
  <!-- CSS Files -->
  <link id="pagestyle" href="assets/css/material-dashboard.css?v=3.0.4" rel="stylesheet" />
  <link href="assets/css/animate.min.css" rel="stylesheet" />
  
  <style>
    body {
        font-family: 'Roboto', sans-serif;
        overflow-x: hidden;
    }
    
    /* Hero Section with Gradient */
    .hero-section {
        background: linear-gradient(135deg, <?php echo $landing_bg_color1; ?> 0%, <?php echo $landing_bg_color2; ?> 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    
    .hero-shapes {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
    }
    
    .shape {
        position: absolute;
        opacity: 0.1;
        background: white;
        border-radius: 50%;
    }
    
    .shape-1 { top: -10%; left: -10%; width: 500px; height: 500px; }
    .shape-2 { bottom: -10%; right: -10%; width: 400px; height: 400px; }
    .shape-3 { top: 30%; right: 20%; width: 100px; height: 100px; opacity: 0.05; }
    
    .hero-content {
        z-index: 1;
        position: relative;
        padding-top: 2rem;
    }
    
    .glass-card {
        background: <?php echo $card_bg_color; ?>;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: transform 0.3s ease;
    }
    
    .glass-card:hover {
        transform: translateY(-5px);
    }
    
    .feature-icon {
        background: linear-gradient(135deg, #FF0080 0%, #FF8C00 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .btn-get-started {
        background: white;
        color: #764ba2;
        font-weight: bold;
        padding: 15px 40px;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .btn-get-started:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        color: #667eea;
    }
    
    .school-logo {
        max-width: 150px;
        margin-bottom: 20px;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
        100% { transform: translateY(0px); }
    }
    
    .hero-title {
        font-weight: 800;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        margin-bottom: 1rem;
    }
    
    .hero-subtitle {
        font-weight: 300;
        margin-bottom: 2.5rem;
        font-size: 1.1rem;
        opacity: 0.9;
    }
    
    /* Feature Cards for Mobile Scroll */
    .features-scroll {
        display: flex;
        overflow-x: auto;
        padding-bottom: 20px;
        gap: 20px;
        scrollbar-width: none; /* Firefox */
    }
    
    .features-scroll::-webkit-scrollbar {
        display: none; /* Chrome/Safari */
    }
    
    .feature-card {
        min-width: 250px;
        background: rgba(255,255,255,0.95);
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        color: #333;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .install-pwa-card {
        display: none; /* Hidden by default, shown via JS if applicable */
        background: linear-gradient(45deg, #ff9966, #ff5e62);
        color: white;
        border-radius: 15px;
        padding: 15px;
        margin-top: 20px;
        text-align: center;
        cursor: pointer;
    }

    /* Floating Login Button for Mobile */
    .mobile-login-fab {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #e91e63;
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        z-index: 1000;
        transition: transform 0.3s;
    }
    
    .mobile-login-fab:hover {
        transform: scale(1.1);
    }
    
    .stagger-down { margin-top: 30px; }
    .stagger-up { margin-top: -30px; }

    @media (max-width: 768px) {
        .hero-section {
            display: block;
            padding-top: 60px;
            height: auto;
            min-height: 100vh;
        }
        
        .hero-text {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .glass-card {
            margin: 0;
            padding: 1.5rem;
        }
        
        .stagger-down, .stagger-up {
            margin-top: 0 !important;
        }
    }
  </style>
</head>

<body>
  <div style="background: #ffeb3b; color: #000; text-align: center; padding: 10px; font-weight: bold; position: fixed; top: 0; width: 100%; z-index: 9999; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
      🚀 TEST AUTO-DEPLOY BERHASIL! mantap 🚀
  </div>
  
  <div class="hero-section">
    <!-- Animated Shapes -->
    <div class="hero-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <div class="container hero-content">
      <div class="row align-items-center">
        <!-- Left Content -->
        <div class="col-lg-6 hero-text text-white animate__animated animate__fadeInLeft">
          <div class="text-center text-lg-start">
              <img src="assets/img/arducoding_corp.png" class="school-logo" alt="Logo Sekolah" onerror="this.src='assets/img/logo-ct.png'">
          </div>
          <h1 class="hero-title display-4 text-center text-lg-start"><?php echo $title_bar; ?></h1>
          <p class="hero-subtitle text-center text-lg-start">
            Sistem Absensi Digital Terintegrasi dengan Notifikasi WhatsApp Realtime.
            Memudahkan Guru, Siswa, dan Orang Tua dalam memantau kehadiran.
          </p>
          
          <div class="d-flex justify-content-center justify-content-lg-start gap-3">
              <?php if (isset($_SESSION['id'])) { ?>
                  <a href="<?php echo ($_SESSION['akses'] == 'Guru') ? 'pages/dashboard/dashboard_guru.php' : 'pages/dashboard/'; ?>" class="btn btn-get-started">
                    <i class="fas fa-columns me-2"></i> Buka Dashboard
                  </a>
              <?php } else { ?>
                  <a href="login.php" class="btn btn-get-started">
                    <i class="fas fa-sign-in-alt me-2"></i> Login Sekarang
                  </a>
              <?php } ?>
              
              <a href="pages/dashboard/user_izin.php" class="btn btn-outline-light rounded-pill px-4 py-3 fw-bold" style="border: 2px solid rgba(255,255,255,0.8);">
                 <i class="material-icons align-middle me-1">assignment</i> Pengajuan Izin
              </a>
          </div>
          
          <!-- PWA Install Button (Hidden by default) -->
          <div id="install-pwa" class="install-pwa-card mt-4 mx-auto mx-lg-0" style="max-width: 300px;">
              <i class="material-icons align-middle">download</i> Install Aplikasi
          </div>
        </div>
        
        <!-- Right Content (Features) -->
        <div class="col-lg-6 mt-5 mt-lg-0 animate__animated animate__fadeInRight">
           <div class="row g-4">
               <div class="col-6">
                   <div class="glass-card text-center text-white">
                       <i class="material-icons feature-icon text-white">notifications_active</i>
                       <h5>Notifikasi WA</h5>
                       <p class="small opacity-75">Kirim pesan otomatis ke Orang Tua saat siswa tapping kartu.</p>
                   </div>
               </div>
               <div class="col-6">
                   <div class="glass-card text-center text-white stagger-down">
                       <i class="material-icons feature-icon text-white">dashboard</i>
                       <h5>Dashboard Guru</h5>
                       <p class="small opacity-75">Kelola izin, sakit, dan absensi kelas dengan mudah.</p>
                   </div>
               </div>
               <div class="col-6">
                   <div class="glass-card text-center text-white stagger-up">
                       <i class="material-icons feature-icon text-white">credit_card</i>
                       <h5>Kartu RFID</h5>
                       <p class="small opacity-75">Absensi cepat dan akurat menggunakan kartu pelajar.</p>
                   </div>
               </div>
               <div class="col-6">
                   <div class="glass-card text-center text-white">
                       <i class="material-icons feature-icon text-white">phone_iphone</i>
                       <h5>Akses Mobile</h5>
                       <p class="small opacity-75">Desain responsif, mudah diakses dari HP (Android/iOS).</p>
                   </div>
               </div>
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
      installBtn.style.display = 'block';
      
      installBtn.addEventListener('click', (e) => {
        installBtn.style.display = 'none';
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
             // Registration was successful
             console.log('ServiceWorker registration successful with scope: ', registration.scope);
             
             registration.onupdatefound = function() {
                const installingWorker = registration.installing;
                installingWorker.onstatechange = function() {
                    if (installingWorker.state === 'installed') {
                        if (navigator.serviceWorker.controller) {
                            // New update available
                            console.log('New content is available; please refresh.');
                            if(confirm("Versi baru aplikasi tersedia. Muat ulang sekarang?")) {
                                window.location.reload();
                            }
                        } else {
                            // Content is cached for the first time
                            console.log('Content is cached for offline use.');
                        }
                    }
                };
             };
        }, function(err) {
          // registration failed :(
          console.log('ServiceWorker registration failed: ', err);
        });
      });
    }
  </script>
</body>
</html>