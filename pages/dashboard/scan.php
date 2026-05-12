<?php 
date_default_timezone_set('Asia/Jakarta');
session_start();
if ( $_SESSION['akses']!= 'Admin'){// handling if dont'have session

	header('location:../../index'); 
	exit();
} 
$ses_name = $_SESSION['name'];
$_SESSION['pages']="Scan";

require_once "../../include/db_config.php";
require_once "../../include/helpers.php";
include "control/confignusers_data.php";

// Define variables and initialize with empty values
$p_uid = "";
$p_name = "";
$p_gender = "";
$p_bday = "";
$p_division = "";
$p_position = "";
$p_phone = "";
$p_mail = "";
$p_picture = "";
$p_info = "";
$p_created = "";

$p_uid_err = "";
$p_name_err = "";
$p_gender_err = "";
$p_bday_err = "";
$p_division_err = "";
$p_position_err = "";
$p_phone_err = "";
$p_mail_err = "";
$p_picture_err = "";
$p_info_err = "";
$p_created_err = "";


// Processing form data when form is submitted
if(isset($_POST["p_id"]) && !empty($_POST["p_id"])){
    // Get hidden input value
    $p_id = $_POST["p_id"];

    $p_uid = trim($_POST["p_uid"]);
		$p_name = trim($_POST["p_name"]);
		$p_gender = trim($_POST["p_gender"]);
		$p_bday = trim($_POST["p_bday"]);
		$p_division = trim($_POST["p_division"]);
		$p_position = trim($_POST["p_position"]);
		$p_phone = trim($_POST["p_phone"]);
		$p_mail = trim($_POST["p_mail"]);
		$p_picture = trim($_POST["p_picture"]);
		$p_info = trim($_POST["p_info"]);
		

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

    $vars = parse_columns('personnel', $_POST);
    $stmt = $pdo->prepare("UPDATE personnel SET p_uid=?,p_name=?,p_gender=?,p_bday=?,p_division=?,p_position=?,p_phone=?,p_mail=?,p_picture=? WHERE p_id=?");

    if(!$stmt->execute([ $p_uid,$p_name,$p_gender,$p_bday,$p_division,$p_position,$p_phone,$p_mail,$p_picture,$p_id  ])) {
        echo "Something went wrong. Please try again later.";
        header("location: error.php");
    } else {
        $stmt = null;
        //header("location: personnel-read.php?p_id=$p_id");
		$info=base64_encode($p_name."'s data has been updated successfully");
		header('location:member?msg='.base64_encode(200).'&info='.$info);
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
  
   <!--
  <link id="pagestyle" href="../../assets/vendor/DataTables_package/jquery.dataTables.min.css" rel="stylesheet" /> 
  
  <link href="https://unpkg.com/dropzone/dist/dropzone.css" rel="stylesheet" />
  <link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/>
 
  <link id="pagestyle" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css" rel="stylesheet" /> 
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.bootstrap5.min.css" />
  -->
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
        <div class="col-12">		  
		  <div class="multisteps-form mb-9" id="data_edit">
			<div class="row">
				<div class="col-12 col-lg-8 mx-auto my-3">
				</div>
			</div>			
			<div class="row" id="view_data_edit mb-5">
				<div class="col-12 col-lg-8 m-auto">
					<div class="card" style="height:auto;" >
						<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
							<div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
								<h6 class="text-white text-capitalize ps-3">Scan Kartu</h6>
							</div>
						</div>
						
						<div class="card-body mb-4">
							<div class="multisteps-form__panel border-radius-xl bg-white js-active" data-animation="FadeIn">
								<div class="multisteps-form__content">
									<div id="scan_element" class="d-flex g-3 justify-content-center align-items-center"> </div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		  
		  </div>				
            
          </div>
        </div>
	</div>
	
	
	<!-- footer -->
	 <?php include 'view/part_footer.php';?>
	<!-- footer -->
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
  
  <!-- <script src="../../assets/js/plugins/multistep-form.js"></script> -->
  
  <!-- Github buttons -->
  <script async defer src="../../assets/js/buttons_github.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../assets/js/material-dashboard-pro.min.js?v=3.0.6"></script>
   <!-- 
   <script src="https://unpkg.com/dropzone"></script>
  <script src="https://unpkg.com/cropperjs"></script>
  -->
  
  <script>
  $(document).ready(function(){
		$("#scan_element").load('view/scan_element.php');
		setInterval(function(){
			$("#scan_element").load('view/scan_element.php')
		}, 5000);		
	});
  </script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
</body>

</html>