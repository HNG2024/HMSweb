<?php
// Include the database connection
include 'db_connection.php';

// Fetch sales grouped by date
$sql = "SELECT sale_date, SUM(quantity_sold) as total_quantity, SUM(quantity_sold * sale_price) as total_sales
        FROM sales
        GROUP BY sale_date
        ORDER BY sale_date DESC";
$sales_by_date = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales by Date</title>
</head>
<body>
    <h2>Sales by Date</h2>
    <a href="index.php">Back to Stock Management</a><br><br>
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
</body>
</html>

<?php
$conn->close();
?>
