<?php

session_start();

header("Content-Type: application/json");

require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
	echo json_encode([
		"status" => "error",
		"message" => "Please login first."
	]);
	exit;
}

$stmt = $conn->prepare(
	"SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.notes,
			p.pet_name, s.service_name, st.fullname AS staff_name
	 FROM appointments a
	 INNER JOIN pets p ON p.id = a.pet_id
	 INNER JOIN services s ON s.id = a.service_id
	 LEFT JOIN staff st ON st.id = a.staff_id
	 WHERE a.user_id = ?
	 ORDER BY a.appointment_date DESC, a.appointment_time DESC"
);

$stmt->bind_param("i", $_SESSION["user_id"]);
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
