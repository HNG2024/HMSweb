<?php

include('session.php');

// Check if the user is logged in
if (!isset($_SESSION['login_user'])) {
    header('Location: https://hgstore.in/login71.php');
    exit();
}

include 'connection.php';
$conn = Connect();
    $conn->select_db($u_id1);
if (isset($_GET['booking_id'])) {
    $booking_id = mysqli_real_escape_string($conn, $_GET['booking_id']);
    $sql = "SELECT * FROM room_booking WHERE booking_id = '$booking_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<p><strong>Guest Name:</strong> " . htmlspecialchars($row['guest_name']) . "</p>";
        echo "<p><strong>Check-in Date:</strong> " . htmlspecialchars($row['check_in_date']) . "</p>";
        echo "<p><strong>Check-out Date:</strong> " . htmlspecialchars($row['check_out_date']) . "</p>";
        echo "<p><strong>Room Numbers:</strong> " . htmlspecialchars(implode(', ', json_decode($row['room_number'], true))) . "</p>";
        echo "<p><strong>Payment Type:</strong> " . htmlspecialchars($row['payment_type']) . "</p>";
        echo "<p><strong>Total Price:</strong> " . htmlspecialchars($row['total_price']) . "</p>";
        // Add more fields as necessary
    } else {
        echo "<p>No booking details found.</p>";
    }
} else {
    echo "<p>Invalid booking ID.</p>";
}

$conn->close();
?>
