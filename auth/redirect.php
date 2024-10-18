<?php
session_start();
include '../includes/config.php'; // Database connection

// Redirect to login if not authenticated
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Redirect based on user role
switch ($_SESSION['role']) {
    case 'Student':
        header("Location: ../courses/all_courses.php");
        break;
    case 'Teacher':
        header("Location: ../teachers/manage_students.php");
        break;
    case 'Admin':
        header("Location: ../admin/manage_users.php");
        break;
    default:
        header("Location: login.php");
        break;
}

exit;
?>
