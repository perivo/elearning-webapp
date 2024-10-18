<?php
session_start();
include '../includes/config.php'; // Database connection
include_once '../includes/functions.php'; // Include functions once

// Check if the user is logged in and has the role of Teacher
if (!isLoggedIn() || $_SESSION['role'] !== 'Teacher') {
    logout(); // Redirect to login if not authenticated
}

// Handle file upload
if (isset($_POST['submit'])) {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $content_title = mysqli_real_escape_string($conn, $_POST['title']);
    $content_file = $_FILES['content_file']['name'];
    $content_tmp = $_FILES['content_file']['tmp_name'];
    $upload_dir = "../uploads/";

    // Check for file upload errors
    if ($_FILES['content_file']['error'] === UPLOAD_ERR_OK) {
        // Move uploaded file
        if (move_uploaded_file($content_tmp, $upload_dir . $content_file)) {
            // Insert content information into the database
            $query = "INSERT INTO course_content (course_id, title, file_path) VALUES ('$course_id', '$content_title', '$content_file')";
            if (mysqli_query($conn, $query)) {
                $success = "Content uploaded successfully!";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        } else {
            $error = "Failed to move uploaded file. Please try again.";
        }
    } else {
        $error = "File upload error: " . $_FILES['content_file']['error'];
    }
}

// Fetch courses taught by the teacher for selection
$teacher_id = $_SESSION['user_id'];
$query = "SELECT * FROM courses WHERE teacher_id = '$teacher_id'";
$result = mysqli_query($conn, $query);
$courses = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Include header and sidebar
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<section class="container">
    <h2>Upload Course Content</h2>

    <?php if (isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <form action="" method="post" enctype="multipart/form-data">
        <p>Select Course:</p>
        <select name="course_id" required>
            <option value="">-- Select Course --</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?php echo htmlspecialchars($course['id']); ?>"><?php echo htmlspecialchars($course['title']); ?></option>
            <?php endforeach; ?>
        </select>

        <p>Content Title <span>*</span></p>
        <input type="text" name="title" placeholder="Enter content title" required class="box">

        <p>Upload Content File <span>*</span></p>
        <input type="file" name="content_file" accept=".pdf, .doc, .docx, .ppt, .pptx, .mp4" required class="box">

        <input type="submit" value="Upload Content" name="submit" class="btn">
    </form>
</section>

<?php include '../includes/footer.php'; ?>
