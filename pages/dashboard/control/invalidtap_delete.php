<?php
	session_start();
	if ( !isset($_SESSION['name'])){// handling if dont'have session

		header('location:../index.php'); 
		exit();
	} 
	$ses_name = $_SESSION['name'];
	$_SESSION['pages']="Invalid";

	// Include config file
	require_once "../../configs/database.php";
	include "confignusers_data.php";
	
    if($_SERVER["REQUEST_METHOD"] == "POST"){
	
		// Prepare a delete statement
		$sql = "DELETE FROM data_invalid WHERE id = ?";

		if($stmt = mysqli_prepare($link, $sql)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "i", $param_id);

			// Set parameters
			$param_id = trim($_POST["id"]);

			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Records deleted successfully. Redirect to landing page
				echo '<script language="javascript" type="text/javascript"> 
									alert("Data berhasil dihapus");
									window.location.replace("invalidtap.php");
						  </script>';
				exit();
			} else{
				echo "Oops! Something went wrong. Please try again later.";
			}
		}

		// Close statement
		mysqli_stmt_close($stmt);

		// Close connection
		mysqli_close($link);
	}
	else{	

		// Check existence of id parameter before processing further
		if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
			// Include config file
			//require_once "config.php";

			// Prepare a select statement
			$sql = "SELECT * FROM data_invalid WHERE id = ?";

			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "i", $param_id);

				// Set parameters
				$param_id = trim($_GET["id"]);

				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					$result = mysqli_stmt_get_result($stmt);

					if(mysqli_num_rows($result) == 1){
						/* Fetch result row as an associative array. Since the result set
						contains only one row, we don't need to use while loop */
						$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

						/* Retrieve individual field value
						{INDIVIDUAL_FIELDS}
						$name = $row["name"];
						$address = $row["address"];
						$salary = $row["salary"];
						 */
					} else{
						// URL doesn't contain valid id parameter. Redirect to error page
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
		} else{
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
  <link rel="icon" type="image/png" href="<?php echo $icon_bar; ?>">
  <title><?php echo $title_bar; ?></title>
  
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
  <link href="../../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="../../assets/css/material-dashboard.css?v=3.0.4" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-200">
  <!-- Sidebar -->
  <?php include 'part_sidenav.php';?>
  <!-- End of Sidebar -->
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
	 <?php include 'part_topnav.php';?>
	<!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row min-vh-80 mb-3">
        <div class="col-12">
          <div class="card my-4 h-100">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Kartu Invalid</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2 h-80">
				<div class="row justify-content-center"> 
				 <div class="col-md-80 w-80">
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
						  <div class="alert alert-secondary text-white h-40" role="alert">
								<p>Apakah anda ingin menghapus data ini?</p>
								 <p>UID     : <b><?php echo $row["uid"]; ?></b> <br>
									Tanggal : <?php echo $row["tanggal"]; ?><br>
									Jam Tap : <?php echo $row["waktu"]; ?><br>
									Status  : <?php echo $row["status"]; ?></p><br>
						  </div>
						  <input type="hidden" name="id" value="<?php echo $row["id"]; ?>"/>
						  <hr>
						<div class="d-flex justify-content-end">
							<input type="submit" value="Ya" class="btn bg-gradient-danger mb-0 toast-btn"> &nbsp
							<a href="invalidtap.php" class="btn bg-gradient-info mb-0 toast-btn">Batal</a>
						</div> 
						</form>
										
				 </div>
				</div> 
            </div>
          </div>
        </div>
      </div>
      <!-- footer -->
		 <?php include 'part_footer.php';?>
	  <!-- footer -->
	</div>
  </main>
  <?php include 'part_theme_config.php';?>
  
  <!--   Core JS Files   -->
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
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../assets/js/material-dashboard.min.js?v=3.0.4"></script>
</body>

</html>