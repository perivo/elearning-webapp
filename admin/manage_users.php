<?php 
session_start();
include '../includes/config.php'; // Database connection

// Check if the user is logged in and has the role of Admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php"); // Redirect to login if not authenticated
    exit;
}

// Handle user creation or updating
if (isset($_POST['submit_user'])) {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null; // Hash the password

    if ($user_id > 0) {
        // Update existing user
        $query = "UPDATE users SET name='$name', email='$email', role='$role'" . ($password ? ", password='$password'" : "") . " WHERE id='$user_id'";
        $action_message = "updated";
    } else {
        // Create new user
        $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
        $action_message = "created";
    }

    if (mysqli_query($conn, $query)) {
        $success = "User successfully $action_message!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM users WHERE id='$delete_id'";
    if (mysqli_query($conn, $delete_query)) {
        $success = "User successfully deleted!";
    } else {
        $error = "Error deleting user: " . mysqli_error($conn);
    }
}
// Handle course creation or updating
if (isset($_POST['submit_course'])) {
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $course_title = mysqli_real_escape_string($conn, $_POST['title']);
    $course_description = mysqli_real_escape_string($conn, $_POST['description']);
    $teacher_id = intval($_POST['teacher_id']); // Get teacher_id from the form

    // Check if the title or description is empty
    if (empty($course_title) || empty($course_description) || $teacher_id <= 0) {
        $error = "Title, description, and teacher selection are required!";
    } else {
        if ($course_id > 0) {
            // Update existing course
            $query = "UPDATE courses SET title='$course_title', description='$course_description', teacher_id='$teacher_id' WHERE id='$course_id'";
            $action_message = "updated";
        } else {
            // Create new course
            $query = "INSERT INTO courses (title, description, teacher_id) VALUES ('$course_title', '$course_description', '$teacher_id')";
            $action_message = "created";
        }

        if (mysqli_query($conn, $query)) {
            $success = "Course successfully $action_message!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}


// Fetch all users for display
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch all courses for display
$query = "SELECT * FROM courses";
$result = mysqli_query($conn, $query);
$courses = mysqli_fetch_all($result, MYSQLI_ASSOC);


// Handle certificate issuance
if (isset($_POST['submit_certificate'])) {
    $student_id = intval($_POST['student_id']);
    $course_id = intval($_POST['course_id']);
    $certificate_code = uniqid('CERT-'); // Generate a unique certificate code

    // Ensure student and course IDs are valid
    if ($student_id > 0 && $course_id > 0) {
        // Check if the student_id exists in the users table
        $check_query = "SELECT id FROM users WHERE id='$student_id'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) == 0) {
            $error = "Student ID does not exist.";
        } else {
            // Check if the course_id exists in the courses table
            $course_check_query = "SELECT id FROM courses WHERE id='$course_id'";
            $course_check_result = mysqli_query($conn, $course_check_query);

            if (mysqli_num_rows($course_check_result) == 0) {
                $error = "Course ID does not exist.";
            } else {
                // Generate a UUID for the certificate number
                $certificate_number = 'CERTNUM-' . uniqid() . '-' . bin2hex(random_bytes(6));

                // Proceed with the insert into the certificates table
                $query = "INSERT INTO certificates (user_id, course_id, certificate_number, certificate_code) VALUES ('$student_id', '$course_id', '$certificate_number', '$certificate_code')";

                // Debugging output
                error_log("Inserting certificate with user_id: $student_id, course_id: $course_id, certificate_number: $certificate_number, certificate_code: $certificate_code");

                if (mysqli_query($conn, $query)) {
                    $success = "Certificate issued successfully! Code: $certificate_code, Number: $certificate_number";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }
        }
    } else {
        $error = "Invalid student or course selection.";
    }
}

// Fetch issued certificates for the user along with the course title
$certificate_query = "
    SELECT certificates.certificate_number, certificates.issued_at, courses.title AS course_title
    FROM certificates
    INNER JOIN courses ON certificates.course_id = courses.id
    WHERE certificates.user_id='$student_id'
";
$certificate_result = mysqli_query($conn, $certificate_query);
$certificates = mysqli_fetch_all($certificate_result, MYSQLI_ASSOC);


// Fetch all students for the dropdown
$query = "SELECT * FROM users WHERE role='student'";
$result = mysqli_query($conn, $query);
$students = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users, Courses & Certificates</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header class="header">
    <section class="flex">
        <a href="../home.php" class="logo">Educa.</a>
        <div class="navbar">
            <a href="../auth/logout.php">Logout</a>
        </div>
    </section>
</header>

<section class="container">
    <h2>Manage Users</h2>

    <?php if (isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <form action="" method="post">
        <input type="hidden" name="user_id" id="user_id" value="">
        <p>Name <span>*</span></p>
        <input type="text" name="name" placeholder="Enter user's name" required class="box">
        
        <p>Email <span>*</span></p>
        <input type="email" name="email" placeholder="Enter user's email" required class="box">

        <p>Password (leave blank if unchanged)</p>
        <input type="password" name="password" placeholder="Enter user's password" class="box">

        <p>Role <span>*</span></p>
        <select name="role" required class="box">
            <option value="student">Student</option>
            <option value="admin">Admin</option>
        </select>

        <input type="submit" value="Save User" name="submit_user" class="btn">
    </form>

    <h3>Existing Users</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td>
                    <a href="#" onclick="editUser(<?php echo $user['id']; ?>, '<?php echo addslashes($user['name']); ?>', '<?php echo addslashes($user['email']); ?>', '<?php echo $user['role']; ?>')">Edit</a>
                    <a href="?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Manage Courses</h2>
<form action="" method="post">
    <input type="hidden" name="course_id" id="course_id" value="">
    
    <p>Course Title <span>*</span></p>
    <input type="text" name="title" placeholder="Enter course title" required class="box">
    
    <p>Course Description <span>*</span></p>
    <textarea name="description" placeholder="Enter course description" required class="box"></textarea>

    <p>Select Teacher <span>*</span></p>
    <select name="teacher_id" required class="box">
        <option value="">Select Teacher</option>
        <?php foreach ($users as $user): ?>
            <?php if ($user['role'] === 'teacher'): ?>
                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>

    <input type="submit" value="Save Course" name="submit_course" class="btn">
</form>


    <h3>Existing Courses</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
            <tr>
                <td><?php echo htmlspecialchars($course['id']); ?></td>
                <td><?php echo htmlspecialchars($course['title']); ?></td>
                <td><?php echo htmlspecialchars($course['description']); ?></td>
                <td>
                    <a href="#" onclick="editCourse(<?php echo $course['id']; ?>, '<?php echo addslashes($course['title']); ?>', '<?php echo addslashes($course['description']); ?>')">Edit</a>
                    <a href="delete_course.php?id=<?php echo $course['id']; ?>" onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Issue Certificate</h2>
    <form action="" method="post">
        <p>Select Student <span>*</span></p>
        <select name="student_id" required class="box">
            <option value="">Select Student</option>
            <?php foreach ($students as $student): ?>
                <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <p>Select Course <span>*</span></p>
        <select name="course_id" required class="box">
            <option value="">Select Course</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Issue Certificate" name="submit_certificate" class="btn">
    </form>
    <h2>Issued Certificates</h2>
<table>
    <thead>
        <tr>
            <th>Certificate Number</th>
            <th>Course Title</th>
            <th>Issued On</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($certificates as $certificate): ?>
    <tr>
        <td><?= $certificate['certificate_number'] ?></td>
        <td><?= $certificate['course_title'] ?></td> <!-- Display course title -->
        <td><?= date('F j, Y', strtotime($certificate['issued_at'])) ?></td> <!-- Display issue date -->
        <td>
    <a href="download_certificate.php?cert_id=<?= $certificate['certificate_number'] ?>">Download</a>
</td>

    </tr>
<?php endforeach; ?>


    </tbody>
</table>

</section>

<script>
function editUser(id, name, email, role) {
    document.getElementById('user_id').value = id;
    document.querySelector('input[name="name"]').value = name;
    document.querySelector('input[name="email"]').value = email;
    document.querySelector('select[name="role"]').value = role;
}

function editCourse(id, title, description) {
    document.getElementById('course_id').value = id;
    document.querySelector('input[name="title"]').value = title;
    document.querySelector('textarea[name="description"]').value = description;
}
</script>

</body>
</html>
