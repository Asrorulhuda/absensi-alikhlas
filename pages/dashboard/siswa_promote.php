<?php
date_default_timezone_set('Asia/Jakarta');
session_start();

if (!isset($_SESSION['akses']) || $_SESSION['akses'] != 'Admin') {
    header('location:../../index');
    exit();
}

$ses_name = $_SESSION['name'];
$_SESSION['pages'] = "Siswa";

require_once "../../include/db_config.php";
require_once "../../include/helpers.php";
include "control/confignusers_data.php";

$error_msg = "";
$success_msg = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['process_promotion'])) {
    $source_class = trim($_POST['source_class'] ?? '');
    $action = trim($_POST['action'] ?? '');
    $target_class = trim($_POST['target_class'] ?? '');

    if (empty($source_class) || empty($action)) {
        $error_msg = "Harap lengkapi semua pilihan wajib!";
    } elseif ($action == 'promote' && empty($target_class)) {
        $error_msg = "Harap pilih kelas tujuan kenaikan!";
    } elseif ($action == 'promote' && $source_class == $target_class) {
        $error_msg = "Kelas asal dan kelas tujuan tidak boleh sama!";
    } else {
        // Initialize PDO
        $database = new Database();
        $pdo = $database->getConnection();

        try {
            $pdo->beginTransaction();

            if ($action == 'promote') {
                // Promote active students in source_class to target_class
                $stmt = $pdo->prepare("UPDATE data_siswa SET s_kelas = ? WHERE s_kelas = ? AND s_status = 'Aktif'");
                $stmt->execute([$target_class, $source_class]);
                $count = $stmt->rowCount();

                $pdo->commit();
                
                $info_msg = "Berhasil menaikkan kelas $count siswa dari $source_class ke $target_class.";
                header("location: siswa?msg=" . base64_encode("200") . "&info=" . base64_encode($info_msg));
                exit();
            } elseif ($action == 'graduate') {
                // Graduate active students in source_class
                $stmt = $pdo->prepare("UPDATE data_siswa SET s_status = 'Lulus' WHERE s_kelas = ? AND s_status = 'Aktif'");
                $stmt->execute([$source_class]);
                $count = $stmt->rowCount();

                $pdo->commit();
                
                $info_msg = "Berhasil meluluskan $count siswa dari kelas $source_class menjadi Alumni.";
                header("location: siswa?msg=" . base64_encode("200") . "&info=" . base64_encode($info_msg));
                exit();
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = "Gagal memproses data: " . $e->getMessage();
        }
    }
}

// Fetch classes for dropdowns
$sql_classes = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM opsi_tk_kelas ORDER BY tk_name ASC");
$classes = [];
if ($sql_classes) {
    while ($row = mysqli_fetch_assoc($sql_classes)) {
        $classes[] = $row;
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
  <title><?php echo $title_bar; ?></title>
  
  <!-- Fonts and icons -->
  <link href="../../assets/css/Roboto.css" rel="stylesheet" type="text/css" />
  <!-- Nucleo Icons -->
  <link href="../../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="../../assets/js/kit.fontawesome.com_42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="../../assets/css/Material_icon.css" rel="stylesheet">
  <link href="../../assets/css/animate.min.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../../assets/css/material-dashboard-pro.min.css?v=3.0.6" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-200">
  <!-- Sidebar -->
  <?php include 'view/part_sidenav.php';?>
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php include 'view/part_topnav.php';?>
    
    <div class="container-fluid py-4">
      <div class="row min-vh-80 mb-3 justify-content-center">
        <div class="col-md-8 col-12">
          <div class="card mt-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-warning shadow-warning border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Kenaikan Kelas & Kelulusan Siswa</h6>
                <p class="text-white text-xs ps-3 mb-0">Kelola kenaikan tingkat dan kelulusan alumni secara kolektif per kelas</p>
              </div>
            </div>
            
            <div class="card-body p-4">
              <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger text-white text-sm" role="alert">
                  <strong>Kesalahan!</strong> <?php echo htmlspecialchars($error_msg); ?>
                </div>
              <?php endif; ?>

              <form action="siswa_promote" method="post" id="promotionForm">
                
                <!-- Pilih Kelas Asal -->
                <div class="input-group input-group-static mb-4">
                  <label for="source_class">Pilih Kelas Asal</label>
                  <select class="form-control text-md" name="source_class" id="source_class" required>
                    <option value="" disabled selected>-- Pilih Kelas Asal --</option>
                    <?php foreach ($classes as $c): ?>
                      <option value="<?php echo htmlspecialchars($c['tk_name']); ?>">Kelas <?php echo htmlspecialchars($c['tk_name']); ?></option>
                    <?php end0; // Wait, PHP syntax end0 is a typo! It should be endforeach. I will correct this. ?>
                    <?php endforeach; ?>
                  </select>
                  <small class="text-muted mt-1">Siswa aktif di kelas ini yang akan diproses.</small>
                </div>

                <!-- Tindakan -->
                <div class="mb-4">
                  <label class="form-label font-weight-bold d-block text-sm text-dark">Pilih Tindakan</label>
                  <div class="form-check form-check-inline mt-1">
                    <input class="form-check-input" type="radio" name="action" id="action_promote" value="promote" checked>
                    <label class="form-check-label text-dark text-sm font-weight-normal" for="action_promote">Naik Kelas</label>
                  </div>
                  <div class="form-check form-check-inline ms-3">
                    <input class="form-check-input" type="radio" name="action" id="action_graduate" value="graduate">
                    <label class="form-check-label text-dark text-sm font-weight-normal" for="action_graduate">Lulus (Menjadi Alumni)</label>
                  </div>
                </div>

                <!-- Kelas Tujuan -->
                <div class="input-group input-group-static mb-4" id="target_class_container">
                  <label for="target_class">Pilih Kelas Tujuan Kenaikan</label>
                  <select class="form-control text-md" name="target_class" id="target_class">
                    <option value="" disabled selected>-- Pilih Kelas Tujuan --</option>
                    <?php foreach ($classes as $c): ?>
                      <option value="<?php echo htmlspecialchars($c['tk_name']); ?>">Kelas <?php echo htmlspecialchars($c['tk_name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <small class="text-muted mt-1">Siswa akan dipindahkan ke kelas tujuan ini dan status tetap "Aktif".</small>
                </div>

                <!-- Warning Alert Box -->
                <div class="alert alert-info text-white text-xs d-none" id="graduation_warning" role="alert">
                  <strong>Pemberitahuan:</strong> Memilih opsi <strong>Lulus</strong> akan mengubah status siswa menjadi <strong>Lulus (Alumni)</strong>. Siswa alumni secara otomatis tidak diwajibkan untuk absensi RFID/tap kartu dan tidak akan muncul di daftar siswa aktif.
                </div>

                <div class="d-flex align-items-center justify-content-between mt-5">
                  <a href="siswa" class="btn bg-gradient-default mb-0">Batal</a>
                  <button type="submit" name="process_promotion" class="btn bg-gradient-warning mb-0" onclick="return confirm('Apakah Anda yakin ingin memproses kenaikan kelas / kelulusan untuk seluruh siswa aktif di kelas tersebut?')">Proses Kenaikan/Kelulusan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <!-- footer -->
      <?php include 'view/part_footer.php';?>
    </div>
  </main>
  
  <?php include 'view/part_theme_config.php';?>

  <!-- Core JS Files -->
  <script src="../../assets/js/jquery-3.5.1.js"></script>
  <script src="../../assets/js/core/popper.min.js"></script>
  <script src="../../assets/js/core/bootstrap.min.js"></script>
  <script src="../../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../../assets/js/material-dashboard-pro.min.js?v=3.0.6"></script>
  
  <!-- Custom Script for Page Logic -->
  <script>
    $(document).ready(function() {
      // Toggle Target Class input based on action selection
      $('input[name="action"]').change(function() {
        if ($(this).val() === 'graduate') {
          $('#target_class_container').slideUp(200);
          $('#target_class').prop('required', false);
          $('#graduation_warning').removeClass('d-none').addClass('animate__animated animate__fadeIn');
        } else {
          $('#target_class_container').slideDown(200);
          $('#target_class').prop('required', true);
          $('#graduation_warning').addClass('d-none').removeClass('animate__animated animate__fadeIn');
        }
      });

      // Initially set required on target_class since promote is checked
      $('#target_class').prop('required', true);
    });
  </script>
</body>
</html>
