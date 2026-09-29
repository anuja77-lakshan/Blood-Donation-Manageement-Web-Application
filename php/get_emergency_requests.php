<?php
include 'db.php';

header('Content-Type: application/json');

// Table data
$sql = "SELECT * FROM emergency_requests 
        ORDER BY CASE WHEN status = 'Critical' THEN 1 ELSE 2 END, id DESC";

// SQL query run and after show resulat
$result = mysqli_query($conn, $sql);

$data = array();

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode($data); // print as a json
?>
