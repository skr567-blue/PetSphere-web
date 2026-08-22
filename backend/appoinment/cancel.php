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


$appointment_id = $_POST["appointment_id"] ?? null;


if (empty($appointment_id)) {

    echo json_encode([
        "status" => "error",
        "message" => "Appointment ID is required."
    ]);

    exit;
}



$sql = "SELECT id, status
        FROM appointments
        WHERE id = ?
        AND user_id = ?";

$stmt = $conn->prepare($sql);


if (!$stmt) {

    echo json_encode([
        "status" => "error",
        "message" => "Database error."
    ]);

    exit;
}


$stmt->bind_param(
    "ii",
    $appointment_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {

    echo json_encode([
        "status" => "error",
        "message" => "Appointment not found."
    ]);

    $stmt->close();
    $conn->close();

    exit;
}


$appointment = $result->fetch_assoc();

$stmt->close();



if (
    $appointment["status"] === "Cancelled"
) {

    echo json_encode([
        "status" => "error",
        "message" => "This appointment is already cancelled."
    ]);

    $conn->close();

    exit;
}


if (
    $appointment["status"] === "Completed"
) {

    echo json_encode([
        "status" => "error",
        "message" => "A completed appointment cannot be cancelled."
    ]);

    $conn->close();

    exit;
}



$sql = "UPDATE appointments
        SET status = 'Cancelled'
        WHERE id = ?
        AND user_id = ?";


$stmt = $conn->prepare($sql);


if (!$stmt) {

    echo json_encode([
        "status" => "error",
        "message" => "Database error."
    ]);

    exit;
}


$stmt->bind_param(
    "ii",
    $appointment_id,
    $user_id
);


if ($stmt->execute()) {

    echo json_encode([
        "status" => "success",
        "message" => "Appointment cancelled successfully."
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Failed to cancel appointment."
    ]);
}


$stmt->close();
$conn->close();

?>