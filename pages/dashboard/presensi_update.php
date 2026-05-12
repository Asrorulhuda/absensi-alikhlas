<?php 
date_default_timezone_set('Asia/Jakarta');
session_start();
if ( $_SESSION['akses']!= 'Admin'){// handling if dont'have session

	header('location:../../index'); 
	exit();
} 
$ses_name = $_SESSION['name'];
$_SESSION['pages']="Presensi";

require_once "../../include/db_config.php";
require_once "../../include/helpers.php";
include "control/confignusers_data.php";

// Define variables and initialize with empty values
	$tanggal = "";
	$jam_masuk = "";
	$jam_keluar = "";
	$status = "";
	$nama = "";
	$keterangan="";

	$tanggal_err = "";
	$jam_masuk_err = "";
	$jam_keluar_err = "";
	$status_err = "";
	$keterangan_err= "";
	$nama_err = "";



	// Processing form data when form is submitted
	if(isset($_POST["id_absen"]) && !empty($_POST["id_absen"])){
		// Get hidden input value
		//mysqli_real_escape_string() function escapes special characters in a string and create a legal SQL string to provide security against SQL injection.
		$id = $_POST["id_absen"];   
		$nama = $_POST["nama"];  
		$tanggal = mysqli_real_escape_string($link,trim($_POST["tanggal"]));
		$jam_masuk = mysqli_real_escape_string($link,trim($_POST["jam_masuk"]));
		$jam_keluar = mysqli_real_escape_string($link,trim($_POST["jam_keluar"]));
		$uid = mysqli_real_escape_string($link,trim($_POST["uid"]));
		$status = mysqli_real_escape_string($link,trim($_POST["status"]));
		$keterangan = mysqli_real_escape_string($link,trim($_POST["keterangan"]));
		if ($keterangan !== "HADIR"){
				$status = "OUT";
		}
		
		$sql = "UPDATE data_absen SET tanggal='$tanggal',jam_masuk='$jam_masuk',jam_keluar='$jam_keluar',uid='$uid',status='$status',keterangan='$keterangan' WHERE id='$id'";
				
		if(mysqli_query($link, $sql)){
			//echo '<script language="javascript" type="text/javascript"> 
				//	window.location.replace("presensi_harian");
				 // </script>';
			echo '<script language="javascript" type="text/javascript"> 
								window.location.replace("presensi_harian?stat_modal=edit_ok&nama='.$nama.'&tgl='.$tanggal.'");
					  </script>';
		} else{
			echo '<script language="javascript" type="text/javascript"> 
									alert("Gagal update data");
									window.location.replace("presensi_harian");
						  </script>';
		}

	} else {
		// Check existence of id parameter before processing further
		if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
			// Get URL parameter
			$id =  trim($_GET["id"]);

			// Prepare a select statement
			$sql = "SELECT data_absen.id, data_absen.uid, tanggal, jam_masuk, jam_keluar, status, keterangan, s_picture, s_nama
				FROM data_absen, data_siswa WHERE data_absen.uid=data_siswa.s_uid AND data_absen.id = ?";
				
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "i", $param_id);

				// Set parameters
				$param_id = $id;

				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					$result = mysqli_stmt_get_result($stmt);

					if(mysqli_num_rows($result) == 1){
						/* Fetch result row as an associative array. Since the result set
						contains only one row, we don't need to use while loop */
						$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
						$id_absen = $row["id"];
						$tanggal = $row["tanggal"];
						$jam_masuk = $row["jam_masuk"];
						$jam_keluar = $row["jam_keluar"];
						$status = $row["status"];
						$keterangan = $row["keterangan"];
						$nama = $row["s_nama"];
						$uid = $row["uid"];
						$picture = $row["s_picture"];

					} else{
						// URL doesn't contain valid id. Redirect to error page
						header("location: error.php");
						exit();
					}

				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}
			}

			// Close statement
			mysqli_stmt_close($stmt);

			// Close connection
			mysqli_close($link);

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
  
  <link href="../../assets/vendor/dropzone.css" rel="stylesheet" />
  <link href="../../assets/vendor/cropper.css" rel="stylesheet"/>
  

  <!--
  <link id="pagestyle" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css" rel="stylesheet" /> 
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
				<div class="col-12 col-lg-8 mx-auto my-5">
				</div>
			</div>			
			<div class="row" id="view_data_edit">
				<div class="col-12 col-lg-8 m-auto">
					<div class="card" style="height:auto;" >
						<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
							<div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
								<h6 class="text-white text-capitalize ps-3">Update Data Presensi</h6>
							</div>
						</div>
						
						<div class="card-body mb-4">
						
						<form class="multisteps-form__form" style="height: 500px;"  action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post" >
							<div class="multisteps-form__panel border-radius-xl bg-white js-active" data-animation="FadeIn">
								<h5 class="font-weight-bolder mb-0">Data Presensi</h5>
								<p class="mb-0 text-sm">Perbaikan dan perubahan data presensi</p>
								
								<div class="multisteps-form__content">
									<div class="row mt-3" >
										<div class="col-12 col-sm-6 ms-3">
										    <div class="row mt-3 justify-content-center" style="height: 306px;">	
													<div class="d-flex justify-content-center align-items-center">
													  <div class="image_area" id="avatar_area" style="width:auto;">
															<label for="upload_image" class="text-center">
																<img src="<?= $picture;?>" id="uploaded_image" class="rounded-circle z-depth-2" width="60%" height="60%" />
																<div class="overlay">
																	<div class="text mt-3" id="uid_info"><?=$uid?></div>
																</div>
															</label>
													  </div>
													</div>
											</div>
											
										</div>
										<div class="col-12 col-sm-5 mt-3 mt-sm-0">
										
											<div class="row">	
												<div class="input-group input-group-dynamic">
												    <label class="form-label" onfocus="focused(this)" >Siswa</label>
													<input type="hidden" class="multisteps-form__input form-control" type="text" onfocus="focused(this)" onfocusout="defocused(this)" value="aa">
													<select class="multisteps-form__input form-control choices_input" nfocus="focused(this)" onfocusout="defocused(this)" name="uid" id="choices-siswa" placeholder="">
													  
													  <?php
													        
															$sql_siswa = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM data_siswa");
															while($data_siswa = mysqli_fetch_assoc($sql_siswa)){
																  if($uid == $data_siswa['s_uid']){
																	  echo '<option value="'.$data_siswa['s_uid'].'" selected data-picture="'.$data_siswa['s_picture'].'" data-uid="'.$data_siswa['s_uid'].'" >'.$data_siswa['s_nama'].'</option>';
																  }else{
																	  echo '<option value="'.$data_siswa['s_uid'].'" data-picture="'.$data_siswa['s_picture'].'" data-uid="'.$data_siswa['s_uid'].'" >'.$data_siswa['s_nama'].'</option>';
																  }
															}
														?>
														<input type="hidden" name="nama" value="<?=$nama; ?>">
													</select>
												</div>
											</div>
											<div class="row mt-4">	
												<div class="input-group input-group-dynamic">
													<label class="form-label">Tanggal</label>
													<input class="multisteps-form__input form-control" type="date" onfocus="focused(this)" 
													       onfocusout="defocused(this)" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" required>
												</div>
											</div>
											<div class="row mt-4">	
												<div class="input-group input-group-dynamic">
												    <label class="form-label" onfocus="focused(this)" >Keterangan</label>
													<input type="hidden" class="multisteps-form__input form-control" type="text" onfocus="focused(this)" onfocusout="defocused(this)" value="aa">
													<select class="multisteps-form__input form-control choices_input" nfocus="focused(this)" onfocusout="defocused(this)" name="keterangan" id="choices-keterangan" placeholder="">
													    <option value="HADIR" <?php if($keterangan=="HADIR") echo 'selected="selected"'; ?>>HADIR</option>
													    <option value="SAKIT"<?php if($keterangan=="SAKIT") echo 'selected="selected"'; ?>>SAKIT</option>
													    <option value="CUTI"<?php if($keterangan=="CUTI") echo 'selected="selected"'; ?>>CUTI</option>
													    <option value="IZIN"<?php if($keterangan=="IZIN") echo 'selected="selected"'; ?>>IZIN</option>
													</select>
												</div>
											</div>
											<div class="row mt-4">	
												<div class="input-group input-group-dynamic">
													<label class="form-label">Jam Keluar</label>
													<input class="multisteps-form__input form-control" onfocus="focused(this)" 
													       onfocusout="defocused(this)" type="time" name="jam_masuk" id="jam_masuk" value="<?php echo htmlspecialchars($jam_masuk); ?>" >
												</div>
											</div>
											
											<div class="row mt-4">	
												<div class="input-group input-group-dynamic">
													<label class="form-label">jam Keluar</label>
													<input class="multisteps-form__input form-control" onfocus="focused(this)" 
													       onfocusout="defocused(this)" type="time" name="jam_keluar" id="jam_keluar" value="<?php echo htmlspecialchars($jam_keluar); ?>">
												</div>
											</div>
											<div class="row mt-4">	
												<div class="input-group input-group-dynamic">
												    <label class="form-label" onfocus="focused(this)" >Status</label>
													<input type="hidden" class="multisteps-form__input form-control" type="text" onfocus="focused(this)" onfocusout="defocused(this)" value="aa">
													<select class="multisteps-form__input form-control choices_input" nfocus="focused(this)" onfocusout="defocused(this)"  name="status"  id="choices-status" placeholder="">
													    <option value="IN" <?php if($status=="IN") echo 'selected="selected"'; ?> > IN</option>
														<option value="OUT" <?php if($status=="OUT") echo 'selected="selected"'; ?> > OUT</option>
													</select>
												</div>
											</div>
										</div>
									</div>
									<!-- hidden val -->
									<input type="hidden" name="id_absen" id="id_absen" value="<?= $id_absen;?>" />
									<div class="button-row d-flex mt-4">
										<input type="submit" class="btn bg-gradient-primary ms-auto mb-0 js-btn-next" style="color:white" value="Update">
										<a class="btn bg-gradient-info ms-2 mb-0" style="color:white" type="button" href="presensi_harian">Cancel</a>
									</div>
								</div>
							</div>

						</form>
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
  <script src="../../assets/vendor/dropzone.js"></script>
  <script src="../../assets/vendor/cropper.js"></script>
  
   <script>
    $("#choices-siswa").change(function(){
		var pict = $(this).find(':selected').data('picture'); 
		var uid = $(this).find(':selected').data('uid'); 
		$("#uploaded_image").attr("src",pict);
		$("#uid_info").text(uid);
	   
    });
	$("#choices-keterangan").change(function(){
        if ($(this).val() !== "HADIR") {
			 //$("#jam_masuk").prop('disabled', true);
			 //$("#jam_keluar").prop('disabled', true);
			 //$("#choices-status").prop('disabled', true);
			 $("#choices-status").val('OUT');
			 if($("#jam_masuk").val()==""){
				 $("#jam_masuk").val('00:00:00');
			 }
			 if($("#jam_keluar").val()==""){
				 $("#jam_keluar").val('00:00:00');
			 }
         }
        else {
         $("#jam_masuk").prop('disabled', false);
         $("#jam_keluar").prop('disabled', false);
		 $("#status").prop('disabled', false);

        }
	});
  </script>
  <script>
    if (document.getElementById('choices-siswa')) {
      var element = document.getElementById('choices-siswa');
      const example = new Choices(element, {
        searchEnabled: true
      });
    };
	 if (document.getElementById('choices-keterangan')) {
      var element = document.getElementById('choices-keterangan');
      const example = new Choices(element, {
        searchEnabled: false
      });
    };
	if (document.getElementById('choices-status')) {
      var element = document.getElementById('choices-status');
      const example = new Choices(element, {
        searchEnabled: false
      });
    };
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