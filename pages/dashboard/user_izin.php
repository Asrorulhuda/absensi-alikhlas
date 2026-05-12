<?php 
	session_start();
    date_default_timezone_set("Asia/Jakarta");
    // Ensure only User (Siswa) can access this page. Admin might access too if needed, but primary is User.
	if ($_SESSION['akses'] != 'User' && $_SESSION['akses'] != 'Admin') {
        // If not logged in, redirect to login via index
        if(!isset($_SESSION['akses'])) {
             header('location:../../login.php');
        } else {
             header('location:../../index.php'); 
        }
        exit();
    }
	$ses_name = $_SESSION['name'];
	$_SESSION['pages'] = "Pengajuan Izin";
	
	require_once "../../include/db_config.php";
    include "control/confignusers_data.php";
    
    // Initialize PDO
    $database = new Database();
    $pdo = $database->getConnection();
    
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../../<?php echo $icon_bar; ?>">
  <title><?php echo $title_bar; ?></title>
  <!--     Fonts and icons     -->
  <link href="../../assets/css/Roboto.css" rel="stylesheet" type="text/css" />
  <!-- Nucleo Icons -->
  <link href="../../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="../../assets/js/kit.fontawesome.com_42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="../../assets/css/Material_icon.css" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="../../assets/css/material-dashboard.css?v=3.0.4" rel="stylesheet" />
  
  <!-- Choices.js for nice selects -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
  
</head>

<body class="g-sidenav-show  bg-gray-200">
  <!-- Sidebar -->
  <?php include 'view/part_sidenav.php';?>
  <!-- End of Sidebar -->
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
	 <?php include 'view/part_topnav.php';?>
	<!-- End Navbar -->
    <div class="container-fluid py-4">
      
      <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Form Pengajuan Izin / Sakit (Siswa)</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="p-4">
                        <?php
                        if(isset($_GET['msg'])) {
                            $msg = $_GET['msg'];
                            if($msg == 'success') {
                                echo '<div class="alert alert-success text-white">Pengajuan berhasil dikirim. Menunggu persetujuan.</div>';
                            } elseif($msg == 'error') {
                                echo '<div class="alert alert-danger text-white">Terjadi kesalahan. Silakan coba lagi.</div>';
                            } elseif($msg == 'invalid') {
                                echo '<div class="alert alert-warning text-white">Data tidak lengkap.</div>';
                            }
                        }
                        ?>
                        
                        <form action="user_izin_process.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Pengajuan</label>
                                    <select class="form-control" name="jenis_izin" id="jenis_izin" required>
                                        <option value="Izin">Izin</option>
                                        <option value="Sakit">Sakit</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control border px-2" name="tanggal_mulai" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control border px-2" name="tanggal_selesai" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <textarea class="form-control border px-2" name="keterangan" rows="4" required placeholder="Jelaskan alasan pengajuan..."></textarea>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Bukti Foto (Surat Dokter/Lainnya) - Optional</label>
                                    <input type="file" class="form-control border px-2" name="bukti_foto" accept="image/*,.pdf">
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                                    <a href="index.php" class="btn btn-secondary">Batal</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <?php include 'view/part_footer.php'; ?>
      <?php include 'view/part_bottomnav.php'; ?>
    </div>
  </main>
  
  <!--   Core JS Files   -->
  <script src="../../assets/js/core/popper.min.js"></script>
  <script src="../../assets/js/core/bootstrap.min.js"></script>
  <script src="../../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
    
    // Initialize Choices.js
    const element = document.querySelector('#jenis_izin');
    const choices = new Choices(element, {
       searchEnabled: false,
       itemSelectText: '',
    });
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../assets/js/material-dashboard.min.js?v=3.0.4"></script>
</body>

</html>