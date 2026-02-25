<?php
require_once 'db.php';
$conn = getDbConnection();
$result = $conn->query("DESCRIBE bus");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>