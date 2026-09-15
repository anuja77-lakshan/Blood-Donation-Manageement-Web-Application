<?php
session_start();
require_once 'db.php'; // DB connection

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login-register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_email = mysqli_real_escape_string($conn, $_SESSION['user_email']);

    // Sanitize submitted inputs
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $phone     = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $new_email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $location  = mysqli_real_escape_string($conn, trim($_POST['location']));
    $district  = mysqli_real_escape_string($conn, $_POST['district']);
    $weight    = mysqli_real_escape_string($conn, $_POST['weight']);

    // Validation checks
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format'); window.history.back();</script>";
        exit();
    }

    if (!preg_match("/^0[0-9]{9}$/", $phone)) {
        echo "<script>alert('Phone number must be exactly 10 digits starting with 0'); window.history.back();</script>";
        exit();
    }

    if ($weight < 45) {
        echo "<script>alert('Minimum weight requirement is 45 kg'); window.history.back();</script>";
        exit();
    }

    // Check if new email is already taken by another user
    if ($new_email !== $current_email) {
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$new_email' LIMIT 1");
        if (mysqli_num_rows($check_email) > 0) {
            echo "<script>alert('This email is already in use by another account'); window.history.back();</script>";
            exit();
        }
    }

    // Update user profile in database
    $sql = "UPDATE users 
            SET full_name = '$full_name', 
                phone = '$phone', 
                email = '$new_email', 
                location = '$location', 
                district = '$district', 
                weight = '$weight' 
            WHERE email = '$current_email'";

    if (mysqli_query($conn, $sql)) {
        // Update active session values
        $_SESSION['user_email'] = $new_email;
        $_SESSION['user_name']  = $full_name;

        echo "<script>alert('Profile updated successfully!'); window.location.href = '../personal-account.php';</script>";
        exit();
    } else {
        echo "Database error: " . mysqli_error($conn);
    }
} else {
    header("Location: ../personal-account.php");
    exit();
}
?>