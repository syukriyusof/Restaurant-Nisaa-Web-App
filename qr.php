<?php
session_start();

// Mark this session as a guest session
$_SESSION['guest'] = true;

// Optional: assign a temporary guest name
if (!isset($_SESSION['guest_name'])) {
    $_SESSION['guest_name'] = 'Guest-' . rand(1000,9999);
}

// Redirect straight to menu
header("Location: /fyp-nisaa/pages/menu.php?qr=1");
exit;