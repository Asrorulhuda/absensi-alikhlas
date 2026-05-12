<?php 
session_start();
date_default_timezone_set('Asia/Jakarta');
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
$company = "";
$sign_in_bg = "";
$title_bar = "";
$icon_bar = "";
$icon_dashboard = "";
$print_logo = "";
$footer = "";
$landing_bg_color1 = "";
$landing_bg_color2 = "";
$card_bg_color = "";

$company_err = "";
$sign_in_bg_err = "";
$title_bar_err = "";
$icon_bar_err = "";
$icon_dashboard_err = "";
$print_logo_err = "";
$footer_err = "";


// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
	// Get hidden input value
	$id = $_POST["id"];

	$company = trim($_POST["company"]);
	$title_bar = trim($_POST["title_bar"]);
	$footer = trim($_POST["footer"]);
    $landing_bg_color1 = trim($_POST["landing_bg_color1"]);
    $landing_bg_color2 = trim($_POST["landing_bg_color2"]);
    $card_bg_color = trim($_POST["card_bg_color"]);

	// Function to handle file uploads
	function handleFileUpload($fileInputName, $currentValue) {
		if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == 0) {
			$target_dir = "../../assets/img/";
			if (!file_exists($target_dir)) {
				@mkdir($target_dir, 0777, true);
			}
			$file_name = time() . '_' . basename($_FILES[$fileInputName]["name"]);
			$target_file = $target_dir . $file_name;
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
			$allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'ico', 'svg'];
			
			if (in_array($imageFileType, $allowed_types)) {
				if (move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $target_file)) {
					return "assets/img/" . $file_name;
				}
			}
		}
		return $currentValue;
	}

	$sign_in_bg = handleFileUpload('sign_in_bg_file', trim($_POST["sign_in_bg"]));
	$icon_bar = handleFileUpload('icon_bar_file', trim($_POST["icon_bar"]));
	$icon_dashboard = handleFileUpload('icon_dashboard_file', trim($_POST["icon_dashboard"]));
	$print_logo = handleFileUpload('print_logo_file', trim($_POST["print_logo"]));
		

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

	$vars = parse_columns('system_config', $_POST);
	$stmt = $pdo->prepare("UPDATE system_config SET company=?,sign_in_bg=?,title_bar=?,icon_bar=?,icon_dashboard=?,print_logo=?,footer=?,jm_masuk=?,batas_absen_masuk=?,jm_pulang=?,landing_bg_color1=?,landing_bg_color2=?,card_bg_color=? WHERE id=?");

	if(!$stmt->execute([ $company,$sign_in_bg,$title_bar,$icon_bar,$icon_dashboard,$print_logo,$footer,$jm_masuk,$batas_absen_masuk,$jm_pulang,$landing_bg_color1,$landing_bg_color2,$card_bg_color,$id  ])) {
		echo "Something went wrong. Please try again later.";
		header("location: error.php");
	} else {
		$stmt = null;
		header("location: system?id=$id&stat_update=ok");
	}
} else {
	// Check existence of id parameter before processing further
	$_GET["id"] = trim($_GET["id"]);
	if(isset($_GET["id"]) && !empty($_GET["id"])){
		// Get URL parameter
		$id =  trim($_GET["id"]);

		// Prepare a select statement
		$sql = "SELECT * FROM system_config WHERE id = ?";
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

					$company = htmlspecialchars($row["company"]);
					$sign_in_bg = htmlspecialchars($row["sign_in_bg"]);
					$title_bar = htmlspecialchars($row["title_bar"]);
					$icon_bar = htmlspecialchars($row["icon_bar"]);
					$icon_dashboard = htmlspecialchars($row["icon_dashboard"]);
					$print_logo = htmlspecialchars($row["print_logo"]);
					$footer = htmlspecialchars($row["footer"]);
                    $landing_bg_color1 = htmlspecialchars($row["landing_bg_color1"] ?? '#667eea');
                    $landing_bg_color2 = htmlspecialchars($row["landing_bg_color2"] ?? '#764ba2');
                    $card_bg_color = htmlspecialchars($row["card_bg_color"] ?? 'rgba(255, 255, 255, 0.1)');
					


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
					<h6 class="text-white text-capitalize ps-3">Application Settings</h6>
				  </div>
				</div>
				
				<div class="card-body">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
					<div class="row">
						<!-- Left Column -->
						<div class="col-md-6">
							<div class="input-group input-group-outline my-3 <?= !empty($company) ? 'is-filled' : '' ?>">
								<label class="form-label">Corporation / Nama Sekolah</label>
								<input type="text" name="company" class="form-control" value="<?php echo $company; ?>" required>
							</div>
							<div class="input-group input-group-outline my-3 <?= !empty($title_bar) ? 'is-filled' : '' ?>">
								<label class="form-label">Title Bar</label>
								<input type="text" name="title_bar" class="form-control" value="<?php echo $title_bar; ?>" required>
							</div>
							
							<!-- Fav Icon -->
							<div class="card card-plain border my-3">
								<div class="card-body p-3">
									<h6 class="mb-0 text-sm font-weight-bold">Fav Icon</h6>
									<p class="text-xs text-secondary mb-2">Upload a small icon for the browser tab.</p>
									<div class="d-flex align-items-center">
										<?php if($icon_bar): ?>
											<div class="avatar avatar-lg me-3 border-radius-lg bg-gray-100 p-1">
												<img src="../../<?php echo $icon_bar; ?>" alt="Fav Icon" class="w-100 h-100" style="object-fit: contain;">
											</div>
										<?php endif; ?>
										<div class="flex-grow-1">
											<input type="hidden" name="icon_bar" value="<?php echo $icon_bar; ?>">
											<input type="file" name="icon_bar_file" class="form-control form-control-sm border p-2">
										</div>
									</div>
								</div>
							</div>

							<!-- Dashboard Icon -->
							<div class="card card-plain border my-3">
								<div class="card-body p-3">
									<h6 class="mb-0 text-sm font-weight-bold">Dashboard Icon</h6>
									<p class="text-xs text-secondary mb-2">Icon displayed on the dashboard header.</p>
									<div class="d-flex align-items-center">
										<?php if($icon_dashboard): ?>
											<div class="avatar avatar-lg me-3 border-radius-lg bg-gray-100 p-1">
												<img src="../../<?php echo $icon_dashboard; ?>" alt="Dashboard Icon" class="w-100 h-100" style="object-fit: contain;">
											</div>
										<?php endif; ?>
										<div class="flex-grow-1">
											<input type="hidden" name="icon_dashboard" value="<?php echo $icon_dashboard; ?>">
											<input type="file" name="icon_dashboard_file" class="form-control form-control-sm border p-2">
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Right Column -->
						<div class="col-md-6">
							<!-- Sign-in Background -->
							<div class="card card-plain border my-3">
								<div class="card-body p-3">
									<h6 class="mb-0 text-sm font-weight-bold">Sign-in Background</h6>
									<p class="text-xs text-secondary mb-2">Background image for the login page.</p>
									<div class="d-flex flex-column">
										<?php if($sign_in_bg): ?>
											<div class="mb-3 border-radius-lg overflow-hidden position-relative" style="max-height: 150px;">
												<img src="../../<?php echo $sign_in_bg; ?>" alt="Sign In Background" class="w-100" style="object-fit: cover;">
											</div>
										<?php endif; ?>
										<input type="hidden" name="sign_in_bg" value="<?php echo $sign_in_bg; ?>">
										<input type="file" name="sign_in_bg_file" class="form-control form-control-sm border p-2">
									</div>
								</div>
							</div>

							<!-- Logo Print -->
							<div class="card card-plain border my-3">
								<div class="card-body p-3">
									<h6 class="mb-0 text-sm font-weight-bold">Logo Print</h6>
									<p class="text-xs text-secondary mb-2">Logo used for printed documents.</p>
									<div class="d-flex align-items-center">
										<?php if($print_logo): ?>
											<div class="avatar avatar-xl me-3 border-radius-lg bg-gray-100 p-1">
												<img src="../../<?php echo $print_logo; ?>" alt="Print Logo" class="w-100 h-100" style="object-fit: contain;">
											</div>
										<?php endif; ?>
										<div class="flex-grow-1">
											<input type="hidden" name="print_logo" value="<?php echo $print_logo; ?>">
											<input type="file" name="print_logo_file" class="form-control form-control-sm border p-2">
										</div>
									</div>
								</div>
							</div>

							<div class="input-group input-group-outline my-3 <?= !empty($footer) ? 'is-filled' : '' ?>">
								<label class="form-label">Footer Text</label>
								<input type="text" name="footer" class="form-control" value="<?php echo $footer; ?>">
							</div>
                            
                            <h6 class="text-sm font-weight-bold mt-4 mb-2">Landing Page Colors</h6>
                            <div class="row">
                                <div class="col-6">
                                    <div class="input-group input-group-outline my-3 is-filled">
                                        <label class="form-label">Background Color 1</label>
                                        <input type="color" name="landing_bg_color1" class="form-control form-control-color" value="<?php echo $landing_bg_color1; ?>" title="Choose color">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-outline my-3 is-filled">
                                        <label class="form-label">Background Color 2</label>
                                        <input type="color" name="landing_bg_color2" class="form-control form-control-color" value="<?php echo $landing_bg_color2; ?>" title="Choose color">
                                    </div>
                                </div>
                            </div>
                            <div class="input-group input-group-outline my-3 is-filled">
                                <label class="form-label">Card Background Color (RGBA/Hex)</label>
                                <input type="text" name="card_bg_color" class="form-control" value="<?php echo $card_bg_color; ?>" placeholder="rgba(255, 255, 255, 0.1)">
                            </div>
						</div>
					</div>
					
					<hr class="horizontal dark mt-4 mb-4">
					
					<div class="d-flex justify-content-end mt-4">
						<input type="hidden" name="id" value="<?php echo $id; ?>"/>
						<button type="submit" class="btn btn-sm bg-gradient-primary mb-0"><i class="material-icons text-sm">save</i>&nbsp; Save Changes</button>
					</div>
				</form>
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
			<a role="button" class="btn bg-gradient-success"  href="system.php?id=<?php echo $id; ?>" aria-pressed="true">OK</a>
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
  
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
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