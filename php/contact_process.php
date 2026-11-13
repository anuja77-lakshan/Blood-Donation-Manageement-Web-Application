<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "bloodlink_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo "error";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = $_POST['firstName'] ?? '';
    $last_name  = $_POST['lastName'] ?? '';
    $email      = $_POST['email'] ?? '';
    $message    = $_POST['message'] ?? '';

    $stmt = $conn->prepare("INSERT INTO contact_messages (first_name, last_name, email, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $message);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>