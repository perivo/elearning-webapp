<?php 
include "templates/header.php";
include "templates/sidebar.php";
?>

<!-- About Page Content -->
<section class="about">
   <div class="row">
      <div class="image">
         <img src="images/about-img.svg" alt="About Us">
      </div>
      <div class="content">
         <h3>Why Choose Us?</h3>
         <p>We are committed to providing high-quality education and fostering growth for students of all backgrounds. Our platform offers diverse courses from industry experts to help you achieve your learning goals.</p>
         <p>Our mission is to make learning accessible, engaging, and impactful for all our students. We offer personalized learning paths and a supportive community to help you succeed.</p>
         <a href="courses.php" class="inline-btn">Explore Our Courses</a>
      </div>
   </div>
</section>

<section class="team">
   <h1 class="heading">Our Team</h1>
   <div class="box-container">

      <div class="box">
         <img src="images/pic-1.jpg" alt="">
         <h3>John Doe</h3>
         <p>Co-Founder & CEO</p>
         <div class="social-links">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-linkedin"></i></a>
         </div>
      </div>

      <div class="box">
         <img src="images/pic-2.jpg" alt="">
         <h3>Jane Smith</h3>
         <p>Lead Instructor</p>
         <div class="social-links">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-linkedin"></i></a>
         </div>
      </div>

      <!-- Add more team members here -->

   </div>
</section>

<?php include 'templates/footer.php'; ?>
