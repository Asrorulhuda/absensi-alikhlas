<?php
session_start();
require_once "include/db_config.php";

// If already logged in, redirect to dashboard
if (isset($_SESSION['id'])) {
    if ($_SESSION['akses'] == 'Guru') {
        header('Location: pages/dashboard/dashboard_guru.php');
    } else {
        header('Location: pages/dashboard/');
    }
    exit();
}

// --- PERSISTENT LOGIN (REMEMBER ME) CHECK ---
if (!isset($_SESSION['id']) && isset($_COOKIE['remember_me'])) {
    list($cookie_id, $cookie_token) = explode(':', $_COOKIE['remember_me']);
    
    // Sanitize
    $cookie_id = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $cookie_id);
    $cookie_token = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $cookie_token);
    
    $sql_check = "SELECT * FROM users WHERE id='$cookie_id' AND remember_token='$cookie_token'";
    $result_check = mysqli_query($GLOBALS["___mysqli_ston"], $sql_check);
    
    if ($result_check && mysqli_num_rows($result_check) > 0) {
        $user_data = mysqli_fetch_array($result_check);
        $_SESSION['name'] = $user_data["name"];
        $_SESSION['id'] = $user_data["id"];
        $_SESSION['akses'] = $user_data["level_akses"];
        $_SESSION['id_siswa'] = $user_data["id_siswa"];
        $_SESSION['id_guru'] = $user_data["id_guru"];
        
        // Refresh cookie for another 90 days
        setcookie('remember_me', $cookie_id . ':' . $cookie_token, time() + (86400 * 90), "/");
        
        if ($_SESSION['akses'] == 'Guru') {
             header('Location: pages/dashboard/dashboard_guru.php');
        } else {
             header('Location: pages/dashboard/');
        }
        exit();
    }
}
// --------------------------------------------

$sql = "SELECT * FROM system_config WHERE id = 1";
$system_conf = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
$row = mysqli_fetch_array($system_conf);

$nama_perusahaan = $row["company"] ?? "Nama Perusahaan";
$title_bar = $row["title_bar"] ?? "Sistem Absensi Digital";
$icon_bar = $row["icon_bar"] ?? "";
$landing_bg_color1 = $row["landing_bg_color1"] ?? '#4f46e5'; // Indigo-600
$landing_bg_color2 = $row["landing_bg_color2"] ?? '#7c3aed'; // Violet-600

if(isset($_POST["username"]) && !empty($_POST["username"])){
	$username = $_POST["username"];
	$raw_password = $_POST["password"];
	$md5_password = md5($raw_password);
	
	// Fetch user by username using Prepared Statement
	$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], "SELECT * FROM users WHERE username = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $login_success = false;
        $listdata_users = null;

        if($result && mysqli_num_rows($result) > 0){ 
            $listdata_users = mysqli_fetch_array($result);
            
            // 1. Check Bcrypt
            if (password_verify($raw_password, $listdata_users['password'])) {
                $login_success = true;
            } 
            // 2. Check MD5 (Legacy Fallback)
            elseif ($listdata_users['password'] === $md5_password) {
                $login_success = true;
                // Upgrade to Bcrypt immediately
                $new_hash = password_hash($raw_password, PASSWORD_BCRYPT);
                $uid = $listdata_users['id'];
                
                $stmt_update = mysqli_prepare($GLOBALS["___mysqli_ston"], "UPDATE users SET password=? WHERE id=?");
                if ($stmt_update) {
                    mysqli_stmt_bind_param($stmt_update, "si", $new_hash, $uid);
                    mysqli_stmt_execute($stmt_update);
                    mysqli_stmt_close($stmt_update);
                }
            }
        }
        mysqli_stmt_close($stmt);
    } else {
        $login_success = false;
    }

    if ($login_success && $listdata_users) {
		$_SESSION['name'] = $listdata_users["name"];
		$_SESSION['id'] = $listdata_users["id"];
		$_SESSION['akses'] = $listdata_users["level_akses"];
		$_SESSION['id_siswa'] = $listdata_users["id_siswa"];
        $_SESSION['id_guru'] = $listdata_users["id_guru"];
        
        // --- SET REMEMBER ME COOKIE (90 DAYS) ---
        $token = bin2hex(random_bytes(32));
        $uid = $listdata_users['id'];
        $update_token_sql = "UPDATE users SET remember_token='$token' WHERE id='$uid'";
        mysqli_query($GLOBALS["___mysqli_ston"], $update_token_sql);
        
        $secure_cookie = false; // Set to true if HTTPS is enabled
        setcookie('remember_me', $uid . ':' . $token, time() + (86400 * 90), "/", "", $secure_cookie, true);
        
        if ($_SESSION['akses'] == 'Guru') {
             header('location:pages/dashboard/dashboard_guru.php');
             exit();
        }
		header('Location: pages/dashboard/');
        exit();
    } else {
        header('location:login.php?msg='.base64_encode('nok'));		
        exit();
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
  
  <title>Masuk — <?php echo $title_bar; ?></title>
  
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
          }
        }
      }
    }
  </script>
  
  <style>
    body {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        overflow: hidden;
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
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
    }

    .blob {
        position: absolute;
        filter: blur(60px);
        z-index: 0;
        opacity: 0.5;
    }
  </style>
  
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('service-worker.js?v=2').then(function(registration) {
          console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function(err) {
          console.log('ServiceWorker registration failed: ', err);
        });
      });
    }
  </script>
</head>

<body class="hero-bg min-h-screen flex items-center justify-center p-6 relative select-none">
  
  <!-- Soft Blurry Blobs -->
  <div class="blob bg-purple-500 w-80 h-80 rounded-full top-10 left-10"></div>
  <div class="blob bg-blue-400 w-80 h-80 rounded-full bottom-10 right-10"></div>
  
  <!-- Main Glassmorphic Login Card -->
  <div class="glass-card w-full max-w-md rounded-3xl p-8 relative z-10 animate__animated animate__fadeInUp">
    
    <!-- Card Header -->
    <div class="text-center mb-8">
      <a href="index.php" class="inline-flex items-center gap-2 text-white/80 hover:text-white text-xs font-semibold mb-6 transition-colors">
        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
      </a>
      
      <div class="flex justify-center mb-4">
        <img src="assets/img/asr_edu.png" class="max-w-[70px] drop-shadow-xl animate__animated animate__pulse animate__infinite" alt="Logo" onerror="this.src='assets/img/logo-ct.png'">
      </div>
      
      <h3 class="text-2xl font-extrabold tracking-tight text-white mb-1"><?php echo $nama_perusahaan; ?></h3>
      <p class="text-xs text-indigo-100/70 font-light">Masuk ke Sistem Absensi Digital</p>
    </div>
    
    <!-- Login Form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-5">
      
      <!-- Username Field -->
      <div>
        <label class="block text-[10px] font-bold text-indigo-100 uppercase tracking-wider mb-2" for="username">Username</label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-white/50">
            <i class="material-icons-round text-sm">person</i>
          </span>
          <input type="text" id="username" name="username" required placeholder="Masukkan username"
                 class="w-full bg-white/10 border border-white/20 rounded-xl py-3.5 pl-11 pr-4 text-sm font-semibold text-white placeholder-white/40 focus:outline-none focus:border-white focus:bg-white/20 transition-all">
        </div>
      </div>
      
      <!-- Password Field -->
      <div>
        <label class="block text-[10px] font-bold text-indigo-100 uppercase tracking-wider mb-2" for="password">Password</label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-white/50">
            <i class="material-icons-round text-sm">lock</i>
          </span>
          <input type="password" id="password" name="password" required placeholder="••••••••"
                 class="w-full bg-white/10 border border-white/20 rounded-xl py-3.5 pl-11 pr-4 text-sm font-semibold text-white placeholder-white/40 focus:outline-none focus:border-white focus:bg-white/20 transition-all">
        </div>
      </div>
      
      <!-- Remember Me Switch -->
      <div class="flex items-center justify-between">
        <label class="flex items-center gap-3 cursor-pointer select-none">
          <input class="w-4 h-4 rounded border-white/20 bg-white/10 text-blue-600 focus:ring-0 focus:ring-offset-0 cursor-pointer" type="checkbox" id="rememberMe" name="rememberMe">
          <span class="text-xs text-indigo-100/90 font-medium">Ingat Saya</span>
        </label>
      </div>
      
      <!-- Submit Button -->
      <div class="pt-2">
        <button type="submit" class="w-full bg-white hover:bg-indigo-50 text-indigo-900 font-extrabold text-sm py-4 rounded-xl shadow-lg transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
          Masuk Sekarang
        </button>
      </div>

      <!-- Accent Divider -->
      <div class="relative flex py-2 items-center">
        <div class="flex-grow border-t border-white/10"></div>
        <span class="flex-shrink mx-4 text-[9px] font-bold text-indigo-100/40 uppercase tracking-wider">Akses Cepat</span>
        <div class="flex-grow border-t border-white/10"></div>
      </div>
      
      <!-- Scan QR / RFID Monitor link -->
      <div>
        <a href="pages/dashboard/scan2.php" 
           class="w-full glass-panel hover:bg-white/20 text-white font-bold text-xs py-3.5 rounded-xl transition-all flex items-center justify-center gap-2">
          <i class="material-icons-round text-base">qr_code_scanner</i> Buka Scan Presensi
        </a>
      </div>

      <!-- Card Registration link -->
      <div>
        <a href="registrasi_kartu.php" 
           class="w-full border border-white/10 hover:border-white/30 text-white/80 hover:text-white font-semibold text-[11px] py-3 rounded-xl transition-all flex items-center justify-center gap-2">
          Registrasi Kartu Baru
        </a>
      </div>
      
    </form>
  </div>

  <!-- Custom Error Modal (Tailwind, animated) -->
  <div id="error-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] <?php echo (isset($_GET['msg']) && base64_decode($_GET['msg']) == 'nok') ? 'flex' : 'hidden'; ?> items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full shadow-2xl border border-slate-100 overflow-hidden animate__animated animate__zoomIn">
      <div class="p-6 text-center">
        <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-4 text-3xl">
          <i class="material-icons-round">error_outline</i>
        </div>
        <h4 class="text-lg font-extrabold text-slate-800 mb-2">Login Gagal!</h4>
        <p class="text-xs text-slate-500 leading-relaxed mb-6">Username atau password yang Anda masukkan tidak sesuai. Silakan coba kembali.</p>
        <button id="close-error-btn" class="w-full bg-[#0B2545] hover:bg-[#134074] text-white font-extrabold text-xs py-3.5 rounded-xl transition-all shadow-md">
          OK, Mengerti
        </button>
      </div>
    </div>
  </div>

  <!-- jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.6.3.min.js" crossorigin="anonymous"></script>
  
  <script>
    $(document).ready(function() {
      // Close custom modal alert
      $('#close-error-btn').click(function() {
        $('#error-modal').addClass('hidden').removeClass('flex');
      });
      
      // Close error modal if clicking outside modal content
      $('#error-modal').click(function(e) {
        if (e.target === this) {
          $(this).addClass('hidden').removeClass('flex');
        }
      });
    });
  </script>
</body>
</html>