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

if (empty($pet_id)) {

    echo json_encode([
        "status" => "error",
        "message" => "Pet ID is required."
    ]);

    exit;
}

$stmt = $conn->prepare(
    "DELETE FROM pets
     WHERE id = ?
     AND user_id = ?"
);


$stmt->bind_param(
    "ii",
    $pet_id,
    $user_id
);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        echo json_encode([
            "status" => "success",
            "message" => "Pet deleted successfully."
        ]);

    } else {

        echo json_encode([
            "status" => "error",
            "message" => "Pet not found or you do not have permission to delete it."
        ]);
    }

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Failed to delete pet."
    ]);
}

$stmt->close();

$conn->close();

?>