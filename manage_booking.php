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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $action = mysqli_real_escape_string($conn, $_POST['action']);
    $time = isset($_POST['time']) ? mysqli_real_escape_string($conn, $_POST['time']) : null;

    switch ($action) {
        case 'checkin':
            $sql = "UPDATE roominfo SET checking_room = '$time', status = 'Occupied' WHERE booking_id = '$booking_id'";
            $sql1 = "UPDATE room_booking SET late_checkin = '$time', status = 'Occupied' WHERE booking_id = '$booking_id'";
            $message = "Check-in time saved successfully";
            break;

        case 'checkout':
            $sql = "UPDATE roominfo SET status = 'undercleaning', checking_room = NULL WHERE booking_id = '$booking_id'";
            $sql1 = "UPDATE room_booking SET check_out_info = '$time', status = 'checkout' WHERE booking_id = '$booking_id'";
            $message = "Check-out time saved successfully";
            break;

        case 'cancel':
            $sql = "UPDATE roominfo SET status = 'undercleaning', checking_room = NULL WHERE booking_id = '$booking_id'";
            $sql1 = "UPDATE room_booking SET status = 'cancelled' WHERE booking_id = '$booking_id'";
            $message = "Booking cancelled successfully";
            break;

        default:
            echo "Invalid action";
            exit();
    }

    // Execute the first query
    if ($conn->query($sql) === TRUE) {
        // Execute the second query
        if ($conn->query($sql1) === TRUE) {
            echo $message;
        } else {
            echo "Error updating room_booking record: " . $conn->error;
        }
    } else {
        echo "Error updating roominfo record: " . $conn->error;
    }

    $conn->close();
}
