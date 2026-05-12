<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include_once '../../include/db_config.php';

date_default_timezone_set('Asia/Jakarta');

$today = date('d-m-Y');
//$data_arr = array();
$total_users = 0;
$tap_in_today = 0;
$tap_out_today = 0;
$r_name = "";
$r_location = "";
$r_capacity = "";
$r_picture = "";
$r_info = "";

$_GET["id_venue"] = trim($_GET["id_venue"]);
if(isset($_GET["id_venue"]) && !empty($_GET["id_venue"])){
  $id_venue = $_GET['id_venue'];
  
  $sql = "SELECT * FROM room WHERE r_id='$id_venue'";
  $s_venue = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
  $d_venue = mysqli_fetch_array($s_venue);
  $r_name  =  $d_venue['r_name'];
  $r_location  =  $d_venue['r_name'];
  $r_capacity  =  $d_venue['r_capacity'];
  $r_picture  =  $d_venue['r_picture'];
  $r_info  =  $d_venue['r_info'];
  
  
  $s_tu= mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM pob_monitor WHERE pob_room = '$id_venue' AND pob_status = 'IN' ");
  $total_users = mysqli_num_rows($s_tu);
  
  $s_tap_in = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM pob_monitor WHERE pob_room = '$id_venue' AND DATE(pob_time_in) = '$today' ");
  $tap_in_today = mysqli_num_rows($s_tap_in);
  
  $s_tap_out = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM pob_monitor WHERE pob_room = '$id_venue' AND DATE(pob_time_out) = '$today' ");
  $tap_out_today = mysqli_num_rows($s_tap_out);
  
  $data_arr = array(
			"date"  => $today,
			"total_user" => $total_users,
			"tap_in" => $tap_in_today,
			"tap_out" =>  $tap_out_today
		);	
  http_response_code(200);
  echo json_encode($data_arr);
  
} else {
  http_response_code(404);
  echo json_encode("Failed!");
}

?>