<?php
include('session.php');

// Check if the user is logged in
if (!isset($_SESSION['login_user'])) {
    header('Location: https://hgstore.in/login71.php');
    exit();
}
include('login_c_check.php');
// Database connection
require 'connection.php';
$conn = Connect();

// Handle form submission
if (isset($_POST['submit'])) {
    $Room_Number = mysqli_real_escape_string($conn, $_POST['Room_Number']);
    $Floor_No = mysqli_real_escape_string($conn, $_POST['Floor_No']);
    $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
    $view_type = mysqli_real_escape_string($conn, $_POST['view_type']);
    $bed_type = mysqli_real_escape_string($conn, $_POST['bed_type']);
    $max_occupancy = mysqli_real_escape_string($conn, $_POST['max_occupancy']);
    $room_size = mysqli_real_escape_string($conn, $_POST['room_size']);
    $price_pernight = mysqli_real_escape_string($conn, $_POST['price_pernight']);
    $amenities_room = mysqli_real_escape_string($conn, $_POST['amenities_room']);
    $exclusive_services = mysqli_real_escape_string($conn, $_POST['exclusive_services']);
    $available_room = mysqli_real_escape_string($conn, $_POST['available_room']) ? 'Available' : 'Not Available';

    // Select the appropriate database
    $conn->select_db($u_id1);
  $roomID = $u_id . $Room_Number;
    
    // Select the database based on the modified u_id1
   
    // Create the table if it does not exist
    $query1 = "CREATE TABLE IF NOT EXISTS roominfo (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        room_id VARCHAR(200) NOT NULL,
        room_no VARCHAR(50) NOT NULL,
        floor_no INT NOT NULL,
        room_type VARCHAR(150) NULL,
        view_type VARCHAR(250) NULL,
        bed_type VARCHAR(100) NULL,
        max_occupancy INT NULL,
        room_size INT NULL,
        price_pernight INT NULL,
        amenities_room VARCHAR(200) NULL,
        exclusive_services VARCHAR(100) NULL,
        available_room VARCHAR(100) NULL,
        checking_room VARCHAR(200) NULL,
        status VARCHAR(100) NULL,
        booking_id VARCHAR(255) NULL,
        damaged_items varchar(300) NULL
        
    );";
    mysqli_query($conn, $query1);

    // Insert room data into the database
    $query = "INSERT INTO roominfo (room_id, room_no, floor_no, room_type, view_type, bed_type, max_occupancy, room_size, price_pernight, amenities_room, exclusive_services, available_room,status) 
              VALUES ('$roomID','$Room_Number', '$Floor_No', '$room_type', '$view_type', '$bed_type', '$max_occupancy', '$room_size', '$price_pernight', '$amenities_room', '$exclusive_services', '$available_room', 'available')";

    if (mysqli_query($conn, $query)) {
       echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            showNotification("Room Added Successfully!");
        });
    </script>';
    } else {
        die("Error adding Room: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .room_add {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .room_add .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .room_add .form-area {
            background-color: #fff;
            padding: 30px;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .room_add h3 {
            margin-bottom: 30px;
            text-align: center;
            font-size: 24px;
            color: #444;
        }

        .room_add .form-group {
            margin-bottom: 20px;
        }

        .room_add .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        .room_add .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            color: #555;
            transition: border-color 0.3s ease;
        }

        .room_add .form-control:focus {
            border-color: #007bff;
            outline: none;
        }

        .room_add .form-check-input {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            vertical-align: middle;
        }

        .room_add .form-check-label {
            font-size: 16px;
            color: #555;
        }

        .room_add .btn-primary {
            background-color: #007bff;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            display: block;
            width: 100%;
            text-align: center;
        }

        .room_add .btn-primary:hover {
            background-color: #0056b3;
        }

 .room_add .form-check {
        position: relative;
        padding-left: 35px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 18px;
        user-select: none;
    }

    .room_add .form-check-input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .room_add .form-check-label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 25px;
        height: 25px;
        border-radius: 5px;
        background-color: #e6e6e6;
        border: 2px solid #ccc;
        transition: background-color 0.3s, border-color 0.3s;
    }

    .room_add .form-check-input:checked ~ .form-check-label::before {
        background-color: #007bff;
        border-color: #007bff;
    }

    .room_add .form-check-label::after {
        content: '';
        position: absolute;
        left: 9px;
        top: 5px;
        width: 8px;
        height: 15px;
        border: solid white;
        border-width: 0 3px 3px 0;
        transform: rotate(45deg);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .room_add .form-check-input:checked ~ .form-check-label::after {
        opacity: 1;
    }
        /* Responsive Design */
        @media (max-width: 1024px) {
            .room_add .container {
                padding: 10px;
                width: 75vw;
            }

            .room_add .form-area {
                padding: 20px;
                margin: 10px 0;
            }

            .room_add h3 {
                font-size: 22px;
            }

            .room_add .form-group label {
                font-size: 14px;
            }

            .room_add .form-control {
                font-size: 14px;
                padding: 8px;
            }

            .room_add .btn-primary {
                font-size: 14px;
                padding: 10px 15px;
            }
        }

        @media (max-width: 768px) {
            .room_add h3 {
                font-size: 20px;
            }

            .room_add .form-group label {
                font-size: 13px;
            }

            .room_add .form-control {
                font-size: 13px;
                padding: 7px;
            }

            .room_add .btn-primary {
                font-size: 13px;
                padding: 8px 12px;
            }
        }

        @media (max-width: 480px) {
            .room_add .container {
                padding: 5px;
            }

            .room_add .form-area {
                padding: 15px;
            }

            .room_add h3 {
                font-size: 18px;
            }

            .room_add .form-group label {
                font-size: 12px;
            }

            .room_add .form-control {
                font-size: 12px;
                padding: 6px;
            }

            .room_add .btn-primary {
                font-size: 12px;
                padding: 7px 10px;
            }
        }
        /* Notification container */
 .room_add .notification {
    visibility: hidden;
    min-width: 300px;
    background-color: #007bff;
    color: #fff;
    text-align: center;
    border-radius: 5px;
    padding: 15px;
    position: fixed;
    z-index: 999;
    bottom: 30px;
    right: 30px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    opacity: 0;
    transition: opacity 0.5s, bottom 0.5s;
}

/* Show the notification */
 .room_add .notification.show {
    visibility: visible;
    opacity: 1;
    bottom: 50px;
}

/* Responsive adjustments */
@media (max-width: 767px) {
     .room_add .notification {
        min-width: 250px;
        padding: 10px;
        bottom: 20px;
        right: 20px;
    }

     .room_add .notification.show {
        bottom: 40px;
    }
}

    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <section class="room_add">
        <div class="container">
            <div class="form-area">
                <form action="" method="POST" enctype="multipart/form-data">
                    <h3>Add New Room</h3>

                    <div class="form-group">
                        <label for="Room_Number"><span class="text-danger">*</span> Room Number: </label>
                        <input type="text" class="form-control" id="Room_Number" name="Room_Number" placeholder="Enter Room Number" required>
                    </div>

                    <div class="form-group">
                        <label for="Floor_No"><span class="text-danger">*</span> Floor No: </label>
                        <input type="number" class="form-control" id="Floor_No" placeholder="Enter Floor No" name="Floor_No" required>
                    </div>

                    <div class="form-group">
                        <label for="room_type"><span class="text-danger">*</span> Room Type: </label>
                        <select class="form-control" id="room_type" name="room_type" required>
                            <option value="" disabled selected>Choose Type</option>
                            <option value="Dulex">Dulex</option>
                            <option value="Super Dulex">Super Dulex</option>
                            <option value="Presidetials Suite">Presidetials Suite</option>
                            <option value="Royal Suite">Royal Suite</option>
                            <option value="Villa">Villa</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="view_type"><span class="text-danger">*</span> Room View Type: </label>
                        <select class="form-control" id="view_type" name="view_type" required>
                            <option value="" disabled selected>Choose View Type</option>
                            <option value="Normal View">Normal View</option>
                            <option value="Balcony View">Balcony View</option>
                            <option value="Ocean View">Ocean View</option>
                            <option value="City View">City View</option>
                            <option value="Mountain View">Mountain View</option>
                            <option value="none">none</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bed_type"><span class="text-danger">*</span> Room Bed Type: </label>
                        <select class="form-control" id="bed_type" name="bed_type" required>
                            <option value="" disabled selected>Choose Bed Type</option>
                            <option value="Single Bed">Single Bed</option>
                            <option value="Double Bed">Double Bed</option>
                            <option value="Saparate Bed">Saparate Bed</option>
                            <option value="King Size Bed">King Size Bed</option>
                            <option value="Queen Size Bed">Queen Size Bed</option>
                            <option value="none">none</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="max_occupancy"><span class="text-danger">*</span> Max Room Occupancy: </label>
                        <input type="text" class="form-control" id="max_occupancy" name="max_occupancy" placeholder="Enter Max Room Occupancy" required>
                    </div>

                    <div class="form-group">
                        <label for="room_size"><span class="text-danger">*</span> Room Size: </label>
                        <input type="text" class="form-control" id="room_size" name="room_size" placeholder="Enter Room Size">
                    </div>

                    <div class="form-group">
                        <label for="price_pernight"><span class="text-danger">*</span> Room Price Per-Night: </label>
                        <input type="text" class="form-control" id="price_pernight" name="price_pernight" placeholder="Enter Price Per-Night">
                    </div>

                    <div class="form-group">
                        <label for="amenities_room"><span class="text-danger">*</span> Room Amenities: </label>
                        <input type="text" class="form-control" id="amenities_room" name="amenities_room" placeholder="Enter Room Amenities">
                    </div>

                    <div class="form-group">
                        <label for="exclusive_services"><span class="text-danger">*</span> Room Exclusive Services: </label>
                        <input type="text" class="form-control" id="exclusive_services" name="exclusive_services" placeholder="Enter Room Exclusive Services">
                    </div>

                   <div class="form-group">
    <label for="available_room"><span class="text-danger">*</span> Available Room: </label>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="available_room" name="available_room" value="Available">
        <label class="form-check-label" for="available_room">
            Available
        </label>
    </div>
</div>

                    <div class="form-group">
                        <button type="submit" id="submit" name="submit" class="btn btn-primary">Add Room</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="notification" class="notification">
    <p id="notification-message"></p>
</div>

    </section>
    
    <script>
        function showNotification(message) {
            var notification = document.getElementById("notification");
            var notificationMessage = document.getElementById("notification-message");

            notificationMessage.textContent = message;
            notification.classList.add("show");

            // Hide the notification after 3 seconds
            setTimeout(function() {
                notification.classList.remove("show");
            }, 3000);
        }
    </script>
    
</body>

</html>
