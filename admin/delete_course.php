<?php 
session_start();
include '../includes/config.php'; // Database connection

// Check if the user is logged in and has the role of Admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); // Redirect to login if not authenticated
    exit;
}

// Handle course deletion
if (isset($_GET['id'])) {
    $course_id = intval($_GET['id']);

    // Check if course exists before attempting to delete
    $check_query = "SELECT id FROM courses WHERE id='$course_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Proceed to delete the course
        $delete_query = "DELETE FROM courses WHERE id='$course_id'";
        if (mysqli_query($conn, $delete_query)) {
            // Redirect with a success message
            header("Location: manage_users.php?success=Course successfully deleted!");
        } else {
            // Redirect with an error message
            header("Location: manage_users.php?error=Error deleting course: " . mysqli_error($conn));
        }
    } else {
        // Redirect with an error message if course not found
        header("Location: manage_users.php?error=Course not found!");
    }
} else {
    // Redirect if no course ID is provided
    header("Location: manage_users.php?error=No course ID specified!");
}
?>
