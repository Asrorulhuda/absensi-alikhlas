<?php 
date_default_timezone_set('Asia/Jakarta');
session_start();
if ( $_SESSION['akses']!= 'Admin'){// handling if dont'have session

	header('location:../../index'); 
	exit();
} 
$ses_name = $_SESSION['name'];
$_SESSION['pages']="User";

require_once "../../include/db_config.php";
require_once "../../include/helpers.php";
include "control/confignusers_data.php";

// Prepare PDO connection globally
$dsn = "mysql:host=$db_server;dbname=$db_name;charset=utf8mb4";
$options = [
	PDO::ATTR_EMULATE_PREPARES   => false, 
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
];
try {
	$pdo = new PDO($dsn, $db_user, $db_password, $options);
} catch (Exception $e) {
	error_log($e->getMessage());
	exit('Database connection failed');
}

// Define variables and initialize with empty values
$name = "";
$email = "";
$username = "";
$password = "";
$picture = "";
$level_akses = "";
$id_siswa = "";

$name_err = "";
$email_err = "";
$username_err = "";
$password_err = "";
$picture_err = "";
$level_akses_err = "";
$id_siswa_err = "";



//$_GET["id_siswa"] = trim($_GET["id_siswa"]);
if(isset($_GET["id_siswa"]) && !empty($_GET["id_siswa"])){
	$id_siswa = $_GET["id_siswa"];
	//cek apakah user siswa sudah ada
	$stmt = $pdo->prepare("SELECT * FROM users WHERE id_siswa=?");
	$stmt->execute([$id_siswa]);
	$d_user = $stmt->fetch();
	
	if($d_user){
		$name = $d_user['name'];
		$username =  $d_user['username'];
		$msg = base64_encode('400');
		$info = "User <b>$name</b> sudah terdaftar <br> dengan akun <b> $username</b> !";
		$info = base64_encode($info);
		header('location:user?msg='.base64_encode('400').'&info='.$info.'');
		exit();
	}else{
		$stmt_s = $pdo->prepare("SELECT * FROM data_siswa WHERE s_id=?");
		$stmt_s->execute([$id_siswa]);
		$d_siswa = $stmt_s->fetch();
		
		if ($d_siswa) {
			$picture = $d_siswa['s_picture'];
			$name = $d_siswa['s_nama'];
			$username = random_username($name);
		} else {
			// Handle case where siswa not found if necessary
			$picture = "";
			$name = "";
			$username = "";
		}
		
		if ($picture==""){
			$picture = '../../assets/img/operator_pict/user_default.png';
		}
	}
	
	
}else{
	$picture = '../../assets/img/operator_pict/user_default.png';
	$uid_user = uniqid();
	$id_siswa = 0;
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
        $name = trim($_POST["name"]);
		$email = trim($_POST["email"]);
		$username = trim($_POST["username"]);
		// Use Bcrypt
		$password = password_hash($_POST["password"], PASSWORD_BCRYPT);
		$password1 = trim($_POST["password1"]);
		$picture = trim($_POST["picture"]);
		$level_akses = trim($_POST["level_akses"]);
		$id_siswa = trim($_POST["id_siswa"]);
		

        // PDO connection already established above

        $vars = parse_columns('users', $_POST);
        $stmt = $pdo->prepare("INSERT INTO users (name,email,username,password,picture,level_akses,id_siswa) VALUES (?,?,?,?,?,?,?)");

        if($stmt->execute([ $name,$email,$username,$password,$picture,$level_akses,$id_siswa  ])) {
			
			//update user_stat
			//update data_siswa  set user_stat=1 where s_id=1;
			$sql = "UPDATE data_siswa  SET user_stat=1 WHERE s_id=?";
			$stmt_up = $pdo->prepare($sql);
			$stmt_up->execute([$id_siswa]);
			
			$info = "Berhasil mendaftarkan <b>$name</b> sebagai user aplikasi<br> dengan akun <b> $username</b> !";
			$info = base64_encode($info);
			header('location:user?msg='.base64_encode('401').'&info='.$info.'');
			exit();
			
		} else{
			$info = "Gagal menambahkan <b>$name</b> sebagai user aplikasi!";
			$info = base64_encode($info);
			header('location:user?msg='.base64_encode('402').'&info='.$info.'');
			exit();
		}

}


function random_username($string) {
	$pattern = " ";
	$firstPart = strstr(strtolower($string), $pattern, true);
	$secondPart = substr(strstr(strtolower($string), $pattern, false), 0,3);
	$nrRand = rand(0, 900);
    if ($secondPart == Null){
		$username = trim(strtolower($string)).trim($nrRand);
	}else{
		$username = trim($firstPart).trim($secondPart).trim($nrRand);
	}
	return $username;
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
								<h6 class="text-white text-capitalize ps-3">Tambahkan User Baru</h6>
							</div>
						</div>
						
						<div class="card-body mb-4">
						<form class="multisteps-form__form" style="height: 500px;" action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
							<div class="multisteps-form__panel border-radius-xl bg-white js-active" data-animation="FadeIn">
								<h5 class="font-weight-bolder mb-0">Detai User</h5>
								<p class="mb-0 text-sm">Mandatory informations</p>
								
								<div class="multisteps-form__content">
									<div class="row mt-3" >
										<div class="col-12 col-sm-6">
										    <div class="row mt-3 justify-content-center" style="height: 306px;">	
												<form method="post">
													<div class="d-flex justify-content-center align-items-center">
													  <div class="image_area" id="avatar_area" style="width:auto;">
															<label for="upload_image" class="text-center">
																<img src="<?= htmlspecialchars($picture);?>" id="uploaded_image" class="rounded-circle z-depth-2" width="60%" height="60%" />
																<div class="overlay">
																	<div class="text">*Click on the image to change the user photo</div>
																</div>
																<input type="file" name="image" class="image" id="upload_image" style="display:none" />
																<input type="hidden" name="uid_user" id="uid_user" value="<?= htmlspecialchars($uid_user);?>" />
															</label>
														
													  </div>
													</div>
												</form>
												
											</div>
											
										</div>
										<div class="col-12 col-sm-6 mt-3 mt-sm-0">
										    <div class="row mt-3">	
												<div class="input-group input-group-dynamic">
													<label class="form-label">Name</label>
													<input class="multisteps-form__input form-control" type="text" onfocus="focused(this)" 
														   onfocusout="defocused(this)" name="name" value="<?php echo htmlspecialchars($name); ?>" required >
												</div>
											</div>
											<div class="row mt-4">	
												<div class="input-group input-group-dynamic">
													<label class="form-label">E-mail</label>
													<input class="multisteps-form__input form-control" type="text" onfocus="focused(this)" 
													       onfocusout="defocused(this)"  name="email"  required>
												</div>
											</div>
											<div class="row mt-4">	
												<div class="input-group input-group-dynamic">
													<label class="form-label">Username</label>
													<input class="multisteps-form__input form-control" type="text" onfocus="focused(this)" 
													       onfocusout="defocused(this)" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
												</div>
											</div>
											<div class="row mt-4">	
												<div class="input-group input-group-dynamic">
													<label class="form-label" onfocus="focused(this)" >Password</label>
													<input type="hidden" class="multisteps-form__input form-control" type="text" onfocus="focused(this)" onfocusout="defocused(this)" value="aa">
													
													<div class="col-6 col-sm-6">
														<div class="input-group input-group-dynamic">
															<input class="multisteps-form__input form-control" onfocus="focused(this)" onfocusout="defocused(this)" 
																type="password" name="password" id="password" placeholder="Input password" required >
														</div>
													</div>
													<div class="col-6 col-sm-6">
														<div class="input-group input-group-dynamic">
															<label class="form-label">Retype Password</label>
															<input class="multisteps-form__input form-control" onfocus="focused(this)" onfocusout="defocused(this)" 
																type="password" name="password1" id="password1" placeholder="Retype the password" required >
														</div>
													</div>
												</div>
											</div>
											<div class="row mt-4">		
												<div class="input-group input-group-dynamic">
												    <label class="form-label" onfocus="focused(this)" >Access Level</label>
													<input type="hidden" class="multisteps-form__input form-control" type="text" onfocus="focused(this)" onfocusout="defocused(this)" value="aa">
													<select class="multisteps-form__input form-control choices_input " name="level_akses" id="lvl_akses" placeholder="">
														<option value="Admin">Admin</option>
														<option value="User" >User</option>
													</select>
												</div>
												
												
											</div>
										</div>
									</div>
									<!-- hidden val -->
									<input type="hidden" name="id_siswa" value="<?php echo htmlspecialchars($id_siswa); ?>"/>
									<input type="hidden" name="picture" id="picture" value="<?php echo htmlspecialchars($picture); ?>">
									<div class="button-row d-flex mt-4">
										<input type="submit" class="btn bg-gradient-primary ms-auto mb-0 js-btn-next" value="Tambahkan User" onclick="return checkform()">
										<a class="btn bg-gradient-info ms-2 mb-0" style="color:white" type="button" href="user">Cancel</a>
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
	
	<!-- crop Modal-->
	<div class="modal fade" id="modal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content" >
				<div class="modal-header">
					<p class="modal-title" id="exampleModalLabel" style="color:#728394;">Cropping Avatar Photo</p>
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
	 
	 <!-- Modal warning -->
	<div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="warningModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title font-weight-normal" id="exampleModalLabel">Alert</h5>
			<button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			 <div class="d-flex justify-content-center">
			   <p>Password yang diinput tidak sama. Pastikan kembali password yang diinput dalam keadaan sama!</p><br>
			 </div>
			 
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn bg-gradient-primary" data-bs-dismiss="modal" onclick="okBack()">OK</button>
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
					var uid_user = $('#uid_user').val();
					$.ajax({
						url:'control/user_pict_update.php',
						method:'POST',
						data:{image:base64data, uid_user: uid_user},
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
	if (document.getElementById('lvl_akses')) {
      var element = document.getElementById('lvl_akses');
      const example = new Choices(element, {
        searchEnabled: false
      });
    };
  </script>
    
  <script type='text/javascript'>	
		function checkform(){
			var pw = $("#password").val();
			var pw1 = $("#password1").val();
			if(pw != pw1){
				$('#warningModal').modal('show');
				return false;
			}
			return true;
		}
		function okBack(){
			$("#password").focus();
		}
	
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