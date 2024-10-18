<?php
session_start();
include '../includes/config.php'; // Database connection
include_once '../includes/functions.php'; // Include functions once

// Check if the user is logged in and has the role of Teacher
if (!isLoggedIn() || $_SESSION['role'] !== 'Teacher') {
    logout(); // Redirect to login if not authenticated
}

// Get student_id from the URL, ensuring it's a valid integer
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

// Fetch assignments for the selected student
$query = "
    SELECT assignments.id, assignments.title, assignments.status, submissions.grade 
    FROM assignments 
    LEFT JOIN submissions ON assignments.id = submissions.assignment_id AND submissions.student_id = '$student_id' 
    WHERE assignments.course_id IN (SELECT course_id FROM students WHERE id = '$student_id')
";
$result = mysqli_query($conn, $query);

// Check for errors in the query
if (!$result) {
    $error = "Error fetching assignments: " . mysqli_error($conn);
}

$assignments = mysqli_fetch_all($result, MYSQLI_ASSOC);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<section class="container">
    <h2>Assignments for Student ID: <?php echo htmlspecialchars($student_id); ?></h2>
    
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    
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
</section>

<?php include '../includes/footer.php'; ?>
