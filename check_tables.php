<?php
require_once 'db.php';
$conn = getDbConnection();
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    echo $row[0] . "\n";
}
?>