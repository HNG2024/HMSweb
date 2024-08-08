<?php
include 'php/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = $_POST['room_number'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $check_in_time = $_POST['check_in_time'];
    $guest_name = $_POST['guest_name'];
    $age = $_POST['age'];
    $customer_id = $_POST['customer_id'];
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

    $sql = "INSERT INTO room_booking (room_number, check_in_date, check_out_date, check_in_time, guest_name, age, customer_id, male_count, female_count, child_count, company, company_address, email, address, state, country, company_phone, customer_phone, payment_type, amount, discount, gst_percentage, total_price, advance_amount, id_proof_type, id_proof_number, segment, instructions, regular_customer)
    VALUES ('$room_number', '$check_in_date', '$check_out_date', '$check_in_time', '$guest_name', '$age', '$customer_id', '$male_count', '$female_count', '$child_count', '$company', '$company_address', '$email', '$address', '$state', '$country', '$company_phone', '$customer_phone', '$payment_type', '$amount', '$discount', '$gst_percentage', '$total_price', '$advance_amount', '$id_proof_type', '$id_proof_number', '$segment', '$instructions', '$regular_customer')";

    if ($conn->query($sql) === TRUE) {
        echo "New booking created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'index.php'; ?>
<div class="container">
    <form action="room_booking.php" method="POST" class="booking-form">
        <h2>Room Booking</h2>

        <label for="room_number">Room Number</label>
        <input type="text" id="room_number" name="room_number" required>

        <label for="check_in_date">Check-in Date</label>
        <input type="date" id="check_in_date" name="check_in_date" required>

        <label for="check_out_date">Check-out Date</label>
        <input type="date" id="check_out_date" name="check_out_date" required>

        <label for="check_in_time">Check-in Time</label>
        <input type="time" id="check_in_time" name="check_in_time" required>

        <label for="guest_name">Guest Name</label>
        <input type="text" id="guest_name" name="guest_name" required>

        <label for="age">Age</label>
        <input type="number" id="age" name="age" required>

        <label for="customer_id">Customer ID</label>
        <input type="text" id="customer_id" name="customer_id" required>

        <label for="male_count">Male Count</label>
        <input type="number" id="male_count" name="male_count" required>

        <label for="female_count">Female Count</label>
        <input type="number" id="female_count" name="female_count" required>

        <label for="child_count">Child Count</label>
        <input type="number" id="child_count" name="child_count" required>

        <label for="company">Company</label>
        <input type="text" id="company" name="company">

        <label for="company_address">Company Address</label>
        <input type="text" id="company_address" name="company_address">

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required>

        <label for="address">Address</label>
        <input type="text" id="address" name="address" required>

        <label for="state">State</label>
        <input type="text" id="state" name="state" required>

        <label for="country">Country</label>
        <input type="text" id="country" name="country" required>

        <label for="company_phone">Company Phone</label>
        <input type="text" id="company_phone" name="company_phone">

        <label for="customer_phone">Customer Phone</label>
        <input type="text" id="customer_phone" name="customer_phone" required>

        <label for="payment_type">Payment Type</label>
        <select id="payment_type" name="payment_type" required>
            <option value="CASH">CASH</option>
            <option value="CARD">CARD</option>
            <option value="ONLINE">ONLINE</option>
            <option value="POST PAID">POST PAID</option>
        </select>

        <label for="amount">Amount</label>
        <input type="text" id="amount" name="amount" required>

        <label for="discount">Discount</label>
        <input type="text" id="discount" name="discount">

        <label for="gst_percentage">GST Percentage</label>
        <select id="gst_percentage" name="gst_percentage" required>
            <option value="18%">18%</option>
            <option value="16%">16%</option>
            <option value="12%">12%</option>
            <option value="10%">10%</option>
            <option value="8%">8%</option>
            <option value="with out gst">With Out GST</option>

        </select>

        <label for="total_price">Total Price</label>
        <input type="text" id="total_price" name="total_price" readonly>

        <label for="advance_amount">Advance Amount</label>
        <input type="text" id="advance_amount" name="advance_amount">

        <label for="id_proof_type">ID Proof Type</label>
        <select id="id_proof_type" name="id_proof_type" required>
            <option value="Aadhar Card">Aadhar Card</option>
            <option value="Passport">Passport</option>
            <option value="Driving License">Driving License</option>
            <option value="Pan Card">Pan Card</option>

        </select>

        <label for="id_proof_number">ID Proof Number</label>
        <input type="text" id="id_proof_number" name="id_proof_number" required>

        <label for="segment">Segment</label>
        <select id="segment" name="segment" required>
            <option value="Walk-in">Walk-in</option>
            <option value="Online">Online Booking</option>
            <option value="Corporate booking">Corporate Booking</option>
            <option value="group booking">Group Booking</option>
            <option value="Direct booking">Direct Booking</option>
            <option value="travel agent booking">Travel Agent Booking</option>
        </select>

        <label for="instructions">Any Instructions</label>
        <textarea id="instructions" name="instructions"></textarea>

        <label for="regular_customer">Add Regular Customer</label>
        <input type="checkbox" id="regular_customer" name="regular_customer">

        <button type="submit" class="btn">Book Room</button>
    </form>
</div>

</body>
</html>
