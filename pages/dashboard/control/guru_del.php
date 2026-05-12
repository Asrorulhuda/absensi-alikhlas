<?php 
 require_once "../../../include/db_config.php";
 
$id = $_GET['id'];
$sql = "DELETE FROM data_guru WHERE g_id = '$id'";
$proses = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if ($proses) {
		header('location:../guru?msg='.base64_encode('301'));
		//301 => berhasil dihapus
		
	} else { 
		header('location:../guru?msg='.base64_encode('300'));
	    //300 => gagal dihapus
	}
 ?>