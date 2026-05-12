<?php 
 require_once "../../../include/db_config.php";
 
 if (isset($_GET['id'])) {
 $id = $_GET['id'];
 $type = $_GET['type'];
 $sql ="";
 if ($type == "Jurusan"){
	$sql = "DELETE FROM opsi_jurusan WHERE j_id = '$id'";
	 
 }
 if ($type == "Tingkat"){
	$sql = "DELETE FROM opsi_tk_kelas WHERE tk_id = '$id'";
	 
 }
 
 $proses = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if ($proses) {
		if ($type == "Jurusan"){header('location:../system_option?msg='.base64_encode('301'));}
		if ($type == "Tingkat"){header('location:../system_option?msg='.base64_encode('302'));}
		//301 => berhasil dihapus
		
	} else { 
		
		if ($type == "Jurusan"){header('location:../system_option?msg='.base64_encode('331'));}
		if ($type == "Tingkat"){header('location:../system_option?msg='.base64_encode('332'));}
		//300 => gagal dihapus
	}
 }
 ?>