<?php

include "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];

    // Check required fields
    if (empty($fullname) || empty($email) || empty($password)) {
        die("Please fill in all required fields.");
    }

    // Check if email already exists
    $checkEmail = $conn->prepare(
        "SELECT id FROM users WHERE email = ?"
    );

    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();

    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        die("This email is already registered.");
    }

    // Hash the password
    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    // Insert user into database
    $stmt = $conn->prepare(
        "INSERT INTO users (fullname, email, phone, password)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssss",
        $fullname,
        $email,
        $phone,
        $hashedPassword
    );

    // Execute INSERT
    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Registration failed: " . $stmt->error;
    }

    // Close statements
    $checkEmail->close();
    $stmt->close();
}

$conn->close();

?>