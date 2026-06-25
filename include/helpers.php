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
?>