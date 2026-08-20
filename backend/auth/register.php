<?php

session_start();

header("Content-Type: application/json");

require_once "../config/database.php";


/* ================= CHECK REQUEST ================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);

    exit;
}


/* ================= GET FORM DATA ================= */

$fullname = trim($_POST["fullname"] ?? "");

$email = trim($_POST["email"] ?? "");

$password = $_POST["password"] ?? "";


/* ================= VALIDATION ================= */

if ($fullname === "" || $email === "" || $password === "") {

    echo json_encode([
        "status" => "error",
        "message" => "Please fill in all required fields."
    ]);

    exit;
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "status" => "error",
        "message" => "Please enter a valid email address."
    ]);

    exit;
}


if (strlen($password) < 6) {

    echo json_encode([
        "status" => "error",
        "message" => "Password must contain at least 6 characters."
    ]);

    exit;
}


/* ================= CHECK EXISTING EMAIL ================= */

$check = $conn->prepare(
    "SELECT id FROM users WHERE email = ?"
);

$check->bind_param("s", $email);

$check->execute();

$result = $check->get_result();


if ($result->num_rows > 0) {

    echo json_encode([
        "status" => "error",
        "message" => "An account with this email already exists."
    ]);

    exit;
}


/* ================= HASH PASSWORD ================= */

$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);


/* ================= INSERT USER ================= */

$stmt = $conn->prepare(
    "INSERT INTO users (fullname, email, password)
     VALUES (?, ?, ?)"
);

$stmt->bind_param(
    "sss",
    $fullname,
    $email,
    $hashedPassword
);


/* ================= SAVE USER ================= */

if ($stmt->execute()) {

    echo json_encode([
        "status" => "success",
        "message" => "Registration successful! You can now login."
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Registration failed. Please try again."
    ]);

}


/* ================= CLOSE CONNECTION ================= */

$stmt->close();

$check->close();

$conn->close();

?>