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
            header("Location: ../teachers/manage_students.php");
            exit;
        case 'admin':
            header("Location: ../admin/manage_users.php");
            exit;
        default:
            // If the role is unrecognized, destroy session and redirect to login
            logout();
    }
}

// Initialize error message variable
$error = "";

// Handle login form submission
if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['pass'];

    // Use prepared statements to avoid SQL injection
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if the user exists and verify the password
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            // Set session variables
            $_SESSION['email'] = $email;
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_id'] = $row['id']; // Store user ID in the session
            $_SESSION['role'] = $row['role'];

            // Redirect based on user role
            switch (strtolower($_SESSION['role'])) {
                case 'student':
                    header("Location: ../courses/all_courses.php");
                    exit;
                case 'teacher':
                    header("Location: ../teachers/manage_students.php");
                    exit;
                case 'admin':
                    header("Location: ../admin/manage_users.php");
                    exit;
                default:
                    logout(); // If role is invalid, log out
            }
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}

// Include header and footer only for the login page
if (!isLoggedIn()) {
    include '../includes/header.php';
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body>

    <header class="header">
        <section class="flex">
            <a href="../home.php" class="logo">Educa.</a>
        </section>
    </header>

    <section class="form-container">
        <form action="" method="post">
            <h3>Login Now</h3>
            <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
            <p>Your Email <span>*</span></p>
            <input type="email" name="email" placeholder="Enter your email" required maxlength="50" class="box">
            <p>Your Password <span>*</span></p>
            <input type="password" name="pass" placeholder="Enter your password" required maxlength="20" class="box">
            <input type="submit" value="Login" name="submit" class="btn">
            <p>Don't have an account? <a href="signup.php">Register</a></p>
        </form>
    </section>

    <?php include '../includes/footer.php'; ?>
    </body>
    </html>
    <?php
}
?>
