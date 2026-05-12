<?php 
date_default_timezone_set('Asia/Jakarta');
session_start();
if ( $_SESSION['akses']!= 'Admin'){// handling if dont'have session

	header('location:../../index'); 
	exit();
} 
$ses_name = $_SESSION['name'];
$_SESSION['pages']="System";

require_once "../../include/db_config.php";
require_once "../../include/helpers.php";
include "control/confignusers_data.php";

// Define variables and initialize with empty values


$__q_alter = "ALTER TABLE wa_notification 
  ADD COLUMN IF NOT EXISTS cnfg_template_izin TEXT,
  ADD COLUMN IF NOT EXISTS cnfg_template_sakit TEXT,
  ADD COLUMN IF NOT EXISTS cnfg_template_guru_izin TEXT,
  ADD COLUMN IF NOT EXISTS cnfg_no_kepsek VARCHAR(20)";
@mysqli_query($GLOBALS["___mysqli_ston"], $__q_alter);

$token ="";
$cnfg_sender ="";
$template="";
$cnfg_intro_kbm="";
$cnfg_intro_eskul="";
$cnfg_intro_kegiatan="";
$service_status="";
$cnfg_kbm="";
$cnfg_eskul="";
$cnfg_kegiatan="";
$id="";
$cnfg_template_guru_izin="";
$cnfg_no_kepsek="";

$token_err ="";
$template_err="";
$service_status_err="";
$id_err="";


// Processing form data when form is submitted
if(isset($_POST["cnfg_id"]) && !empty($_POST["cnfg_id"])){
	// Get hidden input value
	$cnfg_id = trim($_POST["cnfg_id"]);
	$cnfg_token = trim($_POST["token"]);
	$cnfg_sender = trim($_POST["sender"]);
	$cnfg_intro = trim($_POST["template"]);
	$cnfg_intro_kbm = trim($_POST["template_kbm"]);
	$cnfg_intro_eskul = trim($_POST["template_eskul"]);
	$cnfg_intro_kegiatan = trim($_POST["template_kegiatan"]);
	$cnfg_template_izin = isset($_POST["template_izin"]) ? trim($_POST["template_izin"]) : "";
	$cnfg_template_sakit = isset($_POST["template_sakit"]) ? trim($_POST["template_sakit"]) : "";
	$cnfg_template_guru_izin = isset($_POST["template_guru_izin"]) ? trim($_POST["template_guru_izin"]) : "";
	$cnfg_no_kepsek = isset($_POST["no_kepsek"]) ? trim($_POST["no_kepsek"]) : "";
	$cnfg_status = trim($_POST["cnfg_status"]);
	
	$cnfg_kbm = (isset($_POST['cnfg_kbm'])) ? 1 : 0;
	$cnfg_eskul = (isset($_POST['cnfg_eskul'])) ? 1 : 0;
	$cnfg_kegiatan = (isset($_POST['cnfg_kegiatan'])) ? 1 : 0;
		

	// Prepare an update statement
	$dsn = "mysql:host=$db_server;dbname=$db_name;charset=utf8mb4";
	$options = [
		PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
	];
	try {
		$pdo = new PDO($dsn, $db_user, $db_password, $options);
	} catch (Exception $e) {
		error_log($e->getMessage());
		exit('Something weird happened');
	}

	$vars = parse_columns('wa_notification', $_POST);
	$stmt = $pdo->prepare("UPDATE wa_notification SET cnfg_token=?,cnfg_sender=?,cnfg_intro=?,cnfg_intro_kbm=?,cnfg_intro_eskul=?,cnfg_intro_kegiatan=?,cnfg_template_izin=?,cnfg_template_sakit=?,cnfg_template_guru_izin=?,cnfg_no_kepsek=?,cnfg_status=?,cnfg_kbm=?,cnfg_eskul=?,cnfg_kegiatan=? WHERE cnfg_id=?");

	if(!$stmt->execute([ $cnfg_token,$cnfg_sender,$cnfg_intro,$cnfg_intro_kbm,$cnfg_intro_eskul,$cnfg_intro_kegiatan,$cnfg_template_izin,$cnfg_template_sakit,$cnfg_template_guru_izin,$cnfg_no_kepsek,$cnfg_status,$cnfg_kbm,$cnfg_eskul,$cnfg_kegiatan,$cnfg_id ])) {
		echo "Something went wrong. Please try again later.";
		header("location: error.php");
	} else {
		$stmt = null;
		header("location: notification?id=$cnfg_id&stat_update=ok");
	}
} else {
	// Check existence of id parameter before processing further
	$_GET["id"] = trim($_GET["id"]);
	if(isset($_GET["id"]) && !empty($_GET["id"])){
		// Get URL parameter
		$id =  trim($_GET["id"]);

		// Prepare a select statement
		$sql = "SELECT * FROM wa_notification WHERE cnfg_id = ?";
		if($stmt = mysqli_prepare($link, $sql)){
			// Set parameters
			$param_id = $id;

			// Bind variables to the prepared statement as parameters
			if (is_int($param_id)) $__vartype = "i";
			elseif (is_string($param_id)) $__vartype = "s";
			elseif (is_numeric($param_id)) $__vartype = "d";
			else $__vartype = "b"; // blob
			mysqli_stmt_bind_param($stmt, $__vartype, $param_id);

			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				$result = mysqli_stmt_get_result($stmt);

				if(mysqli_num_rows($result) == 1){
					/* Fetch result row as an associative array. Since the result set
					contains only one row, we don't need to use while loop */
					$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

					// Retrieve individual field value

					$token = htmlspecialchars($row["cnfg_token"]);
					$cnfg_sender = htmlspecialchars($row["cnfg_sender"]);
					$template = htmlspecialchars($row["cnfg_intro"]);
					$service_status = htmlspecialchars($row["cnfg_status"]);
					$cnfg_kbm = htmlspecialchars($row["cnfg_kbm"]);
					$cnfg_eskul = htmlspecialchars($row["cnfg_eskul"]);
					$cnfg_kegiatan = htmlspecialchars($row["cnfg_kegiatan"]);

                    // Fix: Fetch template content
                    $cnfg_intro_kbm = htmlspecialchars($row["cnfg_intro_kbm"]);
                    $cnfg_intro_eskul = htmlspecialchars($row["cnfg_intro_eskul"]);
                    $cnfg_intro_kegiatan = htmlspecialchars($row["cnfg_intro_kegiatan"]);
					$cnfg_template_izin = htmlspecialchars($row["cnfg_template_izin"]);
					$cnfg_template_sakit = htmlspecialchars($row["cnfg_template_sakit"]);
					$cnfg_template_guru_izin = htmlspecialchars($row["cnfg_template_guru_izin"]);
					$cnfg_no_kepsek = htmlspecialchars($row["cnfg_no_kepsek"]);
				} else{
					// URL doesn't contain valid id. Redirect to error page
					header("location: error.php");
					exit();
				}
				
			} else{
				echo "Oops! Something went wrong. Please try again later.<br>".$stmt->error;
			}
		}

		// Close statement
		mysqli_stmt_close($stmt);

	}  else{
		// URL doesn't contain id parameter. Redirect to error page
		header("location: error.php");
		exit();
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
  
  <link href="../../assets/css/animate.min.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../../assets/css/material-dashboard-pro.min.css?v=3.0.6" rel="stylesheet" />
  <link id="pagestyle" href="../../assets/vendor/DataTables_package/jquery.dataTables.min.css" rel="stylesheet" />  
  
  <link href = "https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.1.1/css/bootstrap5-toggle.min.css" rel="stylesheet">
  <style>
  .toggle.ios,
  .toggle-on.ios,
  .toggle-off.ios {
    border-radius: 20rem;
  }
   .toggle.ios .toggle-handle {
    border-radius: 20rem;
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
      <div class="row min-vh-80 mb-3">
			<div class="col-12" id="view_data_edit">
				<div class="card mt-4 h-80">
				<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
				  <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
					<h6 class="text-white text-capitalize ps-3">Whatsapp Integration</h6>
				  </div>
				</div>
				
				<div class="card-body">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					
                    <!-- Tabs Navigation -->
                    <div class="nav-wrapper position-relative end-0 mb-4">
                        <ul class="nav nav-pills nav-fill p-1" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" href="#general-tabs" role="tab" aria-controls="general" aria-selected="true">
                                    <span class="material-icons align-middle mb-1">settings</span> General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#kbm-tabs" role="tab" aria-controls="kbm" aria-selected="false">
                                    <span class="material-icons align-middle mb-1">school</span> KBM
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#eskul-tabs" role="tab" aria-controls="eskul" aria-selected="false">
                                    <span class="material-icons align-middle mb-1">sports_soccer</span> Eskul
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#kegiatan-tabs" role="tab" aria-controls="kegiatan" aria-selected="false">
                                    <span class="material-icons align-middle mb-1">event</span> Kegiatan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#izin-tabs" role="tab" aria-controls="izin" aria-selected="false">
                                    <span class="material-icons align-middle mb-1">healing</span> Izin/Sakit
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#logs-tabs" role="tab" aria-controls="logs" aria-selected="false">
                                    <span class="material-icons align-middle mb-1">receipt_long</span> Logs
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="myTabContent">
                        
                        <!-- General Tab -->
                        <div class="tab-pane fade show active" id="general-tabs" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-static mb-2">
                                        <label>Main Service Status</label>
                                    </div>
                                    <div class="input-group input-group-static mb-4">
                                        <input type="checkbox" id="stat_select" class="form-control text-md text-white" data-toggle="toggle" data-style="ios" <?php if($service_status == 1){ echo "checked";} ?>>
                                        <small class="form-text text-muted d-block mt-2">Main switch to enable/disable all WhatsApp notifications.</small>
                                    </div>
                                    
                                    <div class="input-group input-group-static mb-4">
                                        <label>API Key (WA Gateway)</label>
                                        <input type="text" name="token" class="form-control text-md" value="<?php echo $token; ?>" placeholder="API Key WA gateway" required>
                                    </div>
                                    
                                    <div class="input-group input-group-static mb-4">
                                        <label>Sender Number</label>
                                        <input type="text" name="sender" class="form-control text-md" value="<?php echo $cnfg_sender; ?>" placeholder="Sender Number (e.g. 62895...)" required>
                                    </div>

                                    <div class="input-group input-group-static mb-4">
                                        <label>Nomor Kepala Sekolah (Target Notifikasi Guru)</label>
                                        <input type="text" name="no_kepsek" class="form-control text-md" value="<?php echo $cnfg_no_kepsek; ?>" placeholder="Nomor WA Kepsek (e.g. 628...)" >
                                    </div>

                                    <!-- Default/Fallback Template (REMOVED as per user request) -->
                                    <input type="hidden" name="template" value="<?php echo $template; ?>">
                                    <!-- 
                                    <div class="input-group input-group-static mt-2 mb-4">
                                        <label>Default Template (Fallback)</label>
                                        <textarea rows="3" name="template" class="form-control text-md" placeholder="Template Default"><?php echo $template ; ?></textarea>
                                        <small class="text-muted">Used if specific template is empty.</small>
                                    </div>
                                    -->
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-info text-white" role="alert">
                                        <strong><i class="material-icons text-sm">info</i> Info:</strong><br>
                                        <small>
                                        Pastikan API Key valid dan paket layanan WhatsApp Gateway aktif.<br>
                                        Template pesan dapat diatur secara spesifik pada tab KBM, Eskul, dan Kegiatan.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KBM Tab -->
                        <div class="tab-pane fade" id="kbm-tabs" role="tabpanel">
                             <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-static mb-2">
                                        <label>Enable KBM Notification</label>
                                    </div>
                                    <div class="input-group input-group-static mb-4">
                                        <input type="checkbox" id="kbm_select" class="form-control text-md text-white" data-toggle="toggle" data-style="ios" <?php if($cnfg_kbm == 1){ echo "checked";} ?>>
                                    </div>
                                    <div class="input-group input-group-static mt-2 mb-4">
                                        <label>KBM Message Template</label>
                                        <textarea rows="6" name="template_kbm" id="template_kbm" class="form-control text-md" placeholder="Template Pesan KBM"><?php echo $cnfg_intro_kbm ; ?></textarea>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 btn-preview" data-target="template_kbm">
                                            <i class="material-icons text-sm">visibility</i> Preview Message
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-gray-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Available Tags (KBM)</h6>
                                            <ul class="list-group list-group-flush bg-transparent">
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{nama_siswa}</code> : Nama Siswa</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{tipe_presensi}</code> : "ABSENSI MASUK" / "ABSENSI PULANG"</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{waktu}</code> : Jam saat tap kartu</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{status_presensi}</code> : "Tepat Waktu" / "Terlambat" / "Pulang Awal"</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{ket_tambahan}</code> : Info jadwal masuk/pulang</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Eskul Tab -->
                        <div class="tab-pane fade" id="eskul-tabs" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-static mb-2">
                                        <label>Enable Eskul Notification</label>
                                    </div>
                                    <div class="input-group input-group-static mb-4">
                                        <input type="checkbox" id="eskul_select" class="form-control text-md text-white" data-toggle="toggle" data-style="ios" <?php if($cnfg_eskul == 1){ echo "checked";} ?>>
                                    </div>
                                    <div class="input-group input-group-static mt-2 mb-4">
                                        <label>Eskul Message Template</label>
                                        <textarea rows="6" name="template_eskul" id="template_eskul" class="form-control text-md" placeholder="Template Pesan Eskul"><?php echo $cnfg_intro_eskul ; ?></textarea>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 btn-preview" data-target="template_eskul">
                                            <i class="material-icons text-sm">visibility</i> Preview Message
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-gray-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Available Tags (Eskul)</h6>
                                            <ul class="list-group list-group-flush bg-transparent">
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{nama_siswa}</code> : Nama Siswa</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{tipe_presensi}</code> : "ABSENSI MASUK" / "ABSENSI PULANG"</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{waktu}</code> : Jam saat tap kartu</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{status_presensi}</code> : Status kehadiran eskul</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{ket_tambahan}</code> : Jadwal Eskul</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kegiatan Tab -->
                        <div class="tab-pane fade" id="kegiatan-tabs" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-static mb-2">
                                        <label>Enable Kegiatan Notification</label>
                                    </div>
                                    <div class="input-group input-group-static mb-4">
                                        <input type="checkbox" id="kegiatan_select" class="form-control text-md text-white" data-toggle="toggle" data-style="ios" <?php if($cnfg_kegiatan == 1){ echo "checked";} ?>>
                                    </div>
                                    <div class="input-group input-group-static mt-2 mb-4">
                                        <label>Kegiatan Message Template</label>
                                        <textarea rows="6" name="template_kegiatan" id="template_kegiatan" class="form-control text-md" placeholder="Template Pesan Kegiatan"><?php echo $cnfg_intro_kegiatan ; ?></textarea>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 btn-preview" data-target="template_kegiatan">
                                            <i class="material-icons text-sm">visibility</i> Preview Message
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-gray-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Available Tags (Kegiatan)</h6>
                                            <ul class="list-group list-group-flush bg-transparent">
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{nama_siswa}</code> : Nama Siswa</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{tipe_presensi}</code> : "ABSENSI KEGIATAN"</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{waktu}</code> : Jam saat tap kartu</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{status_presensi}</code> : "HADIR"</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{ket_tambahan}</code> : Nama Kegiatan / Detail</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="izin-tabs" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-static mt-2 mb-4">
                                        <label>Template Pesan IZIN</label>
                                        <textarea rows="6" name="template_izin" id="template_izin" class="form-control text-md" placeholder="Template Pesan IZIN"><?php echo $cnfg_template_izin ; ?></textarea>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 btn-preview" data-target="template_izin">
                                            <i class="material-icons text-sm">visibility</i> Preview Message
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-static mt-2 mb-4">
                                        <label>Template Pesan SAKIT</label>
                                        <textarea rows="6" name="template_sakit" id="template_sakit" class="form-control text-md" placeholder="Template Pesan SAKIT"><?php echo $cnfg_template_sakit ; ?></textarea>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 btn-preview" data-target="template_sakit">
                                            <i class="material-icons text-sm">visibility</i> Preview Message
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group input-group-static mt-2 mb-4">
                                        <label>Template Pesan Izin/Sakit GURU (ke Kepsek)</label>
                                        <textarea rows="6" name="template_guru_izin" id="template_guru_izin" class="form-control text-md" placeholder="Template Pesan Izin Guru"><?php echo $cnfg_template_guru_izin ; ?></textarea>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 btn-preview" data-target="template_guru_izin">
                                            <i class="material-icons text-sm">visibility</i> Preview Message
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="card bg-gray-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Tag yang Tersedia</h6>
                                            <ul class="list-group list-group-flush bg-transparent">
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{nama_siswa}</code> : Nama Siswa (Untuk Izin Siswa)</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{kelas}</code> : Kelas Siswa</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{nama_guru}</code> : Nama Guru (Untuk Izin Guru)</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{jabatan}</code> : Jabatan Guru</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{tanggal_mulai}</code> : Tanggal Mulai Izin</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{tanggal_selesai}</code> : Tanggal Selesai Izin</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{alasan}</code> : Keterangan/Alasan</li>
                                                <li class="list-group-item bg-transparent py-1 ps-0"><code class="text-primary">{jenis_izin}</code> : Jenis Izin (Sakit/Izin/Cuti)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="logs-tabs" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <?php
                                        mysqli_query($GLOBALS["___mysqli_ston"], "CREATE TABLE IF NOT EXISTS wa_logs (
                                            id INT(11) NOT NULL AUTO_INCREMENT,
                                            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                            siswa_uid VARCHAR(40),
                                            siswa_nama VARCHAR(100),
                                            kelas VARCHAR(50),
                                            tipe VARCHAR(10),
                                            target VARCHAR(20),
                                            phone VARCHAR(30),
                                            status VARCHAR(10),
                                            response TEXT,
                                            message TEXT,
                                            PRIMARY KEY (id)
                                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
                                        $logs = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id,created_at,siswa_nama,kelas,tipe,target,phone,status FROM wa_logs ORDER BY id DESC LIMIT 100");
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Tanggal</th>
                                                    <th>Nama Siswa</th>
                                                    <th>Kelas</th>
                                                    <th>Tipe</th>
                                                    <th>Target</th>
                                                    <th>Nomor</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if($logs){ while($r = mysqli_fetch_assoc($logs)){ ?>
                                                    <tr>
                                                        <td><?php echo intval($r['id']); ?></td>
                                                        <td><?php echo htmlspecialchars($r['created_at']); ?></td>
                                                        <td><?php echo htmlspecialchars($r['siswa_nama']); ?></td>
                                                        <td><?php echo htmlspecialchars($r['kelas']); ?></td>
                                                        <td><?php echo htmlspecialchars($r['tipe']); ?></td>
                                                        <td><?php echo htmlspecialchars($r['target']); ?></td>
                                                        <td><?php echo htmlspecialchars($r['phone']); ?></td>
                                                        <td><?php echo htmlspecialchars($r['status']); ?></td>
                                                    </tr>
                                                <?php } } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- End Tabs Content -->

					<div class="d-flex align-items-top justify-content-end px-2 mt-4">
						<input type="hidden" name="cnfg_status" id="cnfg_status" value="<?php echo $service_status; ?>"/>
						<input type="hidden" name="cnfg_kbm" id="cnfg_kbm" value="<?php echo $cnfg_kbm; ?>"/>
						<input type="hidden" name="cnfg_eskul" id="cnfg_eskul" value="<?php echo $cnfg_eskul; ?>"/>
						<input type="hidden" name="cnfg_kegiatan" id="cnfg_kegiatan" value="<?php echo $cnfg_kegiatan; ?>"/>
						<input type="hidden" name="cnfg_id" value="<?php echo $id; ?>"/>
						<input type="submit" class="btn bg-gradient-info shadow-secondary" value="Save Changes">
					</div>
				</form>
				</div>
				
				
			</div>
		  
		  </div>
	</div>
	
	<!-- Modal Preview -->
	<div class="modal fade" id="Modal_Preview" tabindex="-1" role="dialog" aria-labelledby="previewLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title font-weight-normal" id="previewLabel">Preview WhatsApp Message</h5>
			<button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
            <div class="p-3 bg-gray-100 border-radius-lg">
                <p id="preview_content" class="text-sm text-dark mb-0" style="white-space: pre-wrap; font-family: monospace;"></p>
            </div>
            <small class="text-muted mt-2 d-block">*This is a preview with dummy data.</small>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>

	<!-- Modal status Update Success -->
	<div class="modal fade" id="Modal_stat_update" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title font-weight-normal" id="exampleModalLabel">Update Status</h5>
			<button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			 <div class="d-flex justify-content-center">
			   <p style="font-size:18px;">Cunfiguration successfully updated!</p><br>
			 </div>
			 
		  </div>
		  <div class="modal-footer justify-content-center">
			<a role="button" class="btn bg-gradient-success"  href="notification?id=<?php echo $id; ?>" aria-pressed="true">OK</a>
		  </div>
		</div>
	  </div>
	</div>
	
	<!-- footer -->
	 <?php include 'view/part_footer.php';?>
	<!-- footer -->
	 <?php include 'view/part_bottomnav.php';?>
	</div>
  </main>
  <?php include 'view/part_theme_config.php';?>
 
  
  <!--   Jquery JS Files   -->
  
  <script src="../../assets/js/jquery-3.5.1.js"></script>
  
  <!--   Core JS Files   -->
  <script src="../../assets/js/core/popper.min.js"></script>
  <script src="../../assets/js/core/bootstrap.min.js"></script>
  <script src="../../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../../assets/js/plugins/datatables.js"></script>
  <script src="../../assets/js/plugins/choices.min.js"></script>
  
  <!-- Github buttons -->
  <script async defer src="../../assets/js/buttons_github.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../assets/js/material-dashboard-pro.min.js?v=3.0.6"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.1.1/js/bootstrap5-toggle.jquery.min.js"></script>
  
  
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script>
    $(function () {
        $('#stat_select').change(function () {
            var last_stat = $(this).prop('checked');
			if (last_stat){
				$("#cnfg_status").val(1);
			}else{
				$("#cnfg_status").val(0);
			}
			var inputValue = $("#cnfg_status").val();
			console.log("The value of the input is: " + inputValue);
        })
		
		$('#kbm_select').change(function () {
            var last_stat = $(this).prop('checked');
			if (last_stat){
				$("#cnfg_kbm").val(1);
			}else{
				$("#cnfg_kbm").val(0);
			}
        })
		
		$('#eskul_select').change(function () {
            var last_stat = $(this).prop('checked');
			if (last_stat){
				$("#cnfg_eskul").val(1);
			}else{
				$("#cnfg_eskul").val(0);
			}
        })
		
		$('#kegiatan_select').change(function () {
            var last_stat = $(this).prop('checked');
			if (last_stat){
				$("#cnfg_kegiatan").val(1);
			}else{
				$("#cnfg_kegiatan").val(0);
			}
        })

        // Preview Button Logic
        $('.btn-preview').click(function() {
            var targetId = $(this).data('target');
            var templateText = $('#' + targetId).val();
            
            if (!templateText) {
                templateText = "(Template kosong)";
            }

            // Dummy Data Replacement
            var previewText = templateText
                .replace(/{nama_siswa}/g, "Budi Santoso")
                .replace(/{tipe_presensi}/g, "ABSENSI MASUK")
                .replace(/{waktu}/g, "07:00:00")
                .replace(/{status_presensi}/g, "Tepat Waktu")
                .replace(/{ket_tambahan}/g, "Senin, 01 Jan 2024")
                .replace(/\\n/g, "\n"); // Handle escaped newlines if any

            $('#preview_content').text(previewText);
            $('#Modal_Preview').modal('show');
        });
    })
  </script>
   <?php
  	if(isset($_GET['stat_update'])){
		$stat_del=$_GET['stat_update'];
		if($stat_del == "ok"){
		echo "<script type='text/javascript'>
			$(document).ready(function(){
			 $('#Modal_stat_update').modal('show');
			});
			</script>";
		}
	}
  ?>
</body>

</html>
