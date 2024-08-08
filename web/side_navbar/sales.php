<?php
// Include the database connection
include 'db_connection.php';

if (isset($_POST['record_sale'])) {
    $product_id = $_POST['product_id'];
    $quantity_sold = $_POST['quantity_sold'];
    $sale_price = $_POST['sale_price'];

    // Fetch the current quantity
    $sql = "SELECT quantity, threshold FROM products WHERE id=$product_id";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();

    if ($product['quantity'] >= $quantity_sold) {
        // Record the sale
        $sql = "INSERT INTO sales (product_id, quantity_sold, sale_price) VALUES ($product_id, $quantity_sold, $sale_price)";
        $conn->query($sql);

        // Update the product quantity
        $new_quantity = $product['quantity'] - $quantity_sold;
        $sql = "UPDATE products SET quantity=$new_quantity WHERE id=$product_id";
        $conn->query($sql);

        // Check if stock is below the threshold
        if ($new_quantity < $product['threshold']) {
            echo "<script>alert('Low Stock Alert: Stock is below the threshold!');</script>";
        }

        echo "Sale recorded successfully";
    } else {
        echo "Not enough stock available";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Record Sale</title>
</head>
<body>
    <h2>Record Sale</h2>
    <a href="index.php">Back to Stock Management</a><br><br>

    <form method="post" action="">
        <label for="product_id">Product:</label>
        <select id="product_id" name="product_id" required>
            <?php
            $sql = "SELECT id, product_name, quantity, unit FROM products";
            $products = $conn->query($sql);

            if ($products->num_rows > 0) {
                while($row = $products->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['product_name'] . " (Available: " . $row['quantity'] . " " . $row['unit'] . ")</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="quantity_sold">Quantity Sold:</label>
        <input type="number" id="quantity_sold" name="quantity_sold" required><br><br>

        <label for="sale_price">Sale Price:</label>
        <input type="text" id="sale_price" name="sale_price" required><br><br>

        <input type="submit" name="record_sale" value="Record Sale">
    </form>
</body>
</html>

<?php
$conn->close();
?>
