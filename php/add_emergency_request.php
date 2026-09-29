<?php
header('Content-Type: application/json');
include 'db.php';

// Database connection check
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . mysqli_connect_error()]);
    exit();
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit();
}

// get Data
$patient_name = mysqli_real_escape_string($conn, $data['patientName'] ?? '');
$blood_group  = mysqli_real_escape_string($conn, $data['bloodType'] ?? '');
$hospital     = mysqli_real_escape_string($conn, $data['location'] ?? '');
$contact_no   = mysqli_real_escape_string($conn, $data['contact'] ?? '');
$status       = mysqli_real_escape_string($conn, $data['urgency'] ?? 'Critical');

// Field check
if ($patient_name == '' || $blood_group == '' || $hospital == '' || $contact_no == '') {
    echo json_encode(['success' => false, 'message' => 'Form inputs are empty! Check your HTML IDs.']);
    exit();
}

// SQL insert query
$sql = "INSERT INTO emergency_requests (patient_name, blood_group, hospital, contact_no, status, created_at) 
        VALUES ('$patient_name', '$blood_group', '$hospital', '$contact_no', '$status', NOW())";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'SQL Error: ' . mysqli_error($conn)]);
}
?>