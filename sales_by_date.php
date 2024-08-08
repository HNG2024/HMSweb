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

// Initialize variables for date filtering
$start_date = '';
$end_date = '';

// Check if the form is submitted and set the start and end dates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}

// Modify the SQL query to include the date filter
$sql = "SELECT sale_date, SUM(quantity_sold) as total_quantity, SUM(quantity_sold * sale_price) as total_sales
        FROM sales";

if (!empty($start_date) && !empty($end_date)) {
    $sql .= " WHERE sale_date BETWEEN '$start_date' AND '$end_date'";
}

$sql .= " GROUP BY sale_date ORDER BY sale_date DESC";

// Execute the query and check for errors
$sales_by_date = $conn->query($sql);

if (!$sales_by_date) {
    // Handle query error
    die("Error executing query: " . $conn->error);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Usage by Date</title>
    <style>
        /* General Styles */
        .report {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f5f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .report .container {
            width: 80%;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .report h2 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        .report a {
            color: #3498db;
            text-decoration: none;
            margin-right: 15px;
            font-weight: 500;
        }

        .report a:hover {
            text-decoration: underline;
        }

        /* Button Styles */
        .report  button, .report a.button {
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
        }

        .report  button:hover, .report a.button:hover {
            background-color: #16a085;
        }

        /* Form Styles */
        .report  form {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .report  label {
            font-weight: 500;
            margin-right: 10px;
            color: #34495e;
        }

        .report  input[type="date"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            width: 200px;
            margin-right: 20px;
        }

        /* Table Styles */
        .report  table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ecf0f1;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .report  th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .report  th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }

        .report  tr:hover {
            background-color: #dcdde1;
        }

        .report  tr:nth-child(even) {
            background-color: #f2f3f4;
        }

        .report  td {
            color: #555;
            font-weight: 500;
        }

        /* Header Styles */
        .print-header {
            display: none;
            width: 100%;
            margin-bottom: 20px;
            text-align: center;
        }

        .print-header .left-info, .print-header .right-info {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }

        .print-header .left-info {
            text-align: left;
        }

        .print-header .right-info {
            text-align: right;
        }

        /* Clear floats */
        .print-header::after {
            content: "";
            display: table;
            clear: both;
        }
.hide{
                display: inline-block;
            }
        /* Print-Specific Styles */
       
    </style>
</head>
<body>

<section class="report">
    <div class="container">
        <!-- Print Header -->
        <div class="print-header">
            <div>
                <h1>Hotel Name</h1>
                <p>Powered by HEALNGLOW</p>
            </div>
            <div class="left-info">
                <p>Manager: [Manager Name]</p>
                <p>Phone: [Phone Number]</p>
                <p>Address: [Hotel Address]</p>
            </div>
            <div class="right-info">
                <p>GST No: [GST Number]</p>
                <p>Date: <?php echo date('Y-m-d'); ?></p>
                <p>Time: <?php echo date('H:i:s'); ?></p>
            </div>
        </div>

        <h2>Usage by Date</h2>

        <!-- Date Filter Form -->
        <form method="post" action="" >
            <div class="hide">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" required>
            </div>
            <div class="hide">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" required>
            </div>
            <button type="submit">Filter</button>
            <a href="javascript:history.back()"  class="hide">Back </a>
        </form>

        <!-- Print Button -->
      <!-- Print Button -->
<a href="#" onclick="window.print()" style="background-color: #3498db; color: white; border: none; padding: 10px 25px; cursor: pointer; border-radius: 25px; font-size: 16px; font-weight: 500; text-decoration: none;  margin-right: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);" onmouseover="this.style.backgroundColor='#2980b9'" class="hide" onmouseout="this.style.backgroundColor='#3498db'">Print Report</a>

<!-- Export to Excel -->
<a href="export_to_excel.php" style="background-color: #27ae60; color: white; border: none; padding: 10px 25px; cursor: pointer; border-radius: 25px; font-size: 16px; font-weight: 500; text-decoration: none;  margin-right: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);" onmouseover="this.style.backgroundColor='#219150'" onmouseout="this.style.backgroundColor='#27ae60'" class="hide">Export to Excel</a>

<!-- Export to CSV -->
<a href="export_to_csv.php" style="background-color: #e67e22; color: white; border: none; padding: 10px 25px; cursor: pointer; border-radius: 25px; font-size: 16px; font-weight: 500; text-decoration: none; margin-right: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);" onmouseover="this.style.backgroundColor='#d35400'" onmouseout="this.style.backgroundColor='#e67e22'" class="hide">Export to CSV</a>

        <!-- Sales Data Table -->
        <div id="printableTable">
            <table border="1">
                <tr>
                    <th>Date</th>
                    <th>Total Quantity Sold</th>
                    <th>Total Sales (in $)</th>
                </tr>
                <?php
                if ($sales_by_date->num_rows > 0) {
                    while($row = $sales_by_date->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['sale_date'] . "</td>";
                        echo "<td>" . $row['total_quantity'] . "</td>";
                        echo "<td>" . $row['total_sales'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No sales data found</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    </section>
    <style>
         @media print {
            body * {
                visibility: hidden; /* Hide everything */
            }
            .report .container, .container * {
                visibility: visible; /* Show only the container section */
            }
            .report .container {
                position: absolute;
                top: 0;
                left: 0;
                width: 90%;
                margin-left: 5%;
                box-shadow: none;
                padding: 0;
            }

            .report .print-header {
                display: block; /* Show the header in print */
                margin-bottom: 20px;
            }

            h2, button, a.button, form {
                display: none; /* Hide these elements during printing */
            }

            table {
                border: none;
            }

            th, td {
                border: 1px solid #333;
                padding: 8px;
                font-size: 12pt;
            }

            tr:nth-child(even) {
                background-color: #fff;
            }

            tr:nth-child(odd) {
                background-color: #f2f2f2;
            }

            /* Hide the footer information like URL path */
            @page {
                margin: 0;
            }
            body {
                margin: 1cm;
            } 
            .hide{
                display: none;
            }
        }
        
    </style>
</body>
</html>

<?php
$conn->close();
?>
