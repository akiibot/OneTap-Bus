<?php
require_once 'db.php';
$conn = getDbConnection();

$sql = "CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    bus_id INT NOT NULL,
    seat_number VARCHAR(10) NOT NULL,
    travel_date DATE NOT NULL,
    status ENUM('confirmed', 'cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (bus_id) REFERENCES bus(bus_id),
    UNIQUE KEY unique_seat (bus_id, seat_number, travel_date)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'bookings' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}
?>