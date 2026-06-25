<?php
date_default_timezone_set('Asia/Jakarta');
class Absensi{
	// Connection
	private $conn;

	// Table
	private $db_data_absen = "data_absen";
	private $db_data_siswa = "data_siswa";
	private $db_data_guru = "data_guru";
	private $db_data_invalid = "data_invalid";
	private $db_tmp_datacard = "tmp_datacard"; 
	private $db_system_config = "system_config"; 
	private $db_activities = "activities";
	private $db_activity_rundowns = "activity_rundowns";
	private $db_activity_attendance = "activity_attendance";
	private $db_kbm_schedules = "kbm_schedules";
	private $db_guru_schedules = "guru_schedules"; // BARU: tabel jadwal guru (opsional)

	// Columns
	public $id;
	public $tanggal;
	public $waktu;
	public $uid;
	public $status;
	public $last_status;
	public $nama;
    public $wali_kontak;
	public $wali_nama;
	public $jam_masuk;
	public $jam_keluar;
	public $ket_masuk;
	public $ket_keluar;
	public $ket_absen;
	public $jm_masuk;
	public $jm_pulang;
	public $batas_absen_masuk;
	public $tanggal_data;
    public $attendance_type; // KBM, ESKUL, KEGIATAN
    public $s_kelas;
	public $is_guru = false; // BARU: flag penanda apakah user adalah guru
	

	// Db connection
	public function __construct($db){
		$this->conn = $db;
	}

	// =========================================================
	// FUNGSI BARU: Ambil jadwal guru dari tabel guru_schedules
	// Jika tabel tidak ada / tidak ada jadwal, gunakan jam default
	// =========================================================
	private function getGuruSchedule(){
		$hari_indo = $this->getIndoDay(date('l'));

		// Coba ambil dari tabel guru_schedules jika ada
		try {
			$sqlGuru = "SELECT * FROM " . $this->db_guru_schedules . " 
						WHERE (g_uid = :uid OR g_uid IS NULL OR g_uid = '')
						AND day_name = :hari 
						ORDER BY g_uid DESC 
						LIMIT 1";
			$stmtGuru = $this->conn->prepare($sqlGuru);
			$stmtGuru->bindParam(":uid", $this->uid);
			$stmtGuru->bindParam(":hari", $hari_indo);
			$stmtGuru->execute();

			if($stmtGuru->rowCount() > 0){
				$guruRow = $stmtGuru->fetch(PDO::FETCH_ASSOC);
				$this->jm_masuk = $guruRow['start_time'];
				$this->jm_pulang = $guruRow['end_time'];
				$this->attendance_type = "KBM";
				if(!empty($guruRow['entry_limit'])){
					$this->batas_absen_masuk = $guruRow['entry_limit'];
				} else {
					$this->batas_absen_masuk = date('H:i:s', strtotime($guruRow['start_time']) + 3600);
				}
				return; // Jadwal ditemukan, selesai
			}
		} catch (Exception $e) {
			// Tabel guru_schedules belum ada, lanjut ke default
		}

		// Fallback: Ambil jam default dari system_config jika ada
		try {
			$sqlConfig = "SELECT * FROM " . $this->db_system_config . " WHERE config_key IN ('guru_jam_masuk','guru_jam_pulang','guru_batas_masuk') LIMIT 3";
			$stmtConfig = $this->conn->prepare($sqlConfig);
			$stmtConfig->execute();
			$configData = [];
			while($row = $stmtConfig->fetch(PDO::FETCH_ASSOC)){
				$configData[$row['config_key']] = $row['config_value'];
			}
			if(!empty($configData['guru_jam_masuk'])){
				$this->jm_masuk = $configData['guru_jam_masuk'];
				$this->jm_pulang = isset($configData['guru_jam_pulang']) ? $configData['guru_jam_pulang'] : '16:00:00';
				$this->batas_absen_masuk = isset($configData['guru_batas_masuk']) ? $configData['guru_batas_masuk'] : date('H:i:s', strtotime($this->jm_masuk) + 3600);
				$this->attendance_type = "KBM";
				return;
			}
		} catch (Exception $e) {
			// system_config tidak punya key guru, lanjut ke hardcode default
		}

		// Hardcode default jika semua gagal
		// Sesuaikan jam ini dengan kebutuhan sekolah Anda
		$this->jm_masuk = "07:00:00";
		$this->jm_pulang = "16:00:00";
		$this->batas_absen_masuk = "08:00:00";
		$this->attendance_type = "KBM";
	}

	// CREATE
	public function createData(){
	//Cek net jam masuk dan pulang
	    $this->jm_masuk = null;
		$this->jm_pulang = null;
		$this->batas_absen_masuk = null;
		$this->is_guru = false;
		
	
	//1. Cek user - cek di data_siswa dulu
		$tanggal_now = date("Y-m-d");
		$sqlQuery = "SELECT * FROM ". $this->db_data_siswa ." WHERE s_uid = :uid AND s_status = 'Aktif' LIMIT 0,1";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bindParam(":uid", $this->uid);
		$stmt->execute();
		if($stmt->errorCode() == 0) {
			while(($dataRow = $stmt->fetch(PDO::FETCH_ASSOC)) != false) {
				$this->nama = $dataRow['s_nama'];
				$this->wali_kontak = $dataRow['s_kontak_wali'];
				$this->wali_nama = $dataRow['s_nama_wali'];
                $this->s_kelas = isset($dataRow['s_kelas']) ? $dataRow['s_kelas'] : '';
			}
		} else {
			$errors = $stmt->errorInfo();
			echo($errors[2]);
		}
		$itemCount = $stmt->rowCount();
		
		// Jika tidak ditemukan di data_siswa, cek data_guru
		if($itemCount == 0){
			$sqlQuery = "SELECT * FROM ". $this->db_data_guru ." WHERE g_uid = :uid LIMIT 0,1";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(":uid", $this->uid);
			$stmt->execute();
			if($stmt->errorCode() == 0) {
				while(($dataRow = $stmt->fetch(PDO::FETCH_ASSOC)) != false) {
					$this->nama = $dataRow['g_nama'];
					$this->wali_kontak = $dataRow['g_contact'];
					$this->wali_nama = $dataRow['g_nama'];
					$this->is_guru = true; // TANDAI SEBAGAI GURU
				}
			}
			$itemCount = $stmt->rowCount();
		}
		
		if($itemCount > 0){
            // --- Cek Kegiatan Aktif (berlaku untuk siswa DAN guru) ---
            $cur_date = date("Y-m-d");
            $cur_time = date("H:i:s");
            
            $sqlActivity = "SELECT r.id, r.name as rundown_name, a.name as activity_name 
                            FROM " . $this->db_activity_rundowns . " r
                            JOIN " . $this->db_activities . " a ON a.id = r.activity_id
                            WHERE a.is_active = 1 
                            AND a.date = :cur_date
                            AND :cur_time BETWEEN r.start_time AND r.end_time
                            LIMIT 1";
                            
            $stmtAct = $this->conn->prepare($sqlActivity);
            $stmtAct->bindParam(":cur_date", $cur_date);
            $stmtAct->bindParam(":cur_time", $cur_time);
            $stmtAct->execute();
            
            if($stmtAct->rowCount() > 0){
                $actRow = $stmtAct->fetch(PDO::FETCH_ASSOC);
                $rundown_id = $actRow['id'];
                $activity_name = $actRow['activity_name'];
                $rundown_name = $actRow['rundown_name'];
                
                $checkAtt = $this->conn->prepare("SELECT id FROM " . $this->db_activity_attendance . " WHERE uid = :uid AND rundown_id = :rid");
                $checkAtt->bindParam(":uid", $this->uid);
                $checkAtt->bindParam(":rid", $rundown_id);
                $checkAtt->execute();
                
                $act_status = "HADIR";
                if($checkAtt->rowCount() == 0){
                    $insAtt = $this->conn->prepare("INSERT INTO " . $this->db_activity_attendance . " (uid, rundown_id, timestamp, status) VALUES (:uid, :rid, NOW(), :status)");
                    $insAtt->bindParam(":uid", $this->uid);
                    $insAtt->bindParam(":rid", $rundown_id);
                    $insAtt->bindParam(":status", $act_status);
                    $insAtt->execute();
                    $this->ket_absen = "Hadir Kegiatan: " . $activity_name;
                } else {
                    $this->ket_absen = "Sudah Hadir: " . $activity_name;
                    $act_status = "IN2";
                }
                
                $this->status = "KEGIATAN";
                $this->attendance_type = "KEGIATAN";
                $this->waktu = date("H:i:s");
                
                $sqlQuery1= "TRUNCATE ".$this->db_tmp_datacard ."";
                $stmt1 = $this->conn->prepare($sqlQuery1);
                $stmt1->execute();
                
                $sqlQuery1="INSERT INTO ". $this->db_tmp_datacard ." SET uid = :tmp_uid, jam = :tmp_jam, card_status = :tmp_status";
                $stmt1 = $this->conn->prepare($sqlQuery1);
                $stmt1->bindParam(":tmp_uid", $this->uid);
                $stmt1->bindParam(":tmp_status", $this->status);
                $stmt1->bindParam(":tmp_jam", $this->waktu);
                $stmt1->execute();
                
                return true;
            }

			// =================================================
			// PERCABANGAN: GURU vs SISWA untuk penentuan jadwal
			// =================================================
			if($this->is_guru){
				// GURU: Ambil jadwal dari guru_schedules / system_config / default
				$this->getGuruSchedule();
			} else {
				// SISWA: Logika jadwal KBM dan Eskul (sama seperti sebelumnya)

				// --- Cek Jadwal KBM ---
				$hari_indo = $this->getIndoDay(date('l'));
				if(!empty($this->s_kelas)){
					$sqlKBM = "SELECT * FROM " . $this->db_kbm_schedules . " WHERE class_name = :kelas AND day_name = :hari LIMIT 1";
					$stmtKBM = $this->conn->prepare($sqlKBM);
					$stmtKBM->bindParam(":kelas", $this->s_kelas);
					$stmtKBM->bindParam(":hari", $hari_indo);
					$stmtKBM->execute();
					
					if($stmtKBM->rowCount() > 0){
						$kbmRow = $stmtKBM->fetch(PDO::FETCH_ASSOC);
						$this->jm_masuk = $kbmRow['start_time'];
						$this->jm_pulang = $kbmRow['end_time'];
						$this->attendance_type = "KBM";
						if(!empty($kbmRow['entry_limit'])){
							$this->batas_absen_masuk = $kbmRow['entry_limit'];
						} else {
							$this->batas_absen_masuk = date('H:i:s', strtotime($kbmRow['start_time']) + 3600);
						}
					}
				}
				
				// --- Cek Jadwal Eskul ---
				$hari_indo = $this->getIndoDay(date('l'));
				$sqlEskul = "SELECT e.* FROM eskul e 
							 JOIN eskul_siswa es ON e.id = es.eskul_id 
							 JOIN data_siswa s ON es.siswa_id = s.s_id 
							 WHERE s.s_uid = :uid AND e.day_name = :hari 
							 ORDER BY e.start_time ASC";
				$stmtEskul = $this->conn->prepare($sqlEskul);
				$stmtEskul->bindParam(":uid", $this->uid);
				$stmtEskul->bindParam(":hari", $hari_indo);
				$stmtEskul->execute();
				
				if($stmtEskul->rowCount() > 0){
					$eskulRows = $stmtEskul->fetchAll(PDO::FETCH_ASSOC);
					$pickedEskul = null;
					$cur_time_str = date("H:i:s");
					
					foreach($eskulRows as $row){
						if($cur_time_str < $row['end_time']){ 
							 $pickedEskul = $row;
							 break;
						}
					}
					if(!$pickedEskul && count($eskulRows) > 0) $pickedEskul = $eskulRows[count($eskulRows)-1];
					
					$useEskul = false;
					if($this->jm_masuk === null){
						$useEskul = true;
					} else {
						$kbm_end_timestamp = strtotime($this->jm_pulang);
						if(time() > ($kbm_end_timestamp + 1800)){ 
							$useEskul = true;
						}
					}
					
					if($useEskul && $pickedEskul){
						 $this->jm_masuk = $pickedEskul['start_time'];
						 $this->jm_pulang = $pickedEskul['end_time'];
						 $this->attendance_type = "ESKUL";
						 $this->batas_absen_masuk = date('H:i:s', strtotime($pickedEskul['start_time']) + 1800); 
					}
				}
				
				// --- VALIDASI JADWAL - hanya untuk SISWA ---
				if($this->jm_masuk === null){
					$this->status = "LOCKED";
					$this->ket_absen = "Tidak Ada Jadwal KBM/Eskul";
					$this->waktu = date("H:i:s");
					
					$sqlQuery1= "TRUNCATE ".$this->db_tmp_datacard ."";
					$stmt1 = $this->conn->prepare($sqlQuery1);
					$stmt1->execute();
					
					$sqlQuery1="INSERT INTO ". $this->db_tmp_datacard ." SET uid = :tmp_uid, jam = :tmp_jam, card_status = :tmp_status";
					$stmt1 = $this->conn->prepare($sqlQuery1);
					$stmt1->bindParam(":tmp_uid", $this->uid);
					$stmt1->bindParam(":tmp_status", $this->status);
					$stmt1->bindParam(":tmp_jam", $this->waktu);
					$stmt1->execute();
					
					return false;
				}

			} // end if is_guru / siswa
            
			// =================================================
			// LOGIKA ABSEN (sama untuk guru dan siswa)
			// =================================================
			$sqlQuery = "SELECT * FROM ". $this->db_data_absen ." WHERE uid = :uid ORDER BY id DESC LIMIT 1";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(":uid", $this->uid);
			$stmt->execute();
			$itemCount = $stmt->rowCount();
			if($itemCount > 0){
				if($stmt->errorCode() == 0) {
					while(($dataRow = $stmt->fetch(PDO::FETCH_ASSOC)) != false) {
						$this->last_status = $dataRow['status'];
						
                        $last_type = isset($dataRow['attendance_type']) ? $dataRow['attendance_type'] : 'KBM';
                        
                        if (!empty($this->attendance_type) && $this->attendance_type != $last_type) {
                            $this->last_status = "OUT"; 
                        }

						$this->id = $dataRow['id'];
						$this->tanggal_data = $dataRow['tanggal'];
						$this->jam_masuk = $dataRow['jam_masuk'];
						$this->jam_keluar = $dataRow['jam_keluar'];
					}
					if ($this->tanggal_data != $tanggal_now AND $this->last_status == "IN"){
						$this->last_status = "OUT";
					}
				} else {
					$errors = $stmt->errorInfo();
					echo($errors[2]);
				}
			}else{
				$this->last_status ="OUT";
			}
			
			if ($this->last_status == "IN"){
				$tap_saat_ini = date('Y-m-d H:i:s');
				$tap_saat_ini   =strtotime( $tap_saat_ini);
				$waktu_batas = date('Y-m-d');
				$jam_batas = $this->batas_absen_masuk;
				$waktu_batas = $waktu_batas." ".$jam_batas;
						 
				$waktu_akhir    =strtotime($waktu_batas);

				$diff    =$waktu_akhir - $tap_saat_ini;
				$menit   =$diff / 60;
				  
				if ($menit > -15){
				  
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
                    
					$timestamp_pulang = strtotime($this->jm_pulang);
					$timestamp_keluar = strtotime($this->jam_keluar);
					$diff_seconds = $timestamp_keluar - $timestamp_pulang;
					$diff_minutes = $diff_seconds / 60;
					
					if ($timestamp_pulang > $timestamp_keluar) {
					    $this->ket_keluar = "Pulang Awal";
					    $this->ket_absen = $this->ket_keluar;
					    $this->ket_absen = "ABSEN";
					}else{
					    if($diff_minutes < 15 ){
							$this->ket_keluar = "";
							$this->ket_absen = "COMPLETE";
						}else{
							$this->ket_keluar = "Pulang Telat";
							$this->ket_absen = "COMPLETE";
						}
					}

					$sqlQuery = "UPDATE ". $this->db_data_absen ."
						SET	jam_masuk = :jam_masuk, jam_keluar= :jam_keluar, 
						tanggal = :tanggal, uid = :uid, status = :status, ket_keluar= :ket_keluar
						WHERE id = :id_data";
						
					$stmt = $this->conn->prepare($sqlQuery);
					$this->uid=htmlspecialchars(strip_tags($this->uid));

					$tmp_uid = $this->uid;
					$tmp_status = $this->status;
					$tmp_waktu = $this->waktu;

					$stmt->bindParam(":uid", $this->uid);
					$stmt->bindParam(":status", $this->status);
					$stmt->bindParam(":jam_masuk", $this->jam_masuk);
					$stmt->bindParam(":jam_keluar", $this->jam_keluar);
					$stmt->bindParam(":tanggal", $this->tanggal_data);
					$stmt->bindParam(":id_data", $this->id);
					$stmt->bindParam(":ket_keluar", $this->ket_keluar);
				} 					
			}
			else if($this->last_status == "OUT"){
				$tap_saat_ini = date('Y-m-d H:i:s');
				$tap_saat_ini   =strtotime( $tap_saat_ini);
				$waktu_batas = date('Y-m-d');
				$jam_batas = $this->jm_masuk;
				$waktu_batas = $waktu_batas." ".$jam_batas;
						 
				$waktu_akhir    =strtotime($waktu_batas);

				$diff    =$waktu_akhir - $tap_saat_ini;
				$menit   =$diff / 60;
				
				if ($menit > 60){
				  
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
					
					if ($this->tanggal_data == $tanggal_now AND $this->last_status = "OUT"){
						$tmp_uid = $this->uid;
						$this->status = "LOCKED";
						$this->ket_absen ="Not be able to present!";
						$tmp_status = $this->status;
						$tmp_waktu = $this->waktu;

					}else{
						if (strtotime($this->jm_masuk) < strtotime($this->jam_masuk)) {
						   if(strtotime($this->batas_absen_masuk) < strtotime($this->jam_masuk)){
							   $this->ket_masuk = "Sangat Terlambat";
							   $this->ket_absen = "ABSEN";
							   $this->ket_keluar="";
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
							SET	tanggal = :tanggal, jam_masuk = :jam_masuk, jam_keluar = :jam_keluar, uid = :uid, status = :status, ket_masuk = :ket_masuk, ket_keluar = :ket_keluar, keterangan = :keterangan, attendance_type = :att_type";
						$stmt = $this->conn->prepare($sqlQuery);
						$this->uid=htmlspecialchars(strip_tags($this->uid));
						
						$tmp_uid = $this->uid;
						$tmp_status = $this->status;
						$tmp_waktu = $this->waktu;
						
						$stmt->bindParam(":tanggal", $tanggal_now);
						$stmt->bindParam(":uid", $this->uid);
						$stmt->bindParam(":status", $this->status);
						$stmt->bindParam(":jam_masuk", $this->jam_masuk);
						$stmt->bindParam(":jam_keluar", $this->jam_keluar);
						$stmt->bindParam(":ket_masuk", $this->ket_masuk);
						$stmt->bindParam(":ket_keluar", $this->ket_keluar);
						$stmt->bindParam(":keterangan", $this->ket_absen);
						$stmt->bindParam(":att_type", $this->attendance_type);
					}
				}
			}
			else{
				$tmp_uid = $this->uid;
				$this->status = "LOCKED";
				$this->ket_absen ="Not be able to present!";
				$this->waktu = date("H:i:s");
				$tmp_status = $this->status;
				$tmp_waktu = $this->waktu;
			}
			
			$sqlQuery1= "TRUNCATE ".$this->db_tmp_datacard ."";
			$stmt1 = $this->conn->prepare($sqlQuery1);
			$stmt1->execute();
			
			$sqlQuery1="INSERT INTO ". $this->db_tmp_datacard ." SET uid = :tmp_uid, jam = :tmp_jam, card_status = :tmp_status";
			$stmt1 = $this->conn->prepare($sqlQuery1);
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
			// UID TIDAK TERDAFTAR -> simpan ke data_invalid
			$this->uid=htmlspecialchars(strip_tags($this->uid));
			$this->status= "INVALID";
			$this->nama ="Invalid";
			$this->ket_absen="Kartu tidak terdaftar";
			$this->waktu = date("H:i:s");
			$tanggal_now = date("Y-m-d");

			// Insert ke tmp_datacard
			$sqlQuery1="INSERT INTO ". $this->db_tmp_datacard ." SET uid = :tmp_uid, jam = :tmp_jam, card_status = :tmp_status";
			$stmt1 = $this->conn->prepare($sqlQuery1);
			$stmt1->bindParam(":tmp_uid", $this->uid);
			$stmt1->bindParam(":tmp_status", $this->status);
			$stmt1->bindParam(":tmp_jam", $this->waktu);
			$stmt1->execute();
			
			// Insert ke data_invalid
			$sqlQuery = "INSERT INTO
						". $this->db_data_invalid ."
					SET
						tanggal = :tanggal,
						waktu = :waktu,
						uid = :uid, 
						status = :now_status";
			
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(":tanggal", $tanggal_now);
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
			$sqlQuery = "SELECT data_absen.uid, tanggal, nama, jam_masuk, jam_keluar, status FROM ". $this->db_data_absen .", ". $this->db_data_siswa ."
						 WHERE data_absen.uid = data_siswa.uid AND data_absen.uid = :uid";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(":uid", $this->uid);
			$stmt->execute();
			$itemCount = $stmt->rowCount();
			if($itemCount > 0){
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
			// Cek apakah ada di data_guru
			$sqlQuery = "SELECT * FROM ". $this->db_data_guru ." WHERE g_uid = :uid LIMIT 0,1";
			$stmtGuru = $this->conn->prepare($sqlQuery);
			$stmtGuru->bindParam(":uid", $this->uid);
			$stmtGuru->execute();
			if($stmtGuru->rowCount() > 0){
				$guruRow = $stmtGuru->fetch(PDO::FETCH_ASSOC);
				$this->nama = $guruRow['g_nama'];
				// Ambil data absen terakhir guru dari data_absen
				$sqlQuery = "SELECT * FROM ". $this->db_data_absen ." WHERE uid = :uid ORDER BY id DESC LIMIT 1";
				$stmt = $this->conn->prepare($sqlQuery);
				$stmt->bindParam(":uid", $this->uid);
				$stmt->execute();
				if($stmt->rowCount() > 0){
					while(($dataRow = $stmt->fetch(PDO::FETCH_ASSOC)) != false) {
						$this->status = $dataRow['status'];
						$this->uid = $dataRow['uid'];
						$this->tanggal = $dataRow['tanggal'];
						$this->jam_masuk = $dataRow['jam_masuk'];
						$this->jam_keluar = $dataRow['jam_keluar'];
					}
				}
			} else {
				// Bukan siswa, bukan guru -> cek data_invalid
				$sqlQuery = "SELECT * FROM ". $this->db_data_invalid ." WHERE id=(SELECT max(id) FROM ". $this->db_data_invalid .") 
							AND uid = :uid";

				$stmt = $this->conn->prepare($sqlQuery);
				$stmt->bindParam(":uid", $this->uid);
				$stmt->execute();
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

	private function getIndoDay($day){
		$days = [
			'Sunday' => 'Minggu',
			'Monday' => 'Senin',
			'Tuesday' => 'Selasa',
			'Wednesday' => 'Rabu',
			'Thursday' => 'Kamis',
			'Friday' => 'Jumat',
			'Saturday' => 'Sabtu'
		];
		return isset($days[$day]) ? $days[$day] : $day;
	}
}
?>