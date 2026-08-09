<?php

session_start();

include "../config/database.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if (empty($email) || empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "Please enter your email and password."
    ]);
    exit;
}

/* Find user by email */

$stmt = $conn->prepare(
    "SELECT id, fullname, email, password 
     FROM users 
     WHERE email = ?"
);

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

/* Check whether user exists */

if ($result->num_rows === 0) {

    echo json_encode([
        "status" => "error",
        "message" => "Invalid email or password."
    ]);

    $stmt->close();
    $conn->close();

    exit;
}

/* Get user */

$user = $result->fetch_assoc();

/* Verify password */

if (password_verify($password, $user["password"])) {

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["fullname"] = $user["fullname"];
    $_SESSION["email"] = $user["email"];

    echo json_encode([
        "status" => "success",
        "message" => "Login successful! Welcome, " . $user["fullname"] . "."
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Invalid email or password."
    ]);
}

$stmt->close();
$conn->close();

?>