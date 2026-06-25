<?php
/**
 * WhatsApp Gateway Queue Worker Script
 * Runs in background via Cron Job
 */

// Set time limit and default timezone
set_time_limit(60);
date_default_timezone_set('Asia/Jakarta');

// Include DB config and helpers
require_once __DIR__ . '/include/db_config.php';
require_once __DIR__ . '/include/helpers.php';

// Check if WA service is active
$stmt = mysqli_prepare($link, "SELECT cnfg_token, cnfg_sender, cnfg_status FROM wa_notification WHERE cnfg_id = 1 LIMIT 1");
if (!$stmt) {
    die("Database query error: " . mysqli_error($link));
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$wa_conf = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$wa_conf || $wa_conf['cnfg_status'] != 1 || empty($wa_conf['cnfg_token'])) {
    die("WhatsApp service is disabled or API key is not configured.\n");
}

$cnfg_token = $wa_conf['cnfg_token'];
$cnfg_sender = $wa_conf['cnfg_sender'];

// Fetch pending messages (limit to 10 messages per cron run to prevent timeouts and stay safe)
$q_pending = mysqli_query($link, "SELECT * FROM wa_queue WHERE status = 'PENDING' ORDER BY id ASC LIMIT 10");

if (!$q_pending || mysqli_num_rows($q_pending) == 0) {
    echo "No pending messages in queue.\n";
    exit();
}

echo "Processing " . mysqli_num_rows($q_pending) . " messages...\n";

while ($row = mysqli_fetch_assoc($q_pending)) {
    $id = $row['id'];
    $phone = $row['phone'];
    $message = $row['message'];
    
    // Safety check: ensure number is not empty
    if (empty($phone)) {
        mysqli_query($link, "UPDATE wa_queue SET status = 'FAILED', response = 'Empty phone number', attempts = attempts + 1 WHERE id = $id");
        continue;
    }

    // Anti-blocking strategy: unique message footer using randomized ID if not already appended
    if (strpos($message, 'Ref:') === false) {
        $ref_id = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 5);
        $message .= "\n\nRef: " . $ref_id . " | " . date('d-m-Y H:i:s');
    }

    // Update status to PROCESSING to prevent double send
    mysqli_query($link, "UPDATE wa_queue SET status = 'PROCESSING', attempts = attempts + 1 WHERE id = $id");

    // Send via cURL
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://gateway.asr-desain.my.id/send-message',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 6,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'api_key' => $cnfg_token,
            'sender'  => $cnfg_sender,
            'number'  => $phone,
            'message' => $message,
        ),
    ));

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $status = ($httpcode >= 200 && $httpcode < 300) ? 'SENT' : 'FAILED';
    $response_esc = mysqli_real_escape_string($link, strval($response));
    $message_esc = mysqli_real_escape_string($link, $message);

    // Update wa_queue table
    mysqli_query($link, "UPDATE wa_queue SET status = '$status', response = '$response_esc', sent_at = NOW() WHERE id = $id");

    // Insert into wa_logs so dashboard logs screen updates
    $siswa_uid = mysqli_real_escape_string($link, $row['siswa_uid']);
    $siswa_nama = mysqli_real_escape_string($link, $row['siswa_nama']);
    $kelas = mysqli_real_escape_string($link, $row['kelas']);
    $tipe = mysqli_real_escape_string($link, $row['tipe']);
    $target = mysqli_real_escape_string($link, $row['target']);
    $guru_nama = mysqli_real_escape_string($link, $row['guru_nama']);

    $ins_log = "INSERT INTO wa_logs (siswa_uid, siswa_nama, kelas, tipe, target, guru_nama, phone, status, response, message) 
                VALUES ('$siswa_uid', '$siswa_nama', '$kelas', '$tipe', '$target', '$guru_nama', '$phone', '$status', '$response_esc', '$message_esc')";
    mysqli_query($link, $ins_log);

    echo "Message ID $id to $phone: $status\n";

    // Anti-blocking sleep delay: 2 to 5 seconds
    $sleep_sec = rand(2, 5);
    sleep($sleep_sec);
}

echo "Done.\n";
?>
