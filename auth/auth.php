<?php
session_start();
include '../includes/config.php'; // Database connection
include_once '../includes/functions.php'; // Include functions once

// Redirect users if already logged in
if (isLoggedIn()) {
    switch (strtolower($_SESSION['role'])) {
        case 'student':
            header("Location: ../courses/all_courses.php");
            exit;
        case 'teacher':
            header("Location: ../teachers/manage_portal.php");
            exit;
        case 'admin':
            header("Location: ../admin/manage_users.php");
            exit;
        default:
            // If the role is unrecognized, destroy session and redirect to login
            logout();
    }
}

// Redirect to login if not logged in
header("Location: ../auth/login.php");
exit;
?>
