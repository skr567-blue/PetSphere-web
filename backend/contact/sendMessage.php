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
$email = trim($_POST["email"] ?? "");
$subject = trim($_POST["subject"] ?? "");
$message = trim($_POST["message"] ?? "");

if ($name === "" || $message === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
	echo json_encode([
		"status" => "error",
		"message" => "Please provide a valid name, email, and message."
	]);
	exit;
}

$stmt = $conn->prepare(
	"INSERT INTO contact_messages (name, email, subject, message)
	 VALUES (?, ?, ?, ?)"
);

$stmt->bind_param("ssss", $name, $email, $subject, $message);

if ($stmt->execute()) {
	echo json_encode([
		"status" => "success",
		"message" => "Thanks for reaching out! Our team will get back to you shortly."
	]);
} else {
	echo json_encode([
		"status" => "error",
		"message" => "Unable to send your message. Please try again."
	]);
}

$stmt->close();
$conn->close();

?>
