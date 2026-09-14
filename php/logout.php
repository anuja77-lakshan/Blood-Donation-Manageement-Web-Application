<?php
session_start();
session_unset();
session_destroy();

// Redirect back to root login page
header("Location: ../login-register.php");
exit();
?>