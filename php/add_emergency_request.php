<?php
error_reporting(0);
header('Content-Type: application/json');

require_once 'db.php';

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    $patient_name = $data['patientName'] ?? '';
    $blood_group  = $data['bloodType'] ?? '';
    $hospital     = $data['location'] ?? '';
    $contact_no   = $data['contact'] ?? '';
    $status       = 'Pending';

    if (!empty($patient_name) && !empty($blood_group) && !empty($hospital) && !empty($contact_no)) {
        $stmt = $conn->prepare("INSERT INTO emergency_requests (patient_name, blood_group, hospital, contact_no, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $patient_name, $blood_group, $hospital, $contact_no, $status);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Required fields missing']);
    }
}
$conn->close();
?>