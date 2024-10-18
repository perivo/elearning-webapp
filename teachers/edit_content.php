<?php  
if (!session_id()) {
    session_start(); // Start session if not already started
}

include '../includes/config.php'; // Database connection
include_once '../includes/functions.php'; // Include functions once

// Check if the user is logged in
if (!isLoggedIn() || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit;
}

// Check if content ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_portal.php"); // Redirect if no content ID
    exit;
}

$content_id = intval($_GET['id']);

// Fetch existing content details
$content_query = "SELECT * FROM course_content WHERE id = ?";
$stmt = mysqli_prepare($conn, $content_query);
mysqli_stmt_bind_param($stmt, 'i', $content_id);
mysqli_stmt_execute($stmt);
$content_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($content_result) === 0) {
    header("Location: manage_portal.php"); // Redirect if content not found
    exit;
}

$content = mysqli_fetch_assoc($content_result);
mysqli_stmt_close($stmt);

// Handle content update
if (isset($_POST['update_content'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $course_id = intval($_POST['course_id']);
    
    // Handle file uploads
    $file_path = $content['file_path']; // Keep old file path unless new file is uploaded
    $thumbnail_path = $content['thumbnail']; // Keep old thumbnail path unless new thumbnail is uploaded

    if (!empty($_FILES['content_file']['name'])) {
        // New content file uploaded
        $file_name = basename($_FILES['content_file']['name']);
        $file_tmp = $_FILES['content_file']['tmp_name'];
        $file_path = "../uploads/" . uniqid() . "-" . $file_name; // Generate new unique file name
        move_uploaded_file($file_tmp, $file_path);
    }

    if (!empty($_FILES['thumbnail']['name'])) {
        // New thumbnail uploaded
        $thumbnail_name = basename($_FILES['thumbnail']['name']);
        $thumbnail_tmp = $_FILES['thumbnail']['tmp_name'];
        $thumbnail_path = "../uploads/thumbnails/" . uniqid() . "-" . $thumbnail_name; // Generate new unique thumbnail name
        move_uploaded_file($thumbnail_tmp, $thumbnail_path);
    }

    // Update query
    $update_query = "UPDATE course_content SET title = ?, description = ?, file_path = ?, thumbnail = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, 'ssssi', $title, $description, $file_path, $thumbnail_path, $content_id);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Content updated successfully!";
    } else {
        $error = "Error updating content: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Fetch all courses for the dropdown
$courses_query = "SELECT * FROM courses WHERE teacher_id = ?";
$courses_stmt = mysqli_prepare($conn, $courses_query);
mysqli_stmt_bind_param($courses_stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($courses_stmt);
$courses_result = mysqli_stmt_get_result($courses_stmt);
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);
mysqli_stmt_close($courses_stmt);

// Include header and sidebar
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<section class="container">

    <!-- Display Success/Error Messages -->
    <?php if (isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <h2>Edit Course Content</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($content['course_id']); ?>">

        <p>Content Title <span>*</span></p>
        <input type="text" name="title" value="<?php echo htmlspecialchars($content['title']); ?>" required class="box">

        <p>Content Description <span>*</span></p>
        <textarea name="description" required class="box"><?php echo htmlspecialchars($content['description']); ?></textarea>

        <p>Upload New Content File (Leave blank to keep current file)</p>
        <input type="file" name="content_file" accept=".pdf, .doc, .docx, .ppt, .pptx, .mp4" class="box">

        <p>Upload New Thumbnail (Leave blank to keep current thumbnail)</p>
        <input type="file" name="thumbnail" accept="image/*" class="box">

        <input type="submit" value="Update Content" name="update_content" class="btn">
    </form>

    <h3>Current Content File</h3>
    <p><?php echo htmlspecialchars($content['file_path']); ?></p>

    <h3>Current Thumbnail</h3>
    <img src="<?php echo "../uploads/thumbnails/" . htmlspecialchars($content['thumbnail']); ?>" alt="Thumbnail" style="width: 100px; height: auto;">

</section>

<?php include '../includes/footer.php'; ?>
