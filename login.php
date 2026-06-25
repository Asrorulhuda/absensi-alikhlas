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
        background: radial-gradient(circle at 50% 50%, <?php echo $landing_bg_color2; ?>d9 0%, <?php echo $landing_bg_color1; ?> 100%);
    }

    /* Glassmorphic Panel with dynamic styles */
    .glass-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.4);
        transform-style: preserve-3d;
        transition: transform 0.1s ease, box-shadow 0.3s ease;
    }

    .glass-input-wrapper {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .glass-input-wrapper:focus-within {
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 255, 255, 0.4);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.08);
    }

    /* Floating Labels styling */
    .floating-group {
        position: relative;
    }
    .floating-group input:focus ~ .floating-label,
    .floating-group input:not(:placeholder-shown) ~ .floating-label {
        transform: translateY(-135%) scale(0.85);
        left: 1rem;
        color: #ffffff;
        background: rgba(11, 37, 69, 0.4);
        padding: 0 6px;
        border-radius: 4px;
    }
    .floating-label {
        position: absolute;
        left: 2.75rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        color: rgba(255, 255, 255, 0.45);
    }

    /* Animated background blobs */
    .blob {
        position: absolute;
        filter: blur(90px);
        z-index: 0;
        opacity: 0.6;
        pointer-events: none;
    }

    @keyframes drift-1 {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(40px, -60px) scale(1.15); }
        66% { transform: translate(-30px, 30px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    @keyframes drift-2 {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(-50px, 40px) scale(0.85); }
        66% { transform: translate(40px, -30px) scale(1.2); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    @keyframes drift-3 {
        0% { transform: translate(0px, 0px) scale(1); }
        50% { transform: translate(50px, 50px) scale(1.25); }
        100% { transform: translate(0px, 0px) scale(1); }
    }

    .animate-blob-1 { animation: drift-1 14s infinite ease-in-out; }
    .animate-blob-2 { animation: drift-2 18s infinite ease-in-out; }
    .animate-blob-3 { animation: drift-3 22s infinite ease-in-out; }

    /* Custom Checkbox Switch */
    .switch-checkbox:checked + .switch-label {
        background-color: rgba(255, 255, 255, 0.95);
    }
    .switch-checkbox:checked + .switch-label .switch-dot {
        transform: translateX(100%);
        background-color: <?php echo $landing_bg_color1; ?>;
    }

    /* Button shine keyframe */
    @keyframes shine {
        100% { transform: translateX(100%); }
    }
    .btn-shine-effect:hover .shine-slide {
        animation: shine 0.85s ease-in-out;
    }

    /* Grid Overlay background */
    .grid-bg {
        background-image: linear-gradient(rgba(255,255,255,0.035) 1px, transparent 1px), 
                          linear-gradient(90deg, rgba(255,255,255,0.035) 1px, transparent 1px);
        background-size: 35px 35px;
        background-position: center center;
    }

    @keyframes float {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-8px) rotate(1deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }
    .animate-float {
        animation: float 6s ease-in-out infinite;
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

<body class="hero-bg min-h-screen flex items-center justify-center p-6 relative overflow-hidden select-none">
  
  <!-- Interactive Background Grid Overlay -->
  <div class="absolute inset-0 grid-bg pointer-events-none z-[1]"></div>
  
  <!-- Interactive Canvas Particle system -->
  <canvas id="particle-canvas" class="absolute inset-0 z-[2] pointer-events-none"></canvas>
  
  <!-- Soft Blurry Blobs -->
  <div class="blob bg-indigo-500 w-96 h-96 rounded-full top-10 left-10 animate-blob-1"></div>
  <div class="blob bg-purple-500 w-96 h-96 rounded-full bottom-10 right-10 animate-blob-2"></div>
  <div class="blob bg-pink-500 w-80 h-80 rounded-full top-1/2 left-1/3 -translate-y-1/2 animate-blob-3"></div>
  
  <!-- Main Glassmorphic Login Card -->
  <div class="glass-card w-full max-w-md rounded-3xl p-8 relative z-10 animate__animated animate__fadeInUp">
    
    <!-- Card Header -->
    <div class="text-center mb-8">
      <a href="index.php" class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-xs font-semibold mb-6 transition-colors group">
        <i class="material-icons-round text-sm transition-transform group-hover:-translate-x-0.5">arrow_back</i> Kembali ke Beranda
      </a>
      
      <div class="flex justify-center mb-4">
        <div class="relative p-1 rounded-2xl bg-white/5 border border-white/10 shadow-inner">
          <img src="assets/img/asr_edu.png" class="h-14 object-contain drop-shadow-xl animate-float" alt="Logo" onerror="this.onerror=null; this.src='assets/img/logo_sekolah.png'">
        </div>
      </div>
      
      <h3 class="text-2xl font-extrabold tracking-tight text-white mb-1"><?php echo $nama_perusahaan; ?></h3>
      <p class="text-xs text-indigo-100/70 font-light">Masuk ke Sistem Absensi Digital</p>
    </div>
    
    <!-- Login Form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="login-form" class="space-y-5">
      
      <!-- Username Field -->
      <div class="floating-group">
        <div class="glass-input-wrapper relative rounded-xl flex items-center">
          <span class="absolute left-4 text-white/40 flex items-center">
            <i class="material-icons-round text-lg">person</i>
          </span>
          <input type="text" id="username" name="username" required placeholder=" " autocomplete="off"
                 class="w-full bg-transparent border-0 py-4 pl-12 pr-4 text-sm font-semibold text-white focus:outline-none focus:ring-0">
          <label for="username" class="floating-label text-xs">Username</label>
        </div>
      </div>
      
      <!-- Password Field -->
      <div class="floating-group">
        <div class="glass-input-wrapper relative rounded-xl flex items-center">
          <span class="absolute left-4 text-white/40 flex items-center">
            <i class="material-icons-round text-lg">lock</i>
          </span>
          <input type="password" id="password" name="password" required placeholder=" "
                 class="w-full bg-transparent border-0 py-4 pl-12 pr-12 text-sm font-semibold text-white focus:outline-none focus:ring-0">
          <label for="password" class="floating-label text-xs">Password</label>
          <button type="button" id="toggle-password" class="absolute right-4 text-white/40 hover:text-white transition-colors focus:outline-none flex items-center">
            <i class="material-icons-round text-lg" id="password-eye-icon">visibility</i>
          </button>
        </div>
      </div>
      
      <!-- Remember Me Switch -->
      <div class="flex items-center justify-between">
        <label class="flex items-center gap-3 cursor-pointer select-none">
          <div class="relative">
            <input type="checkbox" id="rememberMe" name="rememberMe" class="sr-only switch-checkbox">
            <div class="w-9 h-5 bg-white/20 rounded-full switch-label transition-colors duration-300 relative">
              <div class="w-4 h-4 bg-white rounded-full absolute top-0.5 left-0.5 switch-dot transition-transform duration-300"></div>
            </div>
          </div>
          <span class="text-xs text-indigo-100/90 font-medium">Ingat Saya</span>
        </label>
      </div>
      
      <!-- Submit Button -->
      <div class="pt-2">
        <button type="submit" id="btn-login-submit" class="w-full bg-white hover:bg-indigo-50 text-indigo-900 font-extrabold text-sm py-4 rounded-xl shadow-lg transition-all hover:scale-[1.01] flex items-center justify-center gap-2 group relative overflow-hidden btn-shine-effect">
          <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/25 to-transparent -translate-x-full shine-slide"></span>
          <span id="btn-text" class="flex items-center gap-2">
            Masuk Sekarang <i class="material-icons-round text-base transition-transform group-hover:translate-x-0.5">arrow_forward</i>
          </span>
          <span id="btn-spinner" class="hidden flex items-center gap-2">
            <i class="material-icons-round text-base animate-spin">sync</i> Memproses...
          </span>
        </button>
      </div>

      <!-- Accent Divider -->
      <div class="relative flex py-2 items-center">
        <div class="flex-grow border-t border-white/10"></div>
        <span class="flex-shrink mx-4 text-[9px] font-bold text-indigo-100/40 uppercase tracking-wider">Akses Cepat</span>
        <div class="flex-grow border-t border-white/10"></div>
      </div>
      
      <!-- Quick Action Buttons -->
      <div class="grid grid-cols-2 gap-3">
        <a href="pages/dashboard/scan2.php" 
           class="w-full bg-white/10 hover:bg-white/20 border border-white/15 text-white font-bold text-xs py-3.5 rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-md">
          <i class="material-icons-round text-base">qr_code_scanner</i> Scan Presensi
        </a>
        <a href="registrasi_kartu.php" 
           class="w-full bg-white/5 hover:bg-white/10 border border-white/10 text-white/90 hover:text-white font-semibold text-xs py-3.5 rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-md">
          <i class="material-icons-round text-base">credit_card</i> Register Kartu
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
    // --- 1. Canvas Particle Animation ---
    const canvas = document.getElementById('particle-canvas');
    const ctx = canvas.getContext('2d');
    let particles = [];

    function resizeCanvas() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    class Particle {
      constructor() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 3 + 1;
        this.speedX = Math.random() * 0.4 - 0.2;
        this.speedY = Math.random() * 0.4 - 0.2;
        this.opacity = Math.random() * 0.5 + 0.1;
      }
      update() {
        this.x += this.speedX;
        this.y += this.speedY;
        if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
        if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
      }
      draw() {
        ctx.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
      }
    }

    function initParticles() {
      particles = [];
      const count = Math.floor((canvas.width * canvas.height) / 11000);
      for (let i = 0; i < Math.min(count, 120); i++) {
        particles.push(new Particle());
      }
    }
    initParticles();

    function animateParticles() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      particles.forEach(p => {
        p.update();
        p.draw();
      });
      requestAnimationFrame(animateParticles);
    }
    animateParticles();

    // --- 2. Interactive Page Scripts ---
    $(document).ready(function() {
      // 3D Card Tilt Effect on mouse movement
      const card = $('.glass-card');
      $(document).on('mousemove', function(e) {
        const cx = window.innerWidth / 2;
        const cy = window.innerHeight / 2;
        const dx = e.clientX - cx;
        const dy = e.clientY - cy;
        const tiltX = (dy / cy) * -6; // max 6 degrees tilt
        const tiltY = (dx / cx) * 6;  // max 6 degrees tilt
        
        card.css({
          'transform': `perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg)`,
          'transition': 'none'
        });
      });
      
      $(document).on('mouseleave', function() {
        card.css({
          'transform': 'perspective(1000px) rotateX(0deg) rotateY(0deg)',
          'transition': 'transform 0.8s cubic-bezier(0.16, 1, 0.3, 1)'
        });
      });

      // Password Toggle
      $('#toggle-password').click(function() {
        const passwordInput = $('#password');
        const eyeIcon = $('#password-eye-icon');
        const isPassword = passwordInput.attr('type') === 'password';
        
        passwordInput.attr('type', isPassword ? 'text' : 'password');
        eyeIcon.text(isPassword ? 'visibility_off' : 'visibility');
      });

      // Submit loading animation
      $('#login-form').submit(function() {
        $('#btn-text').addClass('hidden');
        $('#btn-spinner').removeClass('hidden');
        $('#btn-login-submit').prop('disabled', true).addClass('opacity-90 cursor-not-allowed');
      });

      // Close error modal
      $('#close-error-btn').click(function() {
        $('#error-modal').addClass('hidden').removeClass('flex');
        // Clean URL parameter without reloading
        window.history.replaceState({}, document.title, window.location.pathname);
      });
      
      $('#error-modal').click(function(e) {
        if (e.target === this) {
          $(this).addClass('hidden').removeClass('flex');
          window.history.replaceState({}, document.title, window.location.pathname);
        }
      });
    });
  </script>
</body>
</html>