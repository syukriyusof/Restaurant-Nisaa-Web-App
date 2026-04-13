<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /fyp-nisaa/auth/login.php");
        exit();
    }
}

function require_role($role) {
    require_login();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        http_response_code(403);
        echo "403 Forbidden: You do not have permission to access this page.";
        exit();
    }
}
?>