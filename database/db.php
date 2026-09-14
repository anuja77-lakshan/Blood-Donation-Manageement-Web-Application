<?php
$conn = mysqli_connect("localhost", "root", "", "bloodlink_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>