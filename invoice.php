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

// Get the booking_id from the URL
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : '';

// Fetch the booking details from the database
$sql = "SELECT * FROM room_booking WHERE booking_id = '$booking_id'";
$result = $conn->query($sql);

// Assuming a booking was found
if ($result && $result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    $customer_name = $booking['guest_name'];
    $room_number = $booking['room_number'];
    $check_in_date = $booking['check_in_date'];
    $check_out_date = $booking['check_out_date'];
    
   
    $total_amount = $booking['total_price'];
} else {
    echo "Booking not found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        /* General Styles */
        .invoice_list {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f5f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

       .invoice_list .container {
            width: 80%;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

       .invoice_list h2 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        .invoice-details {
            margin-bottom: 30px;
        }

        .invoice-details p {
            font-size: 16px;
            margin: 5px 0;
        }

        .invoice-details span {
            font-weight: 600;
            color: #2c3e50;
        }

        /* Button Styles */
       .invoice_list button, a.button {
            background-color: #1abc9c;
            color: white;
            border: none;
            padding: 10px 25px;
            cursor: pointer;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

       .invoice_list button:hover, a.button:hover {
            background-color: #16a085;
        }

        /* Table Styles */
       .invoice_list table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ecf0f1;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

       .invoice_list th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

       .invoice_list th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }

       .invoice_list tr:nth-child(even) {
            background-color: #f2f3f4;
        }

       .invoice_list tr:hover {
            background-color: #dcdde1;
        }

        /* Print-Specific Styles */
        @media print {
            body * {
                visibility: hidden; /* Hide everything */
            }
          .invoice_list  .container, .container * {
                visibility: visible; /* Show only the container section */
            }
          .invoice_list  .container {
                position: absolute;
                top: 0;
                left: 0;
                width: 90%;
                margin-left: 5%;
                box-shadow: none;
                padding: 0;
            }

          .invoice_list  h2, button, a.button {
                display: none; /* Hide these elements during printing */
            }

          .invoice_list  table {
                border: none;
            }

          .invoice_list  th, td {
                border: 1px solid #333;
                padding: 8px;
                font-size: 12pt;
            }

          .invoice_list  tr:nth-child(even) {
                background-color: #fff;
            }

          .invoice_list  tr:nth-child(odd) {
                background-color: #f2f2f2;
            }

            /* Hide the footer information like URL path */
            @page {
                margin: 0;
            }
            body {
                margin: 1cm;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?> 
    <section class="invoice_list">
    <div class="container">
        <h2>Invoice</h2>

        <div class="invoice-details">
            <p><span>Booking ID:</span> <?php echo $booking_id; ?></p>
            <p><span>Customer Name:</span> <?php echo $customer_name; ?></p>
            <p><span>Room Number:</span> <?php echo $room_number; ?></p>
            <p><span>Check-in Date:</span> <?php echo $check_in_date; ?></p>
            <p><span>Check-out Date:</span> <?php echo $check_out_date; ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                   
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Room Charges</td>
                   
                    <td>$<?php echo number_format($total_amount, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <p style="text-align: right; font-size: 18px;"><strong>Total Amount Due: $<?php echo number_format($total_amount, 2); ?></strong></p>

        <!-- Print and Download Buttons -->
        <a href="#" onclick="window.print()" class="button">Print Invoice</a>
        <a href="download_invoice.php?booking_id=<?php echo $booking_id; ?>" class="button">Download Invoice</a>
    </div>
   </section>
</body>
</html>

<?php
$conn->close();
?>