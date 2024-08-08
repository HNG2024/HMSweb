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

// Set default report period (e.g., last 30 days)
$start_date = date('Y-m-d', strtotime('-30 days'));
$end_date = date('Y-m-d');

if (isset($_POST['generate_report'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}

// Fetch sales data
$sql = "SELECT products.product_name, SUM(sales.quantity_sold) as total_quantity, 
        SUM(sales.sale_price * sales.quantity_sold) as total_revenue 
        FROM sales 
        JOIN products ON sales.product_id = products.id 
        WHERE sales.sale_date BETWEEN '$start_date' AND '$end_date' 
        GROUP BY products.product_name";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Usage Report</title>
</head>
<body>

    <h2>Sales Report</h2>

    <form method="post" action="">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" required><br><br>

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" required><br><br>

        <input type="submit" name="generate_report" value="Generate Report">
    </form>

    <h2>Report from <?php echo $start_date; ?> to <?php echo $end_date; ?></h2>
    <table border="1">
        <tr>
            <th>Product Name</th>
            <th>Total Quantity Sold</th>
            <th>Total Revenue</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['product_name'] . "</td>";
                echo "<td>" . $row['total_quantity'] . "</td>";
                echo "<td>" . $row['total_revenue'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No sales data found</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
