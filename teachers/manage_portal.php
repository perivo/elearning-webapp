<?php
session_start();
include '../includes/config.php'; // Database connection
include_once '../includes/functions.php'; // Include functions once

// Check if the user is logged in and has the correct role
if (!isLoggedIn() || !in_array($_SESSION['role'], ['Teacher', 'Admin'])) {
    logout(); // Redirect to login if not authenticated
}

// Variables for success/error messages
$success = $error = '';

// Handle student deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM users WHERE id = '$delete_id' AND role = 'Student'";
    
    if (mysqli_query($conn, $delete_query)) {
        $success = "Student successfully deleted!";
        header("Location: manage_portal.php"); // Redirect to avoid resubmission
        exit();
    } else {
        $error = "Error deleting student: " . mysqli_error($conn);
    }
}

// Handle file upload (for teachers)
if (isset($_POST['submit_content'])) {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $content_title = mysqli_real_escape_string($conn, $_POST['title']);
    $content_file = $_FILES['content_file']['name'];
    $content_tmp = $_FILES['content_file']['tmp_name'];
    $upload_dir = "../uploads/";

    if ($_FILES['content_file']['error'] === UPLOAD_ERR_OK) {
        if (move_uploaded_file($content_tmp, $upload_dir . $content_file)) {
            $query = "INSERT INTO course_content (course_id, title, file_path) VALUES ('$course_id', '$content_title', '$content_file')";
            if (mysqli_query($conn, $query)) {
                $success = "Content uploaded successfully!";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        } else {
            $error = "Failed to move uploaded file.";
        }
    } else {
        $error = "File upload error: " . $_FILES['content_file']['error'];
    }
}

// Fetch students for admin
$students = [];
if ($_SESSION['role'] === 'Admin') {
    $query = "SELECT * FROM users WHERE role = 'Student'";
    $result = mysqli_query($conn, $query);
    $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Fetch courses taught by the teacher
$courses = [];
if ($_SESSION['role'] === 'Teacher') {
    $teacher_id = $_SESSION['user_id'];
    $query = "SELECT * FROM courses WHERE teacher_id = '$teacher_id'";
    $result = mysqli_query($conn, $query);
    $courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Fetch assignments for a student (if student_id is provided)
$assignments = [];
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
if ($student_id && $_SESSION['role'] === 'Teacher') {
    $query = "
        SELECT assignments.id, assignments.title, assignments.status, submissions.grade 
        FROM assignments 
        LEFT JOIN submissions ON assignments.id = submissions.assignment_id AND submissions.student_id = '$student_id'
        WHERE assignments.course_id IN (SELECT course_id FROM students WHERE id = '$student_id')
    ";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        $error = "Error fetching assignments: " . mysqli_error($conn);
    }
    $assignments = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Include header and sidebar
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<section class="container">

    <!-- Display Success/Error Messages -->
    <?php if (isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <!-- Admin Section: Manage Students -->
    <?php if ($_SESSION['role'] === 'Admin'): ?>
    <h2>Manage Students</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Profile Picture</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['id']); ?></td>
                <td><?php echo htmlspecialchars($student['name']); ?></td>
                <td><?php echo htmlspecialchars($student['email']); ?></td>
                <td>
                    <img src="../uploads/<?php echo htmlspecialchars($student['profile_pic']); ?>" alt="Profile" style="width:50px;height:50px;">
                </td>
                <td>
                    <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn">Edit</a>
                    <a href="?delete_id=<?php echo $student['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Teacher Section: Upload Course Content -->
    <?php if ($_SESSION['role'] === 'Teacher'): ?>
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

        <p>Upload Content File <span>*</span></p>
        <input type="file" name="content_file" accept=".pdf, .doc, .docx, .ppt, .pptx, .mp4" required class="box">

        <input type="submit" value="Upload Content" name="submit_content" class="btn">
    </form>

    <!-- View Assignments for a Student -->
    <?php if ($student_id): ?>
    <h2>Assignments for Student ID: <?php echo htmlspecialchars($student_id); ?></h2>
    <table>
        <thead>
            <tr>
                <th>Assignment Title</th>
                <th>Status</th>
                <th>Grade</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($assignments)): ?>
                <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['status']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['grade'] !== null ? $assignment['grade'] : 'Not graded'); ?></td>
                        <td>
                            <a href="grade_assignment.php?assignment_id=<?php echo htmlspecialchars($assignment['id']); ?>&student_id=<?php echo htmlspecialchars($student_id); ?>" class="btn">Grade</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No assignments found for this student.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>
