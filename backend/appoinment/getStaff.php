<?php

require_once "../config/database.php";

header("Content-Type: application/json");

$sql = "SELECT id, fullname, staff_type, phone, email FROM staff";

$result = mysqli_query($conn, $sql);

if (!$result) {

    echo json_encode([
        "status" => "error",
        "message" => "Failed to retrieve staff."
    ]);

    exit;
}

$staff = [];

while ($row = mysqli_fetch_assoc($result)) {

    $staff[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $staff
]);

?>