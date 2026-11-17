<?php
session_start();

// Include database connection
require_once 'db.php';

// Check if user is logged in
$is_user_logged_in  = isset($_SESSION['user_email']);
$is_admin_logged_in = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Restrict unauthorized direct access
if ((!$is_user_logged_in && !$is_admin_logged_in) || $_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['camp_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$current_user_id = $_SESSION['user_id'] ?? 0;
$camp_id = intval($_POST['camp_id']);

try {
    // Get creator ID for the requested camp
    $stmt = $conn->prepare("SELECT created_by FROM blood_camps WHERE id = ?");
    $stmt->bind_param("i", $camp_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $camp = $result->fetch_assoc();

    if ($camp) {
        // Allow delete only if user is owner or admin
        if ($is_admin_logged_in || ($current_user_id && intval($camp['created_by']) === intval($current_user_id))) {
            
            // Delete camp from database
            $deleteStmt = $conn->prepare("DELETE FROM blood_camps WHERE id = ?");
            $deleteStmt->bind_param("i", $camp_id);
            
            if ($deleteStmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Blood Camp successfully removed.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to execute delete: ' . $deleteStmt->error]);
            }
            $deleteStmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'You do not have permission to delete this camp.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Camp not found in database.']);
    }
    $stmt->close();

} catch (Exception $e) {
    // Return the actual database/PHP error message to the browser alert
    echo json_encode(['status' => 'error', 'message' => 'Exception Error: ' . $e->getMessage()]);
}
exit();
?>