<?php
include 'db_connection.php';

// Fetch filter status if set
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'All';

// Prepare SQL based on the status filter
$sql = "SELECT * FROM room_booking";
if ($status_filter != 'All') {
    $sql .= " WHERE status = '$status_filter'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container">
    <h2>Manage Rooms</h2>

    <div class="filter-status">
        <label for="status">Filter by Status:</label>
        <select id="status" onchange="filterStatus()">
            <option value="All" <?php echo $status_filter == 'All' ? 'selected' : ''; ?>>All</option>
            <option value="Occupied" <?php echo $status_filter == 'Occupied' ? 'selected' : ''; ?>>Occupied</option>
            <option value="Available" <?php echo $status_filter == 'Available' ? 'selected' : ''; ?>>Available</option>
            <option value="Reserved" <?php echo $status_filter == 'Reserved' ? 'selected' : ''; ?>>Reserved</option>
        </select>
    </div>

    <table class="manage-rooms-table">
        <thead>
            <tr>
                <th>Room</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Status</th>
                <th>Actions</th>
                <th>Booking ID</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="room-status-<?php echo isset($row['status']) ? strtolower($row['status']) : 'unknown'; ?>">
                        <td><?php echo isset($row['room_number']) ? $row['room_number'] : 'N/A'; ?></td>
                        <td><?php echo !empty($row['check_in_date']) ? $row['check_in_date'] : 'N/A'; ?></td>
                        <td><?php echo !empty($row['check_out_date']) ? $row['check_out_date'] : 'N/A'; ?></td>
                        <td class="status"><?php echo isset($row['status']) ? ucfirst($row['status']) : 'Unknown'; ?></td>
                        <td>
                            <button class="btn-checkin" data-id="<?php echo $row['id']; ?>" data-room="<?php echo $row['room_number']; ?>" <?php echo $row['status'] == 'Occupied' ? 'disabled' : ''; ?>>Check In</button>
                            <button class="btn-checkout" data-id="<?php echo $row['id']; ?>" data-room="<?php echo $row['room_number']; ?>" <?php echo $row['status'] == 'Available' ? 'disabled' : ''; ?>>Check Out</button>
                            <button class="btn-noshows" data-id="<?php echo $row['id']; ?>" data-room="<?php echo $row['room_number']; ?>" <?php echo ($row['status'] == 'Available' || $row['status'] == 'Occupied') ? 'disabled style="display:none;"' : ''; ?>>No Show</button>
                            <a href="edit_booking.php?id=<?php echo $row['id']; ?>">Edit</a> |
                            <a href="delete_booking.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                        </td>
                        <td><?php echo $row['booking_id'] ?: 'N/A'; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No rooms booked yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function filterStatus() {
        var status = document.getElementById("status").value;
        window.location.href = "?status=" + status;
    }

    function generateBookingId(roomNumber) {
        const timestamp = Date.now();
        return `${roomNumber}_${timestamp}`;
    }

    function updateRoomStatus(id, status, bookingId = null) {
        $.post('update_status.php', {
            id: id,
            status: status,
            booking_id: bookingId
        }, function(response) {
            location.reload();
        });
    }

    $(document).on('click', '.btn-checkin', function() {
        const roomId = $(this).data('id');
        const roomNumber = $(this).data('room');
        const bookingId = generateBookingId(roomNumber);
        updateRoomStatus(roomId, 'Occupied', bookingId);

        // Hide the Check In and No Show buttons
        $(this).hide();
        $(this).closest('td').find('.btn-noshows').hide();
    });

    $(document).on('click', '.btn-checkout', function() {
        const roomId = $(this).data('id');
        updateRoomStatus(roomId, 'Vacate');

        // Hide the No Show button
        $(this).closest('td').find('.btn-noshows').hide();
    });

    $(document).on('click', '.btn-noshows', function() {
        const roomId = $(this).data('id');
        updateRoomStatus(roomId, 'Available');
    });
</script>

</body>
</html>
