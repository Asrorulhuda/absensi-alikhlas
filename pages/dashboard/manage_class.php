<?php 
	session_start();
    date_default_timezone_set("Asia/Jakarta");
	if ($_SESSION['akses'] != 'Guru') {
        header('location:../../index'); 
        exit();
    }
	$ses_name = $_SESSION['name'];
	$_SESSION['pages'] = "Kelas";
	
	require_once "../../include/db_config.php";
    include "control/confignusers_data.php";
    
    // Initialize PDO
    $database = new Database();
    $pdo = $database->getConnection();

    $user_id = $_SESSION['id'];
    
    // Check for Homeroom Class (Wali Kelas)
    $homeroom_class = null;
    $homeroom_students = [];
    $attendance_map = [];
    
    // Get id_guru from users table
    $stmt_u = $pdo->prepare("SELECT id_guru FROM users WHERE id = ?");
    $stmt_u->execute([$user_id]);
    $u_data = $stmt_u->fetch();
    
    // Handle Date Filter
    $selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
    
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
            
            // Get attendance for selected date
            if (count($homeroom_students) > 0) {
                $uids = array_column($homeroom_students, 's_uid');
                $placeholders = str_repeat('?,', count($uids) - 1) . '?';
                $stmt_att = $pdo->prepare("SELECT uid, keterangan, jam_masuk FROM data_absen WHERE tanggal = ? AND uid IN ($placeholders)");
                $params = array_merge([$selected_date], $uids);
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
      
      <?php if ($homeroom_class): ?>
      <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center ps-3 pe-3">
                            <h6 class="text-white text-capitalize mb-0">Manajemen Kelas: <?php echo $homeroom_class; ?></h6>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="p-3">
                        <!-- Date Filter -->
                        <form action="" method="GET" class="mb-4">
                            <div class="row align-items-end">
                                <div class="col-md-4 col-12 mb-3 mb-md-0">
                                    <label class="form-label">Pilih Tanggal Absensi</label>
                                    <input type="date" name="date" class="form-control border p-2" value="<?php echo $selected_date; ?>" onchange="this.form.submit()">
                                </div>
                            </div>
                        </form>
                        
                        <?php
                        if(isset($_GET['att_msg'])) {
                            if($_GET['att_msg'] == 'success') echo '<div class="alert alert-success text-white">Data absensi berhasil disimpan.</div>';
                            if($_GET['att_msg'] == 'error') echo '<div class="alert alert-danger text-white">Gagal menyimpan data.</div>';
                        }
                        ?>
                        
                        <form action="guru_attendance_process.php" method="POST">
                            <input type="hidden" name="attendance_date" value="<?php echo $selected_date; ?>">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Siswa</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Saat Ini</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Input Absensi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($homeroom_students as $student): 
                                            $uid = $student['s_uid'];
                                            $current_status = isset($attendance_map[$uid]) ? $attendance_map[$uid]['keterangan'] : 'Belum Absen';
                                            $badge_color = 'secondary';
                                            if ($current_status == 'HADIR' || $current_status == 'COMPLETE' || $current_status == 'ABSEN') $badge_color = 'success';
                                            elseif ($current_status == 'IZIN') $badge_color = 'info';
                                            elseif ($current_status == 'SAKIT') $badge_color = 'warning';
                                            elseif ($current_status == 'ALPHA') $badge_color = 'danger';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <?php if(!empty($student['s_picture']) && file_exists("../../assets/img/user/".$student['s_picture'])): ?>
                                                            <img src="../../assets/img/user/<?php echo $student['s_picture']; ?>" class="avatar avatar-sm me-3 border-radius-lg" alt="user">
                                                        <?php else: ?>
                                                            <img src="../../assets/img/user.png" class="avatar avatar-sm me-3 border-radius-lg" alt="user">
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($student['s_nama']); ?></h6>
                                                        <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($student['s_nis']); ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="badge badge-sm bg-gradient-<?php echo $badge_color; ?>"><?php echo $current_status; ?></span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="status[<?php echo $uid; ?>]" value="HADIR" id="h_<?php echo $uid; ?>" <?php echo ($current_status == 'HADIR' || $current_status == 'ABSEN' || $current_status == 'COMPLETE') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="h_<?php echo $uid; ?>">Hadir</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="status[<?php echo $uid; ?>]" value="IZIN" id="i_<?php echo $uid; ?>" <?php echo ($current_status == 'IZIN') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="i_<?php echo $uid; ?>">Izin</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="status[<?php echo $uid; ?>]" value="SAKIT" id="s_<?php echo $uid; ?>" <?php echo ($current_status == 'SAKIT') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="s_<?php echo $uid; ?>">Sakit</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="status[<?php echo $uid; ?>]" value="ALPHA" id="a_<?php echo $uid; ?>" <?php echo ($current_status == 'ALPHA') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="a_<?php echo $uid; ?>">Alpha</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary">Simpan Absensi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <?php else: ?>
        <div class="alert alert-warning text-white">
            Anda belum terdaftar sebagai Wali Kelas. Silakan hubungi Admin.
        </div>
      <?php endif; ?>

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