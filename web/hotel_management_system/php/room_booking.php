<?php
include 'db_connection.php';

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

    // Set initial status to Reserved
    $status = "Reserved";

    $sql = "INSERT INTO room_booking (room_number, check_in_date, check_out_date, check_in_time, guest_name, age, customer_id, male_count, female_count, child_count, company, company_address, email, address, state, country, company_phone, customer_phone, payment_type, amount, discount, gst_percentage, total_price, advance_amount, id_proof_type, id_proof_number, segment, instructions, regular_customer, status)
    VALUES ('$room_number', '$check_in_date', '$check_out_date', '$check_in_time', '$guest_name', '$age', '$customer_id', '$male_count', '$female_count', '$child_count', '$company', '$company_address', '$email', '$address', '$state', '$country', '$company_phone', '$customer_phone', '$payment_type', '$amount', '$discount', '$gst_percentage', '$total_price', '$advance_amount', '$id_proof_type', '$id_proof_number', '$segment', '$instructions', '$regular_customer', '$status')";

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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        /* Variables for Light and Dark Modes */
        :root {
            --background-color: #f0f0f0;
            --text-color: #333;
            --container-bg-color: #ffffff;
            --input-bg-color: #f8f8f8;
            --input-border-color: #ccc;
            --input-focus-border-color: #1E88E5;
            --button-bg-color: #1E88E5;
            --button-hover-bg-color: #1976D2;
            --navbar-bg-color: #1E88E5;
            --navbar-hover-bg-color: #1976D2;
            --footer-bg-color: #1E88E5;
            --footer-hover-bg-color: #1976D2;
            --dropdown-bg-color: #ffffff;
            --dropdown-hover-bg-color: #f0f0f0;
        }

        .dark-mode {
            --background-color: #121212;
            --text-color: #ffffff;
            --container-bg-color: #1e1e1e;
            --input-bg-color: #2e2e2e;
            --input-border-color: #444;
            --input-focus-border-color: #bb86fc;
            --button-bg-color: #bb86fc;
            --button-hover-bg-color: #9e67e3;
            --navbar-bg-color: #333333;
            --navbar-hover-bg-color: #444444;
            --footer-bg-color: #333333;
            --footer-hover-bg-color: #444444;
            --dropdown-bg-color: #2e2e2e;
            --dropdown-hover-bg-color: #37474f;
        }

        /* Navigation Bar */
        .navbar {
            width: 100%;
            background-color: var(--navbar-bg-color);
            overflow: hidden;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-size: 17px;
        }

        .navbar a:hover {
            background-color: var(--navbar-hover-bg-color);
            color: white;
        }

        .navbar .icon {
            display: none;
        }

        /* Dropdown Container */
        .dropdown {
            float: left;
            overflow: hidden;
        }

        .dropdown .dropbtn {
            font-size: 17px;    
            border: none;
            outline: none;
            color: white;
            padding: 14px 20px;
            background-color: inherit;
            font-family: inherit;
            margin: 0;
        }

        .navbar a:hover, .dropdown:hover .dropbtn {
            background-color: var(--navbar-hover-bg-color);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: var(--dropdown-bg-color);
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            float: none;
            color: var(--text-color);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .dropdown-content a:hover {
            background-color: var(--dropdown-hover-bg-color);
            color: var(--text-color);
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Responsive layout for the navbar */
        @media screen and (max-width: 768px) {
            .navbar a:not(:first-child) {
                display: none;
            }
            .navbar a.icon {
                float: right;
                display: block;
            }
        }

        @media screen and (max-width: 768px) {
            .navbar.responsive {
                position: relative;
            }
            .navbar.responsive .icon {
                position: absolute;
                right: 0;
                top: 0;
            }
            .navbar.responsive a, .navbar.responsive .dropdown {float: none;}
            .navbar.responsive .dropdown-content {position: relative;}
            .navbar.responsive .dropdown .dropbtn {
                display: block;
                width: 100%;
                text-align: left;
            }
        }

        .container {
            max-width: 800px; 
            width: 100%;
            margin: 50px auto;
            padding: 30px; 
            background-color: var(--container-bg-color);
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 26px;
            color: var(--text-color);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: var(--text-color);
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="number"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid var(--input-border-color);
            border-radius: 6px;
            background-color: var(--input-bg-color);
            font-size: 16px; 
            color: var(--text-color);
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="time"]:focus,
        input[type="number"]:focus,
        input[type="email"]:focus,
        select:focus,
        textarea:focus {
            border-color: var(--input-focus-border-color);
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        button.btn {
            width: 100%;
            padding: 15px; 
            background-color: var(--button-bg-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px; 
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button.btn:hover {
            background-color: var(--button-hover-bg-color);
            transform: translateY(-2px);
        }

        button.btn:active {
            transform: translateY(0);
        }

        input[type="checkbox"] {
            margin-right: 10px;
        }

        textarea {
            resize: vertical;
        }

        /* Footer */
        .footer {
            background-color: var(--footer-bg-color);
            color: white;
            text-align: center;
            padding: 20px 0;
            position: relative;
            width: 100%;
            bottom: 0;
            box-shadow: 0px -2px 8px rgba(0, 0, 0, 0.1);
        }

        .footer .social-icons a {
            margin: 0 10px;
            color: white;
            font-size: 24px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer .social-icons a:hover {
            color: var(--footer-hover-bg-color);
        }

        .footer p {
            margin: 10px 0;
        }

        .footer .links a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }

        .footer .links a:hover {
            text-decoration: underline;
        }

        /* Dark Mode Toggle Button */
        .toggle-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: var(--button-bg-color);
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .toggle-btn:hover {
            background-color: var(--button-hover-bg-color);
        }

        /* Focus Styles for Accessibility */
        a:focus, input:focus, select:focus, textarea:focus, button:focus {
            outline: 2px solid var(--input-focus-border-color);
            outline-offset: 2px;
        }
    </style>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Dark Mode Toggle Button -->
<button class="toggle-btn" onclick="toggleDarkMode()">Toggle Dark Mode</button>

<!-- Responsive Navigation -->
<nav class="navbar" id="myNavbar" role="navigation">
    <a href="#home" aria-label="Home">Home</a>
    <a href="#booking" aria-label="Booking">Booking</a>
    <div class="dropdown">
        <button class="dropbtn">Complementary Services 
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-content">
            <a href="#"><i class="fas fa-wifi"></i> Free Wi-Fi</a>
            <a href="#"><i class="fas fa-coffee"></i> Complimentary Breakfast</a>
            <a href="#"><i class="fas fa-swimming-pool"></i> Access to Swimming Pool</a>
            <a href="#"><i class="fas fa-dumbbell"></i> Gym Access</a>
            <a href="#"><i class="fas fa-parking"></i> Free Parking</a>
            <a href="#"><i class="fas fa-concierge-bell"></i> 24/7 Room Service</a>
            <a href="#"><i class="fas fa-umbrella-beach"></i> Beach Access</a>
            <a href="#"><i class="fas fa-tv"></i> Free Cable TV</a>
        </div>
    </div> 
    <a href="#contact" aria-label="Contact">Contact</a>
    <a href="#about" aria-label="About">About</a>
    <a href="javascript:void(0);" class="icon" onclick="toggleNavbar()" aria-label="Toggle Navigation Menu">
        &#9776;
    </a>
</nav>

<main class="container">
    <form action="room_booking.php" method="POST" class="booking-form" aria-labelledby="form-title">
        <h2 id="form-title">Room Booking</h2>

        <!-- Form fields -->
        <label for="room_number">Room Number</label>
        <input type="text" id="room_number" name="room_number" required aria-required="true">

        <label for="check_in_date">Check-in Date</label>
        <input type="date" id="check_in_date" name="check_in_date" required aria-required="true">

        <label for="check_out_date">Check-out Date</label>
        <input type="date" id="check_out_date" name="check_out_date" required aria-required="true">

        <label for="check_in_time">Check-in Time</label>
        <input type="time" id="check_in_time" name="check_in_time" required aria-required="true">

        <label for="guest_name">Guest Name</label>
        <input type="text" id="guest_name" name="guest_name" required aria-required="true">

        <label for="age">Age</label>
        <input type="number" id="age" name="age" required aria-required="true">

        <label for="customer_id">Customer ID</label>
        <input type="text" id="customer_id" name="customer_id" required aria-required="true">

        <label for="male_count">Male Count</label>
        <input type="number" id="male_count" name="male_count" required aria-required="true">

        <label for="female_count">Female Count</label>
        <input type="number" id="female_count" name="female_count" required aria-required="true">

        <label for="child_count">Child Count</label>
        <input type="number" id="child_count" name="child_count" required aria-required="true">

        <label for="company">Company</label>
        <input type="text" id="company" name="company">

        <label for="company_address">Company Address</label>
        <input type="text" id="company_address" name="company_address">

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required aria-required="true">

        <label for="address">Address</label>
        <input type="text" id="address" name="address" required aria-required="true">

        <label for="state">State</label>
        <input type="text" id="state" name="state" required aria-required="true">

        <label for="country">Country</label>
        <input type="text" id="country" name="country" required aria-required="true">

        <label for="company_phone">Company Phone</label>
        <input type="text" id="company_phone" name="company_phone">

        <label for="customer_phone">Customer Phone</label>
        <input type="text" id="customer_phone" name="customer_phone" required aria-required="true">

        <label for="payment_type">Payment Type</label>
        <select id="payment_type" name="payment_type" required aria-required="true">
            <option value="CASH">CASH</option>
            <option value="CARD">CARD</option>
            <option value="ONLINE">ONLINE</option>
            <option value="POST PAID">POST PAID</option>
        </select>

        <label for="amount">Amount</label>
        <input type="text" id="amount" name="amount" required aria-required="true">

        <label for="discount">Discount</label>
        <input type="text" id="discount" name="discount">

        <label for="gst_percentage">GST Percentage</label>
        <select id="gst_percentage" name="gst_percentage" required aria-required="true">
            <option value="18%">18%</option>
            <option value="16%">16%</option>
            <option value="12%">12%</option>
            <option value="10%">10%</option>
            <option value="8%">8%</option>
            <option value="with out gst">With Out GST</option>
        </select>

        <label for="total_price">Total Price</label>
        <input type="text" id="total_price" name="total_price" readonly aria-readonly="true">

        <label for="advance_amount">Advance Amount</label>
        <input type="text" id="advance_amount" name="advance_amount">

        <label for="id_proof_type">ID Proof Type</label>
        <select id="id_proof_type" name="id_proof_type" required aria-required="true">
            <option value="Aadhar Card">Aadhar Card</option>
            <option value="Passport">Passport</option>
            <option value="Driving License">Driving License</option>
            <option value="Pan Card">Pan Card</option>
        </select>

        <label for="id_proof_number">ID Proof Number</label>
        <input type="text" id="id_proof_number" name="id_proof_number" required aria-required="true">

        <label for="segment">Segment</label>
        <select id="segment" name="segment" required aria-required="true">
            <option value="Walk-in">Walk-in</option>
            <option value="Online">Online Booking</option>
            <option value="Corporate booking">Corporate Booking</option>
            <option value="group booking">Group Booking</option>
            <option value="Direct booking">Direct Booking</option>
            <option value="travel agent booking">Travel Agent Booking</option>
        </select>

        <label for="instructions">Any Instructions</label>
        <textarea id="instructions" name="instructions"></textarea>

        <label for="regular_customer">
            <input type="checkbox" id="regular_customer" name="regular_customer"> Add Regular Customer
        </label>

        <label for="breakfast">Breakfast</label>
        <select id="breakfast" name="breakfast" aria-label="Select Breakfast">
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>

        <label for="lunch">Lunch</label>
        <select id="lunch" name="lunch" aria-label="Select Lunch">
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>

        <label for="dinner">Dinner</label>
        <select id="dinner" name="dinner" aria-label="Select Dinner">
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>

        <button type="submit" class="btn">Book Room</button>
    </form>
</main>

<!-- Footer -->
<footer class="footer">
    <div class="social-icons" role="navigation" aria-label="Social Media Links">
        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
    </div>
    <p>&copy; 2024 Room Booking System | All Rights Reserved</p>
    <nav class="links" role="navigation" aria-label="Footer Links">
        <a href="#privacy">Privacy Policy</a> | 
        <a href="#terms">Terms of Service</a> | 
        <a href="#contact">Contact Us</a>
    </nav>
</footer>

<script>
    function toggleNavbar() {
        var x = document.getElementById("myNavbar");
        if (x.className === "navbar") {
            x.className += " responsive";
        } else {
            x.className = "navbar";
        }
    }

    function toggleDarkMode() {
        document.body.classList.toggle("dark-mode");
    }
</script>

</body>
</html>
