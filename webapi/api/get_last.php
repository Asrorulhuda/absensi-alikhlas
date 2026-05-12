<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../config/database.php';
    include_once '../class/absensi.php';

    $database = new Database();
    $db = $database->getConnection();

    $item = new Absensi($db);

    $item->uid = isset($_GET['uid']) ? $_GET['uid'] : die();
  
    $item->getLastData();

    if($item->uid != null){
	   
	   if ($item->status == "INVALID"){
		  // create array
			$emp_arr = array(
			    "tanggal" => $item->tanggal,
				"waktu" => $item->waktu,
				"uid" => $item->uid,
				"status" =>  $item->status
			); 
	   }else{
		   // create array
			$emp_arr = array(
			    "tanggal" => $item->tanggal,
				"nama" => $item->nama,
				"jam_masuk" => $item->jam_masuk,
				"jam_keluar" => $item->jam_keluar,
				"status" =>  $item->status
			); 
		   
	   }
        
      
        http_response_code(200);
        echo json_encode($emp_arr);
    }
      
    else{
        http_response_code(404);
        echo json_encode("Data not found.");
    }
?>