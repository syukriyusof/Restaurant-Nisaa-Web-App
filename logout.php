<?php
session_start();
session_unset();
session_destroy();
header("Location: /fyp-nisaa/auth/login.php");
exit();
?>