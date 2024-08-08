<?php

include('session.php');

// Check if the user is logged in
if (!isset($_SESSION['login_user'])) {
    header('Location: https://hgstore.in/login71.php');
    exit();
}

// Include the database connection
include 'connection.php';
$conn = Connect();
$conn->select_db($u_id1);

// Get the category from the query string
$category = isset($_GET['category']) ? $_GET['category'] : 'HNG';

if ($category === 'HNG') {
    $sql = "SELECT id, product_name, quantity, unit FROM products WHERE quantity < threshold &&  category ='HNG'";
} else {
    $sql = "SELECT id, product_name, quantity, unit FROM products WHERE quantity < threshold &&  category ='Other'";
}

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
