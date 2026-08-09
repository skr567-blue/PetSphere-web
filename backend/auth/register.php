<?php

include "../config/database.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);
    exit();
}

    // Get form data
    $fullname = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";

    // Check required fields
    if (empty($fullname) || empty($email) || empty($password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Please fill in all required fields."
        ]);
        exit();
    }

    // Check if email already exists
    $checkEmail = $conn->prepare(
        "SELECT id FROM users WHERE email = ?"
    );

    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();

    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
            echo json_encode([
                "status" => "error",
                "message" => "This email is already registered."
            ]);
            
            $checkEmail->close();
            $conn->close();

            exit();
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
        echo json_encode([
            "status" => "success",
            "message" => "Registration successful! Welcome to PetSphere."
        ]);

    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Registration failed. Please try again"
        ]);    
    }

    // Close statements
    $checkEmail->close();
    $stmt->close();
$conn->close();

?>