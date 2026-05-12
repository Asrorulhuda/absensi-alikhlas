<?php 
date_default_timezone_set('Asia/Jakarta');
session_start();
if ( $_SESSION['akses']!= 'Admin'){// handling if dont'have session

	header('location:../../index'); 
	exit();
} 
$ses_name = $_SESSION['name'];
$_SESSION['pages']="Siswa";

require_once "../../include/db_config.php";
require_once "../../include/helpers.php";
include "control/confignusers_data.php";
// Define variables and initialize with empty values
$s_uid = "";
$s_nama = "";
$s_nis = "";
$s_kelamin = "";
$s_tgl_lahir = "";
$s_phone = "";
$s_alamat = "";
$s_kontak_wali = "";
$s_nama_wali = "";
$s_picture = "";
$s_jurusan = "";
$s_kelas = "";
$s_status = "";
$s_created = "";

$s_uid_err = "";
$s_nama_err = "";
$s_nis_err = "";
$s_kelamin_err = "";
$s_tgl_lahir_err = "";
$s_phone_err = "";
$s_kontak_wali_err = "";
$s_alamat_err = "";
$s_nama_wali_err = "";
$s_picture_err = "";
$s_jurusan_err = "";
$s_kelas_err = "";
$s_status_err = "";
$s_created_err = "";

// Processing form data when form is submitted
if(isset($_POST["s_id"]) && !empty($_POST["s_id"])){
    // Get hidden input value
    $s_id = $_POST["s_id"];

    $s_uid = trim($_POST["s_uid"]);
		$s_nama = trim($_POST["s_nama"]);
		$s_nis = trim($_POST["s_nis"]);
		$s_kelamin = trim($_POST["s_kelamin"]);
		$s_tgl_lahir = trim($_POST["s_tgl_lahir"]);
		$s_phone = trim($_POST["s_phone"]);
		$s_kontak_wali = trim($_POST["s_kontak_wali"]);
		$s_alamat = trim($_POST["s_alamat"]);
		$s_nama_wali = trim($_POST["s_nama_wali"]);
		$s_picture = trim($_POST["s_picture"]);
		$s_jurusan = trim($_POST["s_jurusan"]);
		$s_kelas = trim($_POST["s_kelas"]);
		$s_status = trim($_POST["s_status"]);
		$s_created = date("Y-m-d");
		
    //echo $s_id ;echo " " ; echo $s_uid ;echo " " ; echo $s_nama ;echo " " ;
	
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

    $vars = parse_columns('data_siswa', $_POST);
    $stmt = $pdo->prepare("UPDATE data_siswa SET s_uid=?,s_nama=?,s_nis=?,s_kelamin=?,s_tgl_lahir=?,s_phone=?,s_alamat=?,s_kontak_wali=?,s_nama_wali=?,s_picture=?,s_jurusan=?,s_kelas=?,s_status=?,s_created=? WHERE s_id=?");

    if(!$stmt->execute([ $s_uid,$s_nama,$s_nis,$s_kelamin,$s_tgl_lahir,$s_phone,$s_alamat,$s_kontak_wali,$s_nama_wali,$s_picture,$s_jurusan,$s_kelas,$s_status,$s_created,$s_id  ])) {
        echo "Something went wrong. Please try again later.";
        header("location: error.php");
    } else {
		$stmt = null;
        header("location: siswa");
    }
} else {
    $_GET["id_siswa"] = trim($_GET["id_siswa"]);
    if(isset($_GET["id_siswa"]) && !empty($_GET["id_siswa"])){
        $id_siswa =  base64_decode($_GET["id_siswa"]);
		$sql = "SELECT * FROM data_siswa WHERE s_id='$id_siswa'";
		$s_siswa = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		$d_siswa = mysqli_fetch_array($s_siswa);
	}else{
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
		<div class="row mb-3">
			<div class="col-12">
			  <div class="card my-4 h-200">
				<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
				  <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
					<h6 class="text-white text-capitalize ps-3">Perbaharui Data Siswa</h6>
				  </div>
				</div>
				
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="card-body px-0 pb-2 mx-4">
					<div class="row mb-2 mt-2">
						<h5 class="font-weight-bolder mb-0">Data Siswa</h5>
						<p class="mb-0 text-sm">Detail Informasi Siswa</p>
					</div>
					<div class="row mb-2 mt-2">
						<div class="col-12 col-sm-4">
							<div class="row mt-5 justify-content-center" style="height: 306px;">	
								<div class="d-flex justify-content-center align-items-center">
								  <div class="image_area" id="avatar_area" style="width:auto;">
										<label for="upload_image" class="text-center">
											<img src="<?= $d_siswa['s_picture'];?>" id="uploaded_image" class="rounded-circle z-depth-2" width="60%" height="60%" />
											<div class="overlay">
												<div class="text">*Click gambar untuk mengganti photo</div>
											</div>
											<input type="file" name="image" class="image" id="upload_image" style="display:none" />
											<input type="hidden" name="uid_siswa" id="uid_siswa" value="<?= $d_siswa['s_uid'];?>" />
										</label>
									
								  </div>
								</div>
								
							</div>
							<div class="row mt-2">	
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">UID</label>
							    <div class="input-group input-group-outline mb-4">
								  <input type="text" class="form-control" name="s_uid" id="s_uid" value="<?= $d_siswa['s_uid'];?>" required>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-4 mt-3 mt-sm-0">
							<div class="row">
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Nama</label>
								<div class="input-group input-group-outline mb-4">
									   <input type="text" class="form-control" name="s_nama" value="<?= $d_siswa['s_nama'];?>" required>
								</div>
							</div>
							
							<div class="row mt-1">	
								<div class="col-8 col-sm-7">
									<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">NIS</label>
									<div class="input-group input-group-outline mb-4">
									   <input type="text" class="form-control" name="s_nis" value="<?= $d_siswa['s_nis'];?>" required>
									</div>
								</div>
								<div class="col-4 col-sm-5">
									<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Jenis Kelamin</label>
									<div class="input-group input-group-outline mb-4">
										 <select class="form-control"  name="s_kelamin" id="choices-gender" >
										    <option value="" >Jenis Kelamin</option>
											<option value="Laki-laki" <?php if($d_siswa['s_kelamin']=="Laki-laki") echo 'selected="selected"'; ?> >Laki-laki</option>
											<option value="Perempuan" <?php if($d_siswa['s_kelamin']=="Perempuan") echo 'selected="selected"'; ?> >Perempuan</option>
										 </select>
									</div>
								</div>
							</div>
							<div class="row mt-1">	
								<div class="col-8 col-sm-7">
									<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Tanggal Lahir</label>
									<div class="input-group input-group-outline mb-4">
									   <input type="date" class="form-control" name="s_tgl_lahir"  value="<?= $d_siswa['s_tgl_lahir'];?>" required>
									</div>
								</div>
								<div class="col-4 col-sm-5">
									<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Status Siswa</label>
									<div class="input-group input-group-outline mb-4">
										 <select class="form-control"  name="s_status" id="choices-status" >
										    <option value="" >Status</option>
											<option value="Aktif" <?php if($d_siswa['s_status']=="Aktif") echo 'selected="selected"'; ?>  >Aktif</option>
											<option value="Cuti" <?php if($d_siswa['s_status']=="Cuti") echo 'selected="selected"'; ?> >Cuti</option>
											<option value="Lulus" <?php if($d_siswa['s_status']=="Lulus") echo 'selected="selected"'; ?> >Lulus</option>
											<option value="Drop Out" <?php if($d_siswa['s_status']=="Drop Out") echo 'selected="selected"'; ?> >Drop Out</option>
										 </select>
									</div>
								</div>
							</div>
							<div class="row mt-1">	
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Tingkat</label>
								<div class="input-group input-group-outline mb-4">
										 <select class="form-control"  name="s_kelas" id="choices-kelas" >
										    <option value="">Pilih Tingkatan</option>
											<?php
												$kelas = $d_siswa['s_kelas'];
												$sql_tingkat = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM opsi_tk_kelas");
												while($data_tk = mysqli_fetch_assoc($sql_tingkat)){
													//echo '<option value="'.$data_jurusan['j_id'].'">'.$data_jurusan['j_short'].'</option>';
													if($kelas == $data_tk['tk_name']){
														  echo '<option value="'.$data_tk['tk_name'].'" selected>'.$data_tk['tk_name'].'</option>';
													  }else{
														  echo '<option value="'.$data_tk['tk_name'].'">'.$data_tk['tk_name'].'</option>';
													  }
													
												}
											?>
										 </select>
									</div>
							</div>
							<div class="row mt-1">	
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Kelas/Jurusan</label>
								<div class="input-group input-group-outline mb-4">
										<select class="form-control choices_input" name="s_jurusan" id="choices-jurusan">
										  <option value="">Pilih Kelas/Jurusan</option>
										  <?php
												$jurusan = $d_siswa['s_jurusan'];
												$sql_jurusan = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM opsi_jurusan");
												while($data_jurusan = mysqli_fetch_assoc($sql_jurusan)){
													//echo '<option value="'.$data_jurusan['j_id'].'">'.$data_jurusan['j_short'].'</option>';
													if($jurusan == $data_jurusan['j_id']){
														  echo '<option value="'.$data_jurusan['j_id'].'" selected>'.$data_jurusan['j_name'].' ('.$data_jurusan['j_short'].')</option>';
													  }else{
														  echo '<option value="'.$data_jurusan['j_id'].'">'.$data_jurusan['j_name'].' ('.$data_jurusan['j_short'].')</option>';
													  }
													
												}
											?>
										</select>
									</div>
							</div>
						</div>
						<div class="col-12 col-sm-4 mt-3 mt-sm-0">
							<div class="row">
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Phone</label>
								<div class="input-group input-group-outline mb-4">
									   <input type="text" class="form-control" name="s_phone"  value="<?= $d_siswa['s_phone'];?>" required>
								</div>
							</div>
							
							
							<div class="row mt-1">
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Nama Wali</label>
								<div class="input-group input-group-outline mb-4">
									   <input type="text" class="form-control" name="s_nama_wali" value="<?= $d_siswa['s_nama_wali'];?>" required>
								</div>
							</div>
							<div class="row mt-1">	
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Kontak Wali</label>
							    <div class="input-group input-group-outline mb-4">
									   <input type="text" class="form-control" name="s_kontak_wali" value="<?= $d_siswa['s_kontak_wali'];?>" required>
								</div>
								
							</div>
							
							<div class="row mt-1 mb-2">
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Alamat</label>
								<div class="input-group input-group-outline mb-4">
									   <textarea class="form-control" name="s_alamat" rows="5" ><?= $d_siswa['s_alamat'];?></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="row mb-2 mt-2 mb-4">
						<!-- hidden val -->
						<input type="hidden" name="s_picture" id="picture" value="<?= $d_siswa['s_picture'];?>">
						<input type="hidden" name="s_id" id="s_id" value="<?= $d_siswa['s_id']; ?>">
						<hr>
						<div class="button-row d-flex mt-2">
							<button type="submit" class="btn bg-gradient-primary ms-auto mb-0 js-btn-next" style="color:white"><i class="material-icons text-sm">save</i>&nbsp; Update Data</button>
							<a class="btn bg-gradient-info ms-2 mb-0" style="color:white" type="button" href="siswa"><i class="material-icons text-sm">close</i>&nbsp; Cancel</a>
						</div>
					
					</div>
				</div>
				
				</form>
			  </div>
			</div>
		</div>
	</div>
	
	<!-- crop Modal-->
	<div class="modal fade" id="modal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content" >
				<div class="modal-header">
					<p class="modal-title" id="exampleModalLabel" style="color:#728394;">Crop Picture Image</p>
					<button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
						<div class="img-container" >
							<div class="row" style="max-height:500px">
									<img src="" id="sample_image" style="max-width: auto; max-height: 500px;"/>
								
							</div>
						</div>
				</div>
				<div class="modal-footer justify-content-center">
					<button type="button" id="crop" class="btn btn-primary btn-md">Crop</button>
					<button type="button" class="btn btn-info" data-bs-dismiss="modal">Cancel</button> 
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
  
  <!-- <script src="../../assets/js/plugins/multistep-form.js"></script> -->
  
  <!-- Github buttons -->
  <script async defer src="../../assets/js/buttons_github.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../assets/js/material-dashboard-pro.min.js?v=3.0.6"></script>
  <script src="../../assets/vendor/dropzone.js"></script>
  <script src="../../assets/vendor/cropper.js"></script>
  
   <script>
  $(document).ready(function(){

		var $modal = $('#modal');

		var image = document.getElementById('sample_image');

		var cropper;

		$('#upload_image').change(function(event){
			//$('#modal-wishes').modal('hide');
			var files = event.target.files;

			var done = function(url){
				image.src = url;
				$modal.modal('show');
			};

			if(files && files.length > 0)
			{
				reader = new FileReader();
				reader.onload = function(event)
				{
					done(reader.result);
				};
				reader.readAsDataURL(files[0]);
			}
		});
		
		$modal.on('shown.bs.modal', function() {
			cropper = new Cropper(image, {
				aspectRatio: 1,
				viewMode: 3,
				preview:'.preview'
			});
		}).on('hidden.bs.modal', function(){
			cropper.destroy();
			cropper = null;
		});
		
		$('#crop').click(function(){
			canvas = cropper.getCroppedCanvas({
				width:360,
				height:360
			});

			canvas.toBlob(function(blob){
				url = URL.createObjectURL(blob);
				var reader = new FileReader();
				reader.readAsDataURL(blob);
				reader.onloadend = function(){
					var base64data = reader.result;
					var uid_siswa = $('#s_uid').val();
					if (uid_siswa ==""){
						uid_siswa = $('#uid_siswa').val();
					}
					console.log(uid_siswa);
					$.ajax({
						url:'control/siswa_pict_update.php',
						method:'POST',
						data:{image:base64data, uid_siswa: uid_siswa},
						success:function(data)
						{
							$modal.modal('hide');
							$('#uploaded_image').attr('src', data);
							$('#picture').val(data);
						}
					});
				};
			});
			
			
		});			
	});
  </script>
  <script>
    if (document.getElementById('choices-status')) {
      var element = document.getElementById('choices-status');
      const example = new Choices(element, {
        searchEnabled: false
      });
    };
	if (document.getElementById('choices-jurusan')) {
      var element = document.getElementById('choices-jurusan');
      const example = new Choices(element, {
        searchEnabled: true
      });
    };
	 if (document.getElementById('choices-kelas')) {
      var element = document.getElementById('choices-kelas');
      const example = new Choices(element, {
        searchEnabled: false
      });
    };
	if (document.getElementById('choices-gender')) {
      var element = document.getElementById('choices-gender');
      const example = new Choices(element, {
        searchEnabled: false
      });
    };
	 if (document.getElementById('choices-emergency-user')) {
      var element = document.getElementById('choices-emergency-user');
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