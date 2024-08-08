<?php

include('session.php'); // Ensure this includes session initialization and connection code

// Check if the session is started and the user is logged in
if (!isset($_SESSION['login_user'])) {
    header('Location: https://hgstore.in/login71.php');
    exit();
}
// Include the database connection
include 'connection.php';
$conn = Connect();
$conn->select_db($u_id1);
require('fpdf/fpdf.php');
include 'connection.php';

// Get the booking_id from the URL
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : '';

// Fetch the booking details from the database
$sql = "SELECT * FROM room_booking WHERE booking_id = '$booking_id'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    $customer_name = $booking['guest_name'];
    $room_number = $booking['room_number'];
    $check_in_date = $booking['check_in_date'];
    $check_out_date = $booking['check_out_date'];
   
    $total_amount = $booking['total_price'];;
} else {
    die("Booking not found.");
}

// Create instance of FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Set title and font
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');

// Add some space
$pdf->Ln(10);

// Set font for details
$pdf->SetFont('Arial', '', 12);

// Booking Details
$pdf->Cell(40, 10, 'Booking ID:', 0, 0);
$pdf->Cell(0, 10, $booking_id, 0, 1);

$pdf->Cell(40, 10, 'Customer Name:', 0, 0);
$pdf->Cell(0, 10, $customer_name, 0, 1);

$pdf->Cell(40, 10, 'Room Number:', 0, 0);
$pdf->Cell(0, 10, $room_number, 0, 1);

$pdf->Cell(40, 10, 'Check-in Date:', 0, 0);
$pdf->Cell(0, 10, $check_in_date, 0, 1);

$pdf->Cell(40, 10, 'Check-out Date:', 0, 0);
$pdf->Cell(0, 10, $check_out_date, 0, 1);

// Add some space before table
$pdf->Ln(10);

// Invoice Table
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(80, 10, 'Description', 1);

$pdf->Cell(50, 10, 'Total Amount', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(80, 10, 'Room Charges', 1);

$pdf->Cell(50, 10, '$' . number_format($total_amount, 2), 1);
$pdf->Ln();

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Total Amount Due: $' . number_format($total_amount, 2), 0, 1, 'R');

// Output the PDF (download)
$pdf->Output('D', 'Invoice_' . $booking_id . '.pdf');
?>
