<?php 
	session_start();
	date_default_timezone_set('Asia/Jakarta');
	if ( $_SESSION['akses']!= 'Admin'){// handling if dont'have session

		header('location:../../index'); 
		exit();
	} 
	$ses_name = $_SESSION['name'];
	$_SESSION['pages']="Presensi";
    
	require_once "../../include/db_config.php";
	include "control/confignusers_data.php";
	
	$tanggal_now = date("Y-m-d");
	$set_tanggal = $tanggal_now;
	$kelas="";
	$jurusan="";

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$kelas = $_POST["choices-kelas"]; 
		$jurusan= $_POST["choices-jurusan"]; 
		$set_tanggal = $_POST["set_tanggal"];
		if ($set_tanggal==""){
			$set_tanggal = $tanggal_now;
		}else{
			$set_tanggal = $set_tanggal;
		}
		
		if($jurusan != ""){
			if($kelas!= ""){
				$sql = "SELECT data_absen.id, tanggal, jam_masuk, jam_keluar, s_nama , ket_masuk, ket_keluar, keterangan, status, s_picture, s_kelas, j_short 
						FROM data_absen 
						JOIN data_siswa ON data_absen.uid=s_uid
						LEFT JOIN opsi_jurusan ON s_jurusan=j_id
						WHERE s_jurusan='$jurusan' AND tanggal = '$set_tanggal' AND s_kelas = '$kelas'
						ORDER BY jam_masuk DESC";
			}else{
				$sql = "SELECT data_absen.id, tanggal, jam_masuk, jam_keluar, s_nama , ket_masuk, ket_keluar, keterangan, status, s_picture, s_kelas, j_short 
						FROM data_absen 
						JOIN data_siswa ON data_absen.uid=s_uid
						LEFT JOIN opsi_jurusan ON s_jurusan=j_id
						WHERE s_jurusan='$jurusan'  AND tanggal = '$set_tanggal' 
						ORDER BY jam_masuk DESC";
			}
		}else{
			if($kelas!= ""){
				$sql = "SELECT data_absen.id, tanggal, jam_masuk, jam_keluar, s_nama , ket_masuk, ket_keluar, keterangan, status, s_picture, s_kelas, j_short 
						FROM data_absen 
						JOIN data_siswa ON data_absen.uid=s_uid
						LEFT JOIN opsi_jurusan ON s_jurusan=j_id
						WHERE s_kelas= '$kelas' AND tanggal = '$set_tanggal' 
						ORDER BY jam_masuk DESC";
			}else{
				$sql = "SELECT data_absen.id, tanggal, jam_masuk, jam_keluar, s_nama , ket_masuk, ket_keluar, keterangan, status, s_picture, s_kelas, j_short 
						FROM data_absen 
						JOIN data_siswa ON data_absen.uid=s_uid
						LEFT JOIN opsi_jurusan ON s_jurusan=j_id
						WHERE tanggal = '$set_tanggal' 
						ORDER BY jam_masuk DESC";
			}
		}
	
	}
	else{
		$sql = "SELECT data_absen.id, tanggal, jam_masuk, jam_keluar, s_nama , ket_masuk, ket_keluar, keterangan, status, s_picture, s_kelas, j_short 
		        FROM data_absen 
				JOIN data_siswa ON data_absen.uid = s_uid
				LEFT JOIN opsi_jurusan ON s_jurusan=j_id
				WHERE tanggal = '$tanggal_now'
			    ORDER BY jam_masuk DESC";
	}
	
	$set_tanggal_long = date_create($set_tanggal);
	
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
		  
			<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Data Presensi Harian</h6>
                <p class="text-white text-sm ps-3 mb-0">List seluruh presensi siswa per hari</p>
              </div>
            </div>
			
			<div class="card-body px-0 pb-2">
				<div class="p-4 bg-gray-100 border-radius-lg mb-4 mx-3">
					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
						<div class="row align-items-end">
							<div class="col-md-3 mb-3">
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Tanggal</label>
								<div class="input-group input-group-outline bg-white">
									<input type="date" class="form-control" name="set_tanggal" value="<?=$set_tanggal ?>">
								</div>
							</div>
							
							<div class="col-md-3 mb-3">
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Kelas</label>
								<div class="input-group input-group-outline bg-white">
									<select class="form-control" name="choices-kelas" id="choices-kelas">
										<option value="" selected>Semua Kelas</option>
										<?php
											$sql_tingkat = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM opsi_tk_kelas");
											while($data_tingkat = mysqli_fetch_assoc($sql_tingkat)){
												$selected = ($kelas == $data_tingkat['tk_name']) ? 'selected' : '';
												echo '<option value="'.$data_tingkat['tk_name'].'" '.$selected.'>'.$data_tingkat['tk_name'].'</option>';
											}
										?>
									</select>
								</div>
							</div>
							
							<div class="col-md-3 mb-3">
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Jurusan</label>
								<div class="input-group input-group-outline bg-white">
									<select class="form-control" name="choices-jurusan" id="choices-jurusan">
										<option value="" selected>Semua Jurusan</option>
										<?php
											$sql_jurusan = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM opsi_jurusan");
											while($data_jurusan = mysqli_fetch_assoc($sql_jurusan)){
												$selected = ($jurusan == $data_jurusan['j_id']) ? 'selected' : '';
												echo '<option value="'.$data_jurusan['j_id'].'" '.$selected.'>'.$data_jurusan['j_short'].'</option>';
											}
										?>
									</select>
								</div>
							</div>
							
							<div class="col-md-3 mb-3 d-flex gap-2">
								<button type="submit" class="btn btn-primary btn-sm mb-0 w-100">
									<i class="material-icons text-sm">filter_alt</i> Filter
								</button>
								<a href="presensi_harian" class="btn btn-outline-secondary btn-sm mb-0 w-100">
									<i class="material-icons text-sm">restart_alt</i> Reset
								</a>
							</div>
						</div>
					</form>
					
					<div class="d-flex justify-content-end mt-3">
						<div class="btn-group">
							<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="material-icons text-sm">file_download</i> Export
							</button>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="javascript:void(0)" onclick="$('#table_presensi').DataTable().buttons(0,2).trigger()">PDF</a></li>
								<li><a class="dropdown-item" href="javascript:void(0)" onclick="$('#table_presensi').DataTable().buttons(0,1).trigger()">Excel</a></li>
								<li><a class="dropdown-item" href="javascript:void(0)" onclick="$('#table_presensi').DataTable().buttons(0,0).trigger()">CSV</a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row justify-content-center"> 
					<div class="table-responsive p-0 w-95">
					   
					   <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
					   
					   <table id="table_presensi" class="table table-hover table-fixed" style="width:100%">
					     <!--<table id="table_presensi" class="table align-items-center mb-0 display table-striped" style="width:100%">  -->
							<thead>
								<tr>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Siswa</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Masuk</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Keluar</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Keterangan</th>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
								</tr>
								
							</thead>
								<tbody style="border-style:none;">
								    <?php
									    $numb =0;
										$s_member = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
										while ($d_member=mysqli_fetch_array($s_member)){
											$id64 = base64_encode($d_member['id']);
											$numb++;
									  
									?>
									   <tr style="border-bottom: 0;">
									   
									        <td><p class="text-sm mb-0 mt-2"><?= $numb;?></p></td>
											<td><p class="text-sm mb-0 mt-2"><?= $d_member['tanggal'];?></p></td>
											<td>
												<div class="d-flex px-2 py-1">
													<div><img src="<?= $d_member['s_picture'];?>" class="avatar avatar-sm me-3 border-radius-lg" alt="user<?= $d_member['s_nama'];?>"></div>
													<div class="d-flex flex-column justify-content-center">
														<h5 class="mb-0 text-sm"><?= $d_member['s_nama'];?></h5>
														<p class="text-xs text-secondary mb-0"><?= $d_member['s_kelas'];?>-<?= $d_member['j_short'];?></p> 
													</div>	
												</div>
											</td>
											<td  class="text-center">
												<div class="d-flex flex-column">
													<p class="text-sm text-secondary mb-0 "><?= $d_member['jam_masuk'];?></p>
													<hr class="horizontal dark mt-1 mb-1">
													<p class="text-xs text-secondary mb-0"><?= $d_member['ket_masuk'];?></p>
												</div>
											</td>
											<td class="text-center">
												<div class="d-flex flex-column">
													<p class="text-sm text-secondary mb-0"><?= $d_member['jam_keluar'];?></p>
													<hr class="horizontal dark mt-1 mb-1">
													<p class="text-xs text-secondary mb-0"><?= $d_member['ket_keluar'];?></p>
												</div>
											</td>
											<td class="text-center"><p class="text-sm text-secondary mb-0 mt-2"><?= $d_member['keterangan'];?></p></td>
											<td class="text-center">
											<?php
												$hnt=date_format($set_tanggal_long ,"l, M jS Y");
												echo "<a href='javascript:;' data-bs-toggle='modal' data-bs-target='#detailModal' data-id='". $d_member['id'] ."' id='getDetailUser'
													     title='Detail Record' data-toggle='tooltip'><span class='fa fa-eye'></span></a>&nbsp;&nbsp;";
												echo "<a href='presensi_update?id=". $d_member['id'] ."' title='Update Data' data-toggle='tooltip'><span class='fa fa-edit'></span></a> &nbsp";
												echo "<a href='#deleteModal' id='getDelUser' title='Hapus Data' data-toggle='tooltip' class='trash' data-id='". $d_member['id'] ."' data-nama='". $d_member['s_nama'] ."'  
													   data-keterangan='". $d_member['keterangan'] ."' data-jamin='". $d_member['jam_masuk'] ."'  data-jamout='". $d_member['jam_keluar'] ."'  
													   data-picture='". $d_member['s_picture'] ."' data-tanggal='".$hnt."' role='button' data-toggle='modal'><span class='fa fa-trash'></span></a>";
												
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
	  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-primary">
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">Detail Presensi</h5>
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
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">Hapus Data Presensi</h5>
			<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">X
			</button>
			
		  </div>
		  <div class="modal-body">
			   <div class="row">
				   <p class="text-center">Apakah anda yakin ingin menghapus data Presensi berikut?<br>
				 </div>
			     <div class="d-flex justify-content-center">
				    <div class="col-md-4 text-center">
				        <img id="picture_modal" src="../assets/img/avatar_profile.png" alt="Siswa Pict" class="rounded-circle z-depth-2" width="100px" height="100px" />
						<p class="text-center mt-2"><b id="data_nama"></b></p>
				    </div>
					<div class="col-md-6 mx-3">
					    <p class="mb-0 mt-3" id="tanggal_modal">22-10-2022</p>
						<p class="mb-0"><span id="in_time">00:00:00</span> - <span id="out_time">00:00:00</span></p>
						<p class="mb-0" id="ket_modal">HADIR</p>
				    </div>
				 </div>
		  </div>
		  <div class="modal-footer">
			<a href="#" class="btn bg-gradient-danger"  id="modalDelete" >Hapus</a>&nbsp
			<button type="button" class="btn bg-gradient-secondary shadow-secondary" data-bs-dismiss="modal">Cancel</button> 
		  </div>
		</div>
	  </div>
	</div>
	
		
	<!-- Modal status-->
	<div class="modal fade" id="Modal_stat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		<?php $stat_modal=$_GET['stat_modal'];?>
		  <div class="modal-header bg-success">
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">
			   <?php if ($stat_modal == "del_ok"){?>
				   <i class='far fa-trash-alt'></i> &nbsp; Delete Status
				   
			   <?php } if ($stat_modal == "edit_ok" ){?>
				   <i class='far fa-edit'></i> &nbsp; Update Status
				<?php } if ($stat_modal == "add_ok" ){?>
				   <i class='far fa-sign-in'></i> &nbsp; Presensi Status
				<?php } ?>
			</h5>
			<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
			</button>
		  </div>
		  <div class="modal-body">
			 <div class="d-flex justify-content-center text-center">
			   <p style="font-size:16px;">Data presensi <b><?php echo $_GET["nama"]; ?></b> 
			   <?php if ($stat_modal == "del_ok"){?>
					&nbsp; pada <br> <b><?php echo $_GET["tgl"]; ?></b><br> berhasil dihapus!
				   
			   <?php } if ($stat_modal == "edit_ok" ){?>
				   &nbsp; pada <br> <b><?php echo $_GET["tgl"]; ?></b><br> berhasil di perbaharui.
				<?php } if ($stat_modal == "add_ok" ){?>
				   &nbsp; berhasil ditambahkan untuk tanggal <b><?php echo $_GET["tgl"]; ?></b> 
				<?php } ?>
			   
			   
			  </p><br>
			 </div>
		  </div>
		  <div class="modal-footer justify-content-center">
			<a role="button" class="btn bg-gradient-success"  href="presensi_harian" aria-pressed="true">OK</a>
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
						
			 $('.trash').click(function(){
					var id=$(this).data('id');
							var nama=$(this).data('nama');
							var picture_modal=$(this).data('picture');
							var ket_modal=$(this).data('keterangan');
							var in_modal=$(this).data('jamin');
							var out_modal=$(this).data('jamout');
							var tanggal=$(this).data('tanggal');
							$('#data_nama').text(nama);
							$('#ket_modal').text(ket_modal);
							$('#tanggal_modal').text(tanggal);
							$('#in_time').text(in_modal);
							$('#out_time').text(out_modal);
							$('#picture_modal').attr('src',picture_modal);
							$('#modalDelete').attr('href','control/presensi_delete?id='+id+'&nama='+nama+'&tgl='+tanggal+'');
							$('#deleteModal').modal('show');
			  })
			
			$(document).on('click', '#getDetailUser', function(e){
			  
				 e.preventDefault();
			  
				 var data_absen_id = $(this).data('id'); // get id of clicked row
			  
				 $('#data_detail').html(''); // leave this div blank
				 $('#modal-detailloader').show();      // load ajax loader on button click
				
				 $.ajax({
					  url: 'view/presensi_detail.php',
					  type: 'POST',
					  data: 'id='+data_absen_id ,
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
			
			
			$('#table_presensi').DataTable( {
				columnDefs: [ { 'orderable': false, 'targets': 6 }],
				buttons: [
					{
						extend: 'csvHtml5',
						title:'Presensi Harian <?php echo date("d/m/Y", strtotime($set_tanggal)); ?>',
						exportOptions: {
							columns: [ 0, 1, 2, 3, 4,55 ]
						}
					},
					{
						extend: 'excelHtml5',
						title:'Presensi Harian <?php echo date("d/m/Y", strtotime($set_tanggal)); ?>',
						exportOptions: {
							columns: [ 0, 1, 2, 3, 4, 5 ]
						}
					},
					{
						extend: 'pdfHtml5',
						title:'Presensi Harian <?php echo date("d/m/Y", strtotime($set_tanggal)); ?>',
						exportOptions: {
							columns: [ 0, 1, 2, 3, 4, 5 ]
						},
						customize: function (doc) {
                           doc.defaultStyle.fontSize = 11; //2, 3, 4,etc
                           doc.styles.tableHeader.fontSize = 12; //2, 3, 4, etc
						   doc.styles.title.fontSize = 16;
						   doc.styles.title.bold = true;
                           doc.content[1].table.widths = ['5%', '15%',  '30%', '20%', '20%', '15%'];
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
									margin: 10
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
    if (document.getElementById('choices-kelas')) {
      var element = document.getElementById('choices-kelas');
      const example = new Choices(element, {
        searchEnabled: true
      });
    };
	 if (document.getElementById('choices-jurusan')) {
      var element = document.getElementById('choices-jurusan');
      const example = new Choices(element, {
        searchEnabled: true
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
  	if(isset($_GET['stat_modal'])){
		$stat_modal=$_GET['stat_modal'];
		if($stat_modal == "del_ok" || $stat_modal == "edit_ok" || $stat_modal == "add_ok"){
			echo "<script type='text/javascript'>
				$(document).ready(function(){
				 $('#Modal_stat').modal('show');
				});
				</script>";
		}
	}
  ?>
</body>

</html>