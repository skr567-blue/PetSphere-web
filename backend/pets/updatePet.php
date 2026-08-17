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



$pet_id = $_POST["pet_id"];
$pet_name = trim($_POST["pet_name"]);
$species = trim($_POST["species"]);
$breed = trim($_POST["breed"]);
$gender = $_POST["gender"];
$age = $_POST["age"];
$weight = $_POST["weight"];



if (
    empty($pet_id) ||
    empty($pet_name) ||
    empty($species)
) {

    echo json_encode([
        "status" => "error",
        "message" => "Please fill in all required fields."
    ]);

    exit;
}

$stmt = $conn->prepare(
    "UPDATE pets
     SET pet_name = ?,
         species = ?,
         breed = ?,
         gender = ?,
         age = ?,
         weight = ?
     WHERE id = ?
     AND user_id = ?"
);


$stmt->bind_param(
    "ssssidii",
    $pet_name,
    $species,
    $breed,
    $gender,
    $age,
    $weight,
    $pet_id,
    $user_id
);



if ($stmt->execute()) {

    echo json_encode([
        "status" => "success",
        "message" => "Pet updated successfully."
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Failed to update pet."
    ]);

}



$stmt->close();
$conn->close();

?>