<?php
include 'db_connection.php';

$conn = Connect();

// Fetch all products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $report = "Stock Report - " . date("Y-m-d H:i:s") . "\n\n";
    while ($row = $result->fetch_assoc()) {
        $report .= "Product: " . $row['product_name'] . "\n";
        $report .= "Quantity: " . $row['quantity'] . "\n";
        $report .= "Unit: " . $row['unit'] . "\n";
        $report .= "Price: " . $row['price'] . "\n";
        $report .= "------------------------------------\n";
    }

    // Send report via email (example using PHP mail)
    mail("admin@example.com", "Daily Stock Report", $report);
}

$conn->close();
?>
