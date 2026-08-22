<?php

session_start();

require_once "../config/database.php";

header("Content-Type: application/json");

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

$pet_name = trim($_POST["pet_name"] ?? "");
$species = trim($_POST["species"] ?? "");
$breed = trim($_POST["breed"] ?? "");
$gender = $_POST["gender"] ?? null;
$ageInput = $_POST["age"] ?? "";
$weightInput = $_POST["weight"] ?? "";
$age = $ageInput !== "" ? (int) $ageInput : null;
$weight = $weightInput !== "" ? (float) $weightInput : null;

if ($pet_name === "" || $species === "") {
    echo json_encode([
        "status" => "error",
        "message" => "Pet name and species are required."
    ]);
    exit;
}

$photo = null;

if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK) {
    $uploadDirectory = __DIR__ . "/uploads/";

    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }

    $extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
    $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];

    if (!in_array($extension, $allowedExtensions, true)) {
        echo json_encode([
            "status" => "error",
            "message" => "Please upload a valid image file."
        ]);
        exit;
    }

    $photo = "uploads/" . uniqid("pet_", true) . "." . $extension;

    if (!move_uploaded_file($_FILES["photo"]["tmp_name"], __DIR__ . "/" . $photo)) {
        echo json_encode([
            "status" => "error",
            "message" => "Unable to save the pet photo."
        ]);
        exit;
    }
}

$sql = "INSERT INTO pets
        (user_id, pet_name, species, breed, gender, age, weight, photo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "issssids",
    $user_id,
    $pet_name,
    $species,
    $breed,
    $gender,
    $age,
    $weight,
    $photo
);

if ($stmt->execute()) {

    echo json_encode([
        "status" => "success",
        "message" => "Pet added successfully.",
        "pet_id" => $stmt->insert_id
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