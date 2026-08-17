<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
    exit;
}

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "status" => "error",
        "message" => "Please login first."
    ]);
    exit;
}

$user_id = $_SESSION["user_id"];

$pet_name = $_POST["pet_name"];
$species = $_POST["species"];
$breed = $_POST["breed"];
$gender = $_POST["gender"];
$age = $_POST["age"];
$weight = $_POST["weight"];

$sql = "INSERT INTO pets
        (user_id, pet_name, species, breed, gender, age, weight)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "issssid",
    $user_id,
    $pet_name,
    $species,
    $breed,
    $gender,
    $age,
    $weight
);

if ($stmt->execute()) {

    echo json_encode([
        "status" => "success",
        "message" => "Pet added successfully."
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Failed to add pet."
    ]);
}

$stmt->close();
$conn->close();

?>