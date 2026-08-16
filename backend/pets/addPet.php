<?php

session_start();

header("Content-Type: application/json");

include "../config/database.php";


/* Check whether user is logged in */

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "status" => "error",
        "message" => "Please login first."
    ]);

    exit;
}


/* Only allow POST requests */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);

    exit;
}


/* Get form data */

$user_id = $_SESSION["user_id"];

$pet_name = trim($_POST["pet_name"] ?? "");
$species = trim($_POST["species"] ?? "");
$breed = trim($_POST["breed"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$age = $_POST["age"] ?? null;
$weight = $_POST["weight"] ?? null;


/* Validate required fields */

if (empty($pet_name) || empty($species)) {

    echo json_encode([
        "status" => "error",
        "message" => "Pet name and species are required."
    ]);

    exit;
}


/* Insert pet */

$stmt = $conn->prepare(
    "INSERT INTO pets
    (user_id, pet_name, species, breed, gender, age, weight)
    VALUES (?, ?, ?, ?, ?, ?, ?)"
);

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


/* Execute */

if ($stmt->execute()) {

    echo json_encode([
        "status" => "success",
        "message" => "Pet added successfully!"
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