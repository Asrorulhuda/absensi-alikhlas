<?php
    $tanggal = date('d M Y');
	$day = date('D', strtotime($tanggal));
	$dayList = array(
		'Sun' => 'Minggu',
		'Mon' => 'Senin',
		'Tue' => 'Selasa',
		'Wed' => 'Rabu',
		'Thu' => 'Kamis',
		'Fri' => 'Jumat',
		'Sat' => 'Sabtu'
	);
	$today = date("d-m-Y");
		
 
    $s_member= mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM data_siswa");
	$rowcount = mysqli_num_rows($s_member);
	
	$s_absensi = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM data_absen WHERE tanggal='".$today."' GROUP BY data_absen.uid");
	$absensi = mysqli_num_rows($s_absensi);
	
	$s_invalid = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM data_invalid GROUP BY uid");
	$invalid = mysqli_num_rows($s_invalid);
	
	
	if ($rowcount > 0){
		$prosentase = ($absensi/$rowcount)* 100;	
		$prosentase = number_format($prosentase, 2);
	}else{
		$prosentase = 0;
	}
	
	// Chart Data: Last 7 Days Attendance
	$chart_labels = [];
	$chart_data = [];
	for ($i = 6; $i >= 0; $i--) {
		$d = date('Y-m-d', strtotime("-$i days"));
		$chart_labels[] = date('D', strtotime($d)); // Day name like Mon, Tue
		
		$sql_chart = "SELECT * FROM data_absen WHERE tanggal='$d' GROUP BY uid";
		$q_chart = mysqli_query($GLOBALS["___mysqli_ston"], $sql_chart);
		$chart_data[] = mysqli_num_rows($q_chart);
	}
	$chart_labels_json = json_encode($chart_labels);
	$chart_data_json = json_encode($chart_data);
	
	// Chart Data: Attendance by Class (Top 5)
	$chart_class_labels = [];
	$chart_class_data = [];
	$sql_class = "SELECT s_kelas, COUNT(DISTINCT uid) as total 
				  FROM data_absen 
				  JOIN data_siswa ON data_absen.uid = data_siswa.s_uid 
				  WHERE tanggal='$today' 
				  GROUP BY s_kelas 
				  ORDER BY total DESC 
				  LIMIT 5";
	$q_class = mysqli_query($GLOBALS["___mysqli_ston"], $sql_class);
	while($row_class = mysqli_fetch_assoc($q_class)){
		$chart_class_labels[] = $row_class['s_kelas'];
		$chart_class_data[] = $row_class['total'];
	}
	$chart_class_labels_json = json_encode($chart_class_labels);
	$chart_class_data_json = json_encode($chart_class_data);
    
    // Chart Data: Attendance Status (Pie Chart)
    $chart_status_labels = [];
    $chart_status_data = [];
    $chart_status_colors = [];
    
    // Get 'HADIR', 'SAKIT', 'IZIN', etc.
    $sql_status = "SELECT keterangan, COUNT(*) as total FROM data_absen WHERE tanggal='$today' GROUP BY keterangan";
    $q_status = mysqli_query($GLOBALS["___mysqli_ston"], $sql_status);
    
    $total_recorded = 0;
    while($row_status = mysqli_fetch_assoc($q_status)){
        $chart_status_labels[] = $row_status['keterangan'] ? $row_status['keterangan'] : 'HADIR';
        $chart_status_data[] = $row_status['total'];
        $total_recorded += $row_status['total'];
        
        // Assign colors based on status
        $status_upper = strtoupper($row_status['keterangan']);
        if($status_upper == 'SAKIT') $chart_status_colors[] = '#f53939'; // Red
        else if($status_upper == 'IZIN') $chart_status_colors[] = '#fb8c00'; // Orange
        else $chart_status_colors[] = '#4caf50'; // Green (HADIR)
    }
    
    // Calculate 'BELUM HADIR'
    $belum_hadir = $rowcount - $total_recorded;
    if($belum_hadir > 0){
        $chart_status_labels[] = "BELUM HADIR";
        $chart_status_data[] = $belum_hadir;
        $chart_status_colors[] = '#7b809a'; // Gray
    }
    
    $chart_status_labels_json = json_encode($chart_status_labels);
    $chart_status_data_json = json_encode($chart_status_data);
    $chart_status_colors_json = json_encode($chart_status_colors);
?>