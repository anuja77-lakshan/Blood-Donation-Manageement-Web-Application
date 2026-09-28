<?php
header('Content-Type: application/json');
require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['stocks']) && is_array($input['stocks'])) {
    $stmt = $conn->prepare("UPDATE blood_stocks SET percentage = ? WHERE blood_group = ?");
    
    foreach ($input['stocks'] as $item) {
        $val = (int)$item['val'];
        $group = $item['type'];
        $stmt->bind_param("is", $val, $group);
        $stmt->execute();
    }
    
    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'Stock updated successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data received.']);
}

$conn->close();
?>