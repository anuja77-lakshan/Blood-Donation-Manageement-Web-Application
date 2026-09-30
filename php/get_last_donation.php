<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Unauthorized access. Please login as admin.'
    ]);
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "bloodlink_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit();
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Please enter a valid numeric User ID.'
    ]);
    exit();
}

// 1. Fetch user information
$user_stmt = $conn->prepare("SELECT id, full_name, blood_group FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

if (!$user) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'No user found with ID: ' . $user_id
    ]);
    $conn->close();
    exit();
}

// 2. Fetch the most recent donation from the donations table
$donation_stmt = $conn->prepare("SELECT donation_date, location, camp_name, status 
                                FROM donations 
                                WHERE user_id = ? 
                                ORDER BY donation_date DESC 
                                LIMIT 1");
$donation_stmt->bind_param("i", $user_id);
$donation_stmt->execute();
$donation_result = $donation_stmt->get_result();
$last_donation = $donation_result->fetch_assoc();
$donation_stmt->close();

if ($last_donation && !empty($last_donation['donation_date'])) {
    echo json_encode([
        'status' => 'success',
        'user_id' => $user['id'],
        'full_name' => $user['full_name'],
        'blood_group' => $user['blood_group'],
        'last_donation_date' => date('F d, Y', strtotime($last_donation['donation_date'])),
        'last_donation_time' => date('h:i A', strtotime($last_donation['donation_date'])),
        'location' => $last_donation['location'],
        'camp_name' => $last_donation['camp_name'],
        'donation_status' => $last_donation['status']
    ]);
} else {
    echo json_encode([
        'status' => 'warning',
        'user_id' => $user['id'],
        'full_name' => $user['full_name'],
        'blood_group' => $user['blood_group'],
        'message' => 'User exists, but has no recorded donations in the system.'
    ]);
}

$conn->close();
?>