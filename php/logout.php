<?php
// Start session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session completely
session_destroy();

// Redirect to login page
header("Location: ../login-register.php#login");
exit();
?>