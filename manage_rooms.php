<?php

include('session.php');

// Check if the user is logged in
if (!isset($_SESSION['login_user'])) {
    header('Location: https://hgstore.in/login71.php');
    exit();
}

include 'connection.php';
$conn = Connect();
include('navbar.php');
$conn->select_db($u_id1);

// Fetch filter status if set
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'All';

// Prepare SQL based on the status filter
$sql = "SELECT room_no, booking_id FROM roominfo";
if ($status_filter != 'All') {
    $sql .= " WHERE status = '$status_filter'";
}

$result = $conn->query($sql);

$booking_info_array = [];
while ($row = $result->fetch_assoc()) {
    if (!isset($booking_info_array[$row['booking_id']])) {
        $booking_info_array[$row['booking_id']] = [];
    }
    $booking_info_array[$row['booking_id']][] = $row['room_no'];
}

// Fetch booking details and merge room numbers for the same booking ID
$booking_details_array = [];
foreach ($booking_info_array as $booking_id => $room_numbers) {
    $sql_booking_details1 = "SELECT * FROM room_booking WHERE booking_id = '$booking_id'";
    $sql_booking_details = $conn->query($sql_booking_details1);
    if ($sql_booking_details->num_rows > 0) {
        while ($row = $sql_booking_details->fetch_assoc()) {
            $row['room_number'] = json_encode($room_numbers); // Merge the room numbers
            $booking_details_array[] = $row;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="style71.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px;
            position: relative;
            overflow-y: auto;
            max-height: 70vh;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
        }
    </style>
</head>
<body>
<section class="manage-rooms" style="background-color: #fff;">
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
            <?php if (count($booking_details_array) > 0): ?>
                <?php foreach ($booking_details_array as $row): ?>
                    <tr class="room-status-<?php echo isset($row['status']) ? strtolower($row['status']) : 'unknown'; ?>">
                        <td>
                            <?php 
                            if (isset($row['room_number'])) {
                                $room_numbers = json_decode($row['room_number'], true);
                                if (is_array($room_numbers)) {
                                    echo implode(', ', $room_numbers);
                                } else {
                                    echo 'N/A';
                                }
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td><?php echo !empty($row['check_in_date']) ? $row['check_in_date'] : 'N/A'; ?></td>
                        <td><?php echo !empty($row['check_out_date']) ? $row['check_out_date'] : 'N/A'; ?></td>
                        <td class="status"><?php echo isset($row['status']) ? ucfirst($row['status']) : 'Unknown'; ?></td>
                        <td>
                            <?php if (!in_array($row['status'], ['Available', 'Occupied', 'Cancelled', 'undercleaning'])): ?>
    <button class="btn-checkin" data-booking-id="<?php echo $row['booking_id']; ?>" data-room="<?php echo $row['room_number']; ?>">Clock In</button>
<?php endif; ?>

                            <?php if (!in_array($row['status'], ['Reserved', 'Cancelled', 'undercleaning'])): ?>
                                <button class="btn-checkout" data-booking-id="<?php echo $row['booking_id']; ?>" data-room="<?php echo $row['room_number']; ?>">Clock Out</button>
                            <?php endif; ?>
                            <?php if (!in_array($row['status'], ['Occupied', 'undercleaning'])): ?>
                                <button class="btn-cancel" data-booking-id="<?php echo $row['booking_id']; ?>" data-room="<?php echo $row['room_number']; ?>">Cancel</button>
                            <?php endif; ?>

                            <a href="edit_booking.php?booking_id=<?php echo $row['booking_id']; ?>">Edit</a> |
                            <a href="delete_booking.php?booking_id=<?php echo $row['booking_id']; ?>" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                        </td>
                        <td>
                            <a href="#" class="open-modal" data-booking-id="<?php echo $row['booking_id']; ?>">
                                <?php echo $row['booking_id'] ?: 'N/A'; ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No rooms booked yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal for Booking Details -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Booking Details</h2>
        <div id="modal-body"></div>
    </div>
</div>

<!-- Modal for Clock In/Out -->
<div id="clockModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Select Date and Time</h2>
        <input type="datetime-local" id="clockDateTime" value="<?php echo date('Y-m-d\TH:i'); ?>">
        <button id="saveClockTime" class="btn">Save</button>
    </div>
</div>

<script>
    function openClockModal(bookingId, roomNumber, action) {
        $('#clockModal').css('display', 'flex');
        $('#saveClockTime').off('click').on('click', function() {
            const datetime = $('#clockDateTime').val();
            $.ajax({
                url: 'manage_booking.php',
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    room_number: roomNumber,
                    time: datetime,
                    action: action
                },
                success: function(response) {
                    $('#clockModal').css('display', 'none');
                    alert(response);
                    location.reload();
                },
                error: function() {
                    alert('Failed to save date and time.');
                }
            });
        });
    }

    $(document).on('click', '.btn-checkin', function() {
        const bookingId = $(this).data('booking-id');
        const roomNumber = $(this).data('room');
        openClockModal(bookingId, roomNumber, 'checkin');
    });

    $(document).on('click', '.btn-checkout', function() {
        const bookingId = $(this).data('booking-id');
        const roomNumber = $(this).data('room');
        openClockModal(bookingId, roomNumber, 'checkout');
    });

    $(document).on('click', '.btn-cancel', function() {
        const bookingId = $(this).data('booking-id');
        const roomNumber = $(this).data('room');

        if (confirm('Are you sure you want to cancel this booking?')) {
            $.ajax({
                url: 'manage_booking.php',
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    room_number: roomNumber,
                    action: 'cancel'
                },
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    alert('Failed to cancel booking.');
                }
            });
        }
    });

    // Open booking details modal
    $(document).on('click', '.open-modal', function(event) {
        event.preventDefault();
        const bookingId = $(this).data('booking-id');

        $.ajax({
            url: 'get_booking_details.php',
            type: 'GET',
            data: { booking_id: bookingId },
            success: function(data) {
                $('#modal-body').html(data);
                $('#bookingModal').css('display', 'flex');
            },
            error: function() {
                $('#modal-body').html('<p>Error fetching details.</p>');
                $('#bookingModal').css('display', 'flex');
            }
        });
    });

    // Close modal when the user clicks on <span> (x)
    $(document).on('click', '.close', function() {
        $('#clockModal').css('display', 'none');
        $('#bookingModal').css('display', 'none');
    });

    // Close modal when the user clicks outside of the modal content
    $(window).on('click', function(event) {
        if ($(event.target).is('#clockModal') || $(event.target).is('#bookingModal')) {
            $('#clockModal').css('display', 'none');
            $('#bookingModal').css('display', 'none');
        }
    });

    function filterStatus() {
        const status = document.getElementById("status").value;
        window.location.href = "?status=" + status;
    }
</script>

</section>
</body>
</html>
