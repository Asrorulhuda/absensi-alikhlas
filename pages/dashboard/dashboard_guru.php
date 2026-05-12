<?php 
	session_start();
    date_default_timezone_set("Asia/Jakarta");
	if ($_SESSION['akses'] != 'Guru') {
        header('location:../../index'); 
        exit();
    }
	$ses_name = $_SESSION['name'];
	$_SESSION['pages'] = "Dashboard";
	
	require_once "../../include/db_config.php";
    include "control/confignusers_data.php";
    
    // Initialize PDO
    $database = new Database();
    $pdo = $database->getConnection();

    $user_id = $_SESSION['id'];

    // Get Guru Name (optional, if we want to show it specifically)
    // $stmt = $pdo->prepare("SELECT nama_guru FROM data_guru WHERE id = ?"); // Assuming data_guru has id matching id_guru
    
    // Fetch recent requests
    $stmt = $pdo->prepare("SELECT * FROM pengajuan_izin WHERE uid = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $requests = $stmt->fetchAll();
    
    // Check for Homeroom Class (Wali Kelas)
    $homeroom_class = null;
    $homeroom_students = [];
    $attendance_map = [];
    
    // Get id_guru from users table
    $stmt_u = $pdo->prepare("SELECT id_guru FROM users WHERE id = ?");
    $stmt_u->execute([$user_id]);
    $u_data = $stmt_u->fetch();
    
    if ($u_data && !empty($u_data['id_guru'])) {
        // Get class from data_guru
        $stmt_g = $pdo->prepare("SELECT g_homeroom_class FROM data_guru WHERE g_id = ?");
        $stmt_g->execute([$u_data['id_guru']]);
        $g_data = $stmt_g->fetch();
        
        if ($g_data && !empty($g_data['g_homeroom_class'])) {
            $homeroom_class = $g_data['g_homeroom_class'];
            
            // Get students
            $stmt_s = $pdo->prepare("SELECT s_nama, s_nis, s_uid, s_picture FROM data_siswa WHERE s_kelas = ? ORDER BY s_nama ASC");
            $stmt_s->execute([$homeroom_class]);
            $homeroom_students = $stmt_s->fetchAll();
            
            // Get today's attendance
            $today = date('Y-m-d');
            // We need to fetch for all students in this class
            // Using a loop or IN clause. Since we have students array, let's extract UIDs.
            if (count($homeroom_students) > 0) {
                $uids = array_column($homeroom_students, 's_uid');
                $placeholders = str_repeat('?,', count($uids) - 1) . '?';
                $stmt_att = $pdo->prepare("SELECT uid, keterangan, jam_masuk FROM data_absen WHERE tanggal = ? AND uid IN ($placeholders)");
                $params = array_merge([$today], $uids);
                $stmt_att->execute($params);
                while ($row = $stmt_att->fetch()) {
                    $attendance_map[$row['uid']] = $row;
                }
            }
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
  
  <!-- PWA Manifest & Meta -->
  <link rel="manifest" href="../../manifest.json">
  <meta name="theme-color" content="#e91e63">

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
  
  <style>
    @media (max-width: 768px) {
        /* Mobile layout adjustments */
        .container-fluid {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
        
        /* Transform tables to cards */
        .table-responsive thead {
            display: none;
        }
        
        .table-responsive tbody tr {
            display: block;
            margin-bottom: 1rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(0,0,0,0.05);
            padding: 0.5rem;
        }
        
        .table-responsive td {
            display: block;
            width: 100%;
            text-align: left !important;
            padding: 0.75rem 1rem !important;
            border-bottom: 1px solid #f0f2f5;
        }
        
        .table-responsive td:last-child {
            border-bottom: none;
        }

        /* Specific styles for Attendance Table */
         /* Student Info Section */
         form .table-responsive td:first-child {
             background-color: #f8f9fa;
             border-radius: 8px 8px 0 0;
             margin-bottom: 5px;
         }
 
         /* Status Badge Section */
         form .table-responsive td:nth-child(2) {
              display: flex;
              justify-content: space-between;
              align-items: center;
         }
         form .table-responsive td:nth-child(2):before {
             content: "Status Saat Ini:";
             font-weight: 600;
             font-size: 0.85rem;
             color: #7b809a;
         }
 
         /* Input Radio Buttons Section - Grid Layout */
         form .table-responsive td:nth-child(3) {
             padding-top: 15px !important;
             display: grid !important;
             grid-template-columns: 1fr 1fr;
             gap: 10px;
         }
         
         form .form-check-inline {
             display: flex;
             width: 100%; 
             margin: 0;
             align-items: center;
             padding: 8px;
             border: 1px solid #f0f2f5;
             border-radius: 8px;
         }
        
        /* Make the Submit button sticky or full width */
        .card-body .text-end {
            text-align: center !important;
        }
        .card-body .text-end button {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
        }
        
        /* Adjustments for History Table */
        /* We need to distinguish between the two tables if possible, or use generic :nth-child rules that work for both */
        /* The history table has 6 columns. The attendance table has 3. */
        
        /* Helper to show labels for History Table */
        /* Since we can't easily add data-labels without editing HTML, we'll rely on the visual stack */
    }
  </style>
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
                        <h6 class="text-white text-capitalize ps-3">Selamat Datang, <?php echo $ses_name; ?></h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="p-4">
                        <p>Selamat datang di Dashboard Guru. Di sini Anda dapat melihat status absensi dan mengajukan izin/sakit.</p>
                        <a href="guru_izin.php" class="btn btn-info">
                            <i class="material-icons text-sm">add</i> Ajukan Izin/Sakit
                        </a>
                        <?php if(!empty($homeroom_class)): ?>
                        <a href="manage_class.php" class="btn btn-primary ms-2">
                            <i class="material-icons text-sm">school</i> Manajemen Kelas
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Riwayat Pengajuan Izin/Sakit Terakhir</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal Pengajuan</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Jenis</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal Mulai</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sampai</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($requests) > 0): ?>
                        <?php foreach ($requests as $req): ?>
                        <tr>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm"><?php echo date('d M Y H:i', strtotime($req['created_at'])); ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0"><?php echo $req['jenis_izin']; ?></p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0"><?php echo date('d M Y', strtotime($req['tanggal_mulai'])); ?></p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0"><?php echo date('d M Y', strtotime($req['tanggal_selesai'])); ?></p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <?php 
                                    $status_class = 'bg-gradient-secondary';
                                    if ($req['status'] == 'Pending') $status_class = 'bg-gradient-warning';
                                    elseif ($req['status'] == 'Disetujui') $status_class = 'bg-gradient-success';
                                    elseif ($req['status'] == 'Ditolak') $status_class = 'bg-gradient-danger';
                                ?>
                                <span class="badge badge-sm <?php echo $status_class; ?>"><?php echo $req['status']; ?></span>
                            </td>
                            <td class="align-middle">
                                <!-- Details link could go here -->
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">Belum ada data pengajuan.</td>
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