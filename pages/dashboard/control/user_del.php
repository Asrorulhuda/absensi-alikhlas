<?php 
 require_once "../../../include/db_config.php";
 
$id = $_GET['id'];
$id_siswa = $_GET['id_siswa'];
$sql = "DELETE FROM users WHERE id = '$id'";
$proses = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if ($proses) {
		$sql = "UPDATE data_siswa  SET user_stat=0 WHERE s_id='$id_siswa'";
		$s_siswa = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		
		header('location:../user?msg='.base64_encode('301'));
		//301 => berhasil dihapus
		
	} else { 
		header('location:../user?msg='.base64_encode('300'));
	    //300 => gagal dihapus
	}
 ?>