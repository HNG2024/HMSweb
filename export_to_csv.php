<?php
// Include the database connection
include 'db_connection.php';

// Set the headers to indicate this is a CSV file
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sales_report.csv"');

// Open the output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, array('Date', 'Total Quantity Sold', 'Total Sales (in $)'));

// Fetch sales grouped by date
$sql = "SELECT sale_date, SUM(quantity_sold) as total_quantity, SUM(quantity_sold * sale_price) as total_sales
        FROM sales
        GROUP BY sale_date
        ORDER BY sale_date DESC";
$sales_by_date = $conn->query($sql);

if ($sales_by_date->num_rows > 0) {
    while($row = $sales_by_date->fetch_assoc()) {
        fputcsv($output, $row);
    }
} else {
    fputcsv($output, array('No sales data found', '', ''));
}

fclose($output);
$conn->close();
?>
