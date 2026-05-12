<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include_once '../../include/db_config.php';

date_default_timezone_set('Asia/Jakarta');

$today = date('d-m-y');

$d_eui = "";
$d_type = "";
$d_location = "";
$d_status = "";
$d_base = "";

$_GET["dev_eui"] = trim($_GET["dev_eui"]);
if(isset($_GET["dev_eui"]) && !empty($_GET["dev_eui"])){
  $d_eui = $_GET['dev_eui'];
  $sql = "SELECT * FROM reader_devices,room WHERE d_location=r_id AND d_eui='$d_eui'";
  $s_reader = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
  $rowcount = mysqli_num_rows($s_reader);
  if ($rowcount > 0){
	$d_reader = mysqli_fetch_array($s_reader);
	$d_type  =  $d_reader['d_type'];
	$d_location  =  $d_reader['r_name'];
	$d_base  =  $d_reader['d_base'];
	  
	$data_arr = array(
		"date"  => $today,
		"location" => $d_location,
		"type" => $d_type,
		"base" =>  $d_base
	);	
  }else{
	$data_arr = array(
		"date"  => $today,
		"location" => null,
		"type" => "INVALID",
		"base" =>  null
	);
	  
  }
  http_response_code(200);
  echo json_encode($data_arr);
  
} else {
  http_response_code(404);
  echo json_encode("Failed!");
}

?>