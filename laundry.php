<?php

include('session.php'); // Ensure this includes session initialization and connection code

// Check if the session is started and the user is logged in
if (!isset($_SESSION['login_user'])) {
    header('Location: https://hgstore.in/login71.php');
    exit();
}

// Include the database connection file
include('connection.php');
$conn = Connect();
include('navbar.php');

$conn->select_db($u_id1);


$edit_mode = false; // Track if we are in edit mode
$edit_data = []; // Store data to populate the form when editing

// Handle Assignments
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign'])) {
        $room_number = $_POST['room'];
        $items_sent = $_POST['items_sent'];
        $quantities = $_POST['quantity'];
        $collect_time = $_POST['collect_time'];
        $return_time = $_POST['return_time'];

        // Insert the new assignments into the laundry_assignments table
        $insertQuery = "INSERT INTO laundry_assignments (room_number, items_sent, quantity, collect_time, return_time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);

        foreach ($items_sent as $index => $item) {
            $quantity = $quantities[$index];
            $stmt->bind_param("ssiss", $room_number, $item, $quantity, $collect_time, $return_time);
            if (!$stmt->execute()) {
                echo "Error: " . $stmt->error;
                break;
            }
        }

        $stmt->close();
        $_SESSION['notification'] = "Laundry successfully assigned to room $room_number";
        $_SESSION['notification_type'] = "success";
        header("Location: " . $_SERVER['PHP_SELF']);  // Redirect to the same page
        exit;
    } elseif (isset($_POST['delete'])) {
        $room_number = $_POST['room_number'];
        $deleteQuery = "DELETE FROM laundry_assignments WHERE room_number = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("s", $room_number);
        $stmt->execute();
        $stmt->close();
        $_SESSION['notification'] = "Laundry assignment for room $room_number deleted";
        $_SESSION['notification_type'] = "success";
        header("Location: " . $_SERVER['PHP_SELF']);  // Redirect to the same page
        exit;
    } elseif (isset($_POST['edit'])) {
        $room_number = $_POST['room_number'];

        // Fetch data for the selected room to edit
        $editQuery = "SELECT * FROM laundry_assignments WHERE room_number = ?";
        $stmt = $conn->prepare($editQuery);
        $stmt->bind_param("s", $room_number);
        $stmt->execute();
        $result = $stmt->get_result();

        $edit_data = [];
        while ($row = $result->fetch_assoc()) {
            $edit_data[] = $row;
        }

        $stmt->close();
        $edit_mode = true;
    } elseif (isset($_POST['update'])) {
        $room_number = $_POST['room'];
        $items_sent = $_POST['items_sent'];
        $quantities = $_POST['quantity'];
        $collect_time = $_POST['collect_time'];
        $return_time = $_POST['return_time'];

        // Delete existing assignments for the room
        $deleteQuery = "DELETE FROM laundry_assignments WHERE room_number = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("s", $room_number);
        $stmt->execute();

        // Insert the updated assignments into the laundry_assignments table
        $insertQuery = "INSERT INTO laundry_assignments (room_number, items_sent, quantity, collect_time, return_time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);

        foreach ($items_sent as $index => $item) {
            $quantity = $quantities[$index];
            $stmt->bind_param("ssiss", $room_number, $item, $quantity, $collect_time, $return_time);
            if (!$stmt->execute()) {
                echo "Error: " . $stmt->error;
                break;
            }
        }

        $stmt->close();
        $_SESSION['notification'] = "Laundry assignment for room $room_number updated";
        $_SESSION['notification_type'] = "success";
        header("Location: " . $_SERVER['PHP_SELF']);  // Redirect to the same page
        exit;
    }
}

// Fetch laundry assignments grouped by room number
$laundryQuery = "SELECT 
                    room_number, 
                    GROUP_CONCAT(CONCAT(items_sent, ' (', quantity, ')') ORDER BY id SEPARATOR ', ') AS items_sent, 
                    MIN(collect_time) AS collect_time,
                    MAX(return_time) AS return_time
                FROM 
                    laundry_assignments 
                GROUP BY 
                    room_number";
$laundryResult = $conn->query($laundryQuery);

// Check if the query was successful
if (!$laundryResult) {
    die("Error retrieving laundry assignments: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Add your existing CSS styles here */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9fafc;
            margin: 0;
            padding: 20px;
            animation: fadeIn 1s ease-in-out;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            animation: slideIn 1s ease-in-out;
        }

        .header h1 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #34495e;
            text-align: center;
            font-weight: 600;
        }

        .header nav a {
            text-decoration: none;
            color: #2980b9;
            margin-right: 12px;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .header nav a:hover {
            color: #1c6f9e;
        }
        .header nav a.active {
            color: #000; /* Black color for active link */
            font-weight: bold;
            text-decoration: underline;
        }

        .notification {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            z-index: 1000;
            display: none;
        }

        .assign-form {
            margin-top: 25px;
        }

        .assign-form label {
            font-size: 18px;
            color: #2c3e50;
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .assign-form input[type="text"],
        .assign-form input[type="datetime-local"] {
            width: 100%;
            padding: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 20px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .assign-form input[type="text"]:focus,
        .assign-form input[type="datetime-local"]:focus {
            border-color: #2980b9;
            box-shadow: 0 0 10px rgba(41, 128, 185, 0.2);
        }

        .assign-form .item-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .assign-form .item-container select {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: border 0.3s ease;
        }

        .assign-form .item-container input[type="number"] {
            width: 100px;
            margin-left: 12px;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: border 0.3s ease;
        }

        .assign-form select:focus,
        .assign-form input[type="number"]:focus {
            border-color: #2980b9;
        }

        .assign-form button {
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background-color: #2980b9;
            color: white;
            font-size: 18px;
            transition: background-color 0.3s ease;
            margin-top: 12px;
        }

        .assign-form button:hover {
            background-color: #1c6f9e;
        }

        .table-container {
            margin-top: 40px;
        }

        .room-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            animation: slideIn 1s ease-in-out;
        }

        .room-table th,
        .room-table td {
            padding: 14px 20px;
            text-align: left;
            border-bottom: 1px solid #e2e2e2;
            transition: background-color 0.3s ease;
        }

        .room-table th {
            background-color: #ecf0f1;
            font-weight: bold;
            color: #34495e;
        }

        .room-table td {
            color: #2c3e50;
        }

        .room-table tr:hover {
            background-color: #ecf0f1;
        }

        .edit-btn, .delete-btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            color: white;
            text-align: center;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background-color: #f39c12; /* Orange */
            box-shadow: 0 4px 6px rgba(243, 156, 18, 0.4);
        }

        .edit-btn:hover {
            background-color: #e67e22;
            box-shadow: 0 6px 8px rgba(230, 126, 34, 0.6);
        }

        .delete-btn {
            background-color: #e74c3c; /* Red */
            box-shadow: 0 4px 6px rgba(231, 76, 60, 0.4);
            margin-top: 10px;
        }

        .delete-btn:hover {
            background-color: #c0392b;
            box-shadow: 0 6px 8px rgba(192, 57, 43, 0.6);
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laundry Management</h1>
            <nav>
    <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Housekeeping</a> / 
    <a href="amenity.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'amenity.php' ? 'active' : ''; ?>">Amenity</a> / 
    <a href="laundry.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'laundry.php' ? 'active' : ''; ?>">Laundry</a> / 
    <a href="report.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : ''; ?>">Report</a>
        </nav>

        </div>

        <?php if (isset($_SESSION['notification'])): ?>
            <div class="notification" style="background-color: <?php echo $_SESSION['notification_type'] == 'success' ? '#4CAF50' : '#f44336'; ?>;">
                <?php echo $_SESSION['notification']; ?>
            </div>
            <?php unset($_SESSION['notification']); ?>
        <?php endif; ?>

        <div class="assign-form">
            <h2><?php echo $edit_mode ? 'Edit Laundry Assignment' : 'Assign Laundry to Room'; ?></h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <label for="room">Enter Room No:</label>
                <input type="text" name="room" id="room" value="<?php echo $edit_mode ? $edit_data[0]['room_number'] : ''; ?>" required>

                <label for="items_sent">Select Laundry Items:</label>
                <div id="item-fields">
                    <?php if ($edit_mode): ?>
                        <?php foreach ($edit_data as $data): ?>
                            <div class="item-container">
                                <select name="items_sent[]" class="item-select" required>
                                    <option value="Bed Sheets" <?php echo $data['items_sent'] == 'Bed Sheets' ? 'selected' : ''; ?>>Bed Sheets</option>
                                    <option value="Towels" <?php echo $data['items_sent'] == 'Towels' ? 'selected' : ''; ?>>Towels</option>
                                    <option value="Pillow Covers" <?php echo $data['items_sent'] == 'Pillow Covers' ? 'selected' : ''; ?>>Pillow Covers</option>
                                    <option value="Bathrobe" <?php echo $data['items_sent'] == 'Bathrobe' ? 'selected' : ''; ?>>Bathrobe</option>
                                    <option value="Blankets" <?php echo $data['items_sent'] == 'Blankets' ? 'selected' : ''; ?>>Blankets</option>
                                    <!-- Add more laundry items as needed -->
                                </select>
                                <input type="number" name="quantity[]" placeholder="Qty" min="1" value="<?php echo $data['quantity']; ?>" required>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="item-container">
                            <select name="items_sent[]" class="item-select" required>
                                <option value="Bed Sheets">Bed Sheets</option>
                                <option value="Towels">Towels</option>
                                <option value="Pillow Covers">Pillow Covers</option>
                                <option value="Bathrobe">Bathrobe</option>
                                <option value="Blankets">Blankets</option>
                                <!-- Add more laundry items as needed -->
                            </select>
                            <input type="number" name="quantity[]" placeholder="Qty" min="1" required>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="button" id="add-item-btn">Add Another Item</button>
                <br><br>
                <label for="collect_time">Collect Time:</label>
                <input type="datetime-local" name="collect_time" id="collect_time" value="<?php echo $edit_mode ? $edit_data[0]['collect_time'] : ''; ?>" required>

                <label for="return_time">Return Time:</label>
                <input type="datetime-local" name="return_time" id="return_time" value="<?php echo $edit_mode ? $edit_data[0]['return_time'] : ''; ?>" required>

                <?php if ($edit_mode): ?>
                    <button type="submit" name="update">Update Laundry</button>
                <?php else: ?>
                    <button type="submit" name="assign">Assign Laundry</button>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-container">
            <h2>Assigned Laundry</h2>
            <table class="room-table">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Items Sent</th>
                        <th>Collect Time</th>
                        <th>Return Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($assignment = $laundryResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $assignment['room_number']; ?></td>
                        <td><?php echo $assignment['items_sent']; ?></td>
                        <td><?php echo $assignment['collect_time']; ?></td>
                        <td><?php echo $assignment['return_time']; ?></td>
                        <td>
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display:inline;">
                                <input type="hidden" name="room_number" value="<?php echo $assignment['room_number']; ?>">
                                <button type="submit" name="edit" class="edit-btn">Edit</button>
                                <button type="submit" name="delete" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        // Show notification if present
        if ($('.notification').length) {
            $('.notification').fadeIn().delay(3000).fadeOut();
        }

        // Function to add a new laundry item field
        $('#add-item-btn').on('click', function() {
            const itemField = `
                <div class="item-container">
                    <select name="items_sent[]" class="item-select" required>
                        <option value="">Select Item</option>
                        <option value="Bed Sheets">Bed Sheets</option>
                        <option value="Towels">Towels</option>
                        <option value="Pillow Covers">Pillow Covers</option>
                        <option value="Bathrobe">Bathrobe</option>
                        <option value="Blankets">Blankets</option>
                        <!-- Add more laundry items as needed -->
                    </select>
                    <input type="number" name="quantity[]" placeholder="Qty" min="1" required>
                </div>`;
            $('#item-fields').append(itemField);
        });

        // Initialize the Select2 dropdown
        $('.item-select').select2({
            placeholder: "Select Laundry Items",
            allowClear: true,
            closeOnSelect: false
        });

        // Close the Select2 dropdown on clicking outside of it
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.select2-container').length) {
                $('.item-select').select2('close');
            }
        });
    });
    </script>
</body>
</html>

<?php
$conn->close();
?>
