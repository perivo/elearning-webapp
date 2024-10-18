<?php
if(!isset($_SESSION)) { session_start(); }  // Start session if not already started

// Function to check if the user is logged in
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['email']) && isset($_SESSION['user_id']);
    }
}

/**
 * Function to get user info from the session
 * @return array
 */
if (!function_exists('getUserInfo')) {
    function getUserInfo() {
        return [
            'name' => isLoggedIn() ? $_SESSION['user_name'] : 'Guest',
            'role' => isLoggedIn() ? $_SESSION['role'] : 'Visitor',
            'profile_pic' => isLoggedIn() && isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'default-user.jpg'
        ];
    }
}

/**
 * Function to log out a user
 */
if (!function_exists('logout')) {
    function logout() {
        session_destroy(); // Destroy the session
        header("Location: ../auth/login.php"); // Redirect to login page
        exit();
    }
}

/**
 * Example function for certificate generation (stub)
 * @param string $course_name
 * @param string $user_name
 */
if (!function_exists('generateCertificate')) {
    function generateCertificate($course_name, $user_name) {
        // Logic to generate certificate
        // This can be implemented later
    }
}
?>
