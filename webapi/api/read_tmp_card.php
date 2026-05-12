<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Select the single row from tmp_datacard
$query = "SELECT * FROM tmp_datacard LIMIT 1";

$stmt = $db->prepare($query);
$stmt->execute();

if($stmt->rowCount() > 0){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row);
} else {
    echo json_encode(["uid" => "", "card_status" => ""]);
}
?>