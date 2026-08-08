<?php

include "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];

    // Check that required fields are filled
    if (empty($fullname) || empty($email) || empty($password)) {
        die("Please fill in all required fields.");
    }

    // Check whether email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        die("This email is already registered.");
    }

    // Securely hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user
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

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Registration failed: " . $stmt->error;
    }

    $stmt->close();
    $checkEmail->close();
}

$conn->close();

?>