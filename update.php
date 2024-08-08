<?php
ob_start(); // Start output buffering
include('session.php'); // Ensure this includes session initialization and connection code

// Check if the session is started and the user is logged in
if (!isset($_SESSION['login_user'])) {
    header('Location: https://hgstore.in/login71.php');
    exit();
}
include('navbar.php');
// Include the database connection file
include('connection.php');
$conn = Connect();


$conn->select_db($u_id1);

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
        header("Location: stockmanagement.php"); // Redirect to the main page
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
    <style>
        .update {
            font-family: Arial, sans-serif;
            background-color: #f4f5f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

       .update .update-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

       .update .update-container h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

       .update .update-container label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 8px;
        }

       .update .update-container input[type="text"],
       .update .update-container input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            color: #333;
        }

       .update .update-container input[type="submit"] {
            width: 100%;
            background-color: #1abc9c;
            color: #fff;
            padding: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

       .update .update-container input[type="submit"]:hover {
            background-color: #16a085;
        }
    </style>
</head>
<body>
    <section class="update">
    <div class="update-container">
        <h2>Update Product</h2>

        <form method="post" action="">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" value="<?php echo $product['product_name']; ?>" required>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo $product['quantity']; ?>" required>

            <label for="price">Price:</label>
            <input type="text" id="price" name="price" value="<?php echo $product['price']; ?>" required>

            <label for="alert_threshold">Alert Threshold:</label>
            <input type="number" id="alert_threshold" name="alert_threshold" value="<?php echo $product['threshold']; ?>" required>

            <input type="submit" name="update_product" value="Update Product">
        </form>
    </div>
    </section>
</body>
</html>

<?php
$conn->close();
?>