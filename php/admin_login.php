<?php
// Start session
session_start();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and trim submitted credentials
    $input_username = trim($_POST['admin_username'] ?? '');
    $input_password = trim($_POST['admin_password'] ?? '');

    // List of fixed admin credentials (username => password)
    // Add new admins here: "username" => "password"
    $admins = [
        "admin"      => "123456",
        "superadmin" => "987654"
    ];

    // Validate credentials against the admins list
    if (isset($admins[$input_username]) && $admins[$input_username] === $input_password) {
        // Set admin session flags
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_user'] = $input_username;

        // Redirect to admin dashboard
        header("Location: ../admin_dashboard.php");
        exit();
    } else {
        // Show alert and redirect back
        echo "<script>alert('Invalid Admin Username or Password!'); window.history.back();</script>";
        exit();
    }
} else {
    // Block direct GET access
    header("Location: ../login-register.php");
    exit();
}
?>