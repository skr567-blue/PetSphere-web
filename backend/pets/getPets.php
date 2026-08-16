<?php

session_start();

header("Content-Type: application/json");

include "../config/database.php";

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "status" => "error",
        "message" => "Please login first."
    ]);

    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT id, pet_name, species, breed, gender, age, weight
     FROM pets
     WHERE user_id = ?
     ORDER BY id DESC"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$pets = [];

while ($row = $result->fetch_assoc()) {

    $pets[] = $row;

}

echo json_encode([
    "status" => "success",
    "pets" => $pets
]);

$stmt->close();

$conn->close();

?>