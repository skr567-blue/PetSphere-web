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


$pet_id = $_POST["pet_id"] ?? null;
$service_id = $_POST["service_id"] ?? null;
$staff_id = $_POST["staff_id"] ?? null;
$appointment_date = $_POST["appointment_date"] ?? null;
$appointment_time = $_POST["appointment_time"] ?? null;
$notes = $_POST["notes"] ?? null;


if (
    empty($pet_id) ||
    empty($service_id) ||
    empty($appointment_date) ||
    empty($appointment_time)
) {

    echo json_encode([
        "status" => "error",
        "message" => "Please fill in all required fields."
    ]);

    exit;
}


$sql = "INSERT INTO appointments
        (user_id, pet_id, service_id, staff_id, appointment_date, appointment_time, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?)";


$stmt = $conn->prepare($sql);


if (!$stmt) {

    echo json_encode([
        "status" => "error",
        "message" => "Database error."
    ]);

    exit;
}


$stmt->bind_param(
    "iiiisss",
    $user_id,
    $pet_id,
    $service_id,
    $staff_id,
    $appointment_date,
    $appointment_time,
    $notes
);


if ($stmt->execute()) {

    echo json_encode([
        "status" => "success",
        "message" => "Appointment booked successfully."
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Failed to book appointment."
    ]);
}


$stmt->close();

$conn->close();

?>