<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$ratingId = (int)$data['id'];

$conn = getDbConnection();

$stmt = $conn->prepare("
    DELETE FROM bus_rating
    WHERE rating_id = ?
");
$stmt->bind_param("i", $ratingId);
$stmt->execute();

echo json_encode(['success' => true]);
