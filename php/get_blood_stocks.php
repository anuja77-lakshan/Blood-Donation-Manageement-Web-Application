<?php
header('Content-Type: application/json');
require_once 'db.php';

$query = "SELECT blood_group AS type, percentage AS val FROM blood_stocks";
$result = $conn->query($query);

$stocks = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['val'] = (int)$row['val'];
        $stocks[] = $row;
    }
}

echo json_encode($stocks);
$conn->close();
?>