<?php
require_once 'db.php';
$conn = getDbConnection();
header('Content-Type: application/json');

$busId = $_GET['bus_id'] ?? 0;
$date = $_GET['date'] ?? '';

if (!$busId || !$date) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT seat_number FROM bookings WHERE bus_id = ? AND travel_date = ? AND status = 'confirmed'");
$stmt->bind_param("is", $busId, $date);
$stmt->execute();
$result = $stmt->get_result();

$seats = [];
while ($row = $result->fetch_assoc()) {
    $seats[] = $row['seat_number'];
}

echo json_encode($seats);
?>
