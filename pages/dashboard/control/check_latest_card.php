<?php
require_once "../../../include/db_config.php";

$sql = "SELECT * FROM tmp_datacard ORDER BY id DESC LIMIT 1";
$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);

if ($row = mysqli_fetch_assoc($result)) {
    // Return the UID and timestamp to avoid re-reading the same old card if needed
    // But for now just UID is enough, the frontend can handle "if different" logic
    echo json_encode(['status' => 'success', 'uid' => $row['uid'], 'time' => $row['jam']]);
} else {
    echo json_encode(['status' => 'empty']);
}
?>