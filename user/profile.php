<?php
session_start();
include '../includes/config.php'; // Ensure this file exists and contains the connection setup.

// Check if user is logged in
$is_logged_in = isset($_SESSION['email']); // Define the variable to track if the user is logged in

if (!$is_logged_in) {
    header("Location: ../elearning/auth/login.php");
    exit; // Redirect to login if not logged in
}

// Retrieve user data
$email = $_SESSION['email'];
$query = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Handle form submission for profile update
if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $profile_pic = $_FILES['profile']['name'];
    $profile_tmp = $_FILES['profile']['tmp_name'];

    // Prepare the update query
    $update_query = "UPDATE users SET name='$name', email='$new_email'";

    if (!empty($profile_pic)) {
        // Move uploaded file if a new profile picture is provided
        move_uploaded_file($profile_tmp, "../uploads/$profile_pic");
        $update_query .= ", profile_pic='$profile_pic'";
    }

    $update_query .= " WHERE email='$email'";

    if (mysqli_query($conn, $update_query)) {
        // Update session variables
        $_SESSION['user_name'] = $name;
        $_SESSION['email'] = $new_email; // Update email in session
        if (!empty($profile_pic)) {
            $_SESSION['profile_pic'] = $profile_pic; // Update profile picture in session
        }
        $success_msg = "Profile updated successfully!";
    } else {
        $error_msg = "Error updating profile: " . mysqli_error($conn);
    }
}

// Handle password update
if (isset($_POST['change_password'])) {
    $old_password = mysqli_real_escape_string($conn, $_POST['old_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Fetch the user's current password
    $query = "SELECT password FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user_data = mysqli_fetch_assoc($result);

    // Verify the old password
    if (password_verify($old_password, $user_data['password'])) {
        // Check if new password and confirm password match
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $password_update_query = "UPDATE users SET password='$hashed_password' WHERE email='$email'";

            if (mysqli_query($conn, $password_update_query)) {
                $success_msg = "Password changed successfully!";
            } else {
                $error_msg = "Error changing password: " . mysqli_error($conn);
            }
        } else {
            $error_msg = "New password and confirm password do not match!";
        }
    } else {
        $error_msg = "Old password is incorrect!";
    }
}

// Check if user data was successfully retrieved
if (!$user) {
    $error_msg = "User not found!";
    // You might want to handle this further, e.g., redirect to an error page or logout the user.
}
?>

<?php
include '../includes/header.php';
include '../includes/sidebar.php';
?>

    <div class="profile-container">
        <h1 class="heading">Update Profile</h1>
        <?php if (isset($success_msg)) { echo "<p style='color:green;'>$success_msg</p>"; } ?>
        <?php if (isset($error_msg)) { echo "<p style='color:red;'>$error_msg</p>"; } ?>
        <form action="" method="post" enctype="multipart/form-data">
            <p>Your Name <span>*</span></p>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required maxlength="50" class="box">
            <p>Your Email <span>*</span></p>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required maxlength="50" class="box">
            <p>Select New Profile Picture <span>(optional)</span></p>
            <input type="file" name="profile" accept="image/*" class="box">
            <input type="submit" value="Update Profile" name="update" class="btn">
        </form>

        <h2 class="heading">Change Password</h2>
        <form action="" method="post">
            <p>Old Password <span>*</span></p>
            <input type="password" name="old_password" required maxlength="50" class="box">
            <p>New Password <span>*</span></p>
            <input type="password" name="new_password" required maxlength="50" class="box">
            <p>Confirm New Password <span>*</span></p>
            <input type="password" name="confirm_password" required maxlength="50" class="box">
            <input type="submit" value="Change Password" name="change_password" class="btn">
        </form>
    </div>

    <?php
include '../includes/footer.php';

?>