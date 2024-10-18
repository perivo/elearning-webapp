<?php
    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

// Include the configuration or any necessary files here
include 'includes/config.php'; // Ensure this file connects to the database

// Retrieve user data if logged in
$user = isset($_SESSION['user_id']) ? getUserById($_SESSION['user_id'], $conn) : null; // Assuming you have a function to fetch user by ID

function getUserById($userId, $conn) {
    $query = "SELECT * FROM users WHERE id = '$userId'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}
?>

<div class="side-bar">
    <div id="close-btn">
        <i class="fas fa-times"></i>
    </div>

    <div class="profile">
        <img src="../uploads/<?php echo isset($user['profile_pic']) ? htmlspecialchars($user['profile_pic']) : 'default-user.jpg'; ?>" class="image" alt="Profile Picture">
        <h3 class="name"><?php echo isset($user['name']) ? htmlspecialchars($user['name']) : 'Guest'; ?></h3>
        <p class="role"><?php echo isset($user['role']) ? htmlspecialchars($user['role']) : 'Guest'; ?></p>
        <a href="user/profile.php" class="btn">View Profile</a>
    </div>

    <nav class="navbar">
        <a href="../elearning/home.php"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="../elearning/about.php"><i class="fas fa-question"></i><span>About</span></a>
        <a href="../elearning/contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>

        <!-- Links specific to the Student role -->
        <?php if (isset($user['role']) && $user['role'] === 'Student'): ?>
            <a href="../elearning/courses/all_courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
            <a href="../elearning/user/dashboard.php"><i class="fas fa-user-graduate"></i><span>My Dashboard</span></a>
        <?php endif; ?>

        <!-- Links specific to the Teacher role -->
        <?php if (isset($user['role']) && $user['role'] === 'Teacher'): ?>
            <a href="../elearning/teachers/manage_students.php"><i class="fas fa-users"></i><span>Manage Students</span></a>
            <a href="../elearning/teachers/view_assignments.php"><i class="fas fa-file-alt"></i><span>View Assignments</span></a>
            <a href="../elearning/teachers/upload_content.php"><i class="fas fa-upload"></i><span>Upload Content</span></a>
        <?php endif; ?>

        <!-- Links specific to the Admin role -->
        <?php if (isset($user['role']) && $user['role'] === 'Admin'): ?>
            <a href="../elearning/admin/manage_users.php"><i class="fas fa-user-cog"></i><span>Manage Users</span></a>
            <a href="../elearning/admin/manage_courses.php"><i class="fas fa-book-open"></i><span>Manage Courses</span></a>
            <a href="../elearning/admin/issue_certificate.php"><i class="fas fa-certificate"></i><span>Issue Certificates</span></a>
        <?php endif; ?>
    </nav>
</div>

<!-- Additional CSS for sidebar styling -->
<style>
    .side-bar {
        /* Add your sidebar styles here */
    }
    /* Example styles */
    .profile {
        text-align: center;
        margin-bottom: 20px;
    }
    .profile .image {
        border-radius: 50%;
        width: 80px; /* Adjust as necessary */
        height: 80px; /* Adjust as necessary */
    }
    .navbar {
        /* Navbar styles */
    }
    .navbar a {
        display: flex;
        align-items: center;
        padding: 10px;
        color: #333; /* Link color */
        text-decoration: none;
    }
    .navbar a:hover {
        background-color: #f0f0f0; /* Hover effect */
    }
</style>