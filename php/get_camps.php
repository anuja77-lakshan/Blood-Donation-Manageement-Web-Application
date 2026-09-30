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
//auto delete after 24hours
$conn->query("DELETE FROM blood_camps WHERE TIMESTAMP(camp_date, end_time) < NOW() - INTERVAL 24 HOUR");

// get active or ended but not exceed 24 hour camps
$sql = "SELECT *, 
        DATEDIFF(camp_date, CURDATE()) AS days_diff,
        TIMESTAMPDIFF(MINUTE, NOW(), TIMESTAMP(camp_date, end_time)) AS minutes_left 
        FROM blood_camps 
        WHERE TIMESTAMP(camp_date, end_time) >= NOW() - INTERVAL 24 HOUR 
        ORDER BY camp_date ASC, start_time ASC";

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