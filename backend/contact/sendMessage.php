<?php

header("Content-Type: application/json");

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
    exit;
}

$name = trim($_POST["name"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$email = trim($_POST["email"] ?? "");
$subject = trim($_POST["subject"] ?? "");
$message = trim($_POST["message"] ?? "");

/* Validate required fields */

if ($name === "" || $email === "" || $message === "") {
    echo json_encode([
        "status" => "error",
        "message" => "Please fill in all required fields."
    ]);
    exit;
}

/* Validate email */

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status" => "error",
        "message" => "Please enter a valid email address."
    ]);
    exit;
}

/* Insert message into database */

$sql = "INSERT INTO contact_messages 
        (name, phone, email, subject, message)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param(
    "sssss",
    $name,
    $phone,
    $email,
    $subject,
    $message
);

if ($stmt->execute()) {

    echo json_encode([
        "status" => "success",
        "message" => "Your message has been sent successfully!"
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Failed to save your message."
    ]);
}

$stmt->close();
$conn->close();

?>