<?php
require_once 'db.php';
$conn = getDbConnection();

// 1. Add capacity to bus table if not exists
$check = $conn->query("SHOW COLUMNS FROM bus LIKE 'capacity'");
if ($check->num_rows === 0) {
    if ($conn->query("ALTER TABLE bus ADD COLUMN capacity INT DEFAULT 36")) {
        echo "Added 'capacity' column to 'bus' table.\n";
    } else {
        echo "Error adding column: " . $conn->error . "\n";
    }
} else {
    echo "'capacity' column already exists.\n";
}

// 2. Create bookings table
$sql = "CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    bus_id INT NOT NULL,
    user_id INT NOT NULL,
    seat_number VARCHAR(10) NOT NULL,
    booking_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bus_id) REFERENCES bus(bus_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_booking (bus_id, seat_number, booking_date)
)";

if ($conn->query($sql)) {
    echo "Table 'bookings' created or already exists.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

// 3. Populate Random Capacities & Dummy Bookings for Today
$buses = $conn->query("SELECT bus_id FROM bus");
while ($bus = $buses->fetch_assoc()) {
    $id = $bus['bus_id'];

    // Random Capacity (30, 36, or 40)
    $cap = [30, 36, 40][array_rand([30, 36, 40])];
    $conn->query("UPDATE bus SET capacity = $cap WHERE bus_id = $id");

    // Random Pre-booked Seats for Today
    $seats_booked = rand(5, 15); // Book 5-15 seats randomly
    $today = date('Y-m-d');

    for ($i = 0; $i < $seats_booked; $i++) {
        // Generate random seat (e.g., A1, B3)
        $cols = ['A', 'B', 'C', 'D']; // 2+2 layout typically has 4 columns
        $rows = ceil($cap / 4);

        $col = $cols[array_rand($cols)];
        $row = rand(1, $rows);
        $seat = $col . $row;

        // Try to insert dummy booking (ignore duplicates due to UNIQUE constraint)
        // User ID 1 is assumed to exist (usually admin) or use a valid ID
        $conn->query("INSERT IGNORE INTO bookings (bus_id, user_id, seat_number, booking_date) 
                      VALUES ($id, 1, '$seat', '$today')");
    }
}
echo "Database setup and population complete.\n";
?>