<?php
// Include the database connection
include 'db_connection.php';
$conn = Connect();

// Fetch products with low stock
$sql = "SELECT id, product_name, quantity, unit FROM products WHERE quantity < threshold";
$result = $conn->query($sql);

$low_stock_products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $low_stock_products[] = $row;
    }
}

echo json_encode($low_stock_products);

$conn->close();
?>
