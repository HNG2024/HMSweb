<?php
include('session.php');

// Check if the user is logged in
if (!isset($_SESSION['login_user'])) {
    header('Location: https://hgstore.in/login71.php');
    exit();
}

// Database connection
require 'connection.php';
$conn = Connect();
$conn->select_db($u_id1);
$row = null; // Initialize $row to null

// Check if the booking_id parameter is set and fetch the booking details
if (isset($_GET['booking_id'])) {
    $id = $_GET['booking_id'];
    $sql = "SELECT * FROM room_booking WHERE booking_id = ?";
    
    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "<script>showError('Error preparing the statement: " . htmlspecialchars($conn->error) . "', 'error');</script>";
        exit();
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<script>showError('No record found!', 'error');</script>";
        exit();
    }
} else {
    echo "<script>showError('No ID provided!', 'error');</script>";
    exit();
}

// Handle the form submission for updating the booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure the id is carried over from the hidden input field
    $id = $_POST['booking_id'];
    
    // Prepare and sanitize form inputs
    $room_number = json_encode(array_map('trim', explode(',', $_POST['room_number'])));
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $check_in_time = $_POST['check_in_time'];
    $guest_name = $_POST['guest_name'];
    $customer_id = $_POST['customer_id'];
    $age = $_POST['age'];
    $male_count = $_POST['male_count'];
    $female_count = $_POST['female_count'];
    $child_count = $_POST['child_count'];
    $company = $_POST['company'];
    $company_address = $_POST['company_address'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $company_phone = $_POST['company_phone'];
    $customer_phone = $_POST['customer_phone'];
    $payment_type = $_POST['payment_type'];
    $amount = $_POST['amount'];
    $discount = $_POST['discount'];
    $gst_percentage = $_POST['gst_percentage'];
    $total_price = $_POST['total_price'];
    $advance_amount = $_POST['advance_amount'];
    $id_proof_type = $_POST['id_proof_type'];
    $id_proof_number = $_POST['id_proof_number'];
    $segment = $_POST['segment'];
    $instructions = $_POST['instructions'];
    $regular_customer = isset($_POST['regular_customer']) ? 1 : 0;
    $food_plan = $_POST['food_plan'];
    $late_checkin = $_POST['late_checkin'];
    $grace = $_POST['grace'];
    $used_products = $_POST['used_products'];
    $used_product_price = $_POST['used_product_price'];
    $extend = $_POST['extend'];
    $damaged_items_price = $_POST['damaged_items_price'];
    $check_out_info = $_POST['check_out_info'];

    // Update the booking details in the database using a prepared statement
    $sql = "UPDATE room_booking 
            SET room_number = ?, check_in_date = ?, check_out_date = ?, check_in_time = ?, guest_name = ?, customer_id = ?, 
            age = ?, male_count = ?, female_count = ?, child_count = ?, company = ?, company_address = ?, email = ?, 
            address = ?, state = ?, country = ?, company_phone = ?, customer_phone = ?, payment_type = ?, amount = ?, 
            discount = ?, gst_percentage = ?, total_price = ?, advance_amount = ?, id_proof_type = ?, id_proof_number = ?, 
            segment = ?, instructions = ?, regular_customer = ?, food_plan = ?, late_checkin = ?, grace = ?, used_products = ?, 
            used_product_price = ?, extend = ?, damaged_items_price = ?, check_out_info = ? 
            WHERE booking_id = ?";
            
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "<script>showError('Error preparing the statement: " . htmlspecialchars($conn->error) . "', 'error');</script>";
    } else {
        // Bind the parameters to the placeholders in the SQL query
        $stmt->bind_param(
            "ssssssssssssssssssssssssssssssssssssss", 
            $room_number, $check_in_date, $check_out_date, $check_in_time, $guest_name, $customer_id, $age, $male_count, $female_count, 
            $child_count, $company, $company_address, $email, $address, $state, $country, $company_phone, $customer_phone, $payment_type, 
            $amount, $discount, $gst_percentage, $total_price, $advance_amount, $id_proof_type, $id_proof_number, $segment, $instructions, 
            $regular_customer, $food_plan, $late_checkin, $grace, $used_products, $used_product_price, $extend, $damaged_items_price, 
            $check_out_info, $id
        );

        if ($stmt->execute()) {
            echo "<script>showError('Booking updated successfully', 'success');</script>";
        } else {
            echo "<script>showError('Error: " . htmlspecialchars($stmt->error) . "', 'error');</script>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 900px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            max-height: 90vh;
        }

        .container h2 {
            text-align: center;
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .booking-form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .booking-form label {
            width: 100%;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        .booking-form input,
        .booking-form select,
        .booking-form textarea {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .booking-form input:focus,
        .booking-form select:focus,
        .booking-form textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.1);
            background-color: #fff;
        }

        .booking-form input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }

        .booking-form button {
            width: 100%;
            padding: 15px;
            background-color: #007bff;
            color: white;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .booking-form button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .booking-form button:active {
            background-color: #004494;
            transform: translateY(0);
        }

        @media screen and (min-width: 768px) {
            .booking-form label, 
            .booking-form input,
            .booking-form select,
            .booking-form textarea {
                width: calc(50% - 10px);
            }

            .booking-form label:nth-child(odd), 
            .booking-form input:nth-child(odd),
            .booking-form select:nth-child(odd),
            .booking-form textarea:nth-child(odd) {
                margin-right: 0;
            }

            .booking-form .full-width {
                width: 100%;
            }
        }

        @media screen and (max-width: 480px) {
            .container {
                padding: 20px;
            }

            .booking-form button {
                font-size: 16px;
            }
        }
        .error-message-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: none;
        }

        .error-message {
            background-color: #f44336; /* Red background for error */
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .error-message.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .error-message.success {
            background-color: #4CAF50; /* Green background for success */
        }
    </style>
</head>
<body>

<div class="container">
    <form action="edit_booking.php?booking_id=<?php echo $row['booking_id']; ?>" method="POST" class="booking-form">
        <h2>Edit Booking</h2>

        <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">

        <label for="room_number">Room Number</label>
        <?php
            $room_numbers = json_decode($row['room_number'], true);
            $room_numbers_readable = is_array($room_numbers) ? implode(', ', $room_numbers) : '';
        ?>
        <input type="text" id="room_number" name="room_number" value="<?php echo htmlspecialchars($room_numbers_readable); ?>" required>

        <label for="check_in_date">Check-in Date</label>
        <input type="date" id="check_in_date" name="check_in_date" value="<?php echo htmlspecialchars($row['check_in_date']); ?>" required>

        <label for="check_out_date">Check-out Date</label>
        <input type="date" id="check_out_date" name="check_out_date" value="<?php echo htmlspecialchars($row['check_out_date']); ?>" required>

        <label for="check_in_time">Check-in Time</label>
        <input type="time" id="check_in_time" name="check_in_time" value="<?php echo htmlspecialchars($row['check_in_time']); ?>" required>

        <label for="guest_name">Guest Name</label>
        <input type="text" id="guest_name" name="guest_name" value="<?php echo htmlspecialchars($row['guest_name']); ?>" required>

        <label for="customer_id">Customer ID</label>
        <input type="text" id="customer_id" name="customer_id" value="<?php echo htmlspecialchars($row['customer_id']); ?>" required>

        <label for="age">Age</label>
        <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($row['age']); ?>">

        <label for="male_count">Male Count</label>
        <input type="number" id="male_count" name="male_count" value="<?php echo htmlspecialchars($row['male_count']); ?>">

        <label for="female_count">Female Count</label>
        <input type="number" id="female_count" name="female_count" value="<?php echo htmlspecialchars($row['female_count']); ?>">

        <label for="child_count">Child Count</label>
        <input type="number" id="child_count" name="child_count" value="<?php echo htmlspecialchars($row['child_count']); ?>">

        <label for="company">Company</label>
        <input type="text" id="company" name="company" value="<?php echo htmlspecialchars($row['company']); ?>">

        <label for="company_address">Company Address</label>
        <textarea id="company_address" name="company_address"><?php echo htmlspecialchars($row['company_address']); ?></textarea>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">

        <label for="address">Address</label>
        <textarea id="address" name="address"><?php echo htmlspecialchars($row['address']); ?></textarea>

        <label for="state">State</label>
        <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($row['state']); ?>">

        <label for="country">Country</label>
        <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($row['country']); ?>">

        <label for="company_phone">Company Phone</label>
        <input type="text" id="company_phone" name="company_phone" value="<?php echo htmlspecialchars($row['company_phone']); ?>">

        <label for="customer_phone">Customer Phone</label>
        <input type="text" id="customer_phone" name="customer_phone" value="<?php echo htmlspecialchars($row['customer_phone']); ?>">

        <label for="payment_type">Payment Type</label>
        <select id="payment_type" name="payment_type" required>
            <option value="CASH" <?php echo ($row['payment_type'] == 'CASH') ? 'selected' : ''; ?>>CASH</option>
            <option value="CARD" <?php echo ($row['payment_type'] == 'CARD') ? 'selected' : ''; ?>>CARD</option>
            <option value="ONLINE" <?php echo ($row['payment_type'] == 'ONLINE') ? 'selected' : ''; ?>>ONLINE</option>
            <option value="POST PAID" <?php echo ($row['payment_type'] == 'POST PAID') ? 'selected' : ''; ?>>POST PAID</option>
        </select>

        <label for="amount">Amount</label>
        <input type="number" step="0.01" id="amount" name="amount" value="<?php echo htmlspecialchars($row['amount']); ?>" required>

        <label for="discount">Discount</label>
        <input type="number" step="0.01" id="discount" name="discount" value="<?php echo htmlspecialchars($row['discount']); ?>">

        <label for="gst_percentage">GST Percentage</label>
        <input type="number" step="0.01" id="gst_percentage" name="gst_percentage" value="<?php echo htmlspecialchars($row['gst_percentage']); ?>">

        <label for="total_price">Total Price</label>
        <input type="number" step="0.01" id="total_price" name="total_price" value="<?php echo htmlspecialchars($row['total_price']); ?>">

        <label for="advance_amount">Advance Amount</label>
        <input type="number" step="0.01" id="advance_amount" name="advance_amount" value="<?php echo htmlspecialchars($row['advance_amount']); ?>">

        <label for="id_proof_type">ID Proof Type</label>
        <select id="id_proof_type" name="id_proof_type" required>
            <option value="Aadhar Card" <?php echo ($row['id_proof_type'] == 'Aadhar Card') ? 'selected' : ''; ?>>Aadhar Card</option>
            <option value="Passport" <?php echo ($row['id_proof_type'] == 'Passport') ? 'selected' : ''; ?>>Passport</option>
            <option value="Driving License" <?php echo ($row['id_proof_type'] == 'Driving License') ? 'selected' : ''; ?>>Driving License</option>
            <option value="Pan Card" <?php echo ($row['id_proof_type'] == 'Pan Card') ? 'selected' : ''; ?>>Pan Card</option>
        </select>

        <label for="id_proof_number">ID Proof Number</label>
        <input type="text" id="id_proof_number" name="id_proof_number" value="<?php echo htmlspecialchars($row['id_proof_number']); ?>" required>

        <label for="segment">Segment</label>
        <select id="segment" name="segment">
            <option value="Walk-in" <?php echo ($row['segment'] == 'Walk-in') ? 'selected' : ''; ?>>Walk-in</option>
            <option value="Online" <?php echo ($row['segment'] == 'Online') ? 'selected' : ''; ?>>Online</option>
            <option value="Corporate booking" <?php echo ($row['segment'] == 'Corporate booking') ? 'selected' : ''; ?>>Corporate booking</option>
            <option value="group booking" <?php echo ($row['segment'] == 'group booking') ? 'selected' : ''; ?>>Group booking</option>
            <option value="Direct booking" <?php echo ($row['segment'] == 'Direct booking') ? 'selected' : ''; ?>>Direct booking</option>
            <option value="travel agent booking" <?php echo ($row['segment'] == 'travel agent booking') ? 'selected' : ''; ?>>Travel agent booking</option>
        </select>

        <label for="instructions">Instructions</label>
        <textarea id="instructions" name="instructions"><?php echo htmlspecialchars($row['instructions']); ?></textarea>

        <label for="regular_customer">Regular Customer</label>
        <input type="checkbox" id="regular_customer" name="regular_customer" <?php echo ($row['regular_customer'] == 1) ? 'checked' : ''; ?>>

        <label for="food_plan">Food Plan</label>
        <input type="text" id="food_plan" name="food_plan" value="<?php echo htmlspecialchars($row['food_plan']); ?>">

        <label for="late_checkin">Late Check-in</label>
        <input type="datetime-local" id="late_checkin" name="late_checkin" value="<?php echo htmlspecialchars($row['late_checkin']); ?>">

        <label for="grace">Grace</label>
        <input type="text" id="grace" name="grace" value="<?php echo htmlspecialchars($row['grace'] ?? ''); ?>">

        <label for="used_products">Used Products</label>
        <textarea id="used_products" name="used_products"><?php echo htmlspecialchars($row['used_products'] ?? ''); ?></textarea>

        <label for="used_product_price">Used Product Price</label>
        <input type="text" id="used_product_price" name="used_product_price" value="<?php echo htmlspecialchars($row['used_product_price'] ?? ''); ?>">

        <label for="extend">Extend</label>
        <input type="text" id="extend" name="extend" value="<?php echo htmlspecialchars($row['extend'] ?? ''); ?>">

        <label for="damaged_items_price">Damaged Items Price</label>
        <input type="text" id="damaged_items_price" name="damaged_items_price" value="<?php echo htmlspecialchars($row['damaged_items_price'] ?? ''); ?>">

        <label for="check_out_info">Check-out Info</label>
        <input type="datetime-local" id="check_out_info" name="check_out_info" value="<?php echo htmlspecialchars($row['check_out_info'] ?? ''); ?>">

        <button type="submit" class="btn">Update Booking</button>
    </form>
    <!-- Error Message Container -->
    <div id="error-message-container" class="error-message-container">
        <div id="error-message" class="error-message">
            <!-- Error message will be dynamically inserted here -->
        </div>
    </div>
</div>

<script>
function showError(message, type = 'error') {
    const errorMessageContainer = document.getElementById('error-message-container');
    const errorMessage = document.getElementById('error-message');

    if (type === 'success') {
        errorMessage.classList.add('success');
    } else {
        errorMessage.classList.remove('success');
    }

    errorMessage.textContent = message;
    errorMessageContainer.style.display = 'block';

    // Add the 'show' class to trigger the animation
    errorMessage.classList.add('show');

    // Remove the message after 3 seconds
    setTimeout(() => {
        errorMessage.classList.remove('show');
        setTimeout(() => errorMessageContainer.style.display = 'none', 500); // Delay hiding to finish the animation
    }, 3000);
}
</script>
</body>
</html>
