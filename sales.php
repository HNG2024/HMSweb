<?php

include('session.php'); // Ensure this includes session initialization and connection code

// Check if the session is started and the user is logged in
if (!isset($_SESSION['login_user'])) {
    header('Location: https://hgstore.in/login71.php');
    exit();
}

// Include the database connection file
include('connection.php');
$conn = Connect();
include('navbar.php');

$conn->select_db($u_id1);

if (isset($_POST['record_sale'])) {
    $product_id = $_POST['product_id'];
    $quantity_sold = $_POST['quantity_sold'];
    $sale_price = $_POST['sale_price'];

    // Fetch the current quantity
    $sql = "SELECT quantity, threshold FROM products WHERE id=$product_id ";
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
    <style>
        .sales-body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f5f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .sales-container {
            width: 80%;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .sales-container h2 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        .sales-container a {
            background-color: #1abc9c;
            color: white;
            border: none;
            padding: 10px 25px;
            cursor: pointer;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            text-decoration: none;
            margin-right: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .sales-container a:hover {
            background-color: #16a085;
        }

        .sales-container form {
            margin-top: 20px;
        }

        .sales-container label {
            font-size: 16px;
            font-weight: 500;
            margin-right: 10px;
        }

        .sales-container input[type="number"],
        .sales-container input[type="text"],
        .sales-container select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-bottom: 20px;
            width: calc(100% - 22px); /* To account for padding and border */
        }

        .sales-container input[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 25px;
            cursor: pointer;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .sales-container input[type="submit"]:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

    <section class="sales-body">
    <div class="sales-container">
        
        <h2>Record Sale</h2>
        <a href="javascript:history.back()">Back</a>


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
    </div>
    </section>
</body>
</html>

<?php
$conn->close();
?>
