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
	include "control/confignusers_data.php";
	
	$kelas="";
	$jurusan="";
	$sql="";
	
	
	$sql = "SELECT * FROM data_guru ORDER BY g_id DESC";
	
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
		  
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Daftar Guru</h6>
                <p class="text-white text-sm ps-3 mb-0">List seluruh guru aktif</p>
              </div>
            </div>
				
            <div class="card-body px-0 pb-2">
                <div class="d-flex justify-content-between align-items-center px-4 mt-4 mb-3">
                    <div class="btn-group">
                        <button type="button" class="btn bg-gradient-primary btn-sm mb-0" id="btn_add"  onClick="location.href='guru_registration'">
                            <i class="material-icons text-sm">add</i>&nbsp; Tambah Data Guru
                        </button>
                        <div class="btn-group ms-2">
                            <button id="btnGroupDrop1" type="button" class="btn bg-gradient-info btn-sm mb-0 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="material-icons text-sm">file_download</i>&nbsp; Export
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <li><a class="dropdown-item" href="javascript:void(0)" id="export_pdf"   onclick="$('#table_guru').DataTable().buttons(0,2).trigger()">PDF</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" id="export_excel" onclick="$('#table_guru').DataTable().buttons(0,1).trigger()">Excel</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" id="export_csv"   onclick="$('#table_guru').DataTable().buttons(0,0).trigger()">CSV</a></li>  
                            </ul>
                        </div>
                        <button type="button" class="btn bg-gradient-success btn-sm mb-0 ms-2" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="material-icons text-sm">file_upload</i>&nbsp; Import
                        </button>
                    </div>
                </div>

				<div class="row justify-content-center"> 
					<div class="table-responsive p-0 w-95">
					   
					   <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
					   
					   <table id="table_guru" class="table table-hover table-fixed" style="width:100%">
					     <!--<table id="table_siswa" class="table align-items-center mb-0 display table-striped" style="width:100%">  -->
							<thead>
								<tr>
								    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Guru</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jenis Kalamin</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jabatan/Tugas Tambahan</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kompetensi</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Umur</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
								</tr>
								
							</thead>
								<tbody style="border-style:none;">
								    <?php
									    $no = 0;
										$g_list = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
										while ($g_data=mysqli_fetch_array($g_list)){
											$id64 = base64_encode($g_data['g_id']);
											$no++;
									  
									?>
									   <tr style="border-bottom: 0;">
									        <td><?= $no;?></td>
											<td>
												<div class="d-flex px-2 py-1"><div><img src="<?= $g_data['g_picture'];?>" class="avatar avatar-sm me-3 border-radius-lg" alt="user<?= $g_data['g_id'];?>"></div>
													<div class="d-flex flex-column justify-content-center">
														<h6 class="mb-0 text-sm"><?= $g_data['g_nama'];?></h6>
														<p class="text-xs text-secondary mb-0"><?= $g_data['g_nip'];?></p> 
													</div>	
												</div>
											</td>
											<td  class="text-center"><?= $g_data['g_kelamin'];?></td>
											<td  class="text-center">
												<div class="mb-0"><?= $g_data['g_jabatan'];?></div>
												<p class="text-xs text-secondary mb-0"><?= $g_data['g_tgs_tambahan'];?></p> 
											</td>
											<td class="text-center"><?= $g_data['g_kompetensi'];?> </td>
											<td  class="text-center">
											<?php
												$dateOfBirth = $g_data['g_tgl_lahir'];
												$today = date("Y-m-d");
												$diff = date_diff(date_create($dateOfBirth), date_create($today));
												echo $diff->format('%y');
											?>
											</td>
											<td class="text-center">
											<?php
											    echo "<a href='javascript:;' class='btn btn-link text-info text-gradient px-3 mb-0' data-bs-toggle='modal' data-bs-target='#detailModal' data-id='". $g_data['g_id'] ."' id='getDetailGuru'
													     title='Detail Record' data-toggle='tooltip'><i class='material-icons text-sm me-2'>visibility</i>Detail</a>";
												//echo "<a href='javascript:;' data-id='". $g_data['p_id'] ."' title='Update Record' data-toggle='tooltip' id='UpdateUser' ><i class='far fa-edit'></i></a>&nbsp;&nbsp;";
												echo "<a href='guru_update?id_guru=". $id64 ."' class='btn btn-link text-dark px-3 mb-0' title='Update Record' data-toggle='tooltip'><i class='material-icons text-sm me-2'>edit</i>Edit</a>";
												echo "<a href='javascript:;' class='btn btn-link text-danger text-gradient px-3 mb-0' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='". $g_data['g_id'] ."' id='getDelGuru'
													     title='Delete Record' data-toggle='tooltip'><i class='material-icons text-sm me-2'>delete</i>Delete</a>";
														 
												
													  
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
	
	<!-- Modal detail -->
	<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-primary">
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">Detail Guru</h5>
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
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">Hapus Data Guru</h5>
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
						
			$(document).on('click', '#getDelGuru', function(e){
			  
				 e.preventDefault();
			  
				 var id = $(this).data('id'); // get id of clicked row
			  
				 $('#data_delete').html(''); // leave this div blank
				 $('#modalDelete').attr('href','control/guru_del.php?id='+id);
				 $('#modal-loader').show();      // load ajax loader on button click
				
				 $.ajax({
					  url: 'view/guru_del.php',
					  type: 'POST',
					  data: 'id='+id,
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
			
			$(document).on('click', '#getDetailGuru', function(e){
			  
				 e.preventDefault();
			  
				 var uid = $(this).data('id'); // get id of clicked row
			  
				 $('#data_detail').html(''); // leave this div blank
				 $('#modal-detailloader').show();      // load ajax loader on button click
				
				 $.ajax({
					  url: 'view/guru_detail.php',
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
			
			//table
			//new DataTable('#table_siswa');
			//var table = $('#table_siswa').DataTable({
			//	"columnDefs": [ { "orderable": false, "targets": 5 }]
			//});
			
			
			$('#table_guru').DataTable( {
				columnDefs: [ { 'orderable': false, 'targets': 6 }],
				buttons: [
					{
						extend: 'csvHtml5',
						title:'Daftar Guru',
						exportOptions: {
							columns: [ 0, 1, 2, 3, 4, 5 ]
						}
					},
					{
						extend: 'excelHtml5',
						title:'Daftar Guru',
						exportOptions: {
							columns: [ 0, 1, 2, 3, 4, 5 ]
						}
					},
					{
						extend: 'pdfHtml5',
						title:'Daftar Guru',
						exportOptions: {
							columns: [ 0, 1, 2, 3, 4, 5 ]
						},
						customize: function (doc) {
                           doc.defaultStyle.fontSize = 11; //2, 3, 4,etc
                           doc.styles.tableHeader.fontSize = 12; //2, 3, 4, etc
						   doc.styles.title.fontSize = 16;
						   doc.styles.title.bold = true;
                           doc.content[1].table.widths = [ '5%', '30%',  '15%', '23%', '22%', '10%'];
						   var now = new Date();
						   var jsDate = now.getDate()+'-'+(now.getMonth()+1)+'-'+now.getFullYear();
						   doc['footer']=(function(page, pages) {
								return {
									columns: [
										{
											alignment: 'left',
											text: ['Created on: ', { text: jsDate.toString() }]
										},
										{
											alignment: 'right',
											text: ['page ', { text: page.toString() },	' of ',	{ text: pages.toString() }]
										}
									],
									margin: 20
								}
							});
						   var objLayout = {};
							objLayout['hLineWidth'] = function(i) { return .5; };
							objLayout['vLineWidth'] = function(i) { return .5; };
							objLayout['hLineColor'] = function(i) { return '#aaa'; };
							objLayout['vLineColor'] = function(i) { return '#aaa'; };
							objLayout['paddingLeft'] = function(i) { return 4; };
							objLayout['paddingRight'] = function(i) { return 4; };
							doc.content[1].layout = objLayout;
                       }
					}
				]
			} );
			
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
  <?php
  	if(isset($_GET['msg'])){
		$stat_msg=base64_decode($_GET['msg']);
		
		if($stat_msg == "300"){
		$modal_stat ="Delete Status";
		echo "<script type='text/javascript'>
			$(document).ready(function(){
				$('#Modal_notif_head').addClass('bg-primary'); 
				$('#notif_msg').text('Gagal menghapus data Guru, silahkan ulangi proses!');
				$('#Modal_notif').modal('show');
			});
			</script>";
		}
		if($stat_msg == "301"){
		$modal_stat ="Delete Status";
		echo "<script type='text/javascript'>
			$(document).ready(function(){
			  $('#Modal_notif_head').addClass('bg-success'); 
			  $('#notif_msg').text('Data Guru berhasil dihapus');
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
			<a role="button" class="btn bg-gradient-secondary shadow-secondary"  data-bs-dismiss="modal">OK</a>
		  </div>
		</div>
	  </div>
	</div>
<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success">
                <h5 class="modal-title text-white" id="importModalLabel">Import Data Guru (CSV)</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="guru_import.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Pilih File CSV</label>
                        <input class="form-control border p-2" type="file" name="csv_file" id="csv_file" accept=".csv" required>
                    </div>
                    <div class="mb-3">
                        <p class="text-sm">Gunakan template berikut untuk format data yang benar:</p>
                        <a href="templates/template_guru.csv" class="btn btn-outline-success btn-sm w-100" download>
                            <i class="material-icons text-sm">download</i> Download Template CSV
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn bg-gradient-success">Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>

</html>