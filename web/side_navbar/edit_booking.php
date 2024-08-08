<?php
include 'php/db_connection.php';

$row = null; // Initialize $row to null

// Check if the id parameter is set and fetch the booking details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM room_booking WHERE id = ?";
    
    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing the statement: " . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No record found!";
        exit;
    }
} else {
    echo "No ID provided!";
    exit;
}

// Handle the form submission for updating the booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure the id is carried over from the hidden input field
    $id = $_POST['id'];
    $room_number = $_POST['room_number'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $check_out_time = $_POST['check_out_time'];
    $guest_name = $_POST['guest_name'];

    // Update the booking details in the database using a prepared statement
    $sql = "UPDATE room_booking SET room_number = ?, check_in_date = ?, check_out_date = ?, check_out_time = ?, guest_name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing the statement: " . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("sssssi", $room_number, $check_in_date, $check_out_date, $check_out_time, $guest_name, $id);

    if ($stmt->execute()) {
        echo "Booking updated successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <form action="edit_booking.php?id=<?php echo $row['id']; ?>" method="POST" class="booking-form">
        <h2>Edit Booking</h2>

        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <label for="room_number">Room Number</label>
        <input type="text" id="room_number" name="room_number" value="<?php echo htmlspecialchars($row['room_number']); ?>" required>

        <label for="check_in_date">Check-in Date</label>
        <input type="date" id="check_in_date" name="check_in_date" value="<?php echo htmlspecialchars($row['check_in_date']); ?>" required>

        <label for="check_out_date">Check-out Date</label>
        <input type="date" id="check_out_date" name="check_out_date" value="<?php echo htmlspecialchars($row['check_out_date']); ?>" required>

        <label for="check_out_time">Check-out Time</label>
        <input type="time" id="check_out_time" name="check_out_time" value="<?php echo htmlspecialchars($row['check_out_time']); ?>" required>

        <label for="guest_name">Guest Name</label>
        <input type="text" id="guest_name" name="guest_name" value="<?php echo htmlspecialchars($row['guest_name']); ?>" required>

        <button type="submit" class="btn">Update Booking</button>
    </form>
</div>

</body>
</html>
