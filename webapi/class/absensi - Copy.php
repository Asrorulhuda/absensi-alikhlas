<?php
date_default_timezone_set('Asia/Jakarta');
class Absensi{
	// Connection
	private $conn;

	// Table
	private $db_data_absen = "data_absen";
	private $db_data_siswa = "data_siswa";
	private $db_data_invalid = "data_invalid";
	private $db_tmp_datacard = "tmp_datacard"; 
	private $db_system_config = "system_config"; 

	// Columns
	public $id;
	public $tanggal;
	public $waktu;
	public $uid;
	public $status;
	public $last_status;
	public $nama;
	public $jam_masuk;
	public $jam_keluar;
	public $ket_masuk;
	public $ket_keluar;
	public $ket_absen;
	public $jm_masuk;
	public $jm_pulang;
	public $batas_absen_masuk;
	public $tanggal_data;
	

	// Db connection
	public function __construct($db){
		$this->conn = $db;
	}

	// CREATE
	public function createData(){
	//Cek net jam masuk dan pulang
	    $sqlQuery = "SELECT * FROM ". $this->db_system_config ." WHERE id = 1 LIMIT 0,1";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$itemCount = $stmt->rowCount();
		if($itemCount > 0){
			while(($dataRow = $stmt->fetch(PDO::FETCH_ASSOC)) != false) {
				$this->jm_masuk = $dataRow['jm_masuk'];
				$this->jm_pulang = $dataRow['jm_pulang'];
				$this->batas_absen_masuk = $dataRow['batas_absen_masuk'];
			}
		}
		
	
	//1. Cek user
		$tanggal_now = date("Y-m-d");
		$sqlQuery = "SELECT * FROM ". $this->db_data_siswa ." WHERE s_uid = :uid LIMIT 0,1";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bindParam(":uid", $this->uid);
		$stmt->execute();
		if($stmt->errorCode() == 0) {
			while(($dataRow = $stmt->fetch(PDO::FETCH_ASSOC)) != false) {
				$this->nama = $dataRow['s_nama'];
			}
		} else {
			$errors = $stmt->errorInfo();
			echo($errors[2]);
		}
		$itemCount = $stmt->rowCount();
		
		if($itemCount > 0){
			//UID terdaftar -> cek status terakhir
			//SELECT * FROM data_absen  WHERE uid='7A4240D5' ORDER BY ID DESC LIMIT 1
			
			$sqlQuery = "SELECT * FROM ". $this->db_data_absen ." WHERE uid = :uid ORDER BY id DESC LIMIT 1";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(":uid", $this->uid);
			$stmt->execute();
			$itemCount = $stmt->rowCount();
			if($itemCount > 0){
				if($stmt->errorCode() == 0) {
					while(($dataRow = $stmt->fetch(PDO::FETCH_ASSOC)) != false) {
						$this->last_status = $dataRow['status'];
						$this->id = $dataRow['id'];
						$this->tanggal_data = $dataRow['tanggal'];
						$this->jam_masuk = $dataRow['jam_masuk'];
						$this->jam_keluar = $dataRow['jam_keluar'];
						
						//echo($this->last_status);
					}
					if ($this->tanggal_data != $tanggal_now AND $this->last_status = "IN"){
						$this->last_status = "OUT";
					}
				} else {
					$errors = $stmt->errorInfo();
					echo($errors[2]);
				}
			}else{
				$this->last_status ="OUT";
				//$jam_masuk = date("H:i:s");
			}
			
			//jika status terakhir IN ==> berarti absen pulang
			if ($this->last_status == "IN"){
				//cek dulu kemungkinan double tap
				$tap_saat_ini = date('Y-m-d H:i:s');
				$tap_saat_ini   =strtotime( $tap_saat_ini);
				$waktu_batas = date('Y-m-d');
				$jam_batas = $this->batas_absen_masuk;
				$waktu_batas = $waktu_batas." ".$jam_batas;
						 
				$waktu_akhir    =strtotime($waktu_batas); // bisa juga waktu sekarang now()

				//menghitung selisih dengan hasil detik
				$diff    =$waktu_akhir - $tap_saat_ini;
				$menit   =$diff / 60;
				  
				if ($menit > -15){    //Kurang dari 15 menit batas masuk masih dianggap double tap
				  
				  $this->status = "IN2";
				  $this->waktu = date("H:i:s");
				  
				  $tmp_uid = $this->uid;
				  $tmp_status = $this->status;
				  $tmp_waktu = $this->waktu;
				  $this->ket_absen="Sudah Presensi!";
				  
				}else{
					
					$this->status = "OUT";
					$this->jam_keluar = date("H:i:s");
					$this->waktu = $this->jam_keluar;

					//
					if (strtotime($this->jm_pulang) > strtotime($this->jam_keluar)) {
					   $this->ket_keluar = "Pulang Awal";
					   $this->ket_absen = $this->ket_keluar ;
					   $this->ket_absen = "ABSEN";
					}else{
					   $this->ket_keluar = "";
					   $this->ket_absen = $this->ket_keluar ;
					}

					$sqlQuery = "UPDATE ". $this->db_data_absen ."
						SET	jam_masuk = :jam_masuk, jam_keluar= :jam_keluar, 
						tanggal = :tanggal, uid = :uid, status = :status, ket_keluar= :ket_keluar
						WHERE id = :id_data";
						
					$stmt = $this->conn->prepare($sqlQuery);
					// sanitize
					$this->uid=htmlspecialchars(strip_tags($this->uid));

					$tmp_uid = $this->uid;
					$tmp_status = $this->status;
					$tmp_waktu = $this->waktu;

					// bind data
					$stmt->bindParam(":uid", $this->uid);
					$stmt->bindParam(":status", $this->status);
					$stmt->bindParam(":jam_masuk", $this->jam_masuk);
					$stmt->bindParam(":jam_keluar", $this->jam_keluar);
					$stmt->bindParam(":tanggal", $this->tanggal_data);
					$stmt->bindParam(":id_data", $this->id);
					$stmt->bindParam(":ket_keluar", $this->ket_keluar);
					
				} 					
			}
			//jika status terakhir OUT ==> berarti absen masuk
			else if($this->last_status == "OUT"){
				//cek dulu kemungkinan presensi kepagian
				$tap_saat_ini = date('Y-m-d H:i:s');
				$tap_saat_ini   =strtotime( $tap_saat_ini);
				$waktu_batas = date('Y-m-d');
				$jam_batas = $this->jm_masuk;
				$waktu_batas = $waktu_batas." ".$jam_batas;
						 
				$waktu_akhir    =strtotime($waktu_batas); // bisa juga waktu sekarang now()

				//menghitung selisih dengan hasil detik
				$diff    =$waktu_akhir - $tap_saat_ini;
				$menit   =$diff / 60;
				
				if ($menit > 60){    //Presensi terlalu pagi
				  
				  $this->status = "NA";
				  $this->waktu = date("H:i:s");
				  
				  $tmp_uid = $this->uid;
				  $tmp_status = $this->status;
				  $tmp_waktu = $this->waktu;
				  $this->ket_absen="Presensi Belum dibuka!";
				  
				}else{
					$this->status= "IN";
					$this->jam_keluar = date("00:00:00");
					$this->jam_masuk = date("H:i:s");
					$this->waktu  = $this->jam_masuk;
					
					//cek tanggal apakah sama?
					if ($this->tanggal_data == $tanggal_now AND $this->last_status = "OUT"){
						//udah presensi pulang
						//status=> lock
						$tmp_uid = $this->uid;
						$this->status = "LOCKED";
						$this->ket_absen ="Not be able to present!";
						$tmp_status = $this->status;
						$tmp_waktu = $this->waktu;

					}else{
						if (strtotime($this->jm_masuk) < strtotime($this->jam_masuk)) {
						   // telat banget maka dianggap tidak masuk/pulang awal
						   if(strtotime($this->batas_absen_masuk) < strtotime($this->jam_masuk)){
							   $this->ket_masuk = "Sangat Terlambat";
							   $this->ket_absen = "ABSEN";
							   //$this->jam_keluar = $this->jam_masuk;
							   
							   $this->ket_keluar="";
							   //$this->status= "LOCKED";
							   
						   }else{
								$this->ket_masuk = "Terlambat";
								$this->ket_absen = "HADIR";
								$this->ket_keluar="";
						   }
						  
						}else{
							$this->ket_masuk = "";
							$this->ket_absen = "HADIR";
							$this->ket_keluar="";
						}
						
						$sqlQuery = "INSERT INTO ". $this->db_data_absen ."
							SET	jam_masuk = :jam_masuk, jam_keluar = :jam_keluar, uid = :uid, status = :status, ket_masuk = :ket_masuk, ket_keluar = :ket_keluar, keterangan = :keterangan";
						$stmt = $this->conn->prepare($sqlQuery);
						// sanitize
						$this->uid=htmlspecialchars(strip_tags($this->uid));
						
						$tmp_uid = $this->uid;
						$tmp_status = $this->status;
						$tmp_waktu = $this->waktu;
						
						// bind data
						$stmt->bindParam(":uid", $this->uid);
						$stmt->bindParam(":status", $this->status);
						$stmt->bindParam(":jam_masuk", $this->jam_masuk);
						$stmt->bindParam(":jam_keluar", $this->jam_keluar);
						$stmt->bindParam(":ket_masuk", $this->ket_masuk);
						$stmt->bindParam(":ket_keluar", $this->ket_keluar);
						$stmt->bindParam(":keterangan", $this->ket_absen);
					}
					
				}
			}
			//jika status terakhir LOCKED ==> berarti tidak bisa absen
			else{
				$tmp_uid = $this->uid;
				$this->status = "LOCKED";
				$this->ket_absen ="Not be able to present!";
				$this->waktu = date("H:i:s");
				$tmp_status = $this->status;
				$tmp_waktu = $this->waktu;
				
			}
			
			//delete data tmp_data
			$sqlQuery1= "TRUNCATE ".$this->db_tmp_datacard ."";
			$stmt1 = $this->conn->prepare($sqlQuery1);
			$stmt1->execute();
			
			$sqlQuery1="INSERT INTO ". $this->db_tmp_datacard ." SET uid = :tmp_uid, jam = :tmp_jam, card_status = :tmp_status";
			$stmt1 = $this->conn->prepare($sqlQuery1);
			// bind data
			$tmp_uid =$this->uid;
			$stmt1->bindParam(":tmp_uid", $tmp_uid);
			$stmt1->bindParam(":tmp_status", $tmp_status);
			$stmt1->bindParam(":tmp_jam", $tmp_waktu);
			$stmt1->execute();
					
			if($stmt->execute()){
			   return true;
			}
			return false;
		}
		else{
			//UID tidak terdaftar
			// sanitize
			$this->uid=htmlspecialchars(strip_tags($this->uid));
			$this->status= "INVALID";
			$this->nama ="Invalid";
			$this->ket_absen="Kartu tidak terdaftar";
			$this->waktu = date("H:i:s");
			//Insert Data to tmp_datacard
			$sqlQuery1="INSERT INTO ". $this->db_tmp_datacard ." SET uid = :tmp_uid, jam = :tmp_jam, card_status = :tmp_status";
			$stmt1 = $this->conn->prepare($sqlQuery1);
			// bind data
			$stmt1->bindParam(":tmp_uid", $this->uid);
			$stmt1->bindParam(":tmp_status", $this->status);
			$stmt1->bindParam(":tmp_jam", $this->waktu);
			$stmt1->execute();
			
			//Insert Data to data_invalid	
			$sqlQuery = "INSERT INTO
						". $this->db_data_invalid ."
					SET
						waktu = :waktu,
						uid = :uid, 
						status = :now_status";
			
			$stmt = $this->conn->prepare($sqlQuery);
			// bind data
			$stmt->bindParam(":uid", $this->uid);
			$stmt->bindParam(":now_status", $this->status);
			$stmt->bindParam(":waktu", $this->waktu);
		
			if($stmt->execute()){
			   return true;
			}
			return false;
			
		}
		
	}
	
	public function getLastData(){
		$tanggal_now = date("Y-m-d");
		$sqlQuery = "SELECT * FROM ". $this->db_data_siswa ." WHERE uid = :uid LIMIT 0,1";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bindParam(":uid", $this->uid);
		$stmt->execute();
		if($stmt->errorCode() == 0) {
			while(($dataRow = $stmt->fetch(PDO::FETCH_ASSOC)) != false) {
				$this->nama = $dataRow['nama'];
			}
		} else {
			$errors = $stmt->errorInfo();
			echo($errors[2]);
		}
		$itemCount = $stmt->rowCount();
		
		if($itemCount > 0){
			//cek data terakhir absensi
			$sqlQuery = "SELECT data_absen.uid, tanggal, nama, jam_masuk, jam_keluar, status FROM ". $this->db_data_absen .", ". $this->db_data_siswa ."
						 WHERE data_absen.uid = data_siswa.uid AND data_absen.uid = :uid";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(":uid", $this->uid);
			$stmt->execute();
			$itemCount = $stmt->rowCount();
			if($itemCount > 0){
				//error handling
				if($stmt->errorCode() == 0) {
					while(($dataRow = $stmt->fetch(PDO::FETCH_ASSOC)) != false) {
						$this->status = $dataRow['status'];
						$this->uid = $dataRow['uid'];
						$this->tanggal = $dataRow['tanggal'];
						$this->jam_masuk = $dataRow['jam_masuk'];
						$this->jam_keluar = $dataRow['jam_keluar'];
						$this->nama = $dataRow['nama'];
					}
				} else {
					$errors = $stmt->errorInfo();
					echo($errors[2]);
				}
			}	
			
		}
		else{
			$sqlQuery = "SELECT * FROM ". $this->db_data_invalid ." WHERE id=(SELECT max(id) FROM ". $this->db_data_invalid .") 
						AND uid = :uid";

			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(":uid", $this->uid);
			$stmt->execute();
			//error handling
			if($stmt->errorCode() == 0) {
				while(($dataRow = $stmt->fetch(PDO::FETCH_ASSOC)) != false) {
					$this->status = $dataRow['status'];
					$this->uid = $dataRow['uid'];
					$this->tanggal = $dataRow['tanggal'];
					$this->waktu = $dataRow['waktu'];
				}
			} else {
				$errors = $stmt->errorInfo();
				echo($errors[2]);
			}
			
		}
	}        


}
?>

