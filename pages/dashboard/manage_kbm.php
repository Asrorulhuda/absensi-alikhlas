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

// Handle Save Siswa
if(isset($_POST['save_schedule'])){
    $kelas = $_POST['kelas'];
    $hari_selected = isset($_POST['hari']) ? $_POST['hari'] : [];
    $masuk = $_POST['jam_masuk'];
    $pulang = $_POST['jam_pulang'];
    $batas = $_POST['batas_masuk'];
    
    if(!empty($hari_selected)){
        foreach($hari_selected as $hari){
            // Check if exists
            $check = $pdo->prepare("SELECT id FROM kbm_schedules WHERE class_name = ? AND day_name = ?");
            $check->execute([$kelas, $hari]);
            
            if($check->rowCount() > 0){
                $sql = "UPDATE kbm_schedules SET start_time=?, end_time=?, entry_limit=? WHERE class_name=? AND day_name=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$masuk, $pulang, $batas, $kelas, $hari]);
            } else {
                $sql = "INSERT INTO kbm_schedules (class_name, day_name, start_time, end_time, entry_limit) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$kelas, $hari, $masuk, $pulang, $batas]);
            }
        }
        $msg = "Jadwal Siswa untuk ".count($hari_selected)." hari berhasil disimpan!";
    } else {
        $msg = "Error: Silakan pilih setidaknya satu hari!";
    }
}

// Handle Save Guru
if(isset($_POST['save_guru_schedule'])){
    $g_uid = !empty($_POST['g_uid']) ? $_POST['g_uid'] : null;
    $hari_selected = isset($_POST['hari']) ? $_POST['hari'] : [];
    $masuk = $_POST['jam_masuk'];
    $pulang = $_POST['jam_pulang'];
    $batas = $_POST['batas_masuk'];
    
    if(!empty($hari_selected)){
        foreach($hari_selected as $hari){
            // Check if exists
            if($g_uid === null){
                $check = $pdo->prepare("SELECT id FROM guru_schedules WHERE g_uid IS NULL AND day_name = ?");
                $check->execute([$hari]);
            } else {
                $check = $pdo->prepare("SELECT id FROM guru_schedules WHERE g_uid = ? AND day_name = ?");
                $check->execute([$g_uid, $hari]);
            }
            
            if($check->rowCount() > 0){
                if($g_uid === null){
                    $sql = "UPDATE guru_schedules SET start_time=?, end_time=?, entry_limit=? WHERE g_uid IS NULL AND day_name=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$masuk, $pulang, $batas, $hari]);
                } else {
                    $sql = "UPDATE guru_schedules SET start_time=?, end_time=?, entry_limit=? WHERE g_uid=? AND day_name=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$masuk, $pulang, $batas, $g_uid, $hari]);
                }
            } else {
                $sql = "INSERT INTO guru_schedules (g_uid, day_name, start_time, end_time, entry_limit) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$g_uid, $hari, $masuk, $pulang, $batas]);
            }
        }
        $msg_guru = "Jadwal Guru untuk ".count($hari_selected)." hari berhasil disimpan!";
    } else {
        $msg_guru = "Error: Silakan pilih setidaknya satu hari!";
    }
}

// Handle Delete Siswa
if(isset($_GET['del'])){
    $id = $_GET['del'];
    $pdo->prepare("DELETE FROM kbm_schedules WHERE id=?")->execute([$id]);
    header("Location: manage_kbm.php");
    exit();
}

// Handle Delete Guru
if(isset($_GET['del_guru'])){
    $id = $_GET['del_guru'];
    $pdo->prepare("DELETE FROM guru_schedules WHERE id=?")->execute([$id]);
    header("Location: manage_kbm.php?tab=guru");
    exit();
}

// Get Distinct Classes
$stmtClass = $pdo->query("SELECT DISTINCT s_kelas FROM data_siswa WHERE s_kelas != '' ORDER BY s_kelas ASC");
$classes = $stmtClass->fetchAll(PDO::FETCH_ASSOC);

// Get Guru
$stmtGuru = $pdo->query("SELECT g_uid, g_nama FROM data_guru ORDER BY g_nama ASC");
$gurus = $stmtGuru->fetchAll(PDO::FETCH_ASSOC);

// Get Existing Schedules Siswa
$stmtSched = $pdo->query("SELECT * FROM kbm_schedules ORDER BY class_name, FIELD(day_name, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')");
$schedules = $stmtSched->fetchAll(PDO::FETCH_ASSOC);

// Get Existing Guru Schedules
$stmtGuruSched = $pdo->query("SELECT gs.*, g.g_nama FROM guru_schedules gs LEFT JOIN data_guru g ON gs.g_uid = g.g_uid ORDER BY FIELD(gs.day_name, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), gs.g_uid");
$guru_schedules = $stmtGuruSched->fetchAll(PDO::FETCH_ASSOC);

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : (isset($msg_guru) ? 'guru' : 'siswa');

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
        <div class="col-lg-4 col-md-6 col-12">
          <div class="nav-wrapper position-relative end-0">
            <ul class="nav nav-pills nav-fill p-1" role="tablist">
              <li class="nav-item">
                <a class="nav-link mb-0 px-0 py-1 <?= $active_tab == 'siswa' ? 'active' : '' ?>" data-bs-toggle="tab" href="#siswa-tab" role="tab" aria-controls="siswa" aria-selected="<?= $active_tab == 'siswa' ? 'true' : 'false' ?>">
                  <i class="material-icons text-lg position-relative">person</i>
                  <span class="ms-1">Jadwal Siswa</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link mb-0 px-0 py-1 <?= $active_tab == 'guru' ? 'active' : '' ?>" data-bs-toggle="tab" href="#guru-tab" role="tab" aria-controls="guru" aria-selected="<?= $active_tab == 'guru' ? 'true' : 'false' ?>">
                  <i class="material-icons text-lg position-relative">school</i>
                  <span class="ms-1">Jadwal Guru</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="tab-content">
        <!-- TAB SISWA -->
        <div class="tab-pane fade <?= $active_tab == 'siswa' ? 'show active' : '' ?>" id="siswa-tab" role="tabpanel">
          <div class="row">
            <div class="col-md-4">
              <div class="card mb-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                  <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Tambah/Edit Jadwal Siswa</h6>
                  </div>
                </div>
                <div class="card-body">
                  <?php if(isset($msg)) echo "<div class='alert alert-success text-white' role='alert'>$msg</div>"; ?>
                  <form method="POST">
                    <div class="input-group input-group-static my-3">
                        <label>Kelas</label>
                        <select name="kelas" class="form-control" required>
                            <option value="" selected disabled>Pilih Kelas</option>
                            <?php foreach($classes as $c): ?>
                                <option value="<?= $c['s_kelas'] ?>"><?= $c['s_kelas'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group input-group-static my-3">
                        <label>Hari (Bisa pilih banyak)</label>
                        <div class="d-flex flex-wrap mt-2">
                            <?php foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day): ?>
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" name="hari[]" value="<?= $day ?>" id="siswa_<?= $day ?>">
                                    <label class="custom-control-label mb-0" for="siswa_<?= $day ?>"><?= $day ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="input-group input-group-static my-3">
                        <label>Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="form-control" required>
                    </div>
                    <div class="input-group input-group-static my-3">
                        <label>Jam Pulang</label>
                        <input type="time" name="jam_pulang" class="form-control" required>
                    </div>
                    <div class="input-group input-group-static my-3">
                        <label>Batas Terlambat</label>
                        <input type="time" name="batas_masuk" class="form-control" required>
                    </div>
                    <small class="text-muted d-block mb-3">Siswa dianggap terlambat jika lewat jam ini.</small>
                    <button type="submit" name="save_schedule" class="btn bg-gradient-primary w-100">Simpan Jadwal</button>
                  </form>
                </div>
              </div>
            </div>
            
            <div class="col-md-8">
              <div class="card mb-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                  <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Daftar Jadwal KBM (Siswa)</h6>
                  </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                  <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                      <thead>
                        <tr>
                          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kelas</th>
                          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Hari</th>
                          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Masuk - Pulang</th>
                          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Batas</th>
                          <th class="text-secondary opacity-7"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($schedules as $s): ?>
                        <tr>
                          <td>
                            <div class="d-flex px-3 py-1">
                              <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm"><?= $s['class_name'] ?></h6>
                              </div>
                            </div>
                          </td>
                          <td>
                            <p class="text-xs font-weight-bold mb-0"><?= $s['day_name'] ?></p>
                          </td>
                          <td class="align-middle text-center text-sm">
                            <span class="badge badge-sm bg-gradient-success"><?= substr($s['start_time'],0,5) ?> - <?= substr($s['end_time'],0,5) ?></span>
                          </td>
                          <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold"><?= substr($s['entry_limit'],0,5) ?></span>
                          </td>
                          <td class="align-middle">
                            <a href="?del=<?= $s['id'] ?>" class="text-secondary font-weight-bold text-xs" onclick="return confirm('Hapus jadwal ini?')">
                              <i class="material-icons text-sm me-2">delete</i>Hapus
                            </a>
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
        </div>

        <!-- TAB GURU -->
        <div class="tab-pane fade <?= $active_tab == 'guru' ? 'show active' : '' ?>" id="guru-tab" role="tabpanel">
          <div class="row">
            <div class="col-md-4">
              <div class="card mb-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                  <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Tambah/Edit Jadwal Guru</h6>
                  </div>
                </div>
                <div class="card-body">
                  <?php if(isset($msg_guru)) echo "<div class='alert alert-success text-white' role='alert'>$msg_guru</div>"; ?>
                  <form method="POST">
                    <div class="input-group input-group-static my-3">
                        <label>Guru</label>
                        <select name="g_uid" class="form-control">
                            <option value="">-- Semua Guru --</option>
                            <?php foreach($gurus as $g): ?>
                                <option value="<?= $g['g_uid'] ?>"><?= $g['g_nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group input-group-static my-3">
                        <label>Hari (Bisa pilih banyak)</label>
                        <div class="d-flex flex-wrap mt-2">
                            <?php foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day): ?>
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" name="hari[]" value="<?= $day ?>" id="guru_<?= $day ?>">
                                    <label class="custom-control-label mb-0" for="guru_<?= $day ?>"><?= $day ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="input-group input-group-static my-3">
                        <label>Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="form-control" value="07:00" required>
                    </div>
                    <div class="input-group input-group-static my-3">
                        <label>Jam Pulang</label>
                        <input type="time" name="jam_pulang" class="form-control" value="16:00" required>
                    </div>
                    <div class="input-group input-group-static my-3">
                        <label>Batas Terlambat</label>
                        <input type="time" name="batas_masuk" class="form-control" value="08:00" required>
                    </div>
                    <button type="submit" name="save_guru_schedule" class="btn bg-gradient-primary w-100">Simpan Jadwal Guru</button>
                  </form>
                </div>
              </div>
            </div>
            
            <div class="col-md-8">
              <div class="card mb-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                  <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Daftar Jadwal Guru</h6>
                  </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                  <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                      <thead>
                        <tr>
                          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Guru</th>
                          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Hari</th>
                          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Masuk - Pulang</th>
                          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Batas</th>
                          <th class="text-secondary opacity-7"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($guru_schedules as $gs): ?>
                        <tr>
                          <td>
                            <div class="d-flex px-3 py-1">
                              <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm"><?= $gs['g_nama'] ?? '<span class="text-info">SEMUA GURU</span>' ?></h6>
                              </div>
                            </div>
                          </td>
                          <td>
                            <p class="text-xs font-weight-bold mb-0"><?= $gs['day_name'] ?></p>
                          </td>
                          <td class="align-middle text-center text-sm">
                            <span class="badge badge-sm bg-gradient-success"><?= substr($gs['start_time'],0,5) ?> - <?= substr($gs['end_time'],0,5) ?></span>
                          </td>
                          <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold"><?= substr($gs['entry_limit'],0,5) ?></span>
                          </td>
                          <td class="align-middle">
                            <a href="?del_guru=<?= $gs['id'] ?>" class="text-secondary font-weight-bold text-xs" onclick="return confirm('Hapus jadwal ini?')">
                              <i class="material-icons text-sm me-2">delete</i>Hapus
                            </a>
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
  <script src="../../assets/js/argon-dashboard.min.js?v=2.0.4"></script>
</body>
</html>
