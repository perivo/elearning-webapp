<?php
session_start();
include '../includes/config.php'; // Database connection

$error = []; // Array to hold error messages

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['pass']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['c_pass']);
    $profile = $_FILES['profile']['name'];
    $profile_tmp = $_FILES['profile']['tmp_name'];
    $role = $_POST['role']; // Capture selected role

    // Validate password
    if ($password !== $confirm_password) {
        $error[] = "Passwords do not match!";
    }

    // Check if email already exists
    $check_email_query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email_query);

    if (mysqli_num_rows($result) > 0) {
        $error[] = "This email is already registered!";
    }

    // If no errors, proceed to registration
    if (empty($error)) {
        // Hash password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Move uploaded file
        move_uploaded_file($profile_tmp, "../uploads/$profile");

        // Insert user data into the database
        $query = "INSERT INTO users (name, email, password, profile_pic, role) VALUES ('$name', '$email', '$hashed_password', '$profile', '$role')";

        if (mysqli_query($conn, $query)) {
            $_SESSION['email'] = $email;
            $_SESSION['user_name'] = $name;
            $_SESSION['role'] = $role; // Store the selected role in session
            header("Location: ../auth/redirect.php");
            exit;
        } else {
            $error[] = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <section class="form-container">
        <form action="" method="post" enctype="multipart/form-data">
            <h3>Register Now</h3>
            <?php if (!empty($error)) { foreach ($error as $err) { echo "<p style='color:red;'>$err</p>"; } } ?>
            <p>Your Name <span>*</span></p>
            <input type="text" name="name" placeholder="Enter your name" required class="box">
            <p>Your Email <span>*</span></p>
            <input type="email" name="email" placeholder="Enter your email" required class="box">
            <p>Your Password <span>*</span></p>
            <input type="password" name="pass" placeholder="Enter your password" required class="box">
            <p>Confirm Password <span>*</span></p>
            <input type="password" name="c_pass" placeholder="Confirm your password" required class="box">
            <p>Select Profile Picture <span>*</span></p>
            <input type="file" name="profile" accept="image/*" required class="box">
            
            <p>Select Your Role <span>*</span></p>
            <select name="role" required class="box">
                <option value="Student">Student</option>
                <option value="Teacher">Teacher</option>
                <option value="Admin">Admin</option>
            </select>
            
            <input type="submit" value="Register" name="submit" class="btn">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </form>
    </section>

<?php
include '../includes/footer.php';
?>
