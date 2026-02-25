<?php
require_once 'db.php';
$conn = getDbConnection();

$sql = "ALTER TABLE bookings 
        ADD COLUMN source_id INT AFTER seat_number,
        ADD COLUMN dest_id INT AFTER source_id";

if ($conn->query($sql) === TRUE) {
    echo "Table 'bookings' updated successfully";
} else {
    echo "Error updating table: " . $conn->error;
}
?>