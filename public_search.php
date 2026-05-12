<?php
require_once "include/db_config.php";

if(isset($_POST['query']) && isset($_POST['type'])) {
    $query = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['query']);
    $type = $_POST['type'];
    
    if($type == 'siswa') {
        $sql = "SELECT s_id as id, s_nama as nama, s_nis as nomor, s_uid as uid_kartu 
                FROM data_siswa 
                WHERE s_nama LIKE '%$query%' OR s_nis LIKE '%$query%' 
                ORDER BY s_nama ASC 
                LIMIT 10";
        $result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $uid_display = $row['uid_kartu'] ? $row['uid_kartu'] : 'Belum ada kartu';
                $uid_class = $row['uid_kartu'] ? 'has-card' : 'no-card';
                
                echo '<div class="rfid-search-item" onclick="selectUser(\''.$row['id'].'\', \'siswa\', \''.addslashes($row['nama']).'\', \''.$row['nomor'].'\', \'Siswa\', \''.$row['uid_kartu'].'\')">';
                echo '<h4>👨‍🎓 '.$row['nama'].'</h4>';
                echo '<p>NIS: '.$row['nomor'].'</p>';
                echo '<p class="rfid-uid-info '.$uid_class.'">UID Lama: '.$uid_display.'</p>';
                echo '</div>';
            }
        } else {
            echo '<div style="text-align: center; padding: 50px 20px; color: rgba(255,255,255,0.4);">';
            echo '<span class="material-icons" style="font-size: 56px; opacity: 0.2; display: block; margin-bottom: 12px;">search_off</span>';
            echo '<p style="font-size: 14px;">Tidak ada data siswa ditemukan</p>';
            echo '</div>';
        }
        
    } else if($type == 'guru') {
        $sql = "SELECT g_id as id, g_nama as nama, g_nip as nomor, g_uid as uid_kartu 
                FROM data_guru 
                WHERE g_nama LIKE '%$query%' OR g_nip LIKE '%$query%' 
                ORDER BY g_nama ASC 
                LIMIT 10";
        $result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $uid_display = $row['uid_kartu'] ? $row['uid_kartu'] : 'Belum ada kartu';
                $uid_class = $row['uid_kartu'] ? 'has-card' : 'no-card';
                
                echo '<div class="rfid-search-item" onclick="selectUser(\''.$row['id'].'\', \'guru\', \''.addslashes($row['nama']).'\', \''.$row['nomor'].'\', \'Guru\', \''.$row['uid_kartu'].'\')">';
                echo '<h4>👨‍🏫 '.$row['nama'].'</h4>';
                echo '<p>NIP: '.$row['nomor'].'</p>';
                echo '<p class="rfid-uid-info '.$uid_class.'">UID Lama: '.$uid_display.'</p>';
                echo '</div>';
            }
        } else {
            echo '<div style="text-align: center; padding: 50px 20px; color: rgba(255,255,255,0.4);">';
            echo '<span class="material-icons" style="font-size: 56px; opacity: 0.2; display: block; margin-bottom: 12px;">search_off</span>';
            echo '<p style="font-size: 14px;">Tidak ada data guru ditemukan</p>';
            echo '</div>';
        }
    }
}
?>