<?php
require_once 'db.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request']));
}

if (!isset($_SESSION['logged_in'])) {
    die(json_encode(['success' => false, 'message' => 'Please sign in to book']));
}

$input = json_decode(file_get_contents('php://input'), true);
$bus_id = (int) $input['bus_id'];
$seats = $input['seats']; // Array of seat numbers
$date = $input['date'];
$user_id = $_SESSION['user_id'];
$source_id = isset($input['source_id']) ? (int) $input['source_id'] : 0;
$dest_id = isset($input['dest_id']) ? (int) $input['dest_id'] : 0;

if (empty($seats) || !$bus_id || !$date) {
    die(json_encode(['success' => false, 'message' => 'Missing data']));
}

$conn = getDbConnection();

// Check availability first
$placeholders = implode(',', array_fill(0, count($seats), '?'));
$types = str_repeat('s', count($seats));
$sql = "SELECT seat_number FROM bookings WHERE bus_id = ? AND booking_date = ? AND seat_number IN ($placeholders)";
$stmt = $conn->prepare($sql);
$params = array_merge([$bus_id, $date], $seats);
$stmt->bind_param("is" . $types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $booked = [];
    while ($row = $result->fetch_assoc())
        $booked[] = $row['seat_number'];
    die(json_encode(['success' => false, 'message' => 'Some seats were just taken: ' . implode(', ', $booked)]));
}

// Book them
$conn->begin_transaction();
try {
    $insert = $conn->prepare("INSERT INTO bookings (bus_id, user_id, seat_number, booking_date, source_id, dest_id) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($seats as $seat) {
        $insert->bind_param("iisssi", $bus_id, $user_id, $seat, $date, $source_id, $dest_id);
        $insert->execute();
    }
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>