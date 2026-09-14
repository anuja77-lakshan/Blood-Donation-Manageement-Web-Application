<?php
session_start(); // Session start
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Email 
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Password compair
        if (password_verify($password, $user['password'])) {
            
            //  if Password is correct, Email and Name save the Session 
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            
         
            header("Location: personal-account.php"); 
            exit();
        } else {
            echo "<script>alert('Incorrect Password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Account not found! Please register first.'); window.history.back();</script>";
    }
}
?>