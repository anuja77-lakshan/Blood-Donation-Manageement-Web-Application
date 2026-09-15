<?php
session_start();
require_once 'db.php'; // Same directory

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name        = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $nic              = mysqli_real_escape_string($conn, trim($_POST['nic']));
    $dob              = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender           = mysqli_real_escape_string($conn, $_POST['gender']);
    $email            = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone            = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $location         = mysqli_real_escape_string($conn, trim($_POST['location']));
    $district         = mysqli_real_escape_string($conn, $_POST['district']);
    $blood_group      = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $weight           = mysqli_real_escape_string($conn, $_POST['weight']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check valid email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format'); window.history.back();</script>";
        exit();
    }

    // Check phone number format (10 digits starting with 0)
    if (!preg_match("/^0[0-9]{9}$/", $phone)) {
        echo "<script>alert('Phone number must be exactly 10 digits starting with 0'); window.history.back();</script>";
        exit();
    }

    // Check minimum donor age (18+)
    $birthDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    if ($age < 18) {
        echo "<script>alert('You must be at least 18 years old to register'); window.history.back();</script>";
        exit();
    }

    // Check minimum donor weight (45 kg)
    if ($weight < 45) {
        echo "<script>alert('Minimum weight requirement for blood donation is 45 kg'); window.history.back();</script>";
        exit();
    }

    // Check minimum password length
    if (strlen($password) < 6) {
        echo "<script>alert('Password must be at least 6 characters long'); window.history.back();</script>";
        exit();
    }

    // Check passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match'); window.history.back();</script>";
        exit();
    }

    // Check duplicate email
    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' LIMIT 1");
    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('Email already registered'); window.location.href='../login-register.php';</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user record
    $sql = "INSERT INTO users (full_name, nic, dob, gender, email, phone, location, district, blood_group, weight, password) 
            VALUES ('$full_name', '$nic', '$dob', '$gender', '$email', '$phone', '$location', '$district', '$blood_group', '$weight', '$hashed_password')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Registration successful! Please login.'); window.location.href='../login-register.php';</script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: ../login-register.php");
    exit();
}
?>