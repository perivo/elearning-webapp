<?php
session_start();
include '../includes/config.php'; // Database connection

// Function to check if the user is logged in
function isLoggedIn() {
    return isset($_SESSION['email']);
}

// Function to check if the user has a specific role
function hasRole($role) {
    return isset($_SESSION['role']) && strtolower($_SESSION['role']) === strtolower($role);
}

// Redirect users if already logged in
if (isLoggedIn()) {
    switch (strtolower($_SESSION['role'])) { // Use strtolower for case-insensitive comparison
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
            session_destroy();
            header("Location: ../auth/login.php");
            exit;
    }
}

// Redirect to login if not authenticated
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Fetch user data from session
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['role'];
$email = $_SESSION['email'];
$profile_pic = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'default-user.jpg';

// Redirect based on user role
switch (strtolower($user_role)) { // Use strtolower for case-insensitive comparison
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
        header("Location: login.php");
        exit;
}

include '../includes/header.php'; // Include header
include '../includes/sidebar.php'; // Include sidebar
?>

<div class="dashboard">
    <h1 class="heading">Your Dashboard</h1>
    <div class="user-info">
        <img src="../images/<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-pic">
        <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>
        <p>Role: <?php echo htmlspecialchars($user_role); ?></p>
    </div>
    
    <div class="courses">
        <h2>Enrolled Courses</h2>
        <ul>
            <li><a href="#">Course 1</a> - Progress: 80%</li>
            <li><a href="#">Course 2</a> - Progress: 50%</li>
            <li><a href="#">Course 3</a> - Progress: 30%</li>
        </ul>
    </div>
</div>

<?php
include '../includes/footer.php'; // Include footer
?>
