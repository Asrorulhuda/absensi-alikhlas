<?php
//header('Location: apps'); /* Redirect browser */

/* Make sure that code below does not get executed when we redirect. */
//exit;
session_start();
require_once "include/db_config.php";

	$sql = "SELECT * FROM system_config WHERE id =1";

	$system_conf = mysqli_query($GLOBALS["___mysqli_ston"],$sql);
	$row = mysqli_fetch_array($system_conf);
	
	$nama_perusahaan = $row["company"];
	$title_bar = $row["title_bar"];
	$icon_bar = $row["icon_bar"];
	$icon_dashboard = $row["icon_dashboard"];
	$company = $row["company"];
	$footer = $row["footer"];
	$sign_in_bg = $row["sign_in_bg"];
	
	if(strpos($icon_dashboard, "../../") !== false){
		$subject = $icon_dashboard ;
		$search = '../../' ;
		$icon_trimmed = str_replace($search, '', $subject);
	}else{
		$icon_trimmed = $icon_dashboard;
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
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/system_data/favicon.ico">
  
  <title><?php echo $title_bar; ?> </title>
  <!--     Fonts and icons     -->
  <link href="assets/css/Roboto.css" rel="stylesheet" type="text/css" />
  <!-- Nucleo Icons -->
  <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="assets/js/kit.fontawesome.com_42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="assets/css/Material_icon.css" rel="stylesheet">
  
  <!-- CSS Files -->
  <link id="pagestyle" href="assets/css/material-dashboard-pro.min.css?v=3.0.6" rel="stylesheet" />
  <link href="assets/css/animate.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-200">
  
  <main class="main-content  mt-0">
    <div class="page-header align-items-start min-vh-100" style="background-image: url('<?php echo $sign_in_bg; ?>');">
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-4 col-md-8 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom animate__animated animate__pulse">
              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 animate__animated animate__fadeInDown">
                <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                  <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">System Test</h4>
                  <div class="row mt-3">
				    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0"><?php echo $title_bar; ?></h4>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <form role="form" class="text-start" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<div class="input-group input-group-static mb-4">
					    <label>Member</label>
						<select class="form-control choices_input" name="data_uid" id="data_uid" placeholder="">
						  
						  <?php
								//$division = $d_member['p_division'];
								$sql_member = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM personnel");
								while($data_member = mysqli_fetch_assoc($sql_member)){
									echo '<option value="'.$data_member['p_uid'].'">'.$data_member['p_name'].'  ['.$data_member['p_uid'].']</option>';
								}
							?>
						</select>
					</div>
					<div class="input-group input-group-static mb-4">
					    <label>Reader Type</label>
							<select class="form-control choices_input" name="dev_eui" id="dev_eui" placeholder="Reader Type">
							  <?php
							  
							  
								$sql_deveui = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT r_name,d_type,d_eui FROM reader_devices,room WHERE d_location=r_id");
								while($data_deveui = mysqli_fetch_assoc($sql_deveui)){
									echo '<option value="'.$data_deveui['d_eui'].'">'.$data_deveui['r_name'].'-'.$data_deveui['d_type'].'    ['.$data_deveui['d_eui'].']</option>';
								}
								?>
							</select>
						
					</div>
					
                  <div class="form-check form-switch d-flex align-items-center mb-3">
                  </div>
                  <div class="text-center">
					<button type="button" class="btn bg-gradient-primary w-100 my-4 mb-2" id="button_tap"> TAP </button>
									
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
	
	<!-- Modal -->
	<div class="modal fade" id="modal_response" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title font-weight-normal" id="exampleModalLabel">Reader Response!</h5>
			<button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="row justify-content-center text-center">
			
			   <p id="response_tanggal" style="font-size:20px;">date-time</p><br>
			   <h6 id="response_nama" style="font-size:20px;">date-time</h6><br>
			   <h6 id="response_room" style="font-size:20px;">date-time</h6><br>
			   <p id="response_message" style="font-size:20px;">date-time</p>
			 </div>
		  </div>
		  <div class="modal-footer justify-content-center">
			<button type="button" class="btn bg-gradient-primary" data-bs-dismiss="modal">OK</button>
		  </div>
		</div>
	  </div>
	</div>
	
	
  </main>


  <script src="assets/js/jquery-3.6.3.min.js"></script>
  <!--   Core JS Files  
  <script src="assets/js/plugins/jquery.min.js"></script>  -->
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="assets/js/plugins/choices.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script type="text/javascript">
	$(document).ready(function () {
		$("#button_tap").click(function(){
			var uid = $("#data_uid").val();
			var dev_eui = $("#dev_eui").val();
			var datetime = '';
			var room_stat = '';
			
			$.ajax({
				type: "GET", //we are using GET method to get data from server side
				url: 'webapi/api/create.php?uid='+uid+'&dev_eui='+dev_eui, // get the route value
				success: function (response) {//once the request successfully process to the server side it will return result here
					response = JSON.stringify(response);
					response = JSON.parse(response);
					datetime = response.tanggal+' '+response.waktu;
					room_stat = response.room+' - '+response.status;
					console.log(response.resp_code);
					$("#response_room").text(room_stat);
					$("#response_nama").text(response.nama);
					$("#response_tanggal").text(datetime);
					$("#response_message").text(response.message);
					$("#modal_response").modal("show");
				}
			});
		});
	});
  </script>
  
  <script>
    if (document.getElementById('data_uid')) {
      var element = document.getElementById('data_uid');
      const example = new Choices(element, {
        searchEnabled: true
      });
    };
	 if (document.getElementById('dev_eui')) {
      var element = document.getElementById('dev_eui');
      const example = new Choices(element, {
        searchEnabled: true
      });
    };
  </script>
  <!-- Github buttons async defer src="assets/js/buttons_github.js" -->
  <script src="assets/js/buttons_github.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/material-dashboard.min.js?v=3.0.4"></script>
 
  
</body>

</html>