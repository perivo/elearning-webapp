<?php 
session_start();
include "templates/header.php";
include "templates/sidebar.php";
include 'includes/config.php'; // Database connection

// Fetching total counts of students, teachers, courses, and videos
$query_counts = "
    SELECT 
        (SELECT COUNT(*) FROM users WHERE role = 'student') AS total_students,
        (SELECT COUNT(*) FROM users WHERE role = 'teacher') AS total_teachers,
        (SELECT COUNT(*) FROM courses) AS total_courses,
        (SELECT COUNT(*) FROM course_content) AS total_videos
";
$result_counts = mysqli_query($conn, $query_counts);
$counts = mysqli_fetch_assoc($result_counts);

// Fetching course details with teacher info and video count
$query_courses = "
    SELECT courses.id AS course_id, courses.title AS course_title, courses.description AS course_description,
           users.name AS teacher_name, users.profile_pic AS teacher_pic, courses.created_at,
           IFNULL(course_content_count.total_videos, 0) AS video_count,
           courses.thumbnail AS course_thumbnail
    FROM courses
    JOIN users ON courses.teacher_id = users.id
    LEFT JOIN (
        SELECT course_id, COUNT(*) AS total_videos
        FROM course_content
        GROUP BY course_id
    ) AS course_content_count ON courses.id = course_content_count.course_id
";
$result_courses = mysqli_query($conn, $query_courses);
$courses = mysqli_fetch_all($result_courses, MYSQLI_ASSOC);
?>

<!-- Start of Main Content -->
<section class="home-grid">
   <h1 class="heading">Quick Actions</h1>

   <div class="box-container">
      <div class="box">
         <h3 class="title">Total Overview</h3>
         <p class="overview">Total Students: <span><?php echo $counts['total_students']; ?></span></p>
         <p class="overview">Total Teachers: <span><?php echo $counts['total_teachers']; ?></span></p>
         <p class="overview">Total Courses: <span><?php echo $counts['total_courses']; ?></span></p>
         <p class="overview">Total Videos: <span><?php echo $counts['total_videos']; ?></span></p>
      </div>

      <div class="box">
         <h3 class="title">Quick Actions</h3>
         <a href="register.php" class="inline-btn">Register Student</a>
         <a href="register_teacher.php" class="inline-btn">Register Teacher</a>
         <a href="add_course.php" class="inline-btn">Add New Course</a>
         <a href="manage_courses.php" class="inline-btn">Manage Courses</a>
      </div>

      
   </div>
</section>

<section class="courses">
   <h1 class="heading">Available Courses</h1>

   <div class="box-container">
      <?php foreach ($courses as $course): ?>
      <div class="box">
         <div class="tutor">
            <img src="../uploads/<?php echo htmlspecialchars($course['teacher_pic']); ?>" alt="<?php echo htmlspecialchars($course['teacher_name']); ?>">
            <div class="info">
               <h3><?php echo htmlspecialchars($course['teacher_name']); ?></h3>
               <span><?php echo date('d-m-Y', strtotime($course['created_at'])); ?></span>
            </div>
         </div>
         <div class="thumb">
            <img src="../uploads/<?php echo htmlspecialchars($course['course_thumbnail']); ?>" alt="Course Thumbnail">
            <span><?php echo htmlspecialchars($course['video_count']); ?> Videos</span>
         </div>
         <h3 class="title"><?php echo htmlspecialchars($course['course_title']); ?></h3>
         <a href="playlist.php?id=<?php echo $course['course_id']; ?>" class="inline-btn">View Playlist</a>
      </div>
      <?php endforeach; ?>
   </div>

   <div class="more-btn">
      <a href="courses/all_courses.php" class="inline-option-btn">View All Courses</a>
   </div>
</section>

<?php 
include "templates/footer.php";
?>
