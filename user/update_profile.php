<?php 
$title = "Update Profile"; // Set the title for the page
include '../includes/header.php'; 
include '../includes/sidebar.php'; 
?>

<div class="update-profile">
    <h1 class="heading">Update Your Profile</h1>
    <form action="update_profile_process.php" method="POST">
        <input type="text" name="name" required placeholder="Your Name" value="Shaikh Anas">
        <input type="email" name="email" required placeholder="Your Email" value="example@mail.com">
        <input type="password" name="password" required placeholder="New Password">
        <button type="submit" class="btn">Update</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
