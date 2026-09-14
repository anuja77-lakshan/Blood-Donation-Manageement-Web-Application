<?php
// Prevent PHP error text from corrupting JSON output
error_reporting(0);
header('Content-Type: application/json');

require_once 'db.php';

// Check DB connection
if ($conn->connect_error) {
    echo json_encode([]);
    exit();
}

// Fetch all camps where date is today or upcoming
$sql = "SELECT *, DATEDIFF(camp_date, CURDATE()) AS days_diff 
        FROM blood_camps 
        WHERE camp_date >= CURDATE() 
        ORDER BY camp_date ASC";

$result = $conn->query($sql);

$camps = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $camps[] = $row;
    }
}

echo json_encode($camps);
$conn->close();
?>