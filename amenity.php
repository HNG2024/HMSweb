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

// Handle Assignments
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign'])) {
        $room_number = $_POST['room'];
        $amenity_names = $_POST['amenity'];
        $quantities = $_POST['quantity'];

        if (isset($_POST['edit_id']) && $_POST['edit_id'] !== '') {
            // Handle update (edit) operation
            $edit_id = $_POST['edit_id'];
            $deleteQuery = "DELETE FROM amenity_assignments WHERE room_number = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("s", $edit_id);
            $stmt->execute();
            $stmt->close();
        }

        // Insert the new assignments into the amenity_assignments table
        $insertQuery = "INSERT INTO amenity_assignments (room_number, amenity_name, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);

        foreach ($amenity_names as $index => $amenity_name) {
            $quantity = $quantities[$index]; // Get the corresponding quantity for each amenity
            $stmt->bind_param("ssi", $room_number, $amenity_name, $quantity);
            $stmt->execute();
        }

        $stmt->close();
        $_SESSION['notification'] = 'Amenities successfully assigned to room ' . $room_number;
        $_SESSION['notification_type'] = 'success';
        header("Location: " . $_SERVER['PHP_SELF']);  // Redirect to the same page
        exit();
    } elseif (isset($_POST['delete'])) {
        $room_number = $_POST['room_number'];
        $deleteQuery = "DELETE FROM amenity_assignments WHERE room_number = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("s", $room_number);
        $stmt->execute();
        $stmt->close();
        $_SESSION['notification'] = 'Amenity assignment for room ' . $room_number . ' has been deleted.';
        $_SESSION['notification_type'] = 'error';
        header("Location: " . $_SERVER['PHP_SELF']);  // Redirect to the same page
        exit();
    } elseif (isset($_POST['edit'])) {
        // Handle the edit operation
        $room_number = $_POST['room_number'];
        $editQuery = "SELECT room_number, amenity_name, quantity FROM amenity_assignments WHERE room_number = ?";
        $stmt = $conn->prepare($editQuery);
        $stmt->bind_param("s", $room_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $editData = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

// Fetch assignments with grouped amenity names and quantities
$assignmentQuery = "
    SELECT 
        room_number, 
        GROUP_CONCAT(CONCAT(amenity_name, ' (', quantity, ')') SEPARATOR ', ') AS amenities 
    FROM 
        amenity_assignments 
    GROUP BY 
        room_number";
$assignmentResult = $conn->query($assignmentQuery);

if (!$assignmentResult) {
    die("Error retrieving assignments: " . $conn->error);
}

// Check for a notification session variable and display it
$notification = '';
$notification_type = '';
if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification'];
    $notification_type = $_SESSION['notification_type'];
    unset($_SESSION['notification']); // Clear the notification after it's displayed
    unset($_SESSION['notification_type']); // Clear the notification type after it's displayed
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amenity Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            animation: fadeIn 1s ease-in-out;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            animation: slideIn 1s ease-in-out;
        }

        .header h1 {
            margin-bottom: 20px;
            font-size: 26px;
            color: #333;
            text-align: center;
        }

        .header nav a {
            text-decoration: none;
            color: #007BFF;
            margin-right: 10px;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .header nav a:hover {
            color: #0056b3;
        }

        .header nav a.active {
            color: #000; /* Black color for active link */
            font-weight: bold;
            text-decoration: underline;
        }

        .assign-form {
            margin-top: 20px;
        }

        .assign-form label {
            font-size: 18px;
            color: #555;
            display: block;
            margin-bottom: 8px;
        }

        .assign-form input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            margin-bottom: 20px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .assign-form input[type="text"]:focus {
            border-color: #007BFF;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
        }

        .assign-form .amenity-container {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .assign-form .amenity-container select {
            flex: 1;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            transition: border 0.3s ease;
        }

        .assign-form .amenity-container input[type="number"] {
            width: 80px;
            margin-left: 10px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            transition: border 0.3s ease;
        }

        .assign-form select:focus,
        .assign-form input[type="number"]:focus {
            border-color: #007BFF;
        }

        .assign-form button {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
            font-size: 18px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .assign-form button:hover {
            background-color: #0056b3;
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
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s ease;
        }

        .room-table th {
            background-color: #f7f7f7;
            font-weight: bold;
            color: #333;
        }

        .room-table td {
            color: #555;
        }

        .room-table tr:hover {
            background-color: #f1f1f1;
        }

        .edit-btn, .delete-btn {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            color: white;
            font-size: 14px;
            transition: background-color 0.3s ease;
            margin-bottom: 5px;
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
            background-color: #ff4d4d; /* Red */
            box-shadow: 0 4px 6px rgba(255, 77, 77, 0.4);
        }

        .delete-btn:hover {
            background-color: #e60000;
        }

        /* Notification styles */
        #notification {
            display: none;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: white; /* White text color */
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        #notification.success {
            background-color: #4caf50; /* Green background for success messages */
        }
        #notification.error {
            background-color: #f44336; /* Red background for error messages */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Amenity Management</h1>
            <nav>
    <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Housekeeping</a> / 
    <a href="amenity.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'amenity.php' ? 'active' : ''; ?>">Amenity</a> / 
    <a href="laundry.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'laundry.php' ? 'active' : ''; ?>">Laundry</a> / 
    <a href="report.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : ''; ?>">Report</a>
      </nav>
        </div>

        <!-- Notification Area -->
        <div id="notification" class="<?php echo $notification_type; ?>"><?php echo $notification; ?></div>

        <div class="assign-form">
            <h2><?php echo isset($editData) ? 'Edit' : 'Assign'; ?> Amenities to Room</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" name="edit_id" value="<?php echo isset($editData) ? $editData[0]['room_number'] : ''; ?>">

                <label for="room">Enter Room No:</label>
                <input type="text" name="room" id="room" required value="<?php echo isset($editData) ? $editData[0]['room_number'] : ''; ?>" <?php echo isset($editData) ? 'readonly' : ''; ?>>

                <label for="amenity">Select Amenities:</label>
                <div id="amenity-fields">
                    <?php if (isset($editData)) : ?>
                        <?php foreach ($editData as $data) : ?>
                            <div class="amenity-container">
                                <select name="amenity[]" class="amenity-select" required>
                                    <option value="Soap" <?php echo $data['amenity_name'] == 'Soap' ? 'selected' : ''; ?>>Soap</option>
                                    <option value="Shampoo" <?php echo $data['amenity_name'] == 'Shampoo' ? 'selected' : ''; ?>>Shampoo</option>
                                    <option value="Conditioner" <?php echo $data['amenity_name'] == 'Conditioner' ? 'selected' : ''; ?>>Conditioner</option>
                                    <option value="Bathrobe" <?php echo $data['amenity_name'] == 'Bathrobe' ? 'selected' : ''; ?>>Bathrobe</option>
                                </select>
                                <input type="number" name="quantity[]" placeholder="Qty" min="1" value="<?php echo $data['quantity']; ?>" required>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="amenity-container">
                            <select name="amenity[]" class="amenity-select" required>
                                <option value="Soap">Soap</option>
                                <option value="Shampoo">Shampoo</option>
                                <option value="Conditioner">Conditioner</option>
                                <option value="Bathrobe">Bathrobe</option>
                            </select>
                            <input type="number" name="quantity[]" placeholder="Qty" min="1" required>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="button" id="add-amenity-btn">Add Another Amenity</button>
                <button type="submit" name="assign"><?php echo isset($editData) ? 'Update' : 'Assign'; ?> Amenities</button>
            </form>
        </div>

        <div class="table-container">
            <h2>Assigned Amenities</h2>
            <table class="room-table">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Amenities</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($assignment = $assignmentResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $assignment['room_number']; ?></td>
                        <td><?php echo $assignment['amenities']; ?></td>
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
        // Function to add a new amenity field
        $('#add-amenity-btn').on('click', function() {
            const amenityField = `
                <div class="amenity-container">
                    <select name="amenity[]" class="amenity-select" required>
                        <option value="">Select Item</option>
                        <option value="Soap">Soap</option>
                        <option value="Shampoo">Shampoo</option>
                        <option value="Conditioner">Conditioner</option>
                        <option value="Bathrobe">Bathrobe</option>
                    </select>
                    <input type="number" name="quantity[]" placeholder="Qty" min="1" required>
                </div>`;
            $('#amenity-fields').append(amenityField);
        });

        // Display the notification if it exists
        var notification = "<?php echo $notification; ?>";
        var notificationType = "<?php echo $notification_type; ?>";
        if (notification) {
            $('#notification').text(notification).addClass(notificationType).slideDown().delay(3000).slideUp();
        }
    });
    </script>
</body>
</html>

<?php
$conn->close();
?>
