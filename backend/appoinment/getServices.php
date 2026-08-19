<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$sql = "SELECT id, service_name, price FROM services ORDER BY service_name";

$result = myaql_query($conn, $sql);

if(!$result){
    echo json_encode([
        "status" => "error",
        "message" => "Failed to load services."
    ]);
    exit();
}

$services = [];

while($row = mysqli_fetch_assoc)
?>