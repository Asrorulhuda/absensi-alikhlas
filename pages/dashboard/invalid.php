<?php 
	date_default_timezone_set('Asia/Jakarta');
	session_start();
	if ( $_SESSION['akses']!= 'Admin'){// handling if dont'have session

		header('location:../../index'); 
		exit();
	} 
	$ses_name = $_SESSION['name'];
	$_SESSION['pages']="Invalid";
    
	require_once "../../include/db_config.php";
	include "control/confignusers_data.php";
	$total_data ="";
	
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		
		$id_inval = trim($_POST["id_inval"]);
		$sql = "DELETE FROM data_invalid WHERE id ='$id_inval'";
 
        $proses = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if ($proses) {
			
			header('location:invalid');
			//301 => berhasil dihapus
			
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
  
   
  <link rel="stylesheet" id="pagestyle" href="../../assets/vendor/DataTables_package/jquery.dataTables.min.css"  /> 
  <link rel="stylesheet" type="text/css" href="../../assets/vendor/DataTables_package/buttons.dataTables.min.css" />
  <link rel="stylesheet" type="text/css" href="../../assets/vendor/DataTables_package/dataTables.bootstrap5.min.css" />
  <style>
    table.dataTable.no-footer {
        border-bottom: 0 !important;
    }
  </style>
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
          <div class="card my-4 h-100" id="data_list">
		  
			<div class="card-header pb-0">
				<div class="d-lg-flex">
					<div>
						<h5 class="mb-0">Kartu Invalid</h5>
						<p class="text-sm mb-0">
						Invalid Tap (status kartu belum terdaftar)
						</p>
					</div>
					<div class="ms-auto my-auto mt-lg-0 mt-4">
					    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
						  <button type="button" class="btn btn-primary btn-sm" id="btn_add"  data-bs-toggle="modal" data-bs-target="#deleteAllModal" ><i class="fa fa-trash"> </i>&nbsp Hapus Semua Data</button>
						</div>
					</div>
				</div>
			</div>
				
            <div class="card-body px-0 pb-2">
				<div class="row justify-content-center"> 
					<div class="table-responsive p-0 w-95">
					   
					   <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
					   
					   <table id="table_invalid" class="table table-hover table-fixed" style="width:100%">
					     <!--<table id="table_siswa" class="table align-items-center mb-0 display table-striped" style="width:100%">  -->
							<thead>
								<tr>
								    <th class="text-center">No</th>
									<th class="text-center">Tanggal</th>
									<th class="text-center">Waktu</th>
									<th class="text-center">UID</th>
									<th class="text-center">Status</th>
									<th class="text-center" >Action</th>
								</tr>
								
							</thead>
								<tbody style="border-style:none;">
								    <?php
									    $no = 0;
										$sql = "SELECT * FROM data_invalid ORDER BY tanggal";
										$s_invalid = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
										$total_data = mysqli_num_rows($s_invalid);
										while ($row=mysqli_fetch_array($s_invalid)){
											$no++;
									  
									?>
									   <tr style="border-bottom: 0;">
									        <td class="text-center"><?= $no;?></td>
											<td class="text-center"><?= $row['tanggal'];?></td>
											<td class="text-center"><?= $row['waktu'];?></td>
											<td class="text-center"><?= $row['uid'];?></td>
											<td class="text-center"><?= $row['status'];?></td>
											<td  class="text-center">
											<?php
											    echo "<a href='siswa_registration?uid=". $row['uid'] ."' title='Daftarkan Kartu' data-toggle='tooltip'><span class='fa fa-plus'></span></a> &nbsp;&nbsp;";
												echo "<a href='javascript:;' data-bs-toggle='modal' data-bs-target='#deleteModal' id='getDelInvalid' title='Delete Record' data-toggle='tooltip'
												        data-id='".  $row['id'] ."' data-uid='".  $row['uid'] ."' data-tanggal='".  $row['tanggal'] ."' data-waktu='".  $row['waktu'] ."' >
														<span class='fa fa-trash'></span></a> &nbsp;&nbsp;&nbsp;&nbsp;";
													  
											?>
											</td>
									   
									   </tr>
									
									<?php ;} ?>
								
								</tbody>
						</table>
					   </div>
					</div>
				</div>
            </div>
          </div>
        </div>
	  </div>
	
			
	<!-- Modal ALL delete -->
	<div class="modal fade" id="deleteAllModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-primary">
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">Delete Data Invalid</h5>
			<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">X
			</button>
			
		  </div>
		  <div class="modal-body">
		     <div class="row my-2 mx-2">
				<p class="text-center" style="font-size:16px;">Hapus seluruh data Kartu Invalid ? <br> Terdapat <b><?=$total_data ?></b> total data invalid </p>
		     </div>
			 
		  </div>
		  <div class="modal-footer">
			<a href="control/invalidtap_delete_all" class="btn bg-gradient-danger"  id="modalDelete" >Hapus</a>&nbsp
			<button type="button" class="btn bg-gradient-secondary shadow-secondary" data-bs-dismiss="modal">Cancel</button> 
		  </div>
		</div>
	  </div>
	</div>
	
    <!-- Modal delete -->
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-primary">
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">Hapus Data Invalid</h5>
			<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">X
			</button>
			
		  </div>
		  
		  <div class="modal-body mb-2">
		     <div class="row my-2 mx-2 text-center">
				<p class="text-center" style="font-size:16px;">Hapus data Kartu Invalid berikut?</p>
				<span style="font-size:20px;" id="del_uid"></span>
				<span id="del_tgl"></span>
		     </div>
			 
		  </div>
		  <div class="modal-footer">
			  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			     <input type="hidden" id="id_inval" name="id_inval">
			     <input type="submit" class="btn bg-gradient-primary" style="color:white" value="Hapus Data">&nbsp
				<button type="button" class="btn bg-gradient-secondary shadow-secondary" data-bs-dismiss="modal">Cancel</button> 
			  </form>
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
  <script src="../../assets/vendor/DataTables_package/jquery-3.5.1.js"></script>  
  <script src="../../assets/vendor/DataTables_package/jquery.dataTables.min.js"></script>
  <script src="../../assets/vendor/DataTables_package/dataTables.buttons.min.js"></script>
  <script src="../../assets/vendor/DataTables_package/jszip.min.js"></script>
  <script src="../../assets/vendor/DataTables_package/pdfmake.min.js"></script>
  <script src="../../assets/vendor/DataTables_package/vfs_fonts.js"></script>
  <script src="../../assets/vendor/DataTables_package/buttons.html5.min.js"></script>
  <script src="../../assets/vendor/DataTables_package/buttons.colVis.min.js"></script>
  
  
  <!--   Core JS Files   -->
  <script src="../../assets/js/core/popper.min.js"></script>
  <script src="../../assets/js/core/bootstrap.min.js"></script>
  <script src="../../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../../assets/js/plugins/choices.min.js"></script>
  
  <!-- Github buttons -->
  <script async defer src="../../assets/js/buttons_github.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../assets/js/material-dashboard-pro.min.js?v=3.0.6"></script>
    
  <script type="text/javascript">
        $(document).ready(function(){
			$('[data-toggle="tooltip"]').tooltip();
			var table = $('#table_invalid').DataTable();
						
			$(document).on('click', '#getDelInvalid', function(e){
			  
				 e.preventDefault();
				 var id = $(this).data('id'); 
				 var uid = $(this).data('uid'); 
				 var tanggal = $(this).data('tanggal'); 
				 var waktu = $(this).data('waktu'); 
				 tanggal = tanggal+' '+waktu;
				 //$('#modalDelete').attr('href','control/siswa_del.php?id='+uid);
				 $('#del_tgl').text(tanggal);
				 $('#del_uid').text(uid);
				 $('#id_inval').val(id);
				 $('#modal-loader').show();      // load ajax loader on button click
				

			});
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