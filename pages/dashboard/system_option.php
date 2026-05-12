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
	include "control/confignusers_data.php";
	require_once "../../include/helpers.php";
	
	// Define variables and initialize with empty values
	$j_short = "";
	$j_name = "";
	$j_info = "";
	$j_short_err = "";
	$j_name_err = "";
	$j_info_err = "";
	
	$tk_name = "";
	$tk_ket = "";
	$tk_name_err = "";
	$tk_ket_err = "";

	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buttonTambahJurusan'])) {
			$j_short = trim($_POST["j_short"]);
			$j_name = trim($_POST["j_name"]);
			$j_info = trim($_POST["j_info"]);
			

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
			  exit('Something weird happened'); //something a user can understand
			}

			$vars = parse_columns('opsi_jurusan', $_POST);
			$stmt = $pdo->prepare("INSERT INTO opsi_jurusan (j_short,j_name,j_info) VALUES (?,?,?)");

			if($stmt->execute([ $j_short,$j_name,$j_info  ])) {
					$stmt = null;
					header("location: system_option");
				} else{
					echo "Something went wrong. Please try again later.";
				}

	}
	
	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buttonTambahKelas'])) {
			$tk_name = trim($_POST["tk_name"]);
			$tk_ket = trim($_POST["tk_ket"]);
			

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
			  exit('Something weird happened'); //something a user can understand
			}

			$vars = parse_columns('opsi_tk_kelas', $_POST);
			$stmt = $pdo->prepare("INSERT INTO opsi_tk_kelas (tk_name,tk_ket) VALUES (?,?)");

			if($stmt->execute([ $tk_name,$tk_ket ])) {
					$stmt = null;
					header("location: system_option");
				} else{
					echo "Something went wrong. Please try again later.";
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
  
  <!--   Jquery JS Files   --> 
  <script src="../../assets/js/jquery-3.5.1.js"></script>

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
			<div class="col-md-6 mx-auto mb-3">
				<div class="card my-4 h-80" id="data_list">
					<div class="card-header pb-0">
						<div class="d-lg-flex">
							<div>
								<h5 class="mb-0">Tingkat Kelas</h5>
								<p class="text-sm mb-0">Opsi Tingkat Kelas</p>
							</div>
							<div class="ms-auto my-auto mt-lg-0 mt-4">
								<div class="ms-auto my-auto">
								<a href="javascript:;" class="btn bg-gradient-primary btn-sm mb-0"  data-bs-toggle="modal" data-bs-target="#modal_add_kelas" 
         						   id="addTingkat"	data-type="Kelas">+&nbsp; Tambah Tingkatan</a>
								</div>
							</div>
						</div>
					</div>
						
					<div class="card-body px-0 pb-2">
						<div class="row justify-content-center"> 
							<div class="table-responsive p-0 w-95">
							   
							   <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
								<table id="table_tingkat" class="table align-items-center mb-0 display" style="width:100%">
								<!-- <table id="table_member" class="table align-items-center mb-3 dataTable js-exportable> -->
									<thead>
										<tr>
											<th style="text-align: center;">No</th>
											<th style="text-align: center;">Tingkat Kelas</th>
											<th style="text-align: center;">Keterangan</th>
											<th style="text-align: center;">Action</th>
										</tr>
										<tbody>
											<?php
												$sql_div = "SELECT * FROM opsi_tk_kelas ORDER BY tk_id DESC";
												$s_div = mysqli_query($GLOBALS["___mysqli_ston"], $sql_div);
												$no_div = 0;
												while ($d_div=mysqli_fetch_array($s_div)){
													$id_div64 = base64_encode($d_div['tk_id']);
													$no_div++;
											  
											?>
											   <tr>
													<td style="text-align: center;"><?= $no_div;?></td>
													<td><?= $d_div['tk_name'];?></td>
													<td><?= $d_div['tk_ket'];?></td>
													<td style="text-align: center;">
													<?php
														echo "<a href='javascript:;' data-bs-toggle='modal' data-bs-target='#deleteModal' data-div_id='". $d_div['tk_id'] ."' id='getDelTk'
																 data-div_name='". $d_div['tk_name']."' data-type='Tingkat' title='Hapus Tingkatan' data-toggle='tooltip'><span class='fa fa-trash'></span></a>";
															  
													?>
													</td>
											   
											   </tr>
											
											<?php ;} ?>
										
										</tbody>
									</thead>
								</table>
							   </div>
							</div>
						</div>
					</div>
				</div>		
			</div>
			
			<div class="col-md-6 mx-auto mb-3">
				<div class="card my-4 h-80" id="data_list">
					<div class="card-header pb-0">
						<div class="d-lg-flex">
							<div>
								<h5 class="mb-0">Kelas/Jurusan</h5>
								<p class="text-sm mb-0">Opsi Kelas/Jurusan yang tersedia</p>
							</div>
							<div class="ms-auto my-auto mt-lg-0 mt-4">
								<div class="ms-auto my-auto">
								<a href="javascript:;" class="btn bg-gradient-primary btn-sm mb-0"  data-bs-toggle="modal" data-bs-target="#modal_add_jurusan" 
         						   id="addJurusan"	data-type="Division">+&nbsp; Tambah Jurusan</a>
								</div>
							</div>
						</div>
					</div>
						
					<div class="card-body px-0 pb-2">
						<div class="row justify-content-center"> 
							<div class="table-responsive p-0 w-95">
							   
							   <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
								<table id="table_jurusan" class="table align-items-center mb-0 display" style="width:100%">
								<!-- <table id="table_member" class="table align-items-center mb-3 dataTable js-exportable> -->
									<thead>
										<tr>
											<th style="text-align: center;">No</th>
											<th style="text-align: center;">Jurusan</th>
											<th style="text-align: center;">Acronim</th>
											<th style="text-align: center;">Action</th>
										</tr>
										<tbody>
											<?php
												$sql_div = "SELECT * FROM opsi_jurusan ORDER BY j_id DESC";
												$s_div = mysqli_query($GLOBALS["___mysqli_ston"], $sql_div);
												$no_div = 0;
												while ($d_jur=mysqli_fetch_array($s_div)){
													$id_div64 = base64_encode($d_jur['j_id']);
													$no_div++;
											  
											?>
											   <tr>
													<td style="text-align: center;"><?= $no_div;?></td>
													<td><?= $d_jur['j_name'];?></td>
													<td><?= $d_jur['j_short'];?></td>
													<td style="text-align: center;">
													<?php
														echo "<a href='javascript:;' data-bs-toggle='modal' data-bs-target='#deleteModal' data-jur_id='". $d_jur['j_id'] ."' id='getDelJurusan'
																 data-jur_name='". $d_jur['j_name']."' data-type='Jurusan' title='Hapus Jurusan' data-toggle='tooltip'><span class='fa fa-trash'></span></a>";
															  
													?>
													</td>
											   
											   </tr>
											
											<?php ;} ?>
										
										</tbody>
									</thead>
								</table>
							   </div>
							</div>
						</div>
					</div>
				</div>		
			</div>
		</div>
	  
	<!-- Modal Create Jurusan-->
	<div class="modal fade" id="modal_add_jurusan" tabindex="-1" role="dialog" aria-labelledby="modal_add_jurusanLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-primary">
			<h5 class="modal-title font-weight-normal text-white" id="modal_add_jurusanLabel"></h5>
			<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">X
			</button>
		  </div>
		  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		  <div class="modal-body">
			 <div class="container mt-4 mb-4">
				<div class="row">
					<div class="input-group input-group-static">
						   <label>Nama Kelas/Jurusan</label>
						   <input type="text" class="form-control" name="j_name" required>
					</div>
				</div>
				<div class="row mt-2">
					<div class="input-group input-group-static">
						   <label>Acronim</label>
						   <input type="text" class="form-control" name="j_short" required>
					</div>
				</div>
				<div class="row mt-2">
					<div class="input-group input-group-static">
						   <label>Keterangan</label>
						   <textarea class="form-control" name="j_info"  rows="3"></textarea>
					</div>
				</div>
			 </div>
		  </div>
		  <div class="modal-footer">
			<button class="btn bg-gradient-success shadow-success" type="submit" name="buttonTambahJurusan">Tambah Jurusan</button>&nbsp 
			<button type="button" class="btn bg-gradient-secondary shadow-secondary" data-bs-dismiss="modal">Cancel</button> 
		  </div>
		  </form>
		</div>
	  </div>
	</div>
	
	<!-- Modal Create Kelas-->
	<div class="modal fade" id="modal_add_kelas" tabindex="-1" role="dialog" aria-labelledby="modal_add_kelas_Label" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-primary">
			<h5 class="modal-title font-weight-normal text-white" id="modal_add_kelas_Label"></h5>
			<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">X
			</button>
		  </div>
		  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		  <div class="modal-body">
			 <div class="container mt-4 mb-4">
				<div class="row">
					<div class="input-group input-group-static">
						   <label>Tingkat Kelas</label>
						   <input type="text" class="form-control" name="tk_name" required>
					</div>
				</div>
				<div class="row mt-2">
					<div class="input-group input-group-static">
						   <label>Keterangan</label>
						   <textarea class="form-control" name="tk_ket"  rows="3"></textarea>
					</div>
				</div>
			 </div>
		  </div>
		  <div class="modal-footer">
			<button class="btn bg-gradient-success shadow-success" type="submit" name="buttonTambahKelas">Tambah Kelas</button>&nbsp 
			<button type="button" class="btn bg-gradient-secondary shadow-secondary" data-bs-dismiss="modal">Cancel</button> 
		  </div>
		  </form>
		</div>
	  </div>
	</div>
	
	<!-- Modal delete -->
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-primary">
			<h5 class="modal-title font-weight-normal text-white" id="deleteModalLabel"></h5>
			<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">X
			</button>
			
		  </div>
		  <div class="modal-body mb-3 mt-3">
			 <p id="delete_text"></p>
		  </div>
		  <div class="modal-footer">
			<a href="#" class="btn bg-gradient-danger"  id="modalDelete" >Delete</a>&nbsp
			<button type="button" class="btn bg-gradient-secondary shadow-secondary" data-bs-dismiss="modal">Cancel</button> 
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
  
  <script type="text/javascript">
        $(document).ready(function(){
			$('[data-toggle="tooltip"]').tooltip();
			
			$(document).on('click', '#getDelJurusan', function(e){
			  
				 e.preventDefault();
			     
				 var jur_id = $(this).data('jur_id'); // get id of clicked row
				 var jur_name = $(this).data('jur_name');
				 var data_type = $(this).data('type');
				 var delete_text = 'Yakin akan menghapus Jurusan '+jur_name;
				 $('#deleteModalLabel').text('Hapus Data Jurusan');
				 $('#delete_text').text(delete_text);
				 $('#modalDelete').attr('href','control/option_del.php?type='+data_type+'&id='+jur_id);
			});
			
			$(document).on('click', '#getDelTk', function(e){
			  
				 e.preventDefault();
			     
				 var div_id = $(this).data('div_id'); // get id of clicked row
				 var div_name = $(this).data('div_name');
				 var data_type = $(this).data('type');
				 var delete_text = 'Yakin akan menghapus Tingkat Kelas '+div_name;
				 $('#deleteModalLabel').text('Hapus Data Jurusan');
				 $('#delete_text').text(delete_text);
				 $('#modalDelete').attr('href','control/option_del.php?type='+data_type+'&id='+div_id);
			});
			
			
			$(document).on('click', '#addJurusan', function(e){
				 e.preventDefault();
				 var data_type = $(this).data('type');
				 $('#modal_add_jurusanLabel').text('Tambah Jurusan/Sub-Kelas');
				 $('#tipe_option').val(data_type);
				  $('#data_label').text('Nama Jurusan');
			});
			
			$(document).on('click', '#addTingkat', function(e){
				 e.preventDefault();
				 var data_type = $(this).data('type');
				 $('#modal_add_kelas_Label').text('Tambah Kelas');
				 $('#tipe_option').val(data_type);
				  $('#data_label').text('Tingkatan Kelas');
			});
			
			
        });
  </script>
  <script>
    if (document.getElementById('table_jurusan')) {
      const dataTableSearch = new simpleDatatables.DataTable("#table_jurusan", {
        searchable: true,
        fixedHeight: false,
        perPage: 5
      });
    };
	if (document.getElementById('table_tingkat')) {
      const dataTableSearch = new simpleDatatables.DataTable("#table_tingkat", {
        searchable: true,
        fixedHeight: false,
        perPage: 5
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
  <?php
  	if(isset($_GET['msg'])){
		$stat_msg=base64_decode($_GET['msg']);
		
		if($stat_msg == "331"){
		$modal_stat ="Delete Status";
		echo "<script type='text/javascript'>
			$(document).ready(function(){
				$('#Modal_notif_head').addClass('bg-primary'); 
				$('#notif_msg').text('Gagal menghapus Kelas/Jurusan, ulangi proses!');
				$('#Modal_notif').modal('show');
			});
			</script>";
		}
		if($stat_msg == "332"){
		$modal_stat ="Delete Status";
		echo "<script type='text/javascript'>
			$(document).ready(function(){
				$('#Modal_notif_head').addClass('bg-primary'); 
				$('#notif_msg').text('Gagal menghapus Tingkatan Kelas, ulangi proses!');
				$('#Modal_notif').modal('show');
			});
			</script>";
		}
		if($stat_msg == "301"){
		$modal_stat ="Delete Status";
		echo "<script type='text/javascript'>
			$(document).ready(function(){
			  $('#Modal_notif_head').addClass('bg-success'); 
			  $('#notif_msg').text('Berhasil menghapus data Kelas/Jurusan');
			  $('#Modal_notif').modal('show');
			});
			</script>";
		}
		if($stat_msg == "302"){
		$modal_stat ="Delete Status";
		echo "<script type='text/javascript'>
			$(document).ready(function(){
			  $('#Modal_notif_head').addClass('bg-success'); 
			  $('#notif_msg').text('Berhasil menghapus data Tingkatan Kelas');
			  $('#Modal_notif').modal('show');
			});
			</script>";
		}
		if($stat_msg == "200"){
		$modal_stat ="Update Status";
		$info = base64_decode($_GET['info']);
		echo "<script type='text/javascript'>
			$(document).ready(function(){
			  $('#Modal_notif_head').addClass('bg-success'); 
			  $('#notif_msg').text('".$info."');
			  $('#Modal_notif').modal('show');
			});
			</script>";
		}
	}
  ?>
  <!-- Modal status delete -->
	<div class="modal fade" id="Modal_notif" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div id="Modal_notif_head" class="modal-header">
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">
			<?php
			  if ($modal_stat == "Delete Status"){
				  echo "<i class='far fa-trash-alt'></i>";
				  
			  }
			  if ($modal_stat == "Update Status"){
				  echo "<i class='far fa-edit'></i>";  
			  }
			
			?>
			</i>&nbsp; <?php echo $modal_stat;?></h5>
			<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
			</button>
		  </div>
		  <div class="modal-body">
			 <div class="d-flex justify-content-center">
			   <p id="notif_msg" style="font-size:20px;"></p><br>
			 </div>
		  </div>
		  <div class="modal-footer justify-content-center">
			<a href="system_option" class="btn bg-gradient-secondary shadow-secondary">OK</a>
		  </div>
		</div>
	  </div>
	</div>
</body>

</html>