<?php  
if (!session_id()) {
    session_start(); // Start session if not already started
}

include '../includes/config.php'; // Database connection
include_once '../includes/functions.php'; // Include functions once

// Check if the user is logged in
if (isLoggedIn()) {
    if (isset($_SESSION['user_id'])) {
        switch (strtolower($_SESSION['role'])) {
            case 'student':
                header("Location: ../courses/all_courses.php");
                exit;
            case 'teacher':
            case 'admin':
                // Allow teacher/admin to stay on this page (manage portal)
                break;
            default:
                session_destroy();
                header("Location: ../auth/login.php");
                exit;
        }
    } else {
        logout(); // Log the user out if user_id is not set
    }
} else {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch courses taught by the teacher
$courses = [];
if ($_SESSION['role'] === 'teacher') {
    $teacher_id = $_SESSION['user_id'];
    $query = "SELECT * FROM courses WHERE teacher_id = '$teacher_id'";
    $result = mysqli_query($conn, $query);
    $courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Define the thumbnail directory
$thumbnail_dir = "../uploads/thumbnails/";

// Check if the thumbnail directory exists, if not, create it
if (!is_dir($thumbnail_dir)) {
    mkdir($thumbnail_dir, 0777, true); // Create the directory with permissions
}

// Handle course content upload
if ($_SESSION['role'] === 'teacher' && isset($_POST['submit_content'])) {
    $course_id = intval($_POST['course_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']); // New content description
    $file_name = basename($_FILES['content_file']['name']);
    $file_tmp = $_FILES['content_file']['tmp_name'];
    $file_path = "../uploads/" . uniqid() . "-" . $file_name; // Prevent overwriting

    // Handle thumbnail upload
    $thumbnail_name = basename($_FILES['thumbnail']['name']);
    $thumbnail_tmp = $_FILES['thumbnail']['tmp_name'];
    $thumbnail_path = $thumbnail_dir . uniqid() . "-" . $thumbnail_name; // Prevent overwriting

    // Move files to their respective directories
    if (move_uploaded_file($file_tmp, $file_path) && move_uploaded_file($thumbnail_tmp, $thumbnail_path)) {
        $upload_query = "INSERT INTO course_content (course_id, title, description, file_path, thumbnail) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $upload_query);
        mysqli_stmt_bind_param($stmt, 'issss', $course_id, $title, $description, $file_path, $thumbnail_path);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Content uploaded successfully!";
        } else {
            $error = "Error uploading content: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Failed to upload file or thumbnail!";
    }
}

// Handle course content management (edit, update, delete)
if (isset($_GET['delete_content'])) {
    $content_id = intval($_GET['delete_content']);
    $delete_query = "DELETE FROM course_content WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, 'i', $content_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success = "Content deleted successfully!";
        header("Location: manage_portal.php"); // Avoid form resubmission
        exit();
    } else {
        $error = "Error deleting content: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Fetch content uploaded by the teacher
$course_content = [];
if ($_SESSION['role'] === 'teacher') {
    $teacher_id = $_SESSION['user_id'];
    $content_query = "SELECT * FROM course_content WHERE course_id IN (SELECT id FROM courses WHERE teacher_id = '$teacher_id')";
    $content_result = mysqli_query($conn, $content_query);
    $course_content = mysqli_fetch_all($content_result, MYSQLI_ASSOC);
}

// Fetch student activity (content views) for the teacher's courses
$content_views = [];
if ($_SESSION['role'] === 'teacher') {
    $teacher_id = $_SESSION['user_id'];
    $views_query = "
        SELECT cv.id, u.name AS student_name, cc.title AS content_title, cv.viewed_at 
        FROM content_views cv 
        JOIN course_content cc ON cv.content_id = cc.id 
        JOIN users u ON cv.student_id = u.id 
        WHERE cc.course_id IN (SELECT id FROM courses WHERE teacher_id = '$teacher_id')
        ORDER BY cv.viewed_at DESC";
    $views_result = mysqli_query($conn, $views_query);
    $content_views = mysqli_fetch_all($views_result, MYSQLI_ASSOC);
}

// Include header and sidebar
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<section class="container">

    <!-- Display Success/Error Messages -->
    <?php if (isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <!-- Teacher Section: Upload Course Content -->
    <?php if ($_SESSION['role'] === 'teacher'): ?>
    <h2>Upload Course Content</h2>
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

        <p>Content Description <span>*</span></p>
        <textarea name="description" placeholder="Enter content description" required class="box"></textarea>

        <p>Upload Content File <span>*</span></p>
        <input type="file" name="content_file" accept=".pdf, .doc, .docx, .ppt, .pptx, .mp4" required class="box">

        <p>Upload Thumbnail <span>*</span></p>
        <input type="file" name="thumbnail" accept="image/*" required class="box">

        <input type="submit" value="Upload Content" name="submit_content" class="btn">
    </form>

    <!-- Teacher Section: Manage Course Content -->
    <h2>Manage Uploaded Content</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>File Path</th>
                <th>Thumbnail</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($course_content as $content): ?>
            <tr>
                <td><?php echo htmlspecialchars($content['id']); ?></td>
                <td><?php echo htmlspecialchars($content['title']); ?></td>
                <td><?php echo htmlspecialchars($content['description']); ?></td>
                <td><?php echo htmlspecialchars($content['file_path']); ?></td>
                <td>
                    <img src="../uploads/thumbnails/<?php echo htmlspecialchars($content['thumbnail']); ?>" alt="Thumbnail" style="width: 100px; height: auto;">
                </td>
                <td>
                    <a href="edit_content.php?id=<?php echo $content['id']; ?>" class="btn">Edit</a>
                    <a href="?delete_content=<?php echo $content['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this content?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Teacher Section: View Student Activity -->
    <h2>Student Activity (Content Views)</h2>
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Content Title</th>
                <th>Viewed At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($content_views as $view): ?>
            <tr>
                <td><?php echo htmlspecialchars($view['student_name']); ?></td>
                <td><?php echo htmlspecialchars($view['content_title']); ?></td>
                <td><?php echo htmlspecialchars($view['viewed_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

</section>

<?php include '../includes/footer.php'; ?>
