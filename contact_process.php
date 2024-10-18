<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include configuration for database connection or email settings
    include 'includes/config.php'; 

    // Collect and sanitize form input
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));

    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required.'
        ]);
        exit();
    }

    // Option 1: Sending the data as an email
    $to = "ivopereiraix3@gmail.com"; // Replace with admin email address
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $email_message = "Name: $name\n";
    $email_message .= "Email: $email\n";
    $email_message .= "Subject: $subject\n";
    $email_message .= "Message:\n$message\n";

    // Send email
    if (mail($to, $subject, $email_message, $headers)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Thank you for contacting us. We will get back to you shortly.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'There was an error sending the message. Please try again later.'
        ]);
    }

    // Option 2: Storing data in the database (optional)
    /*
    $query = "INSERT INTO contact_messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    if (mysqli_query($conn, $query)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Thank you for contacting us. We will get back to you shortly.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'There was an error saving the message. Please try again later.'
        ]);
    }
    */

    // Close the database connection
    mysqli_close($conn);
} else {
    // If the request method isn't POST, redirect to the contact page
    header("Location: contact.php");
    exit();
}
?>
