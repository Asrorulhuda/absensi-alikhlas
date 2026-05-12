<?php 
	date_default_timezone_set('Asia/Jakarta');
	session_start();
	if ( $_SESSION['akses']!= 'Admin'){// handling if dont'have session

		header('location:../../index'); 
		exit();
	} 
	$ses_name = $_SESSION['name'];
	$_SESSION['pages']="Presensi";
    
	require_once "../../include/db_config.php";
	include "control/confignusers_data.php";
	
	$bulan_indo = array(
                '01' => 'JANUARI',
                '02' => 'FEBRUARI',
                '03' => 'MARET',
                '04' => 'APRIL',
                '05' => 'MEI',
                '06' => 'JUNI',
                '07' => 'JULI',
                '08' => 'AGUSTUS',
                '09' => 'SEPTEMBER',
                '10' => 'OKTOBER',
                '11' => 'NOVEMBER',
                '12' => 'DESEMBER',
        );
	
	$time= strtotime(date("Y-m-d"));
	$month_now=date("m",$time);
	$year_now=date("Y",$time);
	$bulan_now = $month_now;
	$bulan_now .="-";
	$bulan_now .=$year_now;
	
	$year="";
	$month="";
	$month_full = "";
	$total_hari_sekolah = 0;
	$set_bulan ="";
	$where_con ="";
	$kelas="";
	$jurusan="";
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$kelas = $_POST["choices-kelas"]; 
		$jurusan= $_POST["choices-jurusan"]; 
		
		$set_bulan = $_POST['set_bulan'];
		$time= strtotime("01-". $_POST['set_bulan']);
		$month = date("m",$time);
		$month_full=date("F",$time);
		$year= date("Y",$time);
		if ($set_bulan = $bulan_now){
			$total_hari_sekolah = countWorkingDaysUntilToday($year, $month);
		}else{
			$workingDaysCount = countWorkingDaysInMonth($year, $month);
			$nationalHolidaysCount = countNationalHolidaysInMonth($year, $month);
			$total_hari_sekolah = $workingDaysCount-$nationalHolidaysCount;
		}
		
		
		if($jurusan != ""){
			if($kelas!= ""){
				$where_con= "s_jurusan='$jurusan' AND s_jurusan=j_id AND s_kelas = '$kelas' AND s_status = 'Aktif'";		
			}else{
				$where_con = "s_jurusan='$jurusan' AND s_jurusan=j_id  AND s_status = 'Aktif'";
			}
		}else{
			if($kelas!= ""){
				$where_con = "s_kelas= '$kelas' AND s_jurusan=j_id AND s_status = 'Aktif'";
			}else{
				$where_con = "s_status = 'Aktif' AND s_jurusan=j_id ";
			}
		}
	
	}else{
		$month=date("m",$time);
		$month_full=date("F",$time);
		$year=date("Y",$time);
		$set_bulan = $month;
		$set_bulan .="-";
		$set_bulan .=$year;
		$total_hari_sekolah = countWorkingDaysUntilToday($year, $month);
		$where_con = "s_status = 'Aktif' AND s_jurusan=j_id ";
	}
	
	$bulan = $month;
	$tahun = $year; 
	$tgl = 1;
    $jumtgl = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun); // dapat jumlah tanggal	
	
function fetch_data(){  
	$output = '';
    global $month, $year, $link, $where_con, $kelas;	
	$bulan = $month;
	$tahun = $year; 

	$tgl = 1;
	$jumtgl = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun); // jumlah tanggal

	// Ambil daftar siswa sesuai filter
	$sql = "SELECT siswa.s_uid, siswa.s_nama, s_kelas, j_short
			FROM data_siswa AS siswa, opsi_jurusan
			WHERE ".$where_con."
			GROUP BY siswa.s_uid
			ORDER BY siswa.s_nama";
	$result = mysqli_query($link, $sql); 
	$no = 1;	 

	while($row = mysqli_fetch_array($result)){   
		$output .= '<tr style="text-align:center;">';
		$output .= '<td>'.$no.'</td>';
		$output .= '<td style="text-align:left;">'.$row["s_nama"].'</td>';
		$output .= '<td style="text-align:center;">'.$row["s_kelas"].'-'.$row["j_short"].'</td>';

		// Render per-tanggal: jam masuk/pulang + warna status
		for($d=1; $d <= $jumtgl; $d++){
			$q = mysqli_query($link, "SELECT jam_masuk, jam_keluar, ket_masuk, ket_keluar, keterangan, status 
									   FROM data_absen 
									   WHERE uid='".$row['s_uid']."' 
									   AND YEAR(date(tanggal))='".$tahun."' 
									   AND MONTH(date(tanggal))='".$bulan."' 
									   AND DAY(date(tanggal))='".$d."' 
									   LIMIT 1");
			if($q && mysqli_num_rows($q) > 0){
				$att = mysqli_fetch_assoc($q);
				$jm = ($att['jam_masuk'] && $att['jam_masuk'] !== '00:00:00') ? date('H:i', strtotime($att['jam_masuk'])) : '-';
				$jk = ($att['jam_keluar'] && $att['jam_keluar'] !== '00:00:00') ? date('H:i', strtotime($att['jam_keluar'])) : '-';
				$km = $att['ket_masuk'] ?: '';
				$kk = $att['ket_keluar'] ?: '';
				$keterangan = $att['keterangan'] ?: '';
				$status = $att['status'] ?: '';

				// Check for SAKIT, IZIN, or ALPHA (logic)
				if ($keterangan === 'SAKIT') {
					$output .= '<td><span class="badge" style="background:#e53935; color:white;">S</span></td>';
				} elseif ($keterangan === 'IZIN') {
					$output .= '<td><span class="badge" style="background:#ffb300; color:white;">I</span></td>';
				} else {
					// Normal presence (HADIR, COMPLETE, IN, OUT)
					$enterClass = (stripos($km, 'terlambat') !== false) ? 'enter-late' : 'enter-ok';
					$leaveClass = ($kk === 'Pulang Awal') ? 'leave-early' : 'leave-ok';

					$output .= '<td>'
							.'<div class="d-flex flex-column align-items-center">'
							.'<span class="badge-enter '.$enterClass.'">'.$jm.'</span>'
							.'<span class="badge-leave '.$leaveClass.'">'.$jk.'</span>'
							.'</div>'
							.'</td>';
				}
			} else {
				// No record found -> Alpha
				// Check if it's a weekend (optional, but requested "status alpha")
				// Assuming we mark Alpha for empty records on working days? 
				// For now, let's stick to "A" if no record found, or keep "-" if it's cleaner. 
				// User asked: "jika status izin/sakit atau alpha maka ganti stutsnya jadi i/s/a"
				// Usually Alpha is when there is NO record.
				
				// Let's check if it's a weekday to be safe, or just print "A" for all empty days?
				// Usually calendar views show empty cells.
				// But user said "alpha jika siswa tidak di isi absennya" in previous turn.
				// Let's assume for this specific table request, if empty, show A?
				// Or maybe only weekdays?
				
				// Simple approach: Check if it is a past date or today. Future dates shouldn't be Alpha.
				$currentDate = date('Y-m-d');
				$loopDate = sprintf("%04d-%02d-%02d", $tahun, $bulan, $d);
				
				if ($loopDate <= $currentDate) {
					// Check if weekend
					$dayOfWeek = date('N', strtotime($loopDate));
					if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
						// It's a weekday and no record -> Alpha
						$output .= '<td><span class="badge" style="background:#6c757d; color:white;">A</span></td>';
					} else {
						$output .= '<td>-</td>'; // Weekend
					}
				} else {
					$output .= '<td>-</td>'; // Future date
				}
			}
		}

		// Hitung total hadir bulan ini
		$qCount = mysqli_query($link, "SELECT COUNT(*) AS cnt 
									   FROM data_absen 
									   WHERE uid='".$row['s_uid']."' 
									   AND YEAR(date(tanggal))='".$tahun."' 
									   AND MONTH(date(tanggal))='".$bulan."' 
									   AND (keterangan='HADIR' OR keterangan='COMPLETE')");
		$countRow = ($qCount) ? mysqli_fetch_assoc($qCount) : ['cnt'=>0];
		$count = intval($countRow['cnt']);

		global $total_hari_sekolah;
		$percentage = ($total_hari_sekolah > 0) ? ($count/$total_hari_sekolah)*100 : 0;
		$formattedResult = number_format($percentage, 2);
		$ket_percentage = ($percentage < 70) ? "TIDAK LULUS" : "LULUS";

		$output .= '<td>'.$count.' ('.$formattedResult.'%)</td>';
		$output .= '<td style="text-align:center;">'.$ket_percentage.'</td>';
		$output .= '</tr>';
		$no++;
	}	

	// Data Guru merged into same table
	if ($kelas == 'Guru' || $kelas == '') {
		$sql_guru = "SELECT g.g_uid, g.g_nama FROM data_guru g ORDER BY g.g_nama";
		$result_guru = mysqli_query($link, $sql_guru); 
		
		while($row = mysqli_fetch_array($result_guru)){   
		$output .= '<tr style="text-align:center;">';
		$output .= '<td>'.$no.'</td>';
		$output .= '<td style="text-align:left;">'.$row["g_nama"].'</td>';
		$output .= '<td style="text-align:center;">Guru</td>';

		for($d=1; $d <= $jumtgl; $d++){
			$q = mysqli_query($link, "SELECT jam_masuk, jam_keluar, ket_masuk, ket_keluar, keterangan, status 
									   FROM data_absen 
									   WHERE uid='".$row['g_uid']."' 
									   AND YEAR(date(tanggal))='".$tahun."' 
									   AND MONTH(date(tanggal))='".$bulan."' 
									   AND DAY(date(tanggal))='".$d."' 
									   LIMIT 1");
			if($q && mysqli_num_rows($q) > 0){
				$att = mysqli_fetch_assoc($q);
				$jm = ($att['jam_masuk'] && $att['jam_masuk'] !== '00:00:00') ? date('H:i', strtotime($att['jam_masuk'])) : '-';
				$jk = ($att['jam_keluar'] && $att['jam_keluar'] !== '00:00:00') ? date('H:i', strtotime($att['jam_keluar'])) : '-';
				$km = $att['ket_masuk'] ?: '';
				$kk = $att['ket_keluar'] ?: '';
				$keterangan = $att['keterangan'] ?: '';
				
				if ($keterangan === 'SAKIT') {
					$output .= '<td><span class="badge" style="background:#e53935; color:white;">S</span></td>';
				} elseif ($keterangan === 'IZIN') {
					$output .= '<td><span class="badge" style="background:#ffb300; color:white;">I</span></td>';
				} else {
					$enterClass = (stripos($km, 'terlambat') !== false) ? 'enter-late' : 'enter-ok';
					$leaveClass = ($kk === 'Pulang Awal') ? 'leave-early' : 'leave-ok';

					$output .= '<td>'
							.'<div class="d-flex flex-column align-items-center">'
							.'<span class="badge-enter '.$enterClass.'">'.$jm.'</span>'
							.'<span class="badge-leave '.$leaveClass.'">'.$jk.'</span>'
							.'</div>'
							.'</td>';
				}
			} else {
				$currentDate = date('Y-m-d');
				$loopDate = sprintf("%04d-%02d-%02d", $tahun, $bulan, $d);
				
				if ($loopDate <= $currentDate) {
					$dayOfWeek = date('N', strtotime($loopDate));
					if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
						$output .= '<td><span class="badge" style="background:#6c757d; color:white;">A</span></td>';
					} else {
						$output .= '<td>-</td>'; 
					}
				} else {
					$output .= '<td>-</td>';
				}
			}
		}

		$qCount = mysqli_query($link, "SELECT COUNT(*) AS cnt 
									   FROM data_absen 
									   WHERE uid='".$row['g_uid']."' 
									   AND YEAR(date(tanggal))='".$tahun."' 
									   AND MONTH(date(tanggal))='".$bulan."' 
									   AND (keterangan='HADIR' OR keterangan='COMPLETE')");
		$countRow = ($qCount) ? mysqli_fetch_assoc($qCount) : ['cnt'=>0];
		$count = intval($countRow['cnt']);

		global $total_hari_sekolah;
		$percentage = ($total_hari_sekolah > 0) ? ($count/$total_hari_sekolah)*100 : 0;
		$formattedResult = number_format($percentage, 2);

		$output .= '<td>'.$count.' ('.$formattedResult.'%)</td>';
		$output .= '<td style="text-align:center;">-</td>';
		$output .= '</tr>';
		$no++;
	}
	}

	return $output;  
 }



function countWorkingDaysInMonth($year, $month) {
    $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $workingDayCount = 0;

    for ($day = 1; $day <= $totalDays; $day++) {
        $date = new DateTime("$year-$month-$day");
        $dayOfWeek = $date->format('N'); // 1 (Monday) through 7 (Sunday)

        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Monday to Friday
            $workingDayCount++;
        }
    }

    return $workingDayCount;
}


function countNationalHolidaysInMonth($year, $month) {
    $url = "https://api-harilibur.vercel.app/api?year=$year&month=$month";

    $response = file_get_contents($url);

    if ($response === false) {
        echo "Error fetching data from API.";
        return 0; // Return 0 holidays on error
    }

    $holidays = json_decode($response, true);
    $nationalHolidayCount = 0;

    foreach ($holidays as $holiday) {
        if ($holiday['is_national_holiday'] === true) {
            $holidayDate = new DateTime($holiday['holiday_date']);
            $dayOfWeek = $holidayDate->format('N'); // 1 (Monday) through 7 (Sunday)
            
            if ($dayOfWeek != 6 && $dayOfWeek != 7) { // Exclude Saturday (6) and Sunday (7)
                $nationalHolidayCount++;
            }
        }
    }

    return $nationalHolidayCount;
}

function countWorkingDaysUntilToday($year, $month){
    $currentDay = date('d');
    $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $workingDayCount = 0;

    for ($day = 1; $day <= $currentDay; $day++) {
        $date = new DateTime("$year-$month-$day");
        $dayOfWeek = $date->format('N'); // 1 (Monday) through 7 (Sunday)

        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Monday to Friday
            $workingDayCount++;
        }
    }

    return $workingDayCount;
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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
   
  <link rel="stylesheet" id="pagestyle" href="../../assets/vendor/DataTables_package/jquery.dataTables.min.css"  /> 
  <link rel="stylesheet" type="text/css" href="../../assets/vendor/DataTables_package/buttons.dataTables.min.css" />
  <link rel="stylesheet" type="text/css" href="../../assets/vendor/DataTables_package/dataTables.bootstrap5.min.css" />
  <style>
    table.dataTable.no-footer {
        border-bottom: 0 !important;
    }
    .badge-enter, .badge-leave{
        display: inline-block;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 12px;
        color: #fff;
        background: rgba(0,0,0,0.25);
        border: 1px solid rgba(255,255,255,0.12);
        min-width: 64px;
        text-align: center;
    }
    .badge-enter{ margin-bottom: 2px; }
    .enter-late{
        background: #e53935 !important;
        border-color: rgba(255,255,255,0.2) !important;
    }
    .enter-ok{
        background: rgba(63,81,181,0.25);
    }
    .leave-early{
        background: #2e7d32 !important;
        border-color: rgba(255,255,255,0.2) !important;
    }
    .leave-ok{
        background: rgba(63,81,181,0.25);
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
                <div class="d-flex justify-content-between align-items-center ps-3 pe-3">
					<div>
						<h6 class="text-white text-capitalize mb-0">Data Presensi Bulanan</h6>
						<p class="text-white text-sm mb-0">Rekap presensi siswa per bulan</p>
					</div>
					<div class="badge bg-warning text-dark mb-0 fs-6">
						<?php echo $bulan_indo[$month].", ".$year; ?>
					</div>
				</div>
              </div>
            </div>
			
			<div class="card-body px-0 pb-2">
				<div class="p-4 bg-gray-100 border-radius-lg mb-4 mx-3">
					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
						<div class="row align-items-end">
							<div class="col-md-3 mb-3">
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Bulan</label>
								<div class="input-group input-group-outline bg-white">
									<input type="text" class="form-control" name="set_bulan" id="datepicker" value="<?php echo $set_bulan; ?>">
									<span class="input-group-text"><i class="material-icons">calendar_month</i></span>
								</div>
							</div>
							
							<div class="col-md-3 mb-3">
								<label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Kelas</label>
								<div class="input-group input-group-outline bg-white">
									<select class="form-control" name="choices-kelas" id="choices-kelas">
										<option value="" selected>Semua Kelas</option>
										<?php
											$sql_tingkat = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM opsi_tk_kelas");
											while($data_tk = mysqli_fetch_assoc($sql_tingkat)){
												$selected = ($kelas == $data_tk['tk_name']) ? 'selected' : '';
												echo '<option value="'.$data_tk['tk_name'].'" '.$selected.'>'.$data_tk['tk_name'].'</option>';
											}
										?>
										<option value="Guru" <?php echo ($kelas == 'Guru') ? 'selected' : ''; ?>>Guru</option>
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
								<button type="submit" class="btn btn-primary btn-sm mb-0 w-100" name="btn_submit">
									<i class="material-icons text-sm">filter_alt</i> Filter
								</button>
								<button type="button" class="btn btn-outline-secondary btn-sm mb-0 w-100" name="btn_reset" onClick="location.href='presensi_bulanan'">
									<i class="material-icons text-sm">restart_alt</i> Reset
								</button>
							</div>
						</div>
					</form>
					
					<div class="d-flex justify-content-end mt-3">
						<div class="btn-group">
							<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="material-icons text-sm">file_download</i> Export
							</button>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="javascript:void(0)" onclick="$('#table_rekap_bulanan').DataTable().buttons(0,2).trigger()">PDF</a></li>
								<li><a class="dropdown-item" href="javascript:void(0)" onclick="$('#table_rekap_bulanan').DataTable().buttons(0,1).trigger()">Excel</a></li>
								<li><a class="dropdown-item" href="javascript:void(0)" onclick="$('#table_rekap_bulanan').DataTable().buttons(0,0).trigger()">CSV</a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row justify-content-center"> 
					<div class="table-responsive p-0 w-95">
					   
					   <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
					   
					   <table id="table_rekap_bulanan" class="table table-hover display table-striped" style="width:100%">
					     <!--<table id="table_rekap_bulanan" class="table align-items-center mb-0 display table-striped" style="width:100%">  -->
							<thead>
								<tr>  
								   <th width="5%">No</th>  
								   <th width="30%">Name</th> 
								   <th width="30%">Kelas</th>
								   <?php 
									   while($tgl <= $jumtgl) {
										echo "<th>".$tgl."</th>";
										$tgl++;
									   }
								   ?>
								   <th width="30%">Kehadiran</th>
								   <th width="30%">Keterangan</th>
								</tr>  
							</thead>
							<tbody>
								<?php echo fetch_data(); ?>
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
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">Member Detail</h5>
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
			<h5 class="modal-title font-weight-normal text-white" id="exampleModalLabel">Delete Data Siswa</h5>
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
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
  <!-- Github buttons -->
  <script async defer src="../../assets/js/buttons_github.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../assets/js/material-dashboard-pro.min.js?v=3.0.6"></script>
  
  <script type="text/javascript">
        $(document).ready(function(){
			//var judul_bulan = <?php echo $month_full.", ".$year; ?>;
			$('[data-toggle="tooltip"]').tooltip();
			
			$("#datepicker").datepicker( {
				format: "mm-yyyy",
				startView: "months", 
				minViewMode: "months",
				
				});
				
				
		    $('#table_rekap_bulanan').DataTable( {
				"ordering": false,
				buttons: [
					{
						extend: 'csvHtml5',
						title:'Rekap Presensi Bulanan',
						exportOptions: {
							columns: ':visible',
							format: {
								body: function (data, row, column, node) {
									try {
										var $n = $(node);
										var enter = $n.find('.badge-enter').text().trim();
										var leave = $n.find('.badge-leave').text().trim();
										if (enter || leave) {
											return 'Masuk: ' + (enter || '-') + '\nPulang: ' + (leave || '-');
										}
									} catch(e){}
									return (data + '').replace(/<[^>]*>/g, '');
								}
							}
						}
					},
					{
						extend: 'excelHtml5',
						title:'Rekap Presensi Bulanan',
						exportOptions: {
							columns: ':visible',
							format: {
								body: function (data, row, column, node) {
									try {
										var $n = $(node);
										var enter = $n.find('.badge-enter').text().trim();
										var leave = $n.find('.badge-leave').text().trim();
										if (enter || leave) {
											return 'Masuk: ' + (enter || '-') + '\nPulang: ' + (leave || '-');
										}
									} catch(e){}
									return (data + '').replace(/<[^>]*>/g, '');
								}
							}
						}
					},
					{
						extend: 'pdfHtml5',
						title:'Rekap Presensi Bulan <?php echo $bulan_indo[$month].", ".$year; ?>',
						orientation: 'landscape',
						pageSize: 'LEGAL',
						exportOptions: {
							 columns: ':visible',
							 format: {
								body: function (data, row, column, node) {
									try {
										var $n = $(node);
										var enter = $n.find('.badge-enter').text().trim();
										var leave = $n.find('.badge-leave').text().trim();
										if (enter || leave) {
											return 'Masuk: ' + (enter || '-') + '\nPulang: ' + (leave || '-');
										}
									} catch(e){}
									return (data + '').replace(/<[^>]*>/g, '');
								}
							 }
						},
						customize: function (doc) {
                           doc.defaultStyle.fontSize = 11; //2, 3, 4,etc
                           doc.styles.tableHeader.fontSize = 12; //2, 3, 4, etc
						   doc.styles.title.fontSize = 16;
						   doc.styles.title.bold = true;
                           //doc.content[1].table.widths = [ '20%',  '30%', '20%', '20%', '10%'];
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
			

			
			//table
			//new DataTable('#table_rekap_bulanan');
			//var table = $('#table_rekap_bulanan').DataTable({
			//	"columnDefs": [ { "orderable": false, "targets": 5 }]
			//});
			
			//var tables = $('#table_rekap_bulanan').DataTable();
			
			
        });
  </script>
  <script>
    if (document.getElementById('choices-kelas')) {
      var element = document.getElementById('choices-kelas');
      const example = new Choices(element, {
        searchEnabled: false
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
 
</body>

</html>
