<?php

require_once "../config/database.php";

header("Content-Type: application/json");

$sql = "SELECT id, fullname, staff_type FROM staff ORDER BY fullname";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to load staff."
    ]);
    exit;
}

$staff = [];

while ($row = mysqli_fetch_assoc($result)) {
    $staff[] = $row;
}

echo json_encode([
    "status" => "success",
    "staff" => $staff
]);

?>