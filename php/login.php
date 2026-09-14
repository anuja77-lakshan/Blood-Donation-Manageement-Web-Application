<?php
session_start();
require_once 'db.php'; // Same directory

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    // Check user by email
    $sql    = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['user_email'] = $user['email'];
            header("Location: ../personal-account.php");
            exit();
        } else {
            echo "<script>alert('Invalid password'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('No account found with this email'); window.history.back();</script>";
        exit();
    }
} else {
    header("Location: ../login-register.php");
    exit();
}
?>