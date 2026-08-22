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



$sql = "SELECT id
        FROM pets
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
    $pet_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "status" => "error",
        "message" => "You can only book an appointment for your own pet."
    ]);

    $stmt->close();
    $conn->close();

    exit;
}

$stmt->close();



$sql = "SELECT id
        FROM services
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
    "i",
    $service_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "status" => "error",
        "message" => "Selected service does not exist."
    ]);

    $stmt->close();
    $conn->close();

    exit;
}

$stmt->close();



if (!empty($staff_id)) {

    $sql = "SELECT id
            FROM staff
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
        "i",
        $staff_id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {

        echo json_encode([
            "status" => "error",
            "message" => "Selected staff member does not exist."
        ]);

        $stmt->close();
        $conn->close();

        exit;
    }

    $stmt->close();
}



$today = date("Y-m-d");

if ($appointment_date < $today) {

    echo json_encode([
        "status" => "error",
        "message" => "You cannot book an appointment for a past date."
    ]);

    $conn->close();

    exit;
}



if (!empty($staff_id)) {

    $sql = "SELECT id
            FROM appointments
            WHERE staff_id = ?
            AND appointment_date = ?
            AND appointment_time = ?
            AND status IN ('Pending', 'Approved')";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {

        echo json_encode([
            "status" => "error",
            "message" => "Database error."
        ]);

        exit;
    }

    $stmt->bind_param(
        "iss",
        $staff_id,
        $appointment_date,
        $appointment_time
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        echo json_encode([
            "status" => "error",
            "message" => "This staff member is already booked for the selected date and time."
        ]);

        $stmt->close();
        $conn->close();

        exit;
    }

    $stmt->close();
}




$sql = "INSERT INTO appointments
        (
            user_id,
            pet_id,
            service_id,
            staff_id,
            appointment_date,
            appointment_time,
            notes
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)";


$stmt = $conn->prepare($sql);


if (!$stmt) {

    echo json_encode([
        "status" => "error",
        "message" => "Database error."
    ]);

    exit;
}



if (empty($staff_id)) {

    $staff_id = null;
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