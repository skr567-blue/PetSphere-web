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


/* ================= CHECK LOGIN ================= */

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "status" => "error",
        "message" => "Please login first."
    ]);

    exit;
}


/* ================= GET USER ID ================= */

$user_id = $_SESSION["user_id"];


/* ================= GET FORM DATA ================= */

$fullname = trim($_POST["fullname"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$nic = trim($_POST["nic"] ?? "");
$address = trim($_POST["address"] ?? "");


/* ================= VALIDATION ================= */

if ($fullname === "" || $email === "" || $phone === "") {

    echo json_encode([
        "status" => "error",
        "message" => "Please fill in all required owner information."
    ]);

    exit;
}


/* ================= UPDATE USER ================= */

$sql = "UPDATE users
        SET fullname = ?,
            email = ?,
            phone = ?,
            nic = ?,
            address = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "status" => "error",
        "message" => "Database error."
    ]);

    exit;
}


$stmt->bind_param(
    "sssssi",
    $fullname,
    $email,
    $phone,
    $nic,
    $address,
    $user_id
);


if ($stmt->execute()) {

    $_SESSION["fullname"] = $fullname;
    $_SESSION["email"] = $email;

    echo json_encode([
        "status" => "success",
        "message" => "Owner information saved successfully."
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Unable to save owner information."
    ]);
}


$stmt->close();
$conn->close();

?>