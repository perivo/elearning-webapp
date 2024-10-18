<?php 
session_start();
include '../includes/config.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch all courses with teachers and content
$query = "
    SELECT courses.id AS course_id, courses.title AS course_title, courses.description AS course_description,
           users.name AS teacher_name, course_content.id AS content_id, course_content.title AS content_title, 
           course_content.file_path AS content_file, course_content.description AS content_description
    FROM courses
    JOIN users ON courses.teacher_id = users.id
    LEFT JOIN course_content ON course_content.course_id = courses.id
";
$result = mysqli_query($conn, $query);
$courses = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle enrollment
if (isset($_POST['enroll'])) {
    $course_id = intval($_POST['course_id']);
    $user_id = $_SESSION['user_id'];

    // Check if already enrolled
    $enrollment_check_query = "SELECT * FROM enrollments WHERE user_id = '$user_id' AND course_id = '$course_id'";
    $enrollment_check_result = mysqli_query($conn, $enrollment_check_query);

    if (mysqli_num_rows($enrollment_check_result) == 0) {
        $enroll_query = "INSERT INTO enrollments (user_id, course_id) VALUES ('$user_id', '$course_id')";
        if (mysqli_query($conn, $enroll_query)) {
            $success = "Successfully enrolled in the course!";
        } else {
            $error = "Error enrolling in course: " . mysqli_error($conn);
        }
    } else {
        $error = "You are already enrolled in this course!";
    }
}

// Initialize $is_enrolled for each course
$enrolled_courses = [];
$user_id = $_SESSION['user_id'];
$enrollment_query = "SELECT course_id FROM enrollments WHERE user_id = '$user_id'";
$enrollment_result = mysqli_query($conn, $enrollment_query);
while ($row = mysqli_fetch_assoc($enrollment_result)) {
    $enrolled_courses[] = $row['course_id'];
}

include '../includes/header.php'; // Make sure Bootstrap CSS is included here
include '../includes/sidebar.php'; 
?>

<section class="container mt-4">
    <h2 class="text-center">Available Courses</h2>
    
    <?php if (isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
    <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

    <div class="row">
        <?php 
        foreach ($courses as $course): 
            $is_enrolled = in_array($course['course_id'], $enrolled_courses);
        ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($course['course_title']); ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">Teacher: <?php echo htmlspecialchars($course['teacher_name']); ?></h6>
                    <p class="card-text"><?php echo htmlspecialchars($course['course_description']); ?></p>

                    <!-- Enroll button -->
                    <?php if (!$is_enrolled): ?>
                    <form action="" method="post">
                        <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                        <button type="submit" name="enroll" class="btn btn-primary">Enroll in Course</button>
                    </form>
                    <?php else: ?>
                    <p class="text-success">You are enrolled in this course.</p>
                    <?php endif; ?>

                    <!-- Content Section -->
                    <h6 class="mt-3">Course Content</h6>
                    <?php if (!empty($course['content_title'])): ?>
                        <h6><?php echo htmlspecialchars($course['content_title']); ?></h6>
                        <p><?php echo htmlspecialchars($course['content_description']); ?></p>

                        <!-- Check if enrolled and display thumbnail -->
                        <?php if ($is_enrolled): ?>
                        <div class="mb-3">
                            <img src="../uploads/<?php echo htmlspecialchars($course['content_file']); ?>" 
                                 class="img-thumbnail" 
                                 data-toggle="modal" 
                                 data-target="#videoModal<?php echo $course['content_id']; ?>" 
                                 alt="<?php echo htmlspecialchars($course['content_title']); ?>"
                                 style="cursor: pointer; width: 100%; height: auto;">
                        </div>
                        <?php else: ?>
                        <p class="text-danger">You must be enrolled to view this content.</p>
                        <?php endif; ?>
                    <?php else: ?>
                    <p>No content available for this course.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Modal for video -->
        <?php if ($is_enrolled): ?>
        <div class="modal fade" id="videoModal<?php echo $course['content_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="videoModalLabel"><?php echo htmlspecialchars($course['content_title']); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <video width="100%" controls>
                            <source src="../uploads/<?php echo htmlspecialchars($course['content_file']); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<!-- Make sure to include Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
