<?php
require('fpdf/fpdf.php'); // Include FPDF library
include('../includes/config.php'); // Adjust the path to your database connection file

if (isset($_GET['cert_id'])) {
    $cert_id = mysqli_real_escape_string($conn, $_GET['cert_id']);

    // Fetch certificate information
    $query = "
        SELECT certificates.certificate_number, users.name AS student_name, courses.title AS course_title, certificates.issued_at
        FROM certificates
        INNER JOIN users ON certificates.user_id = users.id
        INNER JOIN courses ON certificates.course_id = courses.id
        WHERE certificates.certificate_number = '$cert_id'
    ";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $certificate = mysqli_fetch_assoc($result);

        // Create PDF using FPDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        // Add certificate content to the PDF
        $pdf->Cell(0, 10, 'Certificate of Completion', 0, 1, 'C');
        $pdf->Ln(20); // Line break

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'This certifies that', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, $certificate['student_name'], 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'has successfully completed the course', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, $certificate['course_title'], 0, 1, 'C');
        $pdf->Ln(20);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Issued on: ' . date('F j, Y', strtotime($certificate['issued_at'])), 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'Certificate Number: ' . $certificate['certificate_number'], 0, 1, 'C');

        // Output the PDF for download
        $pdf->Output('D', 'certificate_' . $certificate['certificate_number'] . '.pdf'); // Force download
    } else {
        echo "Certificate not found.";
    }
} else {
    echo "Invalid certificate ID.";
}
