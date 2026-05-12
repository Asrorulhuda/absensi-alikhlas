<?php 
	date_default_timezone_set('Asia/Jakarta');
	session_start();
    
	require_once "../../../include/db_config.php";
	$data_type="";
	if(isset($_GET['data_type'])){
		$data_type = $_GET['data_type'];	
	}
	
	$tanggal = date('F jS, Y');
	// Check existence of id parameter before processing further
	$_GET["id_venue"] = trim($_GET["id_venue"]);
	if(isset($_GET["id_venue"]) && !empty($_GET["id_venue"])){
		$id_venue = $_GET["id_venue"];
		$sql = "SELECT * FROM room WHERE r_id ='$id_venue'";
		$s_venue = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		$d_venue = mysqli_fetch_array($s_venue);
	}
	
	
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
						<h5 class="mb-0">Site List</h5>
						<p class="text-sm mb-0">
						List of all registered sites
						</p>
					</div>
					<!--
					
					-->
					<div class="ms-auto my-auto mt-lg-0 mt-4">
					  <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
						  <div class="btn-group ms-1" role="group">
							<a class="btn btn-info btn-sm" href="javascript:void(0)" id="export_csv"   onclick="$('#table_pob').DataTable().buttons(0,0).trigger()">CSV</a>
							<a class="btn btn-success btn-sm" href="javascript:void(0)" id="export_excel" onclick="$('#table_pob').DataTable().buttons(0,1).trigger()">Excel</a>
							<a class="btn btn-primary btn-sm" href="javascript:void(0)" id="export_pdf"   onclick="$('#table_pob').DataTable().buttons(0,2).trigger()">PDF</a>
						  </div>
						</div>
					</div>
				</div>
			</div>
				
            <div class="card-body px-0 pb-2">
				<div class="row justify-content-center"> 
					<div class="table-responsive p-0 w-95">
					   
					   <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
					   <table id="table_pob" class="table align-items-center mb-0 display" style="width:100%">
						<!-- <table id="table_venue" class="table align-items-center mb-3 dataTable js-exportable> -->
							<thead>
								<tr>
									<th>No</th>
									<th>Personnel</th>
									<th>Division</th>
									<th>Tap IN</th>
									<th>Tap OUT</th>
									<th>Status</th>
								</tr>
								<tbody>
								    <?php
									    $sql_pob = "SELECT pob_id, pob_uid, pob_dev_in, pob_time_in, pob_dev_out, pob_time_out, pob_status,
													p_name, p_picture, p_division FROM pob_monitor, personnel WHERE pob_room ='$id_venue' AND pob_uid = p_uid
													ORDER BY pob_status";
										$s_pob = mysqli_query($GLOBALS["___mysqli_ston"], $sql_pob);
										$no=1;
										while ($d_pob = mysqli_fetch_array($s_pob)){
											$id_pob = $d_pob['pob_id'];
									  
									?>
									   <tr>
											<td><?= $no;?></td>
											<td><?= $d_pob['p_name'];?></td>
											<td><?= $d_pob['p_division'];?></td>											
											<td><?= $d_pob['pob_time_in'];?></td>
											<td><?= $d_pob['pob_time_out'];?></td>
											<td><?= $d_pob['pob_status'];?></td>
											
									   </tr>
									
									<?php $no++;} ?>
								
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
			const site_name = "<?php echo $d_venue["r_name"]; ?>";
			$('#table_pob').DataTable( {
				buttons: [
					{
						extend: 'csvHtml5',
						title:'PoB Records in '+site_name+' Site',
						exportOptions: {
							columns: ':visible'
						}
					},
					{
						extend: 'excelHtml5',
						title:'PoB Records in '+site_name+' Site',
						exportOptions: {
							columns: ':visible'
						}
					},
					{
						extend: 'pdfHtml5',
						title:'PoB Records in '+site_name+' Site',
						exportOptions: {
							columns: ':visible'
						},
						customize: function (doc) {
                           doc.defaultStyle.fontSize = 11; //2, 3, 4,etc
                           doc.styles.tableHeader.fontSize = 12; //2, 3, 4, etc
						   doc.styles.title.fontSize = 16;
						   doc.styles.title.bold = true;
                           doc.content[1].table.widths = [ '5%',  '25%','20%','20%', '20%', '10%'];
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