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

// Fetch available rooms
$roomOptions = [];
$query = "SELECT room_no, floor_no FROM roominfo WHERE status = 'available'";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $roomOptions[] = $row;
    }
} else {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showError('Error fetching rooms: " . mysqli_error($conn) . "', 'error');
        });
    </script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize form inputs
    $check_in_date = mysqli_real_escape_string($conn, $_POST['check_in_date']);
    $check_out_date = mysqli_real_escape_string($conn, $_POST['check_out_date']);
    $check_in_time = mysqli_real_escape_string($conn, $_POST['check_in_time']);
    $guest_name = mysqli_real_escape_string($conn, $_POST['guest_name']);
    $age = isset($_POST['age']) && $_POST['age'] !== '' ? mysqli_real_escape_string($conn, $_POST['age']) : NULL;
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $male_count = isset($_POST['male_count']) && $_POST['male_count'] !== '' ? mysqli_real_escape_string($conn, $_POST['male_count']) : '0';
    $female_count = isset($_POST['female_count']) && $_POST['female_count'] !== '' ? mysqli_real_escape_string($conn, $_POST['female_count']) : '0';
    $child_count = isset($_POST['child_count']) && $_POST['child_count'] !== '' ? mysqli_real_escape_string($conn, $_POST['child_count']) : '0';
    $company = isset($_POST['company']) ? mysqli_real_escape_string($conn, $_POST['company']) : NULL;
    $company_address = isset($_POST['company_address']) ? mysqli_real_escape_string($conn, $_POST['company_address']) : NULL;
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : NULL;
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $company_phone = isset($_POST['company_phone']) ? mysqli_real_escape_string($conn, $_POST['company_phone']) : NULL;
    $customer_phone = isset($_POST['customer_phone']) ? mysqli_real_escape_string($conn, $_POST['customer_phone']) : NULL;
    $payment_type = mysqli_real_escape_string($conn, $_POST['payment_type']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $discount = isset($_POST['discount']) && $_POST['discount'] !== '' ? mysqli_real_escape_string($conn, $_POST['discount']) : '0';
    $gst_percentage = mysqli_real_escape_string($conn, $_POST['gst_percentage']);
    $total_price = mysqli_real_escape_string($conn, $_POST['total_price']);
    $advance_amount = isset($_POST['advance_amount']) && $_POST['advance_amount'] !== '' ? mysqli_real_escape_string($conn, $_POST['advance_amount']) : '0';
    $id_proof_type = mysqli_real_escape_string($conn, $_POST['id_proof_type']);
    $id_proof_number = mysqli_real_escape_string($conn, $_POST['id_proof_number']);
    $segment = isset($_POST['segment']) ? mysqli_real_escape_string($conn, $_POST['segment']) : NULL;
    $instructions = isset($_POST['instructions']) ? mysqli_real_escape_string($conn, $_POST['instructions']) : NULL;
    $regular_customer = isset($_POST['regular_customer']) ? 1 : 0;
    $food_plan = isset($_POST['food_plan']) ? mysqli_real_escape_string($conn, $_POST['food_plan']) : NULL;

    // Sanitize room numbers array
    $room_number_array = isset($_POST['room_number']) ? $_POST['room_number'] : [];
    $sanitized_room_numbers = array_map(function($room_no) use ($conn) {
        return mysqli_real_escape_string($conn, $room_no);
    }, $room_number_array);

    // Convert array to a comma-separated string
    $room_numbers_list = implode("','", $sanitized_room_numbers);

    // Step 1: Check Room Status
    $room_status_query = "SELECT room_no, status FROM roominfo WHERE room_no IN ('$room_numbers_list')";
    $status_result = mysqli_query($conn, $room_status_query);

    $invalid_rooms = [];
    while ($row = mysqli_fetch_assoc($status_result)) {
        if ($row['status'] != 'available') {
            $invalid_rooms[] = $row['room_no'];
        }
    }

    // Step 2: Validate Room Status
    if (!empty($invalid_rooms)) {
        $invalid_rooms_list = implode(', ', $invalid_rooms);
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showError('The following rooms are not available: $invalid_rooms_list', 'error');
            });
        </script>";
    } else {
        // Proceed with booking if all rooms are available

        // Generate customer_id if it's empty
        if (empty($customer_id)) {
            $customer_id = strtoupper(substr($guest_name, 0, 1)) . strtoupper(substr($guest_name, -1)) . date('dmY');
        }

        $booking_id = $u_id . $customer_id;

        // Set initial status to Reserved
        $status = "Reserved";

        // SQL query to create the `room_booking` table if it doesn't exist
        $sql_create_table = "CREATE TABLE IF NOT EXISTS room_booking (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            room_number VARCHAR(255) NOT NULL,
            check_in_date DATE NOT NULL,
            check_out_date DATE NOT NULL,
            check_in_time TIME NOT NULL,
            guest_name VARCHAR(255) NOT NULL,
            customer_id VARCHAR(255) NOT NULL,
            age INT NULL,
            male_count INT NULL,
            female_count INT NULL,
            child_count INT NULL,
            company VARCHAR(255) NULL,
            company_address TEXT NULL,
            email VARCHAR(255) NULL,
            address TEXT NULL,
            state VARCHAR(100) NULL,
            country VARCHAR(100) NULL,
            company_phone VARCHAR(20) NULL,
            customer_phone VARCHAR(20) NULL,
            payment_type ENUM('CASH', 'CARD', 'ONLINE', 'POST PAID') NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            discount DECIMAL(10, 2) NULL,
            gst_percentage DECIMAL(5, 2) NULL,
            total_price DECIMAL(10, 2) NULL,
            advance_amount DECIMAL(10, 2) NULL,
            id_proof_type ENUM('Aadhar Card', 'Passport', 'Driving License', 'Pan Card') NOT NULL,
            id_proof_number VARCHAR(100) NOT NULL,
            segment ENUM('Walk-in', 'Online', 'Corporate booking', 'group booking', 'Direct booking', 'travel agent booking') NULL,
            instructions TEXT NULL,
            regular_customer TINYINT(1) DEFAULT 0,
            status VARCHAR(50) NOT NULL DEFAULT 'Reserved',
            booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            food_plan VARCHAR(255) NULL,
            booking_id VARCHAR(255) NULL,
            late_checkin datetime NULL,
            grace varchar(255) NULL,
            used_products varchar(500) NULL,
            used_product_price varchar(500) NULL,
            extend varchar(255) NULL,
            damaged_items_price varchar(255) NULL,
            check_out_info DATETIME NULL
        )";

        // Execute the table creation query
        if ($conn->query($sql_create_table) === FALSE) {
            die("Error creating table: " . $conn->error);
        }

        // Convert the array of room numbers into a JSON string to store in a single field
        $room_numbers_json = json_encode($sanitized_room_numbers);

        // SQL query to insert the booking information
        $sql_insert = "INSERT INTO room_booking (room_number, check_in_date, check_out_date, check_in_time, guest_name, age, customer_id, male_count, female_count, child_count, company, company_address, email, address, state, country, company_phone, customer_phone, payment_type, amount, discount, gst_percentage, total_price, advance_amount, id_proof_type, id_proof_number, segment, instructions, regular_customer, status, food_plan, booking_id)
        VALUES ('$room_numbers_json', '$check_in_date', '$check_out_date', '$check_in_time', '$guest_name', '$age', '$customer_id', '$male_count', '$female_count', '$child_count', '$company', '$company_address', '$email', '$address', '$state', '$country', '$company_phone', '$customer_phone', '$payment_type', '$amount', '$discount', '$gst_percentage', '$total_price', '$advance_amount', '$id_proof_type', '$id_proof_number', '$segment', '$instructions', '$regular_customer', '$status', '$food_plan', '$booking_id')";

        // SQL query to update the status of the rooms
        $sql_update = "UPDATE roominfo SET booking_id = '$booking_id', status = 'reserved' WHERE room_no IN ('$room_numbers_list')";

        // Execute the insert and update queries
        if ($conn->query($sql_insert) === TRUE && $conn->query($sql_update) === TRUE) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showError('New booking created successfully', 'success');
                });
            </script>";
        } else {
            $errorMessage = "Error: " . $sql_insert . "<br>" . $conn->error;
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showError('$errorMessage', 'error');
                });
            </script>";
        }
    }

    // Close the database connection
    $conn->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* General Styles */
        .roomBook_container {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f9;
            color: #333;
        }

        .roomBook_container1 {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .roomBook_container1 .booking-form {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }

        .roomBook_container1 h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 26px;
            color: #1E88E5;
        }

        .roomBook_container1 label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        .roomBook_container1 input,
        .roomBook_container1 select,
        .roomBook_container1 textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #d1d1d1;
            border-radius: 6px;
            background-color: #f9f9f9;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .roomBook_container1 input:focus,
        .roomBook_container1 select:focus,
        .roomBook_container1 textarea:focus {
            border-color: #1E88E5;
            background-color: #ffffff;
            outline: none;
            box-shadow: 0 0 5px rgba(30, 136, 229, 0.2);
        }

        .roomBook_container1 button.btn {
            width: 100%;
            padding: 15px;
            background-color: #1E88E5;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .roomBook_container1 button.btn:hover {
            background-color: #1976D2;
            transform: translateY(-2px);
        }

        .roomBook_container1 button.btn:active {
            transform: translateY(0);
        }

        /* Modal Styles */
        .roomBook_container1 .modal {
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

        .roomBook_container1 .modal-content {
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

        .roomBook_container1 .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .roomBook_container1 .close:hover,
        .roomBook_container1 .close:focus {
            color: #000;
        }

        .roomBook_container1 #modal-title {
            margin-bottom: 20px;
            font-size: 24px;
            color: #007bff;
            text-align: center;
        }

        .roomBook_container1 #roomOptionsContainer {
            max-height: 300px;
            overflow-y: auto;
        }
.roomBook_container1 .premium-checkbox{
    display: flex;
    
}
        /* Checkbox Styles */
        .roomBook_container1 .checkbox-wrapper-5 .check {
            --size: 30px;
            position: relative;
            background: linear-gradient(90deg, #f19af3, #f099b5);
            line-height: 0;
            perspective: 400px;
            font-size: var(--size);
            height: 30px;
        }

        .roomBook_container1 .checkbox-wrapper-5 .check input[type="checkbox"],
        .roomBook_container1 .checkbox-wrapper-5 .check label,
        .roomBook_container1 .checkbox-wrapper-5 .check label::before,
        .roomBook_container1 .checkbox-wrapper-5 .check label::after,
        .roomBook_container1 .checkbox-wrapper-5 .check {
            appearance: none;
            display: inline-block;
            border-radius: var(--size);
            border: 0;
            transition: .35s ease-in-out;
            box-sizing: border-box;
            cursor: pointer;
        }

        .roomBook_container1 .checkbox-wrapper-5 .check label {
            width: calc(2.2 * var(--size));
            height: var(--size);
            background: #d7d7d7;
            overflow: hidden;
           
        }

        .roomBook_container1 .checkbox-wrapper-5 .check input[type="checkbox"] {
            position: absolute;
            z-index: 1;
            width: calc(.8 * var(--size));
            height: calc(.8 * var(--size));
            top: calc(.1 * var(--size));
            left: calc(.1 * var(--size));
            background: linear-gradient(45deg, #dedede, #ffffff);
            box-shadow: 0 6px 7px rgba(0,0,0,0.3);
            outline: none;
            margin: 0;
        }

        .roomBook_container1 .checkbox-wrapper-5 .check input[type="checkbox"]:checked {
            left: calc(1.3 * var(--size));
        }

        .roomBook_container1 .checkbox-wrapper-5 .check input[type="checkbox"]:checked + label {
            background: transparent;
        }

        .roomBook_container1 .checkbox-wrapper-5 .check label::before,
        .roomBook_container1 .checkbox-wrapper-5 .check label::after {
            content: "· ·";
            position: absolute;
            overflow: hidden;
            left: calc(.15 * var(--size));
            top: calc(.5 * var(--size));
            height: var(--size);
            letter-spacing: calc(-0.04 * var(--size));
            color: #9b9b9b;
            font-family: "Times New Roman", serif;
            z-index: 2;
            font-size: calc(.6 * var(--size));
            border-radius: 0;
            transform-origin: 0 0 calc(-0.5 * var(--size));
            backface-visibility: hidden;
        }

        .roomBook_container1 .checkbox-wrapper-5 .check label::after {
            content: "●";
            top: calc(.65 * var(--size));
            left: calc(.2 * var(--size));
            height: calc(.1 * var(--size));
            width: calc(.35 * var(--size));
            font-size: calc(.2 * var(--size));
            transform-origin: 0 0 calc(-0.4 * var(--size));
        }

        .roomBook_container1 .checkbox-wrapper-5 .check input[type="checkbox"]:checked + label::before,
        .roomBook_container1 .checkbox-wrapper-5 .check input[type="checkbox"]:checked + label::after {
            left: calc(1.55 * var(--size));
            top: calc(.4 * var(--size));
            line-height: calc(.1 * var(--size));
            transform: rotateY(360deg);
        }

        .roomBook_container1 .checkbox-wrapper-5 .check input[type="checkbox"]:checked + label::after {
            height: calc(.16 * var(--size));
            top: calc(.55 * var(--size));
            left: calc(1.6 * var(--size));
            font-size: calc(.6 * var(--size));
            line-height: 0;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .roomBook_container1 {
                padding: 20px;
            }

            .roomBook_container1 h2 {
                font-size: 24px;
            }

            .roomBook_container1 input,
            .roomBook_container1 select,
            .roomBook_container1 textarea {
                font-size: 14px;
            }

            .roomBook_container1 button.btn {
                font-size: 16px;
                padding: 12px;
            }

            .roomBook_container1 .modal-content {
                width: 95%;
            }
        }

        @media (max-width: 480px) {
            .roomBook_container1 h2 {
                font-size: 20px;
            }

            .roomBook_container1 .modal-content {
                width: 98%;
            }
        }
        
         
        .roomBook_container1 .mul-room-sel .checkbox-wrapper-50 *,
.roomBook_container1 .mul-room-sel .checkbox-wrapper-50 *::before,
.roomBook_container1 .mul-room-sel .checkbox-wrapper-50 *::after {
  box-sizing: border-box;
}

.roomBook_container1 .mul-room-sel .checkbox-wrapper-50 .plus-minus {
  --size: 9px;
  --primary: #1E2235;
  --secondary: #FAFBFF;
  --duration: .5s;
  -webkit-appearance: none;
  -moz-appearance: none;
  -webkit-tap-highlight-color: transparent;
  outline: none;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transform-style: preserve-3d;
  perspective: 240px;
  border-radius: 50%;
  width: calc(3 * var(--size));
  height: calc(3 * var(--size));
  border: 2px solid var(--primary);
  background-size: 300% 300%;
  transition: transform 0.3s;
  transform: scale(var(--scale, 1)) translateZ(0);
  -webkit-animation: var(--name, unchecked-50) var(--duration) ease forwards;
  animation: var(--name, unchecked-50) var(--duration) ease forwards;
  margin: 0;
}

.roomBook_container1 .mul-room-sel .checkbox-wrapper-50 .plus-minus::before,
.roomBook_container1 .mul-room-sel .checkbox-wrapper-50 .plus-minus::after {
  content: "";
  position: absolute;
  width: calc(1.33 * var(--size));
  height: var(--height, calc(1.33 * var(--size)));
  left: calc(0.68 * var(--size));
  top: var(--top, calc(0.6 * var(--size)));
  background: var(--background, var(--primary));
  -webkit-animation: var(--name-icon-b, var(--name-icon, unchecked-icon-50)) var(--duration) ease forwards;
  animation: var(--name-icon-b, var(--name-icon, unchecked-icon-50)) var(--duration) ease forwards;
}

.roomBook_container1 .mul-room-sel .checkbox-wrapper-50 .plus-minus::before {
  -webkit-clip-path: polygon(0 calc(0.5 * var(--size)), calc(0.5 * var(--size)) calc(0.5 * var(--size)), calc(0.5 * var(--size)) 0, calc(0.83 * var(--size)) 0, calc(0.83 * var(--size)) calc(0.5 * var(--size)), calc(1.33 * var(--size)) calc(0.5 * var(--size)), calc(1.33 * var(--size)) calc(0.83 * var(--size)), calc(0.83 * var(--size)) calc(0.83 * var(--size)), calc(0.83 * var(--size)) calc(1.33 * var(--size)), calc(0.5 * var(--size)) calc(1.33 * var(--size)), calc(0.5 * var(--size)) calc(0.83 * var(--size)), 0 calc(0.83 * var(--size)));
  clip-path: polygon(0 calc(0.5 * var(--size)), calc(0.5 * var(--size)) calc(0.5 * var(--size)), calc(0.5 * var(--size)) 0, calc(0.83 * var(--size)) 0, calc(0.83 * var(--size)) calc(0.5 * var(--size)), calc(1.33 * var(--size)) calc(0.5 * var(--size)), calc(1.33 * var(--size)) calc(0.83 * var(--size)), calc(0.83 * var(--size)) calc(0.83 * var(--size)), calc(0.83 * var(--size)) calc(1.33 * var(--size)), calc(0.5 * var(--size)) calc(1.33 * var(--size)), calc(0.5 * var(--size)) calc(0.83 * var(--size)), 0 calc(0.83 * var(--size)));
}

.roomBook_container1 .mul-room-sel .checkbox-wrapper-50 .plus-minus::after {
  --height: calc(0.33 * var(--size));
  --top: calc(1 * var(--size));
  --background: var(--secondary);
  --name-icon-b: var(--name-icon-a, checked-icon-50);
}

.roomBook_container1 .mul-room-sel .checkbox-wrapper-50 .plus-minus:active {
  --scale: .95;
}

.roomBook_container1 .mul-room-sel .checkbox-wrapper-50 .plus-minus:checked {
  --name: checked-50;
  --name-icon-b: checked-icon-50;
  --name-icon-a: unchecked-icon-50;
}

@-webkit-keyframes checked-icon-50 {
  from {
    transform: translateZ(calc(3 * var(--size)));
  }
  to {
    transform: translateX(calc(1.33 * var(--size))) rotateY(90deg) translateZ(calc(3 * var(--size)));
  }
}

@keyframes checked-icon-50 {
  from {
    transform: translateZ(calc(3 * var(--size)));
  }
  to {
    transform: translateX(calc(1.33 * var(--size))) rotateY(90deg) translateZ(calc(3 * var(--size)));
  }
}

@-webkit-keyframes unchecked-icon-50 {
  from {
    transform: translateX(calc(-1.33 * var(--size))) rotateY(-90deg) translateZ(calc(3 * var(--size)));
  }
  to {
    transform: translateZ(calc(3 * var(--size)));
  }
}

@keyframes unchecked-icon-50 {
  from {
    transform: translateX(calc(-1.33 * var(--size))) rotateY(-90deg) translateZ(calc(3 * var(--size)));
  }
  to {
    transform: translateZ(calc(3 * var(--size)));
  }
}

@-webkit-keyframes checked-50 {
  from {
    background-image: radial-gradient(ellipse at center, var(--primary) 0%, var(--primary) 25%, var(--secondary) 25.1%, var(--secondary) 100%);
    background-position: 100% 50%;
  }
  to {
    background-image: radial-gradient(ellipse at center, var(--primary) 0%, var(--primary) 25%, var(--secondary) 25.1%, var(--secondary) 100%);
    background-position: 50% 50%;
  }
}

@keyframes checked-50 {
  from {
    background-image: radial-gradient(ellipse at center, var(--primary) 0%, var(--primary) 25%, var(--secondary) 25.1%, var(--secondary) 100%);
    background-position: 100% 50%;
  }
  to {
    background-image: radial-gradient(ellipse at center, var(--primary) 0%, var(--primary) 25%, var(--secondary) 25.1%, var(--secondary) 100%);
    background-position: 50% 50%;
  }
}

@-webkit-keyframes unchecked-50 {
  from {
    background-image: radial-gradient(ellipse at center, var(--secondary) 0%, var(--secondary) 25%, var(--primary) 25.1%, var(--primary) 100%);
    background-position: 100% 50%;
  }
  to {
    background-image: radial-gradient(ellipse at center, var(--secondary) 0%, var(--secondary) 25%, var(--primary) 25.1%, var(--primary) 100%);
    background-position: 50% 50%;
  }
}

@keyframes unchecked-50 {
  from {
    background-image: radial-gradient(ellipse at center, var(--secondary) 0%, var(--secondary) 25%, var(--primary) 25.1%, var(--primary) 100%);
    background-position: 100% 50%;
  }
  to {
    background-image: radial-gradient(ellipse at center, var(--secondary) 0%, var(--secondary) 25%, var(--primary) 25.1%, var(--primary) 100%);
    background-position: 50% 50%;
  }
}

        
        
    </style>
</head>
<body>

<main class="roomBook_container">
    <section class="roomBook_container1">
        <form action="" method="POST" class="booking-form" aria-labelledby="form-title">
            <h2 id="form-title">Room Booking</h2>

            <button id="openModalBtn" type="button" class="btn">Select Rooms</button>

            <!-- The Modal -->
            <div id="roomModal" class="modal">
                <!-- Modal content -->
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2 id="modal-title">Select Room Numbers</h2>
                    <div id="roomOptionsContainer">
                        <?php
                        if (is_array($roomOptions)) {
                            foreach ($roomOptions as $room) {
                                $room_no = $room['room_no'];
                                $floor_no = $room['floor_no'];
                                echo '<section class="mul-room-sel" ><div class="checkbox">
  <label style="display: flex">
    <div class="checkbox-wrapper-50">
      <input type="checkbox" class="plus-minus" name="room_number[]" value="' . $room_no . '" style="margin-right: 5px">
    </div> 
    Room ' . $room_no . ' (Floor ' . $floor_no . ')
  </label>
</div></section>
';
                            }
                        } else {
                            echo "<p>No available rooms.</p>";
                        }
                        ?>
                    </div>
                    <button id="saveRoomsBtn" type="button" class="btn">Save</button>
                </div>
            </div>

            <div class="form-group">
                <label for="check_in_date">Check-in Date</label>
                <input type="date" id="check_in_date" name="check_in_date" required aria-required="true">
            </div>

            <div class="form-group">
                <label for="check_out_date">Check-out Date</label>
                <input type="date" id="check_out_date" name="check_out_date" required aria-required="true">
            </div>

            <div class="form-group">
                <label for="check_in_time">Check-in Time</label>
                <input type="time" id="check_in_time" name="check_in_time" required aria-required="true">
            </div>

            <div class="form-group">
                <label for="guest_name">Guest Name</label>
                <input type="text" id="guest_name" name="guest_name" required aria-required="true">
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age">
            </div>

            <div class="form-group">
                <label for="customer_id">Customer ID</label>
                <input type="text" id="customer_id" name="customer_id">
            </div>

            <div class="form-group">
                <label for="male_count">Male Count</label>
                <input type="number" id="male_count" name="male_count">
            </div>

            <div class="form-group">
                <label for="female_count">Female Count</label>
                <input type="number" id="female_count" name="female_count">
            </div>

            <div class="form-group">
                <label for="child_count">Child Count</label>
                <input type="number" id="child_count" name="child_count">
            </div>

            <div class="form-group">
                <label for="company">Company</label>
                <input type="text" id="company" name="company">
            </div>

            <div class="form-group">
                <label for="company_address">Company Address</label>
                <input type="text" id="company_address" name="company_address">
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address">
            </div>

            <div class="form-group">
                <label for="country">Country</label>
                <select id="country" name="country">
                    <option value="India" selected>India</option>
                </select>
            </div>

            <div class="form-group">
                <label for="state">State</label>
                <select id="state" name="state">
                    <option value="" selected>Select a State</option>
                </select>
            </div>

            <div class="form-group">
                <label for="company_phone">Company Phone</label>
                <input type="text" id="company_phone" name="company_phone">
            </div>

            <div class="form-group">
                <label for="customer_phone">Customer Phone</label>
                <input type="text" id="customer_phone" name="customer_phone">
            </div>

            <div class="form-group">
                <label for="payment_type">Payment Type</label>
                <select id="payment_type" name="payment_type" onchange="toggleAdvanceAmount()">
                   
                    <option value="CASH">CASH</option>
                    <option value="CARD">CARD</option>
                    <option value="ONLINE">ONLINE</option>
                    <option value="POST PAID">POST PAID</option>
                </select>
            </div>

            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" id="amount" name="amount" oninput="calculateTotal()">
            </div>

            <div class="form-group">
                <label for="gst_percentage">GST Percentage</label>
                <select id="gst_percentage" name="gst_percentage" onchange="calculateTotal()">
                    <option value="18">18%</option>
                    <option value="16%">16%</option>
                    <option value="12%">12%</option>
                    <option value="10%">10%</option>
                    <option value="8%">8%</option>
                    <option value="0">Without GST</option>
                </select>
            </div>

            <div class="form-group">
                <label for="total_price">Total Price</label>
                <input type="number" id="total_price" name="total_price" readonly aria-readonly="true">
            </div>

            <div class="form-group">
                <label for="discount">Discount</label>
                <input type="number" id="discount" name="discount" oninput="calculateTotal()">
            </div>

            <div id="advance_amount_container" class="form-group">
                <label for="advance_amount">Advance Amount</label>
                <input type="number" id="advance_amount" name="advance_amount" value="0">
            </div>

            <div class="form-group">
                <label for="id_proof_type">ID Proof Type</label>
                <select id="id_proof_type" name="id_proof_type">
                    <option value="Aadhar Card">Aadhar Card</option>
                    <option value="Passport">Passport</option>
                    <option value="Driving License">Driving License</option>
                    <option value="Pan Card">Pan Card</option>
                </select>
            </div>

            <div class="form-group">
                <label for="id_proof_number">ID Proof Number</label>
                <input type="text" id="id_proof_number" name="id_proof_number">
            </div>

            <div class="form-group">
                <label for="segment">Segment</label>
                <select id="segment" name="segment">
                    <option value="Walk-in">Walk-in</option>
                    <option value="Online">Online Booking</option>
                    <option value="Corporate booking">Corporate Booking</option>
                    <option value="group booking">Group Booking</option>
                    <option value="Direct booking">Direct Booking</option>
                    <option value="travel agent booking">Travel Agent Booking</option>
                </select>
            </div>

            <div class="form-group">
                <label for="instructions">Any Instructions</label>
                <textarea id="instructions" name="instructions"></textarea>
            </div>

            <div class="form-group premium-checkbox">
                <div class="checkbox-wrapper-5">
                    <div class="check">
                        <input id="regular_customer" name="regular_customer" type="checkbox">
                        <label for="regular_customer"></label>
                    </div>
                </div>
                <label for="regular_customer" style=" margin-left: 10px;">Add Regular Customer</label>
            </div>

            <div class="form-group">
                <label for="food_plan">Food Plan</label>
                <select id="food_plan" name="food_plan">
                    <option value="CP">CP</option>
                    <option value="EP">EP</option>
                    <option value="AP">AP</option>
                    <option value="MAP">MAP</option>
                </select>
            </div>

            <button type="submit" class="btn">Book Room</button>
        </form>
        
        <!-- Error Message Container -->
<div id="error-message-container" class="error-message-container">
    <div id="error-message" class="error-message">
        <!-- Error message will be dynamically inserted here -->
    </div>
</div>

<style>
.roomBook_container1 .error-message-container {
    position: fixed;
    bottom: 10px;
    right: 10px;
    z-index: 1000;
    display: none;
}

.roomBook_container1 .error-message {
    background-color: #f44336; /* Red background for error */
    color: white;
    padding: 15px;
    border-radius: 5px;
    font-size: 14px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    opacity: 0;
    transition: opacity 0.5s ease, transform 0.5s ease;
    transform: translateY(20px);
}

.roomBook_container1 .error-message.show {
    display: block;
    opacity: 1;
    transform: translateY(0);
}
</style>

    </section>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const checkInDateInput = document.getElementById('check_in_date');
        const checkOutDateInput = document.getElementById('check_out_date');
        const checkInTimeInput = document.getElementById('check_in_time');

        const now = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(now.getDate() + 1);

        const formattedDate = now.toISOString().split('T')[0];
        const formattedTomorrow = tomorrow.toISOString().split('T')[0];
        const formattedTime = now.toTimeString().split(' ')[0].slice(0, 5);

        checkInDateInput.value = formattedDate;
        checkOutDateInput.value = formattedTomorrow;
        checkInTimeInput.value = formattedTime;
    });

    var modal = document.getElementById("roomModal");
    var btn = document.getElementById("openModalBtn");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
        modal.style.display = "flex";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    document.getElementById("saveRoomsBtn").addEventListener("click", function() {
        modal.style.display = "none";
    });

    document.addEventListener("DOMContentLoaded", function () {
        const countrySelect = document.getElementById("country");
        const stateSelect = document.getElementById("state");

        fetch('https://restcountries.com/v3.1/all')
            .then(response => response.json())
            .then(countries => {
                countries.sort((a, b) => a.name.common.localeCompare(b.name.common));
                countries.forEach(country => {
                    const option = document.createElement("option");
                    option.value = country.name.common;
                    option.text = country.name.common;
                    countrySelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching country data:', error));

        countrySelect.addEventListener('change', function () {
            const selectedCountry = countrySelect.value;
            fetchStates(selectedCountry);
        });

        function fetchStates(country) {
            stateSelect.innerHTML = '';
            fetch('https://countriesnow.space/api/v0.1/countries/states', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ country: country }),
            })
            .then(response => response.json())
            .then(data => {
                const states = data.data.states;
                states.forEach(state => {
                    const option = document.createElement("option");
                    option.value = state.name;
                    option.text = state.name;
                    stateSelect.appendChild(option);
                });

                setDefaultState();
            })
            .catch(error => console.error('Error fetching state data:', error));
        }

        function setDefaultState() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    fetch(`https://geocode.xyz/${lat},${lon}?geoit=json`)
                        .then(response => response.json())
                        .then(data => {
                            const userState = data.state || data.region || '';
                            const options = stateSelect.options;
                            for (let i = 0; i < options.length; i++) {
                                if (options[i].text.toLowerCase() === userState.toLowerCase()) {
                                    options[i].selected = true;
                                    break;
                                }
                            }
                        })
                        .catch(error => console.error('Error fetching geolocation data:', error));
                });
            }
        }

        fetchStates('India');
    });

    function calculateTotal() {
        let amount = parseFloat(document.getElementById('amount').value) || 0;
        let discount = parseFloat(document.getElementById('discount').value) || 0;
        let gstPercentage = parseFloat(document.getElementById('gst_percentage').value) || 0;

        let discountedAmount = amount - discount;
        let gstAmount = (discountedAmount * gstPercentage) / 100;
        let totalPrice = discountedAmount + gstAmount;

        document.getElementById('total_price').value = totalPrice.toFixed(2);
    }

    function toggleAdvanceAmount() {
        var paymentType = document.getElementById('payment_type').value;
        var advanceAmountContainer = document.getElementById('advance_amount_container');

        if (paymentType === 'POST PAID') {
            advanceAmountContainer.style.display = 'block';
        } else {
            advanceAmountContainer.style.display = 'none';
            document.getElementById('advance_amount').value = '';
        }
    }

    window.onload = toggleAdvanceAmount;
    function showError(message, type) {
    const errorMessageContainer = document.getElementById('error-message-container');
    const errorMessage = document.getElementById('error-message');
    
    if (type === 'success') {
        errorMessage.style.backgroundColor = '#4CAF50'; // Green background for success
    } else {
        errorMessage.style.backgroundColor = '#f44336'; // Red background for error
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

