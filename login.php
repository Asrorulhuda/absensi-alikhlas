<?php
//header('Location: apps'); /* Redirect browser */

/* Make sure that code below does not get executed when we redirect. */
//exit;
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

$sql = "SELECT * FROM system_config WHERE id =1";

$system_conf = mysqli_query($GLOBALS["___mysqli_ston"],$sql);
$row = mysqli_fetch_array($system_conf);
	$nama_perusahaan = $row["company"];
	$title_bar = $row["title_bar"];
	$icon_bar = $row["icon_bar"];
	$icon_dashboard = $row["icon_dashboard"];
	$sign_in_bg = $row["sign_in_bg"];



if(isset($_POST["username"]) && !empty($_POST["username"])){
	$username = $_POST["username"]; // No need to escape for prepared stmt
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
                
                // Use prepared statement for update too
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
        // Handle prepare error if needed, or just fail login
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
        // Update token in DB
        $update_token_sql = "UPDATE users SET remember_token='$token' WHERE id='$uid'";
        mysqli_query($GLOBALS["___mysqli_ston"], $update_token_sql);
        // Set Cookie (HttpOnly, Secure if HTTPS)
        $secure_cookie = false; // Set to true if HTTPS is enabled
        setcookie('remember_me', $uid . ':' . $token, time() + (86400 * 90), "/", "", $secure_cookie, true);
        // ----------------------------------------
        
        if ($_SESSION['akses'] == 'Guru') {
             header('location:pages/dashboard/dashboard_guru.php');
             exit();
        }
		header('Location: pages/dashboard/');
		//if ($_SESSION['akses'] == 'Admin'){ header('Location: pages/dashboard/');}
		//if ($_SESSION['akses'] == 'User'){ //header('Location: pages/user/');}
    }else{
	    //echo '<script language="javascript" type="text/javascript"> alert("Error login. Invalid username or password");</script>';
		//exit();
        header('location:login.php?msg='.base64_encode('nok'));		
	}	
}

?>
<!--
=========================================================
* Material Dashboard 2 - v3.0.4
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->

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
  
  <title><?php echo $title_bar; ?> </title>
  <!--     Fonts and icons     -->
  <link href="assets/css/Roboto.css" rel="stylesheet" type="text/css" />
  <!-- Nucleo Icons -->
  <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="assets/js/kit.fontawesome.com_42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="assets/css/Material_icon.css" rel="stylesheet">
  
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

  <!-- CSS Files -->
  <link id="pagestyle" href="assets/css/material-dashboard.css?v=3.0.4" rel="stylesheet" />
  <link href="assets/css/animate.min.css" rel="stylesheet" />
  <style>
    body {
        overflow: hidden; /* Prevent scrollbars for the background */
        font-family: 'Roboto', sans-serif;
    }
    
    /* Modern Animated Gradient Background */
    .bg-modern {
        background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
        background-size: 400% 400%;
        animation: gradient 15s ease infinite;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Glassmorphism Card */
    .card-glass {
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }
    
    .text-shadow {
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .btn-glass {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .btn-glass:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    .input-group-outline.is-focused .form-label, 
    .input-group-outline.is-filled .form-label {
        color: #fff !important; /* Make label white when active */
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }
    
    .input-group.input-group-outline .form-control {
        color: #fff !important; /* White text input */
        border-color: rgba(255,255,255,0.5) !important;
    }
    
    .input-group.input-group-outline.is-focused .form-control {
        border-color: #fff !important;
        box-shadow: inset 0 0 0 1px #fff !important;
    }
    
    .form-check-input:checked {
        background-color: #23d5ab;
        border-color: #23d5ab;
    }
  </style>
</head>

<body class="">
  <div class="bg-modern"></div>
  
  <main class="main-content  mt-0">
    <div class="page-header align-items-start min-vh-100">
      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-4 col-md-8 col-12 mx-auto">
            <div class="card card-glass z-index-0 fadeIn3 fadeInBottom animate__animated animate__fadeInUp">
              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1" style="background: linear-gradient(195deg, #42424a 0%, #191919 100%);">
                  <div class="text-center mb-2">
                      <a href="index.php" class="text-white text-xs opacity-7"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
                  </div>
                  <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Sign in</h4>
                  <div class="row mt-3">
				    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0"><?php echo $title_bar; ?></h4>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <form role="form" class="text-start" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label text-white">Username</label>
                    <input type="text" class="form-control text-white" id="username" name="username" required >
                  </div>
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label text-white">Password</label>
                    <input type="password" class="form-control text-white" id="password" name="password" required>
                  </div>
                  <div class="form-check form-switch d-flex align-items-center mb-3">
                    <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                    <label class="form-check-label mb-0 ms-3 text-white" for="rememberMe">Remember me</label>
                  </div>
                  <div class="text-center">
					<input type="submit" class="btn btn-glass w-100 my-4 mb-2 text-white font-weight-bold" value="Sign in"> 
									
                  </div>
                  <div class="text-center">
                    <a href="pages/dashboard/scan2.php" 
                       class="btn btn-glass w-100 my-4 mb-2 text-white font-weight-bold" style="background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%);">
                        <i class="material-icons opacity-10">qr_code_scanner</i> Scan Presensi
                    </a>
                  </div>
                   <div class="text-center">
                    <a href="registrasi_kartu.php" 
                       class="btn btn-outline-white w-100 mb-2 text-white" style="border: 1px solid rgba(255,255,255,0.5);">
                        Registrasi Kartu Baru
                    </a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
	
	<!-- Modal -->
	<div class="modal fade" id="fail_signin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content card-glass">
		  <div class="modal-header border-0">
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">Sign-in Failed!</h5>
			<button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="d-flex justify-content-center text-center">
			   <p style="font-size:20px;" class="text-white">The username or password you entered does not match. Please try again!</p><br>
			 </div>
		  </div>
		  <div class="modal-footer justify-content-center border-0">
			<button type="button" class="btn btn-glass text-white" data-bs-dismiss="modal">OK</button>
		  </div>
		</div>
	  </div>
	</div>
	
	
  </main>


  <script src="assets/js/jquery-3.6.3.min.js"></script>
  <!--   Core JS Files  
  <script src="assets/js/plugins/jquery.min.js"></script>  -->
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script src="assets/js/buttons_github.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/material-dashboard.min.js?v=3.0.4"></script>
  <?php
    if(isset($_GET['msg'])){
		$msg = base64_decode($_GET['msg']);
		if($msg == "nok"){
		echo "<script type='text/javascript'>
			$(document).ready(function(){
			 $('#fail_signin').modal('show');
			});
			</script>";
		}
	}
  
  ?>
  
</body>

</html>