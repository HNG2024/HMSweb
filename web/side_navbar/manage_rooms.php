<?php
include 'php/db_connection.php';

// Fetch filter status if set
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'All';

// Prepare SQL based on the status filter
$sql = "SELECT * FROM room_booking";
if ($status_filter != 'All') {
    $sql .= " WHERE status = '$status_filter'";
}

$result = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include 'index.php'; ?>
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
                            <?php if (isset($row['status']) && $row['status'] == 'Available'): ?>
                                <button class="btn-checkin" data-id="<?php echo $row['id']; ?>" data-room="<?php echo $row['room_number']; ?>">Check In</button>
                                <button class="btn-checkout disabled" disabled>Check Out</button>
                            <?php elseif (isset($row['status']) && $row['status'] == 'Occupied'): ?>
                                <button class="btn-checkin disabled" disabled>Check In</button>
                                <button class="btn-checkout" data-id="<?php echo $row['id']; ?>" data-room="<?php echo $row['room_number']; ?>">Check Out</button>
                            <?php elseif (isset($row['status']) && $row['status'] == 'Reserved'): ?>
                                <button class="btn-noshows" data-id="<?php echo $row['id']; ?>" data-room="<?php echo $row['room_number']; ?>">No Show</button>
                                <button class="btn-checkin" data-id="<?php echo $row['id']; ?>" data-room="<?php echo $row['room_number']; ?>">Check In</button>
                                <button class="btn-checkout" data-id="<?php echo $row['id']; ?>" data-room="<?php echo $row['room_number']; ?>">Check Out</button>
                            <?php endif; ?>
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
    });

    $(document).on('click', '.btn-checkout', function() {
        const roomId = $(this).data('id');
        updateRoomStatus(roomId, 'Vacate');
    });

    $(document).on('click', '.btn-noshows', function() {
        const roomId = $(this).data('id');
        updateRoomStatus(roomId, 'Reserved');
    });
</script>

</body>
</html>
