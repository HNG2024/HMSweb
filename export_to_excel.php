<?php
// Include the database connection
include 'db_connection.php';

// Set the headers to indicate this is an Excel file
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=sales_report.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Fetch sales grouped by date
$sql = "SELECT sale_date, SUM(quantity_sold) as total_quantity, SUM(quantity_sold * sale_price) as total_sales
        FROM sales
        GROUP BY sale_date
        ORDER BY sale_date DESC";
$sales_by_date = $conn->query($sql);

// Create the table structure
echo "<table border='1'>";
echo "<tr>";
echo "<th>Date</th>";
echo "<th>Total Quantity Sold</th>";
echo "<th>Total Sales (in $)</th>";
echo "</tr>";

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

echo "</table>";

$conn->close();
?>
