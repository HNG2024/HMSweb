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

// Initialize date variable
$filterDate = '';

// Check if date filter is applied
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filter_date'])) {
    $filterDate = $_GET['filter_date'];

    // Fetch amenities data with date filter
    $amenitiesQuery = "
        SELECT 
            room_number, 
            GROUP_CONCAT(CONCAT(amenity_name, ' (', quantity, ')') SEPARATOR ', ') AS amenities 
        FROM 
            amenity_assignments 
        WHERE 
            DATE(assigned_date) = '$filterDate'
        GROUP BY 
            room_number";

    // Fetch laundry data with date filter
    $laundryQuery = "
        SELECT 
            room_number, 
            GROUP_CONCAT(CONCAT(items_sent, ' (', quantity, ')') SEPARATOR ', ') AS laundry_items,
            MIN(collect_time) AS collect_time,
            MAX(return_time) AS return_time
        FROM 
            laundry_assignments 
        WHERE 
            DATE(collect_time) = '$filterDate'
        GROUP BY 
            room_number";

    // Fetch housekeeping data with date filter
    $housekeepingQuery = "
        SELECT 
            room_number, 
            task_description, 
            task_status, 
            assigned_date
        FROM 
            housekeeping_tasks
        WHERE 
            DATE(assigned_date) = '$filterDate'";
} else {
    // Fetch amenities data without date filter
    $amenitiesQuery = "
        SELECT 
            room_number, 
            GROUP_CONCAT(CONCAT(amenity_name, ' (', quantity, ')') SEPARATOR ', ') AS amenities 
        FROM 
            amenity_assignments 
        GROUP BY 
            room_number";

    // Fetch laundry data without date filter
    $laundryQuery = "
        SELECT 
            room_number, 
            GROUP_CONCAT(CONCAT(items_sent, ' (', quantity, ')') SEPARATOR ', ') AS laundry_items,
            MIN(collect_time) AS collect_time,
            MAX(return_time) AS return_time
        FROM 
            laundry_assignments 
        GROUP BY 
            room_number";

    // Fetch housekeeping data without date filter
    $housekeepingQuery = "
        SELECT 
            room_number, 
            task_description, 
            task_status, 
            assigned_date
        FROM 
            housekeeping_tasks";
}

$amenitiesResult = $conn->query($amenitiesQuery);
$totalAmenitiesRooms = $amenitiesResult->num_rows;

$laundryResult = $conn->query($laundryQuery);
$totalLaundryRooms = $laundryResult->num_rows;

$housekeepingResult = $conn->query($housekeepingQuery);
$totalHousekeepingTasks = $housekeepingResult->num_rows;

if (!$amenitiesResult || !$laundryResult || !$housekeepingResult) {
    die("Error retrieving report data: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Housekeeping Report</title>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            animation: fadeIn 1s ease-in-out;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            margin-bottom: 20px;
            font-size: 26px;
            color: #333;
            text-align: center;
        }

        nav {
            margin-bottom: 20px;
            text-align:left;
        }

        nav a {
            text-decoration: none;
            color: #007BFF;
            margin-right: right;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #0056b3;
        }

        nav a.active {
            color: black;
            font-weight: bold;
            text-decoration: underline;
        }

        .filter-form {
            margin-bottom: 30px;
            text-align: center;
        }

        .filter-form input[type="date"] {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 200px;
        }

        .filter-form button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filter-form button:hover {
            background-color: #0056b3;
        }

        .summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }

        .summary-card {
            background-color: #007BFF;
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            flex-grow: 1;
            margin: 0 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
        }

        .summary-card h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .summary-card p {
            font-size: 20px;
            margin: 0;
        }

        .report-section {
            margin-bottom: 40px;
        }

        .report-section h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 3px solid #007BFF;
            padding-bottom: 5px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
        }

        .report-table th,
        .report-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .report-table th {
            background-color: #f9f9f9;
            font-weight: bold;
            color: #333;
        }

        .report-table td {
            color: #555;
        }

        .report-table tr:hover {
            background-color: #f1f1f1;
        }

        @media (max-width: 768px) {
            .summary {
                flex-direction: column;
                align-items: stretch;
            }

            .summary-card {
                margin: 10px 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Housekeeping Report</h1>
        </div>
        <nav>
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Housekeeping</a> /
            <a href="amenity.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'amenity.php' ? 'active' : ''; ?>">Amenity</a> /
            <a href="laundry.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'laundry.php' ? 'active' : ''; ?>">Laundry</a> /
            <a href="report.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : ''; ?>">Report</a>
        </nav>

        <div class="filter-form">
            <form method="GET" action="report.php">
                <input type="date" name="filter_date" value="<?php echo $filterDate; ?>" required>
                <button type="submit">Filter</button>
            </form>
        </div>

        <div class="summary">
            <div class="summary-card">
                <h3>Total Rooms with Amenities</h3>
                <p><?php echo $totalAmenitiesRooms; ?></p>
            </div>
            <div class="summary-card">
                <h3>Total Rooms with Laundry</h3>
                <p><?php echo $totalLaundryRooms; ?></p>
            </div>
            <div class="summary-card">
                <h3>Total Housekeeping Tasks</h3>
                <p><?php echo $totalHousekeepingTasks; ?></p>
            </div>
        </div>

        <div class="report-section">
            <h2>Amenities Data</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Amenities</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($amenities = $amenitiesResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $amenities['room_number']; ?></td>
                        <td><?php echo $amenities['amenities']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="report-section">
            <h2>Laundry Data</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Laundry Items</th>
                        <th>Collect Time</th>
                        <th>Return Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($laundry = $laundryResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $laundry['room_number']; ?></td>
                        <td><?php echo $laundry['laundry_items']; ?></td>
                        <td><?php echo $laundry['collect_time']; ?></td>
                        <td><?php echo $laundry['return_time']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="report-section">
            <h2>Housekeeping Data</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Task Description</th>
                        <th>Task Status</th>
                        <th>Assigned Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($housekeeping = $housekeepingResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $housekeeping['room_number']; ?></td>
                        <td><?php echo $housekeeping['task_description']; ?></td>
                        <td><?php echo $housekeeping['task_status']; ?></td>
                        <td><?php echo $housekeeping['assigned_date']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>
