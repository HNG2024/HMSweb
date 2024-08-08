<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the product ID from the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the product details
    $sql = "SELECT * FROM products WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found";
        exit;
    }
}

// Update product
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $alert_threshold = $_POST['alert_threshold'];

    $sql = "UPDATE products SET product_name='$product_name', quantity=$quantity, price=$price, threshold=$alert_threshold WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        // Check if stock is below the threshold
        if ($quantity < $alert_threshold) {
            echo "<script>alert('Low Stock Alert: Stock is below the threshold!');</script>";
        }
        header("Location: index.php"); // Redirect to the main page
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Product</title>
</head>
<body>
    <h2>Update Product</h2>

    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" value="<?php echo $product['product_name']; ?>" required><br><br>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo $product['quantity']; ?>" required><br><br>

        <label for="price">Price:</label>
        <input type="text" id="price" name="price" value="<?php echo $product['price']; ?>" required><br><br>

        <label for="alert_threshold">Alert Threshold:</label>
        <input type="number" id="alert_threshold" name="alert_threshold" value="<?php echo $product['alert_threshold']; ?>" required><br><br>

        <input type="submit" name="update_product" value="Update Product">
    </form>
</body>
</html>

<?php
$conn->close();
?>
