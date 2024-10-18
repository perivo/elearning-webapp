<?php 
session_start();
include 'includes/config.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Get user ID and role from session
$user_id = intval($_SESSION['user_id']);
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

// Prepare certificate display
header("Content-Type: application/pdf"); // Setting content type for PDF

// Create PDF using FPDF
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
?>
