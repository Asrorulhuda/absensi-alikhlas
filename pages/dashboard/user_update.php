<?php 
date_default_timezone_set('Asia/Jakarta');
session_start();
if ( ($_SESSION['akses']!= 'Admin') && ($_SESSION['akses']!= 'User') && ($_SESSION['akses']!= 'Guru') ){header('location:../../index'); exit();} 
$ses_name = $_SESSION['name'];
$_SESSION['pages']="User";

require_once "../../include/db_config.php";
require_once "../../include/helpers.php";
include "control/confignusers_data.php";

// Prepare PDO connection globally for the file
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
	exit('Database connection failed.');
}

$name = "";
$email = "";
$username = "";
$password = "";
$picture = "";
$level_akses = "";
$id_siswa = "";

if(isset($_POST["id"]) && !empty($_POST["id"])){
	$id = $_POST["id"];
	$name = trim($_POST["name"]);
	$email = trim($_POST["email"]);
	$username = trim($_POST["username"]);
	$password = trim($_POST["password"]);
	$password1 = trim($_POST["password1"]);
	$id_siswa = trim($_POST["id_siswa"]);
	$level_akses=trim($_POST["lvl_akses"]);
	$picture=trim($_POST["avatar"]);
	if ($picture == ""){$picture="../../assets/img/operator_pict/user_default.png";}
	
	
	// Prepare an update statement
	// PDO is already initialized above
	
	if ($password == "") {
	  $sql="UPDATE users SET name=?,email=?,username=?,picture=?,level_akses=?,id_siswa=? WHERE id=?";
	  $colom = array($name,$email,$username,$picture,$level_akses,$id_siswa,$id );
	}else{
	  // Use Bcrypt instead of MD5
	  $password = password_hash($password, PASSWORD_BCRYPT);
	  $sql="UPDATE users SET name=?,email=?,username=?,password=?,picture=?,level_akses=?,id_siswa=? WHERE id=?";
	  $colom = array($name,$email,$username,$password,$picture,$level_akses,$id_siswa,$id );
	}
	
	//$vars = parse_columns('users', $_POST);		
	$stmt = $pdo->prepare($sql);
	if(!$stmt->execute($colom)) {
		if ($_SESSION['akses']== 'Admin'){
			echo '<script language="javascript" type="text/javascript"> 
							alert("Failed to update user data. Please try again latter!");
							window.location.replace("user");
				  </script>';
		}else{
			echo '<script language="javascript" type="text/javascript"> 
							alert("Failed to update user data. Please try again latter!");
							window.location.replace("user_update?id_user='.base64_encode($id).'");
				  </script>';
		}
		
	} else{
		$stmt = null;
		if ($_SESSION['akses']== 'Admin'){
			echo '<script language="javascript" type="text/javascript"> 
						alert("User Data successfully updated!");
						window.location.replace("user");
			  </script>';
		}else{
			echo '<script language="javascript" type="text/javascript"> 
						alert("User Data successfully updated!");
						window.location.replace("user_update?id_user='.base64_encode($id).'");
			  </script>';
		}
		
	}

	
}else {
	if(isset($_GET["id_user"]) && !empty($_GET["id_user"])){
		$_GET["id_user"] = trim($_GET["id_user"]);
		$id_user =  base64_decode($_GET["id_user"]);
		
		// Use PDO prepared statement to prevent SQL Injection
		$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
		$stmt->execute([$id_user]);
		$d_user = $stmt->fetch();
		
		if (!$d_user) {
			// User not found
			echo '<script>alert("User not found!"); window.location.href="user";</script>';
			exit();
		}
		
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
								<h6 class="text-white text-capitalize ps-3">Update User Data</h6>
							</div>
						</div>
						
						<div class="card-body mb-4">
						<form class="multisteps-form__form" style="height: 500px;" action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
							<div class="multisteps-form__panel border-radius-xl bg-white js-active" data-animation="FadeIn">
								<h5 class="font-weight-bolder mb-0">About User</h5>
								<p class="mb-0 text-sm">Mandatory informations</p>
								
								<div class="multisteps-form__content">
									<div class="row mt-3" >
										<div class="col-12 col-sm-6">
										    <div class="row mt-3 justify-content-center" style="height: 306px;">	
												<form method="post">
													<div class="d-flex justify-content-center align-items-center">
													  <div class="image_area" id="avatar_area" style="width:auto;">
															<label for="upload_image" class="text-center">
																<img src="<?= htmlspecialchars($d_user['picture']);?>" id="uploaded_image" class="rounded-circle z-depth-2" width="60%" height="60%" />
																<div class="overlay">
																	<div class="text">*Click on the image to change the user photo</div>
																</div>
																<input type="file" name="image" class="image" id="upload_image" style="display:none" />
																<input type="hidden" name="uid_user" id="uid_user" value="<?= htmlspecialchars($d_user['id']);?>" />
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
														   onfocusout="defocused(this)" value="<?= htmlspecialchars($d_user['name']);?>" name="name" placeholder="Realname of User" required >
												</div>
											</div>
											<div class="row mt-4">	
												<div class="input-group input-group-dynamic">
													<label class="form-label">E-mail</label>
													<input class="multisteps-form__input form-control" type="text" onfocus="focused(this)" 
													       onfocusout="defocused(this)" value="<?= htmlspecialchars($d_user['email']);?>"  name="email" placeholder="Input email user" required>
												</div>
											</div>
											<div class="row mt-4">	
												<div class="input-group input-group-dynamic">
													<label class="form-label">Username</label>
													<input class="multisteps-form__input form-control" type="text" onfocus="focused(this)" 
													       onfocusout="defocused(this)" value="<?= htmlspecialchars($d_user['username']);?>"  name="username" placeholder="Input Username" required>
												</div>
											</div>
											<div class="row mt-4">	
												<div class="input-group input-group-dynamic">
													<label class="form-label" onfocus="focused(this)" >Password</label>
													<input type="hidden" class="multisteps-form__input form-control" type="text" onfocus="focused(this)" onfocusout="defocused(this)" value="aa">
													
													<div class="col-6 col-sm-6">
														<div class="input-group input-group-dynamic">
															<input class="multisteps-form__input form-control" onfocus="focused(this)" onfocusout="defocused(this)" 
																type="password" name="password" id="password"  >
														</div>
													</div>
													<div class="col-6 col-sm-6">
														<div class="input-group input-group-dynamic">
															<label class="form-label">Retype Password</label>
															<input class="multisteps-form__input form-control" onfocus="focused(this)" onfocusout="defocused(this)" 
																type="password" name="password1" id="password1" >
														</div>
													</div>
													<p style="font-size:14px;"><em>leave everything blank if the password is not changed</em></p>
													
												</div>
											</div>
											<?php if ($_SESSION['akses']== 'Admin'){ ?>
											<div class="row mt-4">		
												<div class="input-group input-group-dynamic">
												    <label class="form-label" onfocus="focused(this)" >Access Level</label>
													<input type="hidden" class="multisteps-form__input form-control" type="text" onfocus="focused(this)" onfocusout="defocused(this)" value="aa">
													<select class="multisteps-form__input form-control choices_input " name="lvl_akses" id="lvl_akses" placeholder="">
														<option value="Admin" <?php if($d_user['level_akses']=="Admin") echo 'selected="selected"'; ?> >Admin</option>
														<option value="User"  <?php if($d_user['level_akses']=="User") echo 'selected="selected"'; ?>>User</option>
													</select>
												</div>
											</div>
											<?php } if ($_SESSION['akses']== 'User'){ ?>
											<div class="row mt-4">	
												<div class="input-group input-group-dynamic">
													<label class="form-label">Access Level</label>
													<input class="multisteps-form__input form-control" type="text" onfocus="focused(this)" 
													       onfocusout="defocused(this)" value="<?= htmlspecialchars($d_user['level_akses']);?>"  name="lvl_akses" readonly>
												</div>
											</div>
											<?php } ?>
										</div>
									</div>
									<!-- hidden val -->
									<input type="hidden" name="id" value="<?php echo htmlspecialchars($d_user['id']); ?>"/>
									<input type="hidden" name="id_siswa" value="<?php echo htmlspecialchars($d_user['id_siswa']); ?>"/>
									<input type="hidden" name="avatar" id="picture" value="<?= htmlspecialchars($d_user['picture']);?>" >
									<div class="button-row d-flex mt-4">
										<input type="submit" class="btn bg-gradient-primary ms-auto mb-0 js-btn-next" value="Update" onclick="return checkform()">
										<?php if ($_SESSION['akses']== 'Admin'){ ?>
										<a class="btn bg-gradient-info ms-2 mb-0" style="color:white" type="button" href="user">Cancel</a>
										<?php } ?>
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
			   <p>Recheck your passwords! Enter the same password in the both input password!</p><br>
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