<?php include 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educa - <?php echo htmlspecialchars($page_title); ?></title>

    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

    <!-- Custom CSS File Link -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container"> <!-- Added container to wrap the content -->
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
                <?php $user = getUserInfo(); ?>
                <img src="../uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" class="image" alt="User Profile Picture">
                <h3 class="name"><?php echo htmlspecialchars($user['name']); ?></h3>
                <p class="role"><?php echo htmlspecialchars($user['role']); ?></p>
                <?php if ($user['role'] !== 'Visitor'): ?>
                    <a href="user/profile.php" class="btn">View Profile</a>
                    <div class="flex-btn">
                        <a href="../elearning/auth/logout.php" class="option-btn">Logout</a>
                    </div>
                <?php else: ?>
                    <div class="flex-btn">
                        <a href="../elearning/auth/login.php" class="option-btn">Login</a>
                        <a href="../elearning/auth/signup.php" class="option-btn">Register</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </header>