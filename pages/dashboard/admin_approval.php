<?php 
	session_start();
    date_default_timezone_set("Asia/Jakarta");
	if ($_SESSION['akses'] != 'Admin') {
        header('location:../../index'); 
        exit();
    }
	$ses_name = $_SESSION['name'];
	$_SESSION['pages'] = "Manajemen"; // Highlight Manajemen in sidebar
	
	require_once "../../include/db_config.php";
    include "control/confignusers_data.php";
    
    // Initialize PDO
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Fetch Pending Requests
    try {
        $sql = "SELECT p.*, u.name as nama_guru 
                FROM pengajuan_izin p 
                JOIN users u ON p.uid = u.id 
                WHERE p.status = 'Pending' 
                ORDER BY p.created_at ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $requests = $stmt->fetchAll();
    } catch (PDOException $e) {
        $requests = [];
        // echo $e->getMessage();
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
                <h6 class="text-white text-capitalize ps-3">Persetujuan Izin Guru (Pending)</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              
              <?php
                if(isset($_GET['msg'])) {
                    $msg = $_GET['msg'];
                    if($msg == 'approved') {
                        echo '<div class="alert alert-success text-white mx-4">Pengajuan berhasil disetujui.</div>';
                    } elseif($msg == 'rejected') {
                        echo '<div class="alert alert-danger text-white mx-4">Pengajuan ditolak.</div>';
                    } elseif($msg == 'error') {
                        echo '<div class="alert alert-warning text-white mx-4">Terjadi kesalahan.</div>';
                    }
                }
              ?>

              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Guru</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Jenis</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Keterangan</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Bukti</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($requests) > 0): ?>
                        <?php foreach ($requests as $req): ?>
                        <tr>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($req['nama_guru']); ?></h6>
                                        <p class="text-xs text-secondary mb-0"><?php echo date('d M Y H:i', strtotime($req['created_at'])); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0"><?php echo htmlspecialchars($req['jenis_izin']); ?></p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">
                                    <?php echo date('d M', strtotime($req['tanggal_mulai'])); ?> - 
                                    <?php echo date('d M Y', strtotime($req['tanggal_selesai'])); ?>
                                </p>
                            </td>
                            <td>
                                <p class="text-xs text-secondary mb-0" style="max-width: 200px; white-space: normal;">
                                    <?php echo htmlspecialchars($req['keterangan']); ?>
                                </p>
                            </td>
                            <td>
                                <?php if (!empty($req['bukti_foto'])): ?>
                                    <a href="../../<?php echo $req['bukti_foto']; ?>" target="_blank" class="btn btn-link text-info text-xs p-0 my-0">Lihat</a>
                                <?php else: ?>
                                    <span class="text-xs text-secondary">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle text-center">
                                <a href="admin_approval_process.php?id=<?php echo $req['id']; ?>&action=approve" class="btn btn-success btn-sm px-3 mb-0" onclick="return confirm('Setujui pengajuan ini?')">
                                    <i class="material-icons text-sm">check</i>
                                </a>
                                <a href="admin_approval_process.php?id=<?php echo $req['id']; ?>&action=reject" class="btn btn-danger btn-sm px-3 mb-0" onclick="return confirm('Tolak pengajuan ini?')">
                                    <i class="material-icons text-sm">close</i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">Tidak ada pengajuan pending.</td>
                        </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
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
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../assets/js/material-dashboard.min.js?v=3.0.4"></script>
</body>

</html>