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
	include "control/confignusers_data.php";
	
	$sql = "SELECT * FROM users";
	
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
		  <div class="multisteps-form mb-9" id="data_edit" style="display:none;">
			<div class="row">
				<div class="col-12 col-lg-8 mx-auto my-5">
				</div>
			</div>			
			<div class="row" id="view_data_edit">
			
			</div>
		  
		  </div>
		  
		  <div class="row h-100 align-items-center" id="edit_loader" style="display:none;">
		    <div class="row justify-content-center" >
				    <div class="spinner-border text-danger justify-content-center" role="status">
						 <span class="visually-hidden">Loading...</span>
					</div>
			</div>
		        
		  </div>
		  
          <div class="card my-4 h-100" id="data_list">
		  
			<div class="card-header pb-0">
				<div class="d-lg-flex">
					<div>
						<h5 class="mb-0">User's List</h5>
						<p class="text-sm mb-0">
						Application users list
						</p>
					</div>
					<!--
					
					-->
					<div class="ms-auto my-auto mt-lg-0 mt-4">
						<div class="ms-auto my-auto">
						<a href="user_create" class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; Add User</a>
						
						<div class="btn-group">
							<button id="btnPrintGroup" type="button" class="btn bg-gradient-info btn-sm mb-0 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="material-icons text-sm">print</i>&nbsp; Cetak Akun
							</button>
							<ul class="dropdown-menu" aria-labelledby="btnPrintGroup">
								<li><a class="dropdown-item" href="user_print?role=all" target="_blank">Semua Akun</a></li>
								<li><a class="dropdown-item" href="user_print?role=Admin" target="_blank">Akun Admin</a></li>
								<li><a class="dropdown-item" href="user_print?role=Guru" target="_blank">Akun Guru</a></li>
								<li><a class="dropdown-item" href="user_print?role=User" target="_blank">Akun Siswa</a></li>
							</ul>
						</div>

						<button class="btn btn-outline-primary btn-sm export mb-0 mt-sm-0 mt-1" data-type="csv" type="button" name="button">Export</button>
						</div>
					</div>
				</div>
				
			</div>
				
            <div class="card-body px-0 pb-2">
				<div class="row justify-content-center"> 
					<div class="table-responsive p-0 w-95">
					   
					   <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
					    <table id="table_user" class="table align-items-center mb-0 display" style="width:100%">
						<!-- <table id="table_user" class="table align-items-center mb-3 dataTable js-exportable> -->
							<thead>
								<tr>
									<th>User</th>
									<th>Username</th>
									<th>Mail</th>
									<th>Access Type</th>
									<th>Action</th>
								</tr>
								<tbody>
								    <?php
										$s_user = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
										while ($d_user = mysqli_fetch_array($s_user)){
											$id64 = base64_encode($d_user['id']);
									  
									?>
									   <tr>
											<td>
												<div class="d-flex px-2 py-1">
												    <div>
													    <img src="<?= htmlspecialchars($d_user['picture']);?>" class="avatar avatar-sm me-3 border-radius-lg" alt="venue <?= htmlspecialchars($d_user['username']);?>">
														
												    </div>
													<div class="d-flex flex-column justify-content-center">
														<p class="text-md mb-0"><?= htmlspecialchars($d_user['name']);?></p>
													</div>	
												</div>
											</td>
											<td>
												<p class="text-md mb-0"><?= htmlspecialchars($d_user['username']);?></p>
											</td>
											<td><p class="text-md mb-0"><?= htmlspecialchars($d_user['email']);?></p></td>
											<td><p class="text-md mb-0"><?= htmlspecialchars($d_user['level_akses']);?></p></td>
											
											<td>
											<?php
											    echo "<a href='javascript:;' data-bs-toggle='modal' data-bs-target='#detailModal' data-id='". $d_user['id'] ."' id='getDetailUser'
													     title='Detail User' data-toggle='tooltip'><span class='fa fa-eye'></span></a>&nbsp;&nbsp;";
												echo "<a href='user_print?id=". $id64 ."' target='_blank' title='Cetak Kartu Login' data-toggle='tooltip'><span class='fa fa-print'></span></a>&nbsp;&nbsp;";
												echo "<a href='user_update?id_user=". $id64 ."' title='Update Record' data-toggle='tooltip'><i class='far fa-edit'></i></a>&nbsp;&nbsp;";
												echo "<a href='javascript:;' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='". $d_user['id'] ."' data-id_siswa='". $d_user['id_siswa'] ."' id='getDelUser'
													     title='Delete User' data-toggle='tooltip'><span class='fa fa-trash'></span></a>";
													  
											?>
											</td>
									   
									   </tr>
									
									<?php } ?>
								
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
	
	<!-- Modal detail -->
	<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-primary">
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">User Detail</h5>
			<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">X
			</button>
			
		  </div>
		  <div class="modal-body">
			 <div class="spinner-border text-danger justify-content-center" role="status" id="modal-detailloader" style="display: none;">
			     <span class="visually-hidden">Loading...</span>
			 </div>
			 <div id="data_detail">
			 </div>
		  </div>
		  <div class="modal-footer">
			<!-- <a href="#" class="btn bg-gradient-info"  id="modalDetailPrint" >Print</a>&nbsp -->
			<button type="button" class="btn bg-gradient-secondary shadow-secondary" data-bs-dismiss="modal">OK</button> 
		  </div>
		</div>
	  </div>
	</div>

	
	<!-- Modal delete -->
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-primary">
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">Delete User</h5>
			<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">X
			</button>
			
		  </div>
		  <div class="modal-body">
			 <div class="spinner-border text-danger justify-content-center" role="status" id="modal-loader" style="display: none;">
			     <span class="visually-hidden">Loading...</span>
			 </div>
			 <div id="data_delete">
			 </div>
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
  
  <!-- Github buttons -->
  <script async defer src="../../assets/js/buttons_github.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../assets/js/material-dashboard-pro.min.js?v=3.0.6"></script>
  
  <script type="text/javascript">
        $(document).ready(function(){
			$('[data-toggle="tooltip"]').tooltip();
			
				
			$(document).on('click', '#getDetailUser', function(e){
			  
				 e.preventDefault();
			  
				 var uid = $(this).data('id'); // get id of clicked row
			  
				 $('#data_detail').html(''); // leave this div blank
				 $('#modal-detailloader').show();      // load ajax loader on button click
				
				 $.ajax({
					  url: 'view/user_detail.php',
					  type: 'POST',
					  data: 'id='+uid,
					  dataType: 'html'
				 })
				 .done(function(data){
					  //console.log(data); 
					  $('#data_detail').html(''); // blank before load.
					  $('#data_detail').html(data); // load here  
					  $('#modal-detailloader').hide(); // hide loader 					  
				 })
				 .fail(function(){
					  $('#data_detail').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
					  $('#modal-detailloader').hide();
				 });

			});			
			
			$(document).on('click', '#getDelUser', function(e){
			  
				 e.preventDefault();
			  
				 var uid = $(this).data('id'); // get id of clicked row
				 var id_siswa = $(this).data('id_siswa'); // get id of clicked row
			  
				 $('#data_delete').html(''); // leave this div blank
				 $('#modalDelete').attr('href','control/user_del.php?id='+uid+'&id_siswa='+id_siswa);
				 $('#modal-loader').show();      // load ajax loader on button click
				
				 $.ajax({
					  url: 'view/user_del.php',
					  type: 'POST',
					  data: {id : uid, id_siswa : id_siswa},
					  dataType: 'html'
				 })
				 .done(function(data){
					  //console.log(data); 
					  $('#data_delete').html(''); // blank before load.
					  $('#data_delete').html(data); // load here  
					  $('#modal-loader').hide(); // hide loader 					  
				 })
				 .fail(function(){
					  $('#data_delete').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
					  $('#modal-loader').hide();
				 });

			});
			
        });
  </script>
  <script>
    if (document.getElementById('table_user')) {
      const dataTableSearch = new simpleDatatables.DataTable("#table_user", {
        searchable: true,
        fixedHeight: false,
        perPage: 5
      });

      document.querySelectorAll(".export").forEach(function(el) {
        el.addEventListener("click", function(e) {
          var type = el.dataset.type;

          var data = {
            type: type,
            filename: "List Member-" + type,
          };

          if (type === "csv") {
            data.columnDelimiter = "|";
          }

          dataTableSearch.export(data);
        });
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
		
		if($stat_msg == "300"){
		$modal_stat ="Delete Status";
		echo "<script type='text/javascript'>
			$(document).ready(function(){
				$('#Modal_notif_head').addClass('bg-primary'); 
				$('#notif_msg').text('Failed to delete the user record!');
				$('#Modal_notif').modal('show');
			});
			</script>";
		}
		if($stat_msg == "301"){
		$modal_stat ="Delete Status";
		echo "<script type='text/javascript'>
			$(document).ready(function(){
			  $('#Modal_notif_head').addClass('bg-success'); 
			  $('#notif_msg').text('The User record deleted successfully');
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
		
		if($stat_msg == "400"){
			$modal_stat ="Alert";
			echo "<script type='text/javascript'>
					$(document).ready(function(){
					  $('#Modal_notif_head').addClass('bg-primary'); 
					  $('#Modal_notif').modal('show');
					});
				</script>";
		}
		if($stat_msg == "401"){
			$modal_stat ="Alert";
			echo "<script type='text/javascript'>
					$(document).ready(function(){
					  $('#Modal_notif_head').addClass('bg-success'); 
					  $('#Modal_notif').modal('show');
					});
				</script>";
		}
		if($stat_msg == "402"){
			$modal_stat ="Alert";
			echo "<script type='text/javascript'>
					$(document).ready(function(){
					  $('#Modal_notif_head').addClass('bg-primary'); 
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
			  if ($modal_stat == "Alert"){
				  echo "<i class='far fa-bell'></i>";  
			  }
			
			?>
			</i>&nbsp; <?php echo $modal_stat;?></h5>
			<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
			</button>
		  </div>
		  <div class="modal-body mt-3">
			 <div class="d-flex justify-content-center">
			   <p id="notif_msg" class="text-center" style="font-size:16px;">
			   <?php
			        if ($modal_stat == "Alert"){
							echo base64_decode($_GET['info']);  
					  }
			   ?>
			   </p><br>
			 </div>
		  </div>
		  <div class="modal-footer justify-content-center">
			<a role="button" class="btn bg-gradient-secondary shadow-secondary"  data-bs-dismiss="modal">OK</a>
		  </div>
		</div>
	  </div>
	</div>
</body>

</html>