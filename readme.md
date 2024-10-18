# **Educa: Online Learning Platform**

**Educa** is a fully functional online learning platform where users can enroll in various courses, access playlists, submit assignments, and track their progress. The platform supports user roles like students, teachers, and admins. Teachers can upload course content, manage courses, and interact with students. Students can enroll, track their progress, submit assignments, and receive certificates upon completion. Admins have overall control over platform operations.

---

## **Table of Contents**
1. [Overview](#overview)
2. [Features](#features)
3. [Project Structure](#project-structure)
4. [Database Schema](#database-schema)
5. [Prerequisites](#prerequisites)
6. [Installation Guide](#installation-guide)
7. [Usage](#usage)
8. [Contributing](#contributing)
9. [License](#license)
10. [Author](#author)

---

## **Overview**
Educa is a PHP and MySQL-based web application that allows users to interact with an e-learning system. It provides the ability for teachers to create and manage courses and for students to enroll in courses, view video content, submit assignments, and receive feedback. The system also provides administrators with the ability to oversee users and content.

---

## **Features**

### **General Features:**
- **User Roles:**
  - **Students** can browse and enroll in courses, view course videos, and submit assignments.
  - **Teachers** can create courses, upload video content, and manage assignments.
  - **Admins** can manage users, courses, and platform settings.
- **User Authentication & Authorization:** Secure login and registration for students, teachers, and admins.
- **Responsive Design:** A mobile-friendly layout ensures access across devices.
- **Messaging System:** Students can send feedback and messages to teachers.
  
### **Course Management:**
- **Video Playlist:** Teachers can upload course videos as playlists.
- **Assignments & Submissions:** Students can submit assignments for courses, and teachers can review them.
- **Certificates:** Upon completion of courses, students can receive digital certificates.

### **Student Features:**
- **Enroll in Courses:** View available courses and enroll in the ones they prefer.
- **Progress Tracking:** Students can track their progress in terms of content viewed and assignments submitted.
- **View Certificates:** Downloadable certificates upon course completion.

### **Admin Features:**
- **User Management:** Admins can manage students and teachers, control permissions, and monitor platform activities.
- **Activity Log:** Track admin activities for audit purposes.

---

## **Project Structure**

```
Educa/
├── courses/               # Course-related files
│   ├── all_courses.php     # Page displaying all courses
│   ├── playlist.php        # Playlist page for course content
│
├── includes/               # Configuration files and helper functions
│   ├── config.php          # Database configuration and connection file
│   ├── functions.php       # Helper functions used across the app
│
├── templates/              # Reusable components for HTML
│   ├── header.php          # Header template
│   ├── footer.php          # Footer template
│   ├── sidebar.php         # Sidebar template for navigation
│
├── uploads/                # Directory for uploaded images and course content
│   ├── thumbs/             # Course thumbnail images
│   ├── ...                 # Other user-uploaded images
│
├── css/                    # Stylesheets
│   ├── main.css            # Main stylesheet
│
├── js/                     # JavaScript files for interactive elements
│   ├── main.js             # Main JavaScript functionality
│
├── index.php               # Main landing page of the website
├── login.php               # Login page
├── register.php            # Registration page
├── profile.php             # Profile page for students/teachers
├── dashboard.php           # Dashboard for teachers/admins
├── README.md               # README file with project overview and instructions
```

---

## **Database Schema**

The database is designed to support multiple roles (students, teachers, admins) with relevant permissions. Below is a breakdown of the key tables:

- **Users Table:** Holds information for students, teachers, and admins.
- **Courses Table:** Contains the list of courses with the relevant teacher assigned.
- **Enrollments Table:** Tracks which students have enrolled in which courses.
- **Course Content Table:** Stores information about uploaded video content for each course.
- **Assignments Table:** Handles assignment submissions by students.
- **Certificates Table:** Issues certificates to students upon course completion.

## **Prerequisites**
Before installing and running this project, ensure you have the following:

1. **Web Server:** Apache/Nginx or equivalent (XAMPP, WAMP for local development)
2. **PHP** (version 7.0+)
3. **MySQL** or equivalent (e.g., MariaDB)
4. **Composer** for PHP dependencies (optional, if using libraries like PHPMailer, etc.)

---

## **Installation Guide**

### **Step 1: Clone the Repository**

```bash
git clone https://github.com/your-username/educa.git
cd educa
```

### **Step 2: Set Up Database**
- Import the SQL schema into your MySQL database:
```bash
mysql -u [username] -p [database_name] < educa.sql
```

- Update the **includes/config.php** file with your database connection details:
```php
$host = 'localhost';
$user = 'root';
$password = 'your_password';
$database = 'educa';
$conn = new mysqli($host, $user, $password, $database);
```

### **Step 3: Run the Application**
1. Start your Apache and MySQL services if using XAMPP/WAMP.
2. Open the application in your browser at: `http://localhost/educa`.

### **Step 4: Default Admin User**
You can manually insert an admin user into the database to get started:

```sql
INSERT INTO users (name, email, password, role)
VALUES ('Admin', 'admin@example.com', MD5('admin123'), 'admin');
```

---

## **Usage**

### **Roles:**
1. **Students** can:
   - Browse and enroll in courses.
   - Submit assignments.
   - Track progress and receive certificates.

2. **Teachers** can:
   - Upload course content (videos, materials).
   - Manage enrolled students and assignments.
   - Review assignments and issue grades.

3. **Admins** can:
   - Manage users (students, teachers).
   - Oversee all courses, assignments, and certificates.

---

## **Contributing**

If you wish to contribute to the development of Educa, follow these steps:
1. Fork the repository.
2. Create a new branch.
3. Make your changes and commit them with detailed messages.
4. Submit a pull request to have your changes reviewed.

---

## **License**
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.

---

## **Author**
- **Name:** Ivo Pereira
- **GitHub:** [perivo](https://github.com/perivo)

"# e-learning-webapp" 
