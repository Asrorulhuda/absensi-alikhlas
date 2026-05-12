<?php 
	date_default_timezone_set('Asia/Jakarta');
	session_start();
	if ( ($_SESSION['akses']!= 'Admin') && ($_SESSION['akses']!= 'User') ){header('location:../../index'); exit();}  
	$ses_name = $_SESSION['name'];
	$_SESSION['pages']="Presensi";
	$id_siswa = $_SESSION['id_siswa'];
    
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
	
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		if(isset($_POST['submit_izin_sakit'])){
			$act = $_POST['tipe_absen'] === 'SAKIT' ? 'SAKIT' : 'IZIN';
			$alasan = trim($_POST['alasan']);
			$evidencePath = '';
			if(isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK){
				$allowed = ['image/jpeg','image/png','application/pdf'];
				if(in_array($_FILES['bukti']['type'], $allowed)){
					$ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
					$dir = realpath(__DIR__ . '/../../assets/uploads');
					if($dir === false){ $dir = __DIR__ . '/../../assets/uploads'; }
					if(!is_dir($dir)){ @mkdir($dir, 0775, true); }
					$fname = $act.'_'.date('Ymd').'_'.uniqid().'.'.$ext;
					$target = $dir . '/izin_sakit_' . $fname;
					if(move_uploaded_file($_FILES['bukti']['tmp_name'], $target)){
						$evidencePath = '../../assets/uploads/'. 'izin_sakit_' . $fname;
					}
				}
			}
			$resSis = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT s_uid,s_nama,s_kelas,s_kontak_wali,s_nama_wali FROM data_siswa WHERE s_id='".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $id_siswa)."' LIMIT 1");
			if($resSis && mysqli_num_rows($resSis) > 0){
				$urow = mysqli_fetch_assoc($resSis);
				$uid = $urow['s_uid'];
				$cek = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id,keterangan FROM data_absen WHERE uid='$uid' AND tanggal=CURDATE() LIMIT 1");
				if($cek && mysqli_num_rows($cek) > 0){
					$ex = mysqli_fetch_assoc($cek);
					if($ex['keterangan'] === 'HADIR' || $ex['keterangan'] === 'COMPLETE'){
						$_SESSION['presensi_msg'] = "Sudah tercatat hadir hari ini.";
					}else{
						$id_abs = intval($ex['id']);
						$q = "UPDATE data_absen SET status='$act', keterangan='$act', ket_masuk='".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $alasan)."' ".($evidencePath ? ", ket_keluar='BUKTI:$evidencePath'" : "")." WHERE id=$id_abs";
						mysqli_query($GLOBALS["___mysqli_ston"], $q);
						$_SESSION['presensi_msg'] = "Berhasil mengajukan $act untuk hari ini.";
						mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE tmp_datacard");
						$esc_uid = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $uid);
						$now_jam = date("H:i:s");
						mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO tmp_datacard(uid, jam, card_status) VALUES('$esc_uid','$now_jam','$act')");
					}
				}else{
					$q = "INSERT INTO data_absen(uid,status,keterangan,ket_masuk,ket_keluar) VALUES('$uid','$act','$act','".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $alasan)."','".($evidencePath ? "BUKTI:$evidencePath" : "")."')";
					mysqli_query($GLOBALS["___mysqli_ston"], $q);
					$_SESSION['presensi_msg'] = "Berhasil mengajukan $act untuk hari ini.";
					mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE tmp_datacard");
					$esc_uid = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $uid);
					$now_jam = date("H:i:s");
					mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO tmp_datacard(uid, jam, card_status) VALUES('$esc_uid','$now_jam','$act')");
				}
				$cnfg_status=0; $cnfg_token=''; $cnfg_sender=''; $tpl_izin=''; $tpl_sakit='';
				$wa = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT cnfg_status, cnfg_token, cnfg_sender, cnfg_template_izin, cnfg_template_sakit FROM wa_notification WHERE cnfg_id=1 LIMIT 1");
				if($wa && mysqli_num_rows($wa)>0){
					$c = mysqli_fetch_assoc($wa);
					$cnfg_status = intval($c['cnfg_status']); $cnfg_token = $c['cnfg_token']; $cnfg_sender = $c['cnfg_sender'];
					$tpl_izin = isset($c['cnfg_template_izin']) ? $c['cnfg_template_izin'] : '';
					$tpl_sakit = isset($c['cnfg_template_sakit']) ? $c['cnfg_template_sakit'] : '';
				}
				$kontak_wali = trim($urow['s_kontak_wali']);
				$nama_wali = trim($urow['s_nama_wali']);
				if($cnfg_status == 1){
					mysqli_query($GLOBALS["___mysqli_ston"], "CREATE TABLE IF NOT EXISTS wa_logs (
						id INT(11) NOT NULL AUTO_INCREMENT,
						created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
						siswa_uid VARCHAR(40),
						siswa_nama VARCHAR(100),
						kelas VARCHAR(50),
						tipe VARCHAR(10),
						target VARCHAR(20),
						guru_nama VARCHAR(100),
						phone VARCHAR(30),
						status VARCHAR(10),
						response TEXT,
						message TEXT,
						PRIMARY KEY (id)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
					$targets = [];
					$guruQ = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT g_nama, COALESCE(NULLIF(g_phone,''), g_contact) AS phone FROM data_guru WHERE LOWER(g_status)='gurukelas' AND g_homeroom_class='".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $urow['s_kelas'])."'");
					if($guruQ){
						while($gr = mysqli_fetch_assoc($guruQ)){
							if(!empty($gr['phone'])){ $targets[] = ['nama' => $gr['g_nama'], 'phone' => $gr['phone']]; }
						}
					}
					if(count($targets) > 0){
						$msgTpl = ($act==='SAKIT') ? $tpl_sakit : $tpl_izin;
						$msgBase = "Yth. Wali Kelas,\nSiswa: ".$urow['s_nama']." mengajukan ".$act." pada ".date('d-m-Y').".\nAlasan: ".$alasan."\nTerima kasih.";
						foreach($targets as $t){
							$msg = !empty($msgTpl) ? str_replace(
								['{nama_siswa}','{kelas}','{tanggal}','{alasan}','{tipe}','{nama_guru}'],
								[$urow['s_nama'],$urow['s_kelas'],date('d-m-Y'),$alasan,$act,$t['nama']],
								$msgTpl
							) : $msgBase;
							$curl = curl_init();
							curl_setopt_array($curl, array(
							  CURLOPT_URL => 'https://gateway.asr-desain.my.id/send-message',
							  CURLOPT_RETURNTRANSFER => true,
							  CURLOPT_ENCODING => '',
							  CURLOPT_MAXREDIRS => 10,
							  CURLOPT_TIMEOUT => 6,
							  CURLOPT_FOLLOWLOCATION => true,
							  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							  CURLOPT_CUSTOMREQUEST => 'POST',
							  CURLOPT_POSTFIELDS => array(
							    'api_key' =>  $cnfg_token,
							    'sender'  =>  $cnfg_sender,
 							    'number'  =>  $t['phone'],
							    'message' =>  $msg
							  ),
							));
							$response = curl_exec($curl);
							$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
							curl_close($curl);
							$ins = "INSERT INTO wa_logs (siswa_uid,siswa_nama,kelas,tipe,target,guru_nama,phone,status,response,message) VALUES (
								'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $urow['s_uid'])."',
								'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $urow['s_nama'])."',
								'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $urow['s_kelas'])."',
								'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $act)."',
								'guru_wali',
								'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t['nama'])."',
								'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t['phone'])."',
								'".(($httpcode>=200 && $httpcode<300) ? 'SENT' : 'FAILED')."',
								'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], strval($response))."',
								'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $msg)."'
							)";
							mysqli_query($GLOBALS["___mysqli_ston"], $ins);
						}
					}else if($kontak_wali != ''){
						$msgTpl = ($act==='SAKIT') ? $tpl_sakit : $tpl_izin;
						$msgBase = "Yth. $nama_wali,\nSiswa: ".$urow['s_nama']." mengajukan ".$act." pada ".date('d-m-Y').".\nAlasan: ".$alasan."\nTerima kasih.";
						$msg = !empty($msgTpl) ? str_replace(
							['{nama_siswa}','{kelas}','{tanggal}','{alasan}','{tipe}','{nama_guru}'],
							[$urow['s_nama'],$urow['s_kelas'],date('d-m-Y'),$alasan,$act,$nama_wali],
							$msgTpl
						) : $msgBase;
						$curl = curl_init();
						curl_setopt_array($curl, array(
						  CURLOPT_URL => 'https://gateway.asr-desain.my.id/send-message',
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => '',
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 6,
						  CURLOPT_FOLLOWLOCATION => true,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => 'POST',
						  CURLOPT_POSTFIELDS => array(
						    'api_key' =>  $cnfg_token,
						    'sender'  =>  $cnfg_sender,
						    'number'  =>  $kontak_wali,
						    'message' =>  $msg
						  ),
						));
						$response = curl_exec($curl);
						$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
						curl_close($curl);
						$ins = "INSERT INTO wa_logs (siswa_uid,siswa_nama,kelas,tipe,target,guru_nama,phone,status,response,message) VALUES (
							'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $urow['s_uid'])."',
							'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $urow['s_nama'])."',
							'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $urow['s_kelas'])."',
							'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $act)."',
							'wali_murid',
							'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $nama_wali)."',
							'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $kontak_wali)."',
							'".(($httpcode>=200 && $httpcode<300) ? 'SENT' : 'FAILED')."',
							'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], strval($response))."',
							'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $msg)."'
						)";
						mysqli_query($GLOBALS["___mysqli_ston"], $ins);
					}else{
						$ins = "INSERT INTO wa_logs (siswa_uid,siswa_nama,kelas,tipe,target,guru_nama,phone,status,response,message) VALUES (
							'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $urow['s_uid'])."',
							'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $urow['s_nama'])."',
							'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $urow['s_kelas'])."',
							'".mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $act)."',
							'none',
							'',
							'',
							'SKIPPED',
							'',
							''
						)";
						mysqli_query($GLOBALS["___mysqli_ston"], $ins);
					}
				}
			}
		}
		$set_bulan = $_POST['set_bulan'];
		$time= strtotime("01-". $_POST['set_bulan']);
		$month = date("m",$time);
		$month_full=date("F",$time);
		$year= date("Y",$time);
	}else{
		$month=date("m",$time);
		$month_full=date("F",$time);
		$year=date("Y",$time);
		$set_bulan = $month;
		$set_bulan .="-";
		$set_bulan .=$year;
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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
   
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
	    <div class="col-lg-4 col-md-12 mb-4">	
          <div class="card h-100" id="data_list">
		  
			<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Profil Siswa</h6>
              </div>
            </div>
				
            <div class="card-body px-0 pb-2">

				<div class="row justify-content-center"> 
				   
					<div class="col-md-10 col-ms-8">
					   <?php
							
							$id_siswa_safe = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $id_siswa);
							$sql = "SELECT * FROM data_siswa, opsi_jurusan WHERE s_jurusan = j_id AND s_id='$id_siswa_safe' LIMIT 1";
							$s_siswa = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
							if($s_siswa && mysqli_num_rows($s_siswa) > 0){
								$dd_siswa = mysqli_fetch_assoc($s_siswa);
							}else{
								$dd_siswa = [
									's_picture' => '../../assets/img/user_pict/user_default.png',
									's_nama' => '-',
									's_uid' => '-',
									's_status' => '-',
									's_nis' => '-',
									's_tgl_lahir' => '',
									's_kelas' => '-',
									'j_name' => '-',
									's_phone' => '-',
									's_emergency' => '',
									's_emergency_user' => '',
									's_kontak_wali' => '',
									's_nama_wali' => ''
								];
							}
						  
						?>
		
					   <div class="d-flex justify-content-center animate__animated animate__fadeInDown">
						<img id="picture_modal" src="<?php echo $dd_siswa['s_picture'];?>" alt="Member Avatar" class="rounded-circle z-depth-2" width="200px" height="200px" />
					   </div>
					   <div class="d-flex justify-content-center mb-0">
						   <h5 class="mt-2 text-center"><?php echo $dd_siswa['s_nama'];?></h5>
					   </div>
					   <div class="d-flex justify-content-center mb-1">
							<p><?php echo $dd_siswa['s_uid'];?> 
							<?php 
							if ($dd_siswa['s_status'] == "Aktif"){
								echo "<span class='badge badge-success badge-sm'>".$dd_siswa['s_status']."</span>";				
							}else if ($dd_siswa['s_status'] == "Lulus"){
								echo "<span class='badge badge-danger badge-sm'>".$dd_siswa['s_status']."</span>";
							}else{
								echo "<span class='badge badge-primary badge-sm'>".$dd_siswa['s_status']."</span>";
							}
							?>
							</p> 
					   </div>
					   <div class="d-flex justify-content-center mb-0">
							<table class="table table-borderless">
							  <tbody>
								<tr>
								  <td>NIS</td>
								  <td>: <?php echo $dd_siswa['s_nis'];?></td>
								</tr>
								<tr>
								  <td>Tanggal Lahir</td>
								  <td>: <?php if(!empty($dd_siswa['s_tgl_lahir']) && $dd_siswa['s_tgl_lahir']!='0000-00-00'){ $tgl=date_create($dd_siswa['s_tgl_lahir']); echo date_format($tgl,"j F Y"); } else { echo '-'; }?></td>
								</tr>
								<tr>
								  <td>Kelas</td>
								  <td>: <?php echo $dd_siswa['s_kelas'];?></td>
								</tr>
								<tr>
								  <td>Jurusan</td>
								  <td>: <?php echo $dd_siswa['j_name'];?></td>
								</tr>
								<tr>
								  <td>Phone</td>
								  <td>: <?php echo $dd_siswa['s_phone'];?></td>
								</tr>
								<tr>
								<td>Kontak Darurat</td>
								  <td>: <?php $ev = (isset($dd_siswa['s_emergency']) && $dd_siswa['s_emergency']!='') ? $dd_siswa['s_emergency'] : ((isset($dd_siswa['s_kontak_wali']) && $dd_siswa['s_kontak_wali']!='') ? $dd_siswa['s_kontak_wali'] : '-'); $eu = (isset($dd_siswa['s_emergency_user']) && $dd_siswa['s_emergency_user']!='') ? $dd_siswa['s_emergency_user'] : ((isset($dd_siswa['s_nama_wali']) && $dd_siswa['s_nama_wali']!='') ? $dd_siswa['s_nama_wali'] : '-'); echo $ev.' ('.$eu.')'; ?></td>
								</tr>
							  </tbody>
							</table>
					   </div>
					</div> 
				</div>


            </div>
          </div>
        </div>
        <div class="col-lg-8 col-md-12">	
          <div class="card h-100" id="data_list">
		  
			<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Data Presensi</h6>
                <p class="text-white text-sm ps-3 mb-0">Rekap kehadiran siswa</p>
              </div>
            </div>
				
            <div class="card-body px-0 pb-2">
                <?php if(isset($_SESSION['presensi_msg']) && $_SESSION['presensi_msg']!=""){ echo '<div class="alert alert-info mx-3 mb-2">'.$_SESSION['presensi_msg'].'</div>'; $_SESSION['presensi_msg']=""; } ?>
                <div class="p-3 mx-3 mb-3 mt-4 bg-gray-100 border-radius-lg">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="row align-items-end justify-content-end">
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Pilih Bulan</label>
                                <div class="input-group input-group-outline bg-white <?php echo !empty($set_bulan) ? 'is-filled' : ''; ?>">
                                    <input type="text" class="form-control" name="set_bulan" id="datepicker" value="<?php echo $set_bulan; ?>">
                                    <span class="input-group-text"><i class="material-icons">calendar_month</i></span>
                                </div>
                            </div>
                            <div class="col-md-auto mb-3">
                                <button type="submit" class="btn bg-gradient-success mb-0 me-2" name="btn_submit">
                                    <i class="material-icons text-sm">filter_list</i>&nbsp; Set Bulan
                                </button>
                                <button type="button" class="btn btn-primary mb-0" name="btn_reset" onClick="location.href='presensi_user'">
                                    <i class="material-icons text-sm">restart_alt</i>&nbsp; Reset
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-warning mb-0 me-2" data-bs-toggle="modal" data-bs-target="#modalIzinSakit" data-type="IZIN">
                                <i class="material-icons text-sm">event_busy</i>&nbsp; Ajukan Izin
                            </button>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <button type="button" class="btn btn-danger mb-0" data-bs-toggle="modal" data-bs-target="#modalIzinSakit" data-type="SAKIT">
                                <i class="material-icons text-sm">healing</i>&nbsp; Ajukan Sakit
                            </button>
                        </div>
                    </div>
                </div>
				<div class="row justify-content-center mx-2"> 
					<div class="table-responsive p-0 w-95">
					   
					   <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
					   
					   <table id="table_presensi" class="table table-hover table-fixed" style="width:100%">
					     <!--<table id="table_presensi" class="table align-items-center mb-0 display table-striped" style="width:100%">  -->
							<thead>
								<tr>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
									<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Masuk</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Keluar</th>
									<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Keterangan</th>
								</tr>
								
							</thead>
								<tbody style="border-style:none;">
								    <?php
									    $numb =0;
										$sql = "SELECT  tanggal, jam_masuk, jam_keluar, status, ket_masuk, ket_keluar, keterangan FROM data_absen, data_siswa
												WHERE data_absen.uid=data_siswa.s_uid AND data_absen.uid='".$dd_siswa['s_uid']."' AND YEAR(date(tanggal))='".$year."' AND MONTH(date(tanggal))='".$month."'";
					
										$s_member = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
										while ($d_member=mysqli_fetch_array($s_member)){
											$numb++;
									  
									?>
									   <tr style="border-bottom: 0;">
									   
									        <td><p class="text-sm mb-0 mt-2"><?= $numb;?></p></td>
											<td><p class="text-sm mb-0 mt-2"><?= $d_member['tanggal'];?></p></td>
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
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
  
  
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
			$('#table_presensi').DataTable();
			
        });
		$("#datepicker").datepicker( {
			format: "mm-yyyy",
			startView: "months", 
			minViewMode: "months",
											
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
<!-- Modal Izin/Sakit -->
<div class="modal fade" id="modalIzinSakit" tabindex="-1" role="dialog" aria-labelledby="modalIzinSakitLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title font-weight-normal text-white" id="modalIzinSakitLabel">Pengajuan Izin/Sakit</h5>
        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">X</button>
      </div>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
      <div class="modal-body">
         <div class="container mt-2 mb-2">
           <div class="row">
             <div class="input-group input-group-static">
               <label>Tipe</label>
               <select class="form-control" name="tipe_absen" id="tipe_absen">
                 <option value="IZIN">IZIN</option>
                 <option value="SAKIT">SAKIT</option>
               </select>
             </div>
           </div>
           <div class="row mt-2">
             <div class="input-group input-group-static">
               <label>Alasan</label>
               <textarea class="form-control" name="alasan" rows="3" required placeholder="Tuliskan alasan singkat..."></textarea>
             </div>
           </div>
           <div class="row mt-2">
             <div class="input-group input-group-static">
               <label>Unggah Bukti (jpg/png/pdf)</label>
               <input type="file" class="form-control" name="bukti" accept=".jpg,.jpeg,.png,.pdf">
             </div>
           </div>
           <input type="hidden" name="set_bulan" value="<?php echo $set_bulan; ?>">
         </div>
      </div>
      <div class="modal-footer">
        <button class="btn bg-gradient-primary shadow-primary" type="submit" name="submit_izin_sakit">Kirim Pengajuan</button>
      </div>
      </form>
    </div>
  </div>
</div>
<script>
  var modalIzin = document.getElementById('modalIzinSakit');
  modalIzin.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var type = button.getAttribute('data-type');
    var select = document.getElementById('tipe_absen');
    if(select){ select.value = type || 'IZIN'; }
  });
</script>

