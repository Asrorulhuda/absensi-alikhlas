<?php 
date_default_timezone_set('Asia/Jakarta');

require_once "../../../include/db_config.php";

$sql = "SELECT * FROM tmp_datacard ORDER BY id DESC LIMIT 1";
if($stmt = mysqli_prepare($link, $sql)){
	if(mysqli_stmt_execute($stmt)){
		$result = mysqli_stmt_get_result($stmt);

		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

			$id = $row["id"];
			$uid = $row["uid"];
			$jam = $row["jam"];
			$status = $row["card_status"];
			$card_status = "";
            
            // Determine content based on status
			if ($status == "INVALID"){ 
                $card_status = "Tidak Terdaftar";
                ?>
				<div class="animate__animated animate__fadeInUp">
                    <div style="background: rgba(255,0,0,0.2); padding: 20px; border-radius: 15px; border: 1px solid rgba(255,0,0,0.3); backdrop-filter: blur(5px);">
                        <div style="font-size: 48px; margin-bottom: 10px;">❌</div>
                        <h4 style="color: white; margin: 0;">Kartu Tidak Terdaftar</h4>
                        <p style="color: rgba(255,255,255,0.8); margin: 5px 0;">UID: <?= $uid; ?></p>
                        <a href="siswa_registration?uid=<?= $uid;?>" class="btn btn-sm btn-white" style="margin-top: 10px; background: white; color: #d32f2f; border-radius: 20px; padding: 8px 20px; text-decoration: none; font-weight: bold;">Daftarkan Kartu</a>
                    </div>
				</div>
                <script>
                    document.getElementById('scan_status').style.display = 'none';
                    document.getElementById('scan_circle').style.borderColor = '#f44336';
                </script>
			<?php
			}else{
				$logo_display = 0;
				if ($status=="IN"){$card_status = "Tap IN Berhasil"; $logo_display = 0; $color="#4caf50";}
				if ($status=="OUT"){$card_status = "Tap OUT Berhasil";$logo_display = 1; $color="#ff9800";}
				if ($status=="LOCKED"){$card_status = "Gagal: Presensi Ganda/Terkunci";$logo_display = 2; $color="#f44336";}
				if ($status=="IN2"){$card_status = "Sudah Presensi Hari Ini";$logo_display = 0; $color="#2196f3";}
				if ($status=="KEGIATAN"){$card_status = "Hadir Kegiatan";$logo_display = 0; $color="#9c27b0";}
				if ($status=="IZIN"){$card_status = "Izin";$logo_display = 0; $color="#ffb300";}
				if ($status=="SAKIT"){$card_status = "Sakit";$logo_display = 0; $color="#e53935";}
				
				$sql = "SELECT * FROM data_siswa WHERE s_uid=?";
                // Cek juga guru jika tidak ketemu di siswa? (Optional, based on user request "guru bisa absen")
                // For now stick to existing logic but maybe check guru if not found? 
                // The current system seems to assume data_siswa. If guru logic was added in absensi.php, it writes to tmp_datacard.
                // But here we need to fetch name.
                
                $nama = "Unknown";
                $avatar = "../../assets/img/avatar-default.png";
                $found = false;

				if($stmt_siswa = mysqli_prepare($link, $sql)){
					mysqli_stmt_bind_param($stmt_siswa, "s", $uid);
					if(mysqli_stmt_execute($stmt_siswa)){
						$res_siswa = mysqli_stmt_get_result($stmt_siswa);
						if(mysqli_num_rows($res_siswa) > 0){
							$baris = mysqli_fetch_array($res_siswa, MYSQLI_ASSOC);
							$avatar = $baris["s_picture"];
							$nama = $baris["s_nama"];
                            $found = true;
						}
					}
				}
                
                if(!$found) {
                    // Try Guru
                    $sql_guru = "SELECT * FROM data_guru WHERE g_uid=?";
                    if($stmt_guru = mysqli_prepare($link, $sql_guru)){
						mysqli_stmt_bind_param($stmt_guru, "s", $uid);
                        if(mysqli_stmt_execute($stmt_guru)){
                            $res_guru = mysqli_stmt_get_result($stmt_guru);
                            if(mysqli_num_rows($res_guru) > 0){
                                $baris = mysqli_fetch_array($res_guru, MYSQLI_ASSOC);
                                $avatar = $baris["g_picture"]; // Assuming column name
                                $nama = $baris["g_nama"];
                                $found = true;
                            }
                        }
                    }
                }
                
                // If still not found but status is not invalid (shouldn't happen if logic is correct), fallback
                
				?>
				<div class="animate__animated animate__zoomIn">
                    <div style="background: rgba(255,255,255,0.15); padding: 30px; border-radius: 25px; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(10px); display: flex; flex-direction: column; align-items: center; text-align: center; min-width: 320px; box-shadow: 0 20px 50px rgba(0,0,0,0.3);">
                        
                        <!-- Foto di Atas -->
                        <div style="position: relative; margin-bottom: 20px;">
                            <img src="<?= htmlspecialchars($avatar);?>" style="width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: 0 8px 20px rgba(0,0,0,0.3);" onError="this.src='../../assets/img/avatar-default.png'">
                            <div style="position: absolute; bottom: 5px; right: 5px; background: <?= $color; ?>; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                                <?php if($logo_display == 0) { echo '<i class="material-icons" style="font-size: 18px;">check</i>'; } 
                                      elseif($logo_display == 1) { echo '<i class="material-icons" style="font-size: 18px;">logout</i>'; }
                                      else { echo '<i class="material-icons" style="font-size: 18px;">close</i>'; } ?>
                            </div>
                        </div>

                        <!-- Nama -->
                        <h2 style="color: white; margin: 0 0 5px 0; text-shadow: 0 2px 4px rgba(0,0,0,0.4); font-weight: 800; font-size: 24px;"><?= htmlspecialchars($nama); ?></h2>
                        <div style="color: rgba(255,255,255,0.9); font-size: 14px; margin-bottom: 20px; font-family: monospace; background: rgba(0,0,0,0.2); padding: 2px 8px; border-radius: 8px;">UID: <?= htmlspecialchars($uid); ?></div>
                        
                        <!-- Keterangan Absen -->
                        <div>
                            <span style="background: <?= $color; ?>; color: white; padding: 10px 25px; border-radius: 50px; font-size: 16px; font-weight: bold; box-shadow: 0 4px 15px rgba(0,0,0,0.3); text-transform: uppercase; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 8px;">
                                <?php if($logo_display == 0) { echo '<i class="material-icons">login</i>'; } 
                                      elseif($logo_display == 1) { echo '<i class="material-icons">logout</i>'; }
                                      else { echo '<i class="material-icons">error</i>'; } ?>
                                <?= htmlspecialchars($card_status); ?>
                            </span>
                        </div>

                    </div>
				</div>
                <script>
                    document.getElementById('scan_status').style.display = 'none';
                    document.getElementById('scan_circle').style.borderColor = '<?= $color; ?>';
                </script>
				<?php
			}
			
		} else{
			// No card data
            // Do nothing or reset UI
            ?>
            <script>
                 // Optional: Reset UI if needed when no card is present (but this runs every 1.5s)
                 // Better to only reset if we previously showed a result.
                 // For simplicity, we just ensure status is visible if this returns empty.
                 if(document.getElementById('scan_status').style.display === 'none') {
                     document.getElementById('scan_status').style.display = 'block';
                     document.getElementById('scan_element_container').innerHTML = '';
                     document.getElementById('scan_circle').style.borderColor = 'rgba(255,255,255,0.3)';
                 }
            </script>
            <?php
		}

	} else{
		// echo "Oops! Something went wrong. Please try again later.";
	}
}

// Clear the tmp table
if(isset($id)){
    $sql = "DELETE FROM tmp_datacard WHERE id = ?";
    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
    }
}
?>
