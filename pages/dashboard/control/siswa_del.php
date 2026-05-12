<?php 
 require_once "../../../include/db_config.php";
 
$id = $_GET['id'];
$sql = "DELETE FROM data_siswa WHERE s_id = '$id'";
$proses = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if ($proses) {
		header('location:../siswa?msg='.base64_encode('301'));
		//301 => berhasil dihapus
		
	} else { 
		header('location:../siswa?msg='.base64_encode('300'));
	    //300 => gagal dihapus
	}
 ?>