<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
if (!isset($_SESSION['akses']) || $_SESSION['akses'] != 'Admin') {
    header('location: ../../index');
    exit();
}
$_SESSION['pages'] = "Manajemen";
require_once "../../include/db_config.php";
include "control/confignusers_data.php";

$database = new Database();
$pdo = $database->getConnection();

$eskul_id = isset($_GET['id']) ? $_GET['id'] : 0;
if($eskul_id == 0){
    header("Location: manage_eskul.php");
    exit();
}

// Get Eskul Info
$stmtEskul = $pdo->prepare("SELECT * FROM eskul WHERE id = ?");
$stmtEskul->execute([$eskul_id]);
$eskul = $stmtEskul->fetch(PDO::FETCH_ASSOC);

if(!$eskul){
    header("Location: manage_eskul.php");
    exit();
}

// Handle Add Member
if(isset($_POST['add_member'])){
    $siswa_id = $_POST['siswa_id'];
    
    // Check if already member
    $check = $pdo->prepare("SELECT id FROM eskul_siswa WHERE eskul_id=? AND siswa_id=?");
    $check->execute([$eskul_id, $siswa_id]);
    
    if($check->rowCount() > 0){
        $err = "Siswa sudah terdaftar di eskul ini!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO eskul_siswa (eskul_id, siswa_id) VALUES (?, ?)");
        $stmt->execute([$eskul_id, $siswa_id]);
        $msg = "Anggota berhasil ditambahkan!";
    }
}

// Handle Delete Member
if(isset($_GET['del_member'])){
    $member_id = $_GET['del_member'];
    $pdo->prepare("DELETE FROM eskul_siswa WHERE id=?")->execute([$member_id]);
    header("Location: manage_eskul_members.php?id=$eskul_id");
    exit();
}

// Get Members
$sqlMembers = "SELECT es.id as membership_id, s.s_nama, s.s_kelas, s.s_nis 
               FROM eskul_siswa es 
               JOIN data_siswa s ON es.siswa_id = s.s_id 
               WHERE es.eskul_id = ? 
               ORDER BY s.s_kelas ASC, s.s_nama ASC";
$stmtMembers = $pdo->prepare($sqlMembers);
$stmtMembers->execute([$eskul_id]);
$members = $stmtMembers->fetchAll(PDO::FETCH_ASSOC);

// Get Distinct Classes
$stmtClasses = $pdo->query("SELECT DISTINCT s_kelas FROM data_siswa WHERE s_status='Aktif' ORDER BY s_kelas ASC");
$classes = $stmtClasses->fetchAll(PDO::FETCH_COLUMN);

// Get Students for Dropdown (Filtered)
$kelas_filter = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$sqlStudents = "SELECT s_id, s_nama, s_kelas FROM data_siswa WHERE s_status='Aktif'";
if($kelas_filter){
    $sqlStudents .= " AND s_kelas = '$kelas_filter'";
}
$sqlStudents .= " ORDER BY s_kelas ASC, s_nama ASC";
$stmtStudents = $pdo->query($sqlStudents);
$students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="../../<?php echo $icon_bar; ?>">
  <title><?php echo $title_bar; ?></title>
  <link href="../../assets/css/Roboto.css" rel="stylesheet" type="text/css" />
  <link href="../../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="../../assets/js/kit.fontawesome.com_42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="../../assets/css/Material_icon.css" rel="stylesheet">
  <link href="../../assets/css/animate.min.css" rel="stylesheet" />
  <link id="pagestyle" href="../../assets/css/material-dashboard-pro.min.css?v=3.0.6" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>
  
  <?php include "view/part_sidenav.php"; ?>
  
  <main class="main-content position-relative border-radius-lg ">
    <?php include "view/part_topnav.php"; ?>
    
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <a href="manage_eskul.php" class="btn btn-light mb-0"><i class="material-icons text-sm me-2">arrow_back</i> Kembali ke Daftar Eskul</a>
                <h4 class="text-white mt-3">Anggota Eskul: <?= $eskul['name'] ?> (<?= $eskul['day_name'] ?>)</h4>
            </div>
        </div>
      <div class="row">
        <div class="col-md-4">
          <div class="card mb-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Tambah Anggota</h6>
              </div>
            </div>
            <div class="card-body">
              <?php if(isset($msg)) echo "<div class='alert alert-success text-white' role='alert'>$msg</div>"; ?>
              <?php if(isset($err)) echo "<div class='alert alert-danger text-white' role='alert'>$err</div>"; ?>
              <form method="POST">
                <div class="input-group input-group-outline mb-3 is-filled">
                    <label class="form-label">Filter Kelas</label>
                    <select id="filter-kelas" class="form-control" onchange="window.location.href='?id=<?= $eskul_id ?>&kelas=' + this.value">
                        <option value="">Semua Kelas</option>
                        <?php foreach($classes as $c): ?>
                            <option value="<?= $c ?>" <?= $kelas_filter == $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Pilih Siswa</label>
                    <select name="siswa_id" id="choices-student" class="form-control" required>
                        <option value="" placeholder>Cari siswa...</option>
                        <?php foreach($students as $s): ?>
                            <option value="<?= $s['s_id'] ?>"><?= $s['s_nama'] ?> (<?= $s['s_kelas'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="add_member" class="btn bg-gradient-primary w-100 mt-3">Tambahkan Siswa</button>
              </form>
            </div>
          </div>
        </div>
        
        <div class="col-md-8">
          <div class="card mb-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Daftar Anggota</h6>
              </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Siswa</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kelas</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">NIS</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(count($members) > 0): ?>
                        <?php foreach($members as $m): ?>
                        <tr>
                        <td>
                            <div class="d-flex px-3 py-1">
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm"><?= $m['s_nama'] ?></h6>
                            </div>
                            </div>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold"><?= $m['s_kelas'] ?></span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold"><?= $m['s_nis'] ?></span>
                        </td>
                        <td class="align-middle text-center">
                            <a href="?id=<?= $eskul_id ?>&del_member=<?= $m['membership_id'] ?>" class="btn btn-sm btn-danger mb-0" onclick="return confirm('Hapus siswa ini dari eskul?')">
                            <i class="material-icons text-sm">remove_circle</i>&nbsp; Hapus
                            </a>
                        </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <span class="text-secondary text-sm">Belum ada anggota terdaftar.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php include "view/part_footer.php"; ?>
      <?php include 'view/part_bottomnav.php'; ?>
    </div>
  </main>
  
  <!-- Core JS Files -->
  <script src="../../assets/js/core/popper.min.js"></script>
  <script src="../../assets/js/core/bootstrap.min.js"></script>
  <script src="../../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../../assets/js/plugins/choices.min.js"></script>
  <script>
    if (document.getElementById('choices-student')) {
      var element = document.getElementById('choices-student');
      const example = new Choices(element, {
        searchEnabled: true,
        itemSelectText: ''
      });
    }
    
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script src="../../assets/js/material-dashboard-pro.min.js?v=3.0.6"></script>
</body>
</html>
