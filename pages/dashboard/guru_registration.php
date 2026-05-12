<?php 
date_default_timezone_set('Asia/Jakarta');
session_start();
if ( $_SESSION['akses']!= 'Admin'){// handling if dont'have session

	header('location:../../index'); 
	exit();
} 
$ses_name = $_SESSION['name'];
$_SESSION['pages']="Guru";

require_once "../../include/db_config.php";
require_once "../../include/helpers.php";
include "control/confignusers_data.php";

// Define variables and initialize with empty values
$g_id = "";
$g_nip = "";
$g_uid = "";
$g_nama = "";
$g_tgl_lahir = "";
$g_kelamin = "";
$g_jabatan = "";
$g_status = "";
$g_mail = "";
$g_contact = "";
$g_phone = "";
$g_kompetensi= "";
$g_picture = "";
$g_tgs_tambahan = "";
$g_alamat = "";
$g_homeroom_class = "";


$g_nip_err = "";
$g_nama_err = "";
$g_tgl_lahir_err = "";
$g_kelamin_err = "";
$g_jabatan_err = "";
$g_mail_err = "";
$g_contact_err = "";
$g_kompetensi_err= "";
$g_picture_err = "";
$g_tgs_tambahan_err = "";
$g_alamat_err = "";



// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$g_nip = trim($_POST["g_nip"]);
	$g_uid = trim($_POST["g_uid"]);
	$g_nama = trim($_POST["g_nama"]);
	$g_tgl_lahir = trim($_POST["g_tgl_lahir"]);
	$g_kelamin = trim($_POST["g_kelamin"]);
	$g_jabatan = trim($_POST["g_jabatan"]);
	$g_status = isset($_POST["g_status"]) ? trim($_POST["g_status"]) : "";
	$g_mail = trim($_POST["g_mail"]);
	$g_contact = trim($_POST["g_contact"]);
	$g_phone = isset($_POST["g_phone"]) ? preg_replace('/\D/', '', $_POST["g_phone"]) : "";
	$g_homeroom_class = isset($_POST["g_homeroom_class"]) ? trim($_POST["g_homeroom_class"]) : "";
	$g_kompetensi= trim($_POST["g_kompetensi"]);
	$g_picture = trim($_POST["g_picture"]);
	$g_tgs_tambahan = trim($_POST["g_tgs_tambahan"]);
	$g_alamat = trim($_POST["g_alamat"]);
	

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

	// $vars = parse_columns('data_guru', $_POST);
	$stmt = $pdo->prepare("INSERT INTO data_guru (g_nip,g_uid,g_nama,g_tgl_lahir,g_kelamin,g_jabatan,g_status,g_homeroom_class,g_mail,g_contact,g_phone,g_kompetensi,g_picture,g_tgs_tambahan,g_alamat ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	
	


	if($stmt->execute([ $g_nip,$g_uid,$g_nama,$g_tgl_lahir,$g_kelamin,$g_jabatan,$g_status,$g_homeroom_class,$g_mail,$g_contact,$g_phone,$g_kompetensi,$g_picture,$g_tgs_tambahan,$g_alamat ])) {
			$stmt = null;			
			header("location: guru");
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
  
  <link href="../../assets/vendor/dropzone.css" rel="stylesheet" />
  <link href="../../assets/vendor/cropper.css" rel="stylesheet"/>
  

  <!--
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
		<div class="row mb-3">
			<div class="col-12">
			  <div class="card my-4 h-200">
				<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
				  <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
					<h6 class="text-white text-capitalize ps-3">Tambah Data Guru</h6>
				  </div>
				</div>
				
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="card-body px-0 pb-2 mx-4">
					<div class="row mb-2 mt-2">
						<h5 class="font-weight-bolder mb-0">Data Guru</h5>
						<p class="mb-0 text-sm">Detail Informasi Guru</p>
					</div>
					<div class="row mb-2 mt-2">
						<div class="col-12 col-sm-4">
							<div class="row mt-5 justify-content-center">	
								<div class="d-flex justify-content-center align-items-center position-relative">
								  <div class="image_area" id="avatar_area" style="position: relative; display: inline-block;">
										<label for="upload_image" class="cursor-pointer mb-0">
											<img src="../../assets/img/user_pict/user_default.png" id="uploaded_image" class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover;" />
											<div class="position-absolute bottom-0 end-0 mb-2 me-2">
												<span class="btn btn-icon-only btn-rounded btn-outline-secondary mb-0 p-1 bg-white">
													<i class="material-icons text-sm">edit</i>
												</span>
											</div>
											<input type="file" name="image" class="image" id="upload_image" style="display:none" disabled />
										</label>
								  </div>
								</div>
								<div class="text-center mt-2">
									<p class="text-xs text-secondary mb-0">Isi NIP untuk mengaktifkan upload foto</p>
								</div>
							</div>
							<div class="row mt-4">	
								<div class="input-group input-group-outline <?= !empty($g_nip) ? 'is-filled' : '' ?>">
								  <label class="form-label">NIP/NUPTK</label>
								  <input class="form-control" type="text" name="g_nip" id="g_nip" value="<?php echo $g_nip;?>" required>
								</div>
							</div>
						</div>
						<div class="col-12 col-sm-4 mt-3 mt-sm-0">
							<div class="row">
								<div class="input-group input-group-outline <?= !empty($g_nama) ? 'is-filled' : '' ?>">
									<label class="form-label">Nama Guru</label>
									   <input type="text" class="form-control" name="g_nama" value="<?php echo $g_nama; ?>" required>
								</div>
							</div>
							
							<div class="row mt-4">	
								<div class="col-8 col-sm-7">
									<div class="input-group input-group-outline is-filled">
										<label class="form-label">Tanggal Lahir</label>
									   <input type="date" class="form-control" name="g_tgl_lahir" value="<?php echo $g_tgl_lahir; ?>" required>
									</div>
								</div>
								<div class="col-4 col-sm-5">
									<div class="input-group input-group-outline bg-white is-filled">
										<label class="form-label">Jenis Kelamin</label>
										 <select class="form-control"  name="g_kelamin" id="choices-gender" >
											<option value="Laki-laki" <?= $g_kelamin == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
											<option value="Perempuan" <?= $g_kelamin == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
										 </select>
									</div>
								</div>
							</div>
							<div class="row mt-4">
								<div class="input-group input-group-outline <?= !empty($g_jabatan) ? 'is-filled' : '' ?>">
									<label class="form-label">Jabatan</label>
									   <input type="text" class="form-control" name="g_jabatan" value="<?php echo $g_jabatan; ?>" required>
								</div>
							</div>
							<div class="row mt-4">
								<div class="input-group input-group-outline <?= !empty($g_status) ? 'is-filled' : '' ?> bg-white">
									<label class="form-label">Status Guru</label>
									<select class="form-control" name="g_status" required>
										<option value="" disabled <?= empty($g_status) ? 'selected' : '' ?>>Pilih Status</option>
										<option value="kepala madrasah" <?= $g_status == 'kepala madrasah' ? 'selected' : '' ?>>Kepala Madrasah</option>
										<option value="guru mapel" <?= $g_status == 'guru mapel' ? 'selected' : '' ?>>Guru Mapel</option>
										<option value="gurukelas" <?= $g_status == 'gurukelas' ? 'selected' : '' ?>>Guru Kelas</option>
										<option value="tatausaha" <?= $g_status == 'tatausaha' ? 'selected' : '' ?>>Tata Usaha</option>
										<option value="bendahara" <?= $g_status == 'bendahara' ? 'selected' : '' ?>>Bendahara</option>
									</select>
								</div>
							</div>
							<div class="row mt-4" id="row_homeroom_reg" style="display: <?= ($g_status=='gurukelas') ? 'block' : 'none' ?>;">
								<div class="input-group input-group-outline <?= !empty($g_homeroom_class) ? 'is-filled' : '' ?> bg-white">
									<label class="form-label">Kelas Wali</label>
									<select class="form-control" name="g_homeroom_class">
										<option value="" disabled <?= empty($g_homeroom_class) ? 'selected' : '' ?>>Pilih Kelas</option>
										<?php
											$classes = [];
											$res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT tk_name AS kelas FROM opsi_tk_kelas ORDER BY tk_name");
											if($res){ while($row = mysqli_fetch_assoc($res)){ $classes[] = $row['kelas']; } }
											foreach($classes as $c){ 
												echo '<option value="'.htmlspecialchars($c).'" '.($g_homeroom_class==$c?'selected':'').'>'.htmlspecialchars($c).'</option>'; 
											}
										?>
									</select>
								</div>
							</div>
							<div class="row mt-4">
								<div class="input-group input-group-outline <?= !empty($g_kompetensi) ? 'is-filled' : '' ?>">
									<label class="form-label">Kompetensi / Mapel</label>
									   <input type="text" class="form-control" name="g_kompetensi" value="<?php echo $g_kompetensi; ?>" required>
								</div>
							</div>
							<div class="row mt-4">
								<div class="input-group input-group-outline <?= !empty($g_tgs_tambahan) ? 'is-filled' : '' ?>">
									<label class="form-label">Tugas Tambahan</label>
									   <input type="text" class="form-control" name="g_tgs_tambahan" value="<?php echo $g_tgs_tambahan; ?>" required>
								</div>
							</div>
							<div class="row mt-4">
								<div class="input-group input-group-outline <?= !empty($g_uid) ? 'is-filled' : '' ?>">
									<label class="form-label">UID / RFID</label>
									   <input type="text" class="form-control" name="g_uid" value="<?php echo $g_uid; ?>" required>
								</div>
							</div>
							
						</div>
						<div class="col-12 col-sm-4 mt-3 mt-sm-0">
							<div class="row">
								<div class="input-group input-group-outline <?= !empty($g_contact) ? 'is-filled' : '' ?>">
									<label class="form-label">Kontak / No HP</label>
									   <input type="text" class="form-control" name="g_contact" value="<?php echo $g_contact; ?>" required>
								</div>
							</div>
							<div class="row mt-4">
								<div class="input-group input-group-outline <?= !empty($g_phone) ? 'is-filled' : '' ?>">
									<label class="form-label">Nomor Telepon</label>
									<input type="text" class="form-control" name="g_phone" value="<?php echo $g_phone; ?>" pattern="[0-9]{10,15}" title="Masukkan 10-15 digit angka">
								</div>
							</div>
							
							<div class="row mt-4">
								<div class="input-group input-group-outline <?= !empty($g_mail) ? 'is-filled' : '' ?>">
									<label class="form-label">Email</label>
									   <input type="email" class="form-control" name="g_mail" value="<?php echo $g_mail; ?>" required>
								</div>
							</div>
							<div class="row mt-4 mb-2">
								<div class="input-group input-group-outline <?= !empty($g_alamat) ? 'is-filled' : '' ?>">
									<label class="form-label">Alamat</label>
									   <textarea class="form-control" name="g_alamat" rows="5"><?php echo $g_alamat; ?></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="row mb-2 mt-2 mb-4">
						<!-- hidden val -->
						<input type="hidden" name="g_picture" id="picture" value="../../assets/img/user_pict/user_default.png">
						<hr class="horizontal dark mt-0">
						<div class="button-row d-flex mt-2">
							<button type="submit" class="btn btn-sm bg-gradient-primary ms-auto mb-0"><i class="material-icons text-sm">save</i>&nbsp; Tambah Guru</button>
							<a class="btn btn-sm bg-gradient-info ms-2 mb-0" href="guru"><i class="material-icons text-sm">close</i>&nbsp; Cancel</a>
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
							<div class="row" style="max-height:800px">
									<img src="" id="sample_image" style="max-width: auto; max-height: 800px;"/>
								
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
  <script>
    (function(){
      var statusEl = document.querySelector('select[name="g_status"]');
      var rowHomeroom = document.getElementById('row_homeroom_reg');
      if(statusEl && rowHomeroom){
        statusEl.addEventListener('change', function(){
          if(this.value && this.value.toLowerCase() === 'gurukelas'){
            rowHomeroom.style.display = 'block';
          } else {
            rowHomeroom.style.display = 'none';
          }
        });
      }
    })();
  </script>
  
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
      // Auto-fill UID from temporary card table
      setInterval(function() {
          $.ajax({
              url: 'control/check_latest_card.php',
              type: 'GET',
              dataType: 'json',
              success: function(response) {
                  if (response.status === 'success') {
                      var currentUid = $('#g_uid').val();
                      if (currentUid !== response.uid) {
                           $('#g_uid').val(response.uid);
                           // Trigger change event if needed for labels to move up (Material UI)
                           $('#g_uid').parent().addClass('is-filled');
                           console.log("New card detected: " + response.uid);
                      }
                  }
              },
              error: function(xhr, status, error) {
                  console.error("Error checking card:", error);
              }
          });
      }, 1000); // Check every 1 second

		var $modal = $('#modal');

		var image = document.getElementById('sample_image');
		var cropper;
		
		$('#g_nip').on('input', function() {
                if ($(this).val().length > 5) {
                    $('#upload_image').prop('disabled', false);
                } else {
                    $('#upload_image').prop('disabled', true);
                }
            });

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
					var nip_guru = $('#g_nip').val();
					$.ajax({
						url:'control/guru_pict_update.php',
						method:'POST',
						data:{image:base64data, nip_guru: nip_guru},
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
	if (document.getElementById('choices-gender')) {
      var element = document.getElementById('choices-gender');
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
