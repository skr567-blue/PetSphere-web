<?php

session_start();

require_once "../config/database.php";

header("Content-Type: application/json");


if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "status" => "error",
        "message" => "Please login first."
    ]);

    exit;
}


$user_id = $_SESSION["user_id"];



$sql = "SELECT
            a.id,
            a.pet_id,
            a.service_id,
            a.staff_id,
            a.appointment_date,
            a.appointment_time,
            a.notes,
            a.status,

            p.pet_name,

            s.service_name,

            st.fullname AS staff_name

        FROM appointments a

        LEFT JOIN pets p
            ON a.pet_id = p.id

        LEFT JOIN services s
            ON a.service_id = s.id

        LEFT JOIN staff st
            ON a.staff_id = st.id

        WHERE a.user_id = ?

        ORDER BY a.appointment_date DESC,
                 a.appointment_time DESC";


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
    $user_id
);


$stmt->execute();


$result = $stmt->get_result();



$appointments = [];


while ($row = $result->fetch_assoc()) {

    $appointments[] = $row;
}



echo json_encode([
    "status" => "success",
    "appointments" => $appointments
]);


$stmt->close();

$conn->close();

?>