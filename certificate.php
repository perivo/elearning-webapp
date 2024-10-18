<?php 
session_start();
include 'includes/config.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Get user ID and role from session
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : intval($_SESSION['user_id']);
$user_role = $_SESSION['role']; // Assuming user role is stored in session

// Check if a course ID is provided and sanitize it
if (!isset($_GET['course_id'])) {
    echo "No course ID provided.";
    exit;
}

$course_id = intval($_GET['course_id']);

// Fetch certificate details safely
$query = "
    SELECT users.name AS user_name, courses.title AS course_title, certificates.issue_date
    FROM certificates
    INNER JOIN users ON certificates.user_id = users.id
    INNER JOIN courses ON certificates.course_id = courses.id
    WHERE certificates.user_id = ? AND certificates.course_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $certificate = $result->fetch_assoc();
} else {
    echo "Certificate not found or you are not authorized to view it.";
    exit;
}

// Check if the user requested to download the certificate
if (isset($_GET['download']) && $_GET['download'] === 'true') {
    // Prepare certificate for PDF output
    header("Content-Type: application/pdf"); // Setting content type for PDF
    require('fpdf/fpdf.php'); // Ensure FPDF library is installed

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->Cell(0, 10, 'Certificate of Completion', 0, 1, 'C');

    // Add user name and course title
    $pdf->SetFont('Arial', 'I', 16);
    $pdf->Ln(20);
    $pdf->Cell(0, 10, "This is to certify that", 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 10, $certificate['user_name'], 0, 1, 'C');
    $pdf->SetFont('Arial', 'I', 16);
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "has successfully completed", 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 10, $certificate['course_title'], 0, 1, 'C');
    $pdf->Ln(20);

    // Issue date
    $pdf->SetFont('Arial', 'I', 16);
    $pdf->Cell(0, 10, "Issued on: " . date('F j, Y', strtotime($certificate['issue_date'])), 0, 1, 'C');

    // Output PDF to browser
    $pdf->Output('D', 'certificate_' . $user_id . '_' . $course_id . '.pdf'); // Download the PDF
    exit; // Stop script execution after downloading
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
<section class="certificate-section">
    <div class="certificate-container">
        <h1 class="certificate-title">Certificate of Completion</h1>
        <div class="certificate-frame">
            <p class="certificate-text">This is to certify that</p>
            <h2 class="certificate-name"><?php echo htmlspecialchars($certificate['user_name']); ?></h2>
            <p class="certificate-text">has successfully completed the course</p>
            <h2 class="certificate-course"><?php echo htmlspecialchars($certificate['course_title']); ?></h2>
            <p class="certificate-text">on</p>
            <h2 class="certificate-date"><?php echo date('F j, Y', strtotime($certificate['issue_date'])); ?></h2>
        </div>

        <?php if ($user_role === 'student' || $user_role === 'teacher' || $user_role === 'admin'): ?>
            <div class="download-btn-container">
                <a href="?course_id=<?php echo $course_id; ?>&user_id=<?php echo $user_id; ?>&download=true" class="download-btn">Download Certificate</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Custom CSS for certificate -->
<style>
    .certificate-section {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        text-align: center;
    }

    .certificate-container {
        width: 600px;
        padding: 30px;
        border: 2px solid #000;
        background-color: #f7f7f7;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .certificate-frame {
        border: 5px solid #000;
        padding: 20px;
    }

    .certificate-title {
        font-size: 2rem;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 20px;
    }

    .certificate-name, .certificate-course, .certificate-date {
        font-size: 1.5rem;
        margin: 10px 0;
        font-weight: bold;
    }

    .certificate-text {
        font-size: 1.2rem;
    }

    .download-btn-container {
        margin-top: 20px;
    }

    .download-btn {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-size: 1.1rem;
    }

    .download-btn:hover {
        background-color: #0056b3;
    }
</style>

</body>
</html>
