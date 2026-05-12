<?php 
	date_default_timezone_set('Asia/Jakarta');
	session_start();
    
	require_once "../../../include/db_config.php";
	$data_type="";
	if(isset($_GET['data_type'])){
		$data_type = $_GET['data_type'];	
	}
	
	$sql = "SELECT * FROM data_siswa,opsi_jurusan WHERE s_status = 'Aktif' AND s_jurusan = j_id ORDER BY s_nama";
	
	
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!--     Fonts and icons     -->
  <link href="../../../assets/css/Roboto.css" rel="stylesheet" type="text/css" />
  <!-- Nucleo Icons -->
  <link href="../../../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../../../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="../../../assets/js/kit.fontawesome.com_42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="../../../assets/css/Material_icon.css" rel="stylesheet">
  
  <link href="../../../assets/css/animate.min.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../../../assets/css/material-dashboard-pro.min.css?v=3.0.6" rel="stylesheet" />
  
  <link rel="stylesheet" id="pagestyle" href="../../../assets/vendor/DataTables_package/jquery.dataTables.min.css"  /> 
  <link rel="stylesheet" type="text/css" href="../../../assets/vendor/DataTables_package/buttons.dataTables.min.css" />
  <link rel="stylesheet" type="text/css" href="../../../assets/vendor/DataTables_package/dataTables.bootstrap5.min.css" />
  
</head>

<body class="g-sidenav-show  bg-gray-200">
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row min-vh-80 mb-3 mt-4">
        <div class="col-12">		  
          <div class="card my-4 h-100" id="data_list">
		  
			<div class="card-header pb-0">
				<div class="d-lg-flex">
					<div>
						<h5 class="mb-0">Member List</h5>
						<p class="text-sm mb-0">
						List of all registered members
						</p>
					</div>
					<div class="ms-auto my-auto mt-lg-0 mt-4">
					  <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
						  <div class="btn-group ms-1" role="group">
							<a class="btn btn-info btn-sm" href="javascript:void(0)" id="export_csv"   onclick="$('#table_member').DataTable().buttons(0,0).trigger()">CSV</a>
							<a class="btn btn-success btn-sm" href="javascript:void(0)" id="export_excel" onclick="$('#table_member').DataTable().buttons(0,1).trigger()">Excel</a>
							<a class="btn btn-primary btn-sm" href="javascript:void(0)" id="export_pdf"   onclick="$('#table_member').DataTable().buttons(0,2).trigger()">PDF</a>
						  </div>
						</div>
					</div>
				</div>
			</div>
				
            <div class="card-body px-0 pb-2">
				<div class="row justify-content-center"> 
					<div class="table-responsive p-0 w-95">
					   
					   <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
					   <table id="table_member" class="table table-hover align-items-center mb-0 display " style="width:100%">
					    <!-- <table id="table_siswa" class="table align-items-center mb-0 display" style="width:100%"> -->
							<thead>
								<tr>
									<th>UID</th>
									<th>Siswa</th>
									<th class="text-center">Gender</th>
									<th class="text-center">Kelas</th>
									<th class="text-center">Umur</th>
									<th >Action</th>
								</tr>
								<tbody>
								    <?php
									   
										$s_member = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
										while ($d_member=mysqli_fetch_array($s_member)){
											$id64 = base64_encode($d_member['s_id']);
									  
									?>
									   <tr>
											<td><?= $d_member['s_uid'];?></td>
											<td>
												<div class="d-flex px-2 py-1"><div><img src="<?= $d_member['s_picture'];?>" class="avatar avatar-sm me-3 border-radius-lg" alt="user<?= $d_member['s_id'];?>"></div>
													<div class="d-flex flex-column justify-content-center">
														<h6 class="mb-0 text-sm"><?= $d_member['s_nama'];?></h6>
														<p class="text-xs text-secondary mb-0"><?= $d_member['s_nis'];?></p> 
													</div>	
												</div>
											</td>
											<td  class="text-center"><?= $d_member['s_kelamin'];?></td>
											<td class="text-center"><?= $d_member['s_kelas'];?> - <?= $d_member['j_short'];?> </td>
											<td  class="text-center">
											<?php
												$dateOfBirth = $d_member['s_tgl_lahir'];;
												$today = date("Y-m-d");
												$diff = date_diff(date_create($dateOfBirth), date_create($today));
												echo $diff->format('%y');
											?>
											</td>
											<td>
											<?php
											    echo "<a href='javascript:;' data-bs-toggle='modal' data-bs-target='#detailModal' data-id='". $d_member['s_id'] ."' id='getDetailUser'
													     title='Detail Record' data-toggle='tooltip'><span class='fa fa-eye'></span></a>&nbsp;&nbsp;";
												//echo "<a href='javascript:;' data-id='". $d_member['p_id'] ."' title='Update Record' data-toggle='tooltip' id='UpdateUser' ><i class='far fa-edit'></i></a>&nbsp;&nbsp;";
												echo "<a href='siswa_update?id_siswa=". $id64 ."' title='Update Record' data-toggle='tooltip'><i class='far fa-edit'></i></a>&nbsp;&nbsp;";
												echo "<a href='javascript:;' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='". $d_member['s_id'] ."' id='getDelUser'
													     title='Delete Record' data-toggle='tooltip'><span class='fa fa-trash'></span></a>";
													  
											?>
											</td>
									   
									   </tr>
									
									<?php ;} ?>
								
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
	</div>
  </main> 
  
  <!--   Jquery JS Files   -->
  
  <script src="../../../assets/vendor/DataTables_package/jquery-3.5.1.js"></script>
  <script src="../../../assets/js/core/bootstrap.min.js"></script>
  
  <!-- DataTable -->
 
  <script src="../../../assets/vendor/DataTables_package/jquery.dataTables.min.js"></script>
  <script src="../../../assets/vendor/DataTables_package/dataTables.buttons.min.js"></script>
  <script src="../../../assets/vendor/DataTables_package/jszip.min.js"></script>
  <script src="../../../assets/vendor/DataTables_package/pdfmake.min.js"></script>
  <script src="../../../assets/vendor/DataTables_package/vfs_fonts.js"></script>
  <script src="../../../assets/vendor/DataTables_package/buttons.html5.min.js"></script>
  <script src="../../../assets/vendor/DataTables_package/buttons.colVis.js"></script>
  
  <!-- Github buttons -->
  <script async defer src="../../../assets/js/buttons_github.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../../assets/js/material-dashboard-pro.min.js?v=3.0.6"></script>
  
  <script type="text/javascript">
        $(document).ready(function(){			
			$('#table_member').DataTable( {
				buttons: [
					{
						extend: 'csvHtml5',
						title:'Member List',
						exportOptions: {
							columns: ':visible'
						}
					},
					{
						extend: 'excelHtml5',
						title:'Member List',
						exportOptions: {
							columns: ':visible'
						}
					},
					{
						extend: 'pdfHtml5',
						title:'Member List',
						exportOptions: {
							columns: ':visible'
						},
						customize: function (doc) {
                           doc.defaultStyle.fontSize = 11; //2, 3, 4,etc
                           doc.styles.tableHeader.fontSize = 12; //2, 3, 4, etc
						   doc.styles.title.fontSize = 16;
						   doc.styles.title.bold = true;
                           doc.content[1].table.widths = [ '5%',  '20%', '20%', '20%', '15%', '20%'];
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
			
			const data_type = "<?php echo $data_type; ?>";
			if(data_type == "pdf"){
				document.getElementById('export_pdf').click();
				//window.close();
			}
			if(data_type == "excel"){
				document.getElementById('export_excel').click();
				//window.close();
			}
			if(data_type == "csv"){
				document.getElementById('export_csv').click();
				//window.close();
			}
			
				
        });
  </script>  
  
</body>

</html>