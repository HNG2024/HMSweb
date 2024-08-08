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
// Fetch all invoices (you might fetch from a bookings table or an invoices table)
$sql = "SELECT booking_id, guest_name, room_number, check_in_date, check_out_date FROM room_booking ORDER BY check_in_date DESC";
$invoices = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice List</title>
    <style>
        /* General Styles */
        .invoice {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f5f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .invoice .container {
            width: 80%;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

       .invoice h2 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

       .invoice table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ecf0f1;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

       .invoice th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

       .invoice th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }

       .invoice tr:nth-child(even) {
            background-color: #f2f3f4;
        }

       .invoice tr:hover {
            background-color: #dcdde1;
        }

       .invoice td {
            color: #555;
            font-weight: 500;
        }

       .invoice a.button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            margin: 0 5px;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

       .invoice a.button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?> 
<section class="invoice">
    <div class="container">
        <h2>Invoice List</h2>

        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer Name</th>
                    <th>Room Number</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($invoices->num_rows > 0): ?>
                    <?php while($row = $invoices->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['booking_id']; ?></td>
                            <td><?php echo $row['guest_name']; ?></td>
                            <td><?php echo $row['room_number']; ?></td>
                            <td><?php echo $row['check_in_date']; ?></td>
                            <td><?php echo $row['check_out_date']; ?></td>
                            <td>
                                <a href="invoice.php?booking_id=<?php echo $row['booking_id']; ?>" class="button">View Invoice</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No invoices found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </section><!-- Your linked navbar -->
</body>
</html>

<?php
$conn->close();
?>
