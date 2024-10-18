<?php
if (!isset($_SESSION)) {
    session_start();
}

// Include the configuration or any necessary files here
include '../includes/config.php'; 
include '../includes/functions.php'; // Assuming necessary user-related functions are here

// Retrieve user data if logged in
$user = isset($_SESSION['user_id']) ? getUserById($_SESSION['user_id'], $conn) : ['name' => 'Guest', 'role' => 'Visitor', 'profile_pic' => 'default-user.jpg'];

function getUserById($userId, $conn) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educa - <?= htmlspecialchars($page_title) ?></title>

    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

    <!-- Custom CSS File Link -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <header class="header">
        <section class="flex">
            <a href="home.php" class="logo">Educa.</a>

            <form action="search.php" method="post" class="search-form">
                <input type="text" name="search_box" required placeholder="Search courses..." maxlength="100">
                <button type="submit" class="fas fa-search"></button>
            </form>

            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <div id="search-btn" class="fas fa-search"></div>
                <div id="user-btn" class="fas fa-user"></div>
                <div id="toggle-btn" class="fas fa-sun"></div>
            </div>

            <div class="profile">
                <img src="../uploads/<?= htmlspecialchars($user['profile_pic']); ?>" class="image" alt="User Profile Picture">
                <h3 class="name"><?= htmlspecialchars($user['name']); ?></h3>
                <p class="role"><?= htmlspecialchars($user['role']); ?></p>
                <?php if ($user['role'] !== 'Visitor'): ?>
                    <a href="user/profile.php" class="btn">View Profile</a>
                    <div class="flex-btn">
                        <a href="../auth/logout.php" class="option-btn">Logout</a>
                    </div>
                <?php else: ?>
                    <div class="flex-btn">
                        <a href="../auth/login.php" class="option-btn">Login</a>
                        <a href="../auth/signup.php" class="option-btn">Register</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </header>
