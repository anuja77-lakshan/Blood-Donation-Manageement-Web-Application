<?php
session_start();
session_unset();
session_destroy();
header("Location: login_registrer.html"); // go to login page
exit();
?>