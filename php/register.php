<?php

require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $nic = mysqli_real_escape_string($conn, $_POST['nic']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $district = $_POST['district'];
    $blood_group = $_POST['blood_group'];
    $weight = $_POST['weight'];
    
    if ($_POST['password'] !== $_POST['confirm_password']) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

  
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    $sql = "INSERT INTO users (full_name, nic, dob, gender, email, phone, location, district, blood_group, weight, password) 
            VALUES ('$full_name', '$nic', '$dob', '$gender', '$email', '$phone', '$location', '$district', '$blood_group', '$weight', '$password')";

    if (mysqli_query($conn, $sql)) {
       
        echo "<script>alert('Registration Successful! Please Log in.'); window.location.href='index.html';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>