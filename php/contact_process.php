<?php
require_once '../database/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = $_POST['firstName'] ?? '';
    $last_name  = $_POST['lastName'] ?? '';
    $email      = $_POST['email'] ?? '';
    $message    = $_POST['message'] ?? '';

    $sql = "INSERT INTO contact_messages (first_name, last_name, email, message) 
            VALUES ('$first_name', '$last_name', '$email', '$message')";

    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "error";
    }

    mysqli_close($conn);
    exit();
}
?>