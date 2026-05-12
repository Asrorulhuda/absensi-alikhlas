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

// Handle Save Eskul
if(isset($_POST['save_eskul'])){
    $name = $_POST['name'];
    $day = $_POST['day'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $desc = $_POST['description'];
    
    $sql = "INSERT INTO eskul (name, day_name, start_time, end_time, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $day, $start, $end, $desc]);
    $msg = "Eskul berhasil ditambahkan!";
}

// Handle Delete
if(isset($_GET['del'])){
    $id = $_GET['del'];
    $pdo->prepare("DELETE FROM eskul WHERE id=?")->execute([$id]);
    header("Location: manage_eskul.php");
    exit();
}

// Get Eskuls
$stmt = $pdo->query("SELECT * FROM eskul ORDER BY FIELD(day_name, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), start_time ASC");
$eskuls = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
      <div class="row">
        <div class="col-md-4">
          <div class="card mb-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Tambah Eskul Baru</h6>
              </div>
            </div>
            <div class="card-body">
              <?php if(isset($msg)) echo "<div class='alert alert-success text-white' role='alert'>$msg</div>"; ?>
              <form method="POST">
                <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Nama Eskul</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Hari</label>
                    <select name="day" class="form-control" required>
                        <option value="" selected disabled></option>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>
                <div class="input-group input-group-outline mb-3 is-filled">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="input-group input-group-outline mb-3 is-filled">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
                <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" name="save_eskul" class="btn bg-gradient-primary w-100"><i class="material-icons text-sm">save</i>&nbsp; Simpan Eskul</button>
              </form>
            </div>
          </div>
        </div>
        
        <div class="col-md-8">
          <div class="card mb-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Daftar Ekstrakurikuler</h6>
              </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Eskul</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hari & Waktu</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($eskuls as $e): ?>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?= $e['name'] ?></h6>
                            <p class="text-xs text-secondary mb-0"><?= substr($e['description'],0,30) ?></p>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold"><?= $e['day_name'] ?></span><br>
                        <span class="badge badge-sm bg-gradient-success"><?= substr($e['start_time'],0,5) ?> - <?= substr($e['end_time'],0,5) ?></span>
                      </td>
                      <td class="align-middle text-center">
                        <a href="manage_eskul_members.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-info mb-0"><i class="material-icons text-sm">groups</i>&nbsp; Kelola Anggota</a>
                        <a href="?del=<?= $e['id'] ?>" class="btn btn-sm btn-danger mb-0" onclick="return confirm('Hapus eskul ini?')"><i class="material-icons text-sm">delete</i>&nbsp; Hapus</a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
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
  <script>
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
