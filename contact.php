<?php 
include "templates/header.php";
include "templates/sidebar.php";
?>

<!-- Contact Page Content -->
<section class="contact">
   <h1 class="heading">Contact Us</h1>
   <div class="row">

      <div class="image">
         <img src="images/contact-img.svg" alt="Contact Us">
      </div>

      <form action="contact_process.php" method="POST">
         <h3>Send us a message</h3>
         <input type="text" name="name" placeholder="Your Name" required maxlength="100" class="box">
         <input type="email" name="email" placeholder="Your Email" required maxlength="100" class="box">
         <input type="text" name="subject" placeholder="Subject" required maxlength="100" class="box">
         <textarea name="message" class="box" placeholder="Your Message" required maxlength="1000"></textarea>
         <input type="submit" value="Send Message" class="inline-btn">
      </form>

   </div>
</section>

<?php include 'templates/footer.php'; ?>
