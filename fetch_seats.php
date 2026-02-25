<?php
require_once 'db.php';
header('Content-Type: application/json');

$bus_id = (int) $_GET['bus_id'];
$date = $_GET['date'] ?? date('Y-m-d');

$conn = getDbConnection();

// Get Capacity
$bus = $conn->query("SELECT capacity FROM bus WHERE bus_id = $bus_id")->fetch_assoc();

// Get Booked Seats
$sql = "SELECT seat_number FROM bookings WHERE bus_id = ? AND booking_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $bus_id, $date);
$stmt->execute();
$result = $stmt->get_result();

$booked = [];
while ($row = $result->fetch_assoc()) {
    $booked[] = $row['seat_number'];
}

echo json_encode([
    'capacity' => (int) $bus['capacity'],
    'booked' => $booked
]);
?>