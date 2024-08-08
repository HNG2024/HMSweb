<?php
// Include the database connection
include 'db_connection.php';

// Fetch products with low stock
$sql = "SELECT * FROM products WHERE quantity < threshold";
$low_stock = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Report</title>
</head>
<body>
    <h2>Low Stock Report</h2>
    <a href="index.php">Back to Stock Management</a><br><br>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Threshold</th>
            <th>Unit</th>
            <th>Status</th>
        </tr>
        <?php
        if ($low_stock->num_rows > 0) {
            while($row = $low_stock->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['product_name'] . "</td>";
                echo "<td>" . $row['quantity'] . "</td>";
                echo "<td>" . $row['threshold'] . "</td>";
                echo "<td>" . $row['unit'] . "</td>";
                echo "<td style='color: red;'>Low Stock!</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No products with low stock</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
