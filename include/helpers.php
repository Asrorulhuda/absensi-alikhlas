<?php
// retrieves and enhances postdata table keys and values on CREATE and UPDATE events
function parse_columns($table_name, $postdata) {
    global $link;
    $vars = array();

    // prepare a default return value
    $default = null;

    // get all columns, including the ones not sent by the CRUD form
    $sql = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_name = '".$table_name."'";
    $result = mysqli_query($link,$sql);
    while($row = mysqli_fetch_assoc($result))
    {

        $debug = 0;
        if ($debug) {
            echo "<pre>";
            // print_r($postdata);
            echo $row['COLUMN_NAME'] . "\t";
            echo $row['DATA_TYPE'] . "\t";
            echo $row['IS_NULLABLE'] . "\t";
            echo $row['COLUMN_DEFAULT'] . "\t";
            echo $row['EXTRA'] . "\t";
            echo $default . "\n";
            echo "</pre>";
        }

        switch($row['DATA_TYPE']) {

            // fix "Incorrect decimal value: '' error in STRICT_MODE or STRICT_TRANS_TABLE
            // @see https://dev.mysql.com/doc/refman/5.7/en/sql-mode.html
            case 'decimal':
                $default = 0;
                break;

            // fix "Incorrect datetime value: '0' " on non-null datetime columns
            // with 'CURRENT_TIMESTAMP' default not being set automatically
            // and refusing to take NULL value
            case 'datetime':
                if ($row['COLUMN_DEFAULT'] != 'CURRENT_TIMESTAMP' && $row['IS_NULLABLE'] == 'YES') {
                    $default = null;
                } else {
                    $default =  date('Y-m-d H:i:s');
                }
                if ($postdata[$row['COLUMN_NAME']] == 'CURRENT_TIMESTAMP') {
                    $_POST[$row['COLUMN_NAME']] =  date('Y-m-d H:i:s');
                }
                break;
        }

        // check that fieldname was set before sending values to pdo
        $vars[$row['COLUMN_NAME']] = isset($_POST[$row['COLUMN_NAME']]) && $_POST[$row['COLUMN_NAME']] ? trim($_POST[$row['COLUMN_NAME']]) : $default;
    }
    return $vars;
}



// get extra attributes for  table keys on CREATE and UPDATE events
function get_columns_attributes($table_name, $column) {
    global $link;
    $sql = "SELECT COLUMN_DEFAULT, COLUMN_COMMENT
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_name = '".$table_name."'
            AND column_name = '".$column."'";
    $result = mysqli_query($link,$sql);
    while($row = mysqli_fetch_assoc($result))
    {
        $debug = 0;
        if ($debug) {
            echo "<pre>";
            print_r($row);
            echo "</pre>";
        }
        return $row;
    }
}

// Enqueue WhatsApp message
function enqueue_wa($phone, $message, $meta = []) {
    global $link;
    
    // Ensure inputs are clean
    $phone = mysqli_real_escape_string($link, $phone);
    $message = mysqli_real_escape_string($link, $message);
    
    $siswa_uid = isset($meta['siswa_uid']) ? mysqli_real_escape_string($link, $meta['siswa_uid']) : '';
    $siswa_nama = isset($meta['siswa_nama']) ? mysqli_real_escape_string($link, $meta['siswa_nama']) : '';
    $kelas = isset($meta['kelas']) ? mysqli_real_escape_string($link, $meta['kelas']) : '';
    $tipe = isset($meta['tipe']) ? mysqli_real_escape_string($link, $meta['tipe']) : '';
    $target = isset($meta['target']) ? mysqli_real_escape_string($link, $meta['target']) : '';
    $guru_nama = isset($meta['guru_nama']) ? mysqli_real_escape_string($link, $meta['guru_nama']) : '';

    // Automatically create table if it doesn't exist
    mysqli_query($link, "CREATE TABLE IF NOT EXISTS wa_queue (
        id INT(11) NOT NULL AUTO_INCREMENT,
        phone VARCHAR(30) NOT NULL,
        message TEXT NOT NULL,
        siswa_uid VARCHAR(40) DEFAULT '',
        siswa_nama VARCHAR(100) DEFAULT '',
        kelas VARCHAR(50) DEFAULT '',
        tipe VARCHAR(50) DEFAULT '',
        target VARCHAR(50) DEFAULT '',
        guru_nama VARCHAR(100) DEFAULT '',
        status VARCHAR(20) DEFAULT 'PENDING',
        attempts INT(11) DEFAULT 0,
        response TEXT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        sent_at DATETIME NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $query = "INSERT INTO wa_queue (phone, message, siswa_uid, siswa_nama, kelas, tipe, target, guru_nama, status) 
              VALUES ('$phone', '$message', '$siswa_uid', '$siswa_nama', '$kelas', '$tipe', '$target', '$guru_nama', 'PENDING')";
              
    return mysqli_query($link, $query);
}

// Automatically synchronize and create missing login accounts for teachers (Guru) and students (Siswa)
function sync_user_accounts() {
    global $link;
    
    // 1. Sync Guru Accounts
    $q_missing_guru = mysqli_query($link, "SELECT g_id, g_nama, g_nip, g_picture, g_mail FROM data_guru WHERE g_id NOT IN (SELECT id_guru FROM users WHERE id_guru IS NOT NULL AND id_guru != 0)");
    if ($q_missing_guru && mysqli_num_rows($q_missing_guru) > 0) {
        while ($mg = mysqli_fetch_assoc($q_missing_guru)) {
            $g_id = $mg['g_id'];
            $g_nama = $mg['g_nama'];
            $g_nip = trim($mg['g_nip']);
            $g_mail = trim($mg['g_mail']);
            $g_picture = !empty($mg['g_picture']) ? $mg['g_picture'] : 'assets/img/user_pict/user_default.png';
            
            // Clean username (alphanumeric + random suffix)
            $clean_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $g_nama));
            $uname = $clean_name . rand(100, 999);
            // Default password is NIP, or '123456' if NIP is empty
            $raw_pass = ($g_nip != "") ? $g_nip : '123456';
            $upass = password_hash($raw_pass, PASSWORD_BCRYPT);
            
            $stmt_ins = mysqli_prepare($link, "INSERT INTO users (name, email, username, password, picture, level_akses, id_siswa, id_guru) VALUES (?, ?, ?, ?, ?, 'Guru', 0, ?)");
            if ($stmt_ins) {
                mysqli_stmt_bind_param($stmt_ins, "sssssi", $g_nama, $g_mail, $uname, $upass, $g_picture, $g_id);
                mysqli_stmt_execute($stmt_ins);
                mysqli_stmt_close($stmt_ins);
            }
        }
    }

    // 2. Sync Siswa Accounts
    $q_missing_siswa = mysqli_query($link, "SELECT s_id, s_nama, s_nis, s_picture, s_phone FROM data_siswa WHERE s_id NOT IN (SELECT id_siswa FROM users WHERE id_siswa IS NOT NULL AND id_siswa != 0) AND s_status = 'Aktif'");
    if ($q_missing_siswa && mysqli_num_rows($q_missing_siswa) > 0) {
        while ($ms = mysqli_fetch_assoc($q_missing_siswa)) {
            $s_id = $ms['s_id'];
            $s_nama = $ms['s_nama'];
            $s_nis = trim($ms['s_nis']);
            $s_picture = !empty($ms['s_picture']) ? $ms['s_picture'] : 'assets/img/user_pict/user_default.png';
            $s_phone = trim($ms['s_phone'] ?? '');
            
            $clean_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $s_nama));
            $uname = $clean_name . rand(100, 999);
            // Default password is NIS, or '123456' if NIS is empty
            $raw_pass = ($s_nis != "") ? $s_nis : '123456';
            $upass = password_hash($raw_pass, PASSWORD_BCRYPT);
            
            $stmt_ins = mysqli_prepare($link, "INSERT INTO users (name, email, username, password, picture, level_akses, id_siswa, id_guru) VALUES (?, ?, ?, ?, ?, 'User', ?, 0)");
            if ($stmt_ins) {
                mysqli_stmt_bind_param($stmt_ins, "sssssii", $s_nama, $s_phone, $uname, $upass, $s_picture, $s_id);
                mysqli_stmt_execute($stmt_ins);
                mysqli_stmt_close($stmt_ins);
                
                // Set user_stat = 1
                mysqli_query($link, "UPDATE data_siswa SET user_stat = 1 WHERE s_id = $s_id");
            }
        }
    }
}
?>