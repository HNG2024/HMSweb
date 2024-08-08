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
$productAdded = false; // Initialize a flag for success message

// Add product for HNG Products
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $price = $_POST['price'];
    $threshold = $_POST['threshold'];

    $sql = "INSERT INTO products (product_name, quantity, unit, price, threshold, category ) 
            VALUES ('$product_name', $quantity, '$unit', $price, $threshold, 'HNG') ";
    if ($conn->query($sql) === TRUE) {
        $productAdded = true; // Set flag to true on success
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch Products from the HNG table
$sql = "SELECT * FROM products WHERE category ='HNG'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Stock Management - HNG Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .stockmanagement1 {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f5f7;
            margin: 0;
            padding: 0;
        }

        .stockmanagement1 .navbar {
            background-color: #333;
            overflow: hidden;
        }

        .stockmanagement1 .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .stockmanagement1 .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        .stockmanagement1 .container {
            width: 80%;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .stockmanagement1 h2 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }

        .stockmanagement1 .button-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .stockmanagement1 .button-container a {
            background-color: #1abc9c;
            color: white;
            padding: 10px 25px;
            text-decoration: none;
            border-radius: 25px;
            margin: 0 10px;
            transition: background-color 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .stockmanagement1 .button-container a:hover {
            background-color: #16a085;
        }

        .stockmanagement1 form {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .stockmanagement1 form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .stockmanagement1 form input, form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .stockmanagement1 form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .stockmanagement1 form input[type="submit"]:hover {
            background-color: #45a049;
        }

        .stockmanagement1 table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .stockmanagement1 th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .stockmanagement1 th {
            background-color: #3498db;
            color: white;
        }

        .stockmanagement1 tr:nth-child(even) {
            background-color: #f2f3f4;
        }

        .stockmanagement1 tr:hover {
            background-color: #dcdde1;
        }

        .stockmanagement1 .low-stock {
            color: red;
            font-weight: bold;
        }

        .stockmanagement1 .in-stock {
            color: green;
            font-weight: bold;
        }

        #success-message {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        #success-message .close-btn {
            margin-left: 15px;
            color: white;
            cursor: pointer;
            float: right;
        }

        #notification-hng {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #f44336;
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
        }

        #notification-hng .close-btn {
            margin-left: 15px;
            color: white;
            cursor: pointer;
            float: right;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<section class="stockmanagement1">
<!-- Success Message Container -->
<div id="success-message">
    Product added successfully!
    <span class="close-btn" onclick="closeSuccessMessage()">×</span>
</div>

<div class="container">
    <h2>Stock Management - HNG Products</h2>

    <div class="button-container">
        <a href="sales.php">Record a usage</a>
        <a href="sales_by_date.php">View usage by Date</a>
        <a href="low_stock_report.php?category=HNG">View Low Stock Report</a>
    </div>

    <!-- Add Product Form -->
    <form method="post" action="">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required>

        <label for="unit">Unit of Measurement:</label>
        <select id="unit" name="unit" required>
            <option value="pcs">Pcs</option>
            <option value="box">Box</option>
            <option value="kg">Kg</option>
            <option value="g">Gram</option>
            <option value="liter">Liter</option>
            <option value="ml">Milliliter</option>
            <option value="pack">Pack</option>
            <option value="dozen">Dozen</option>
            <option value="set">Set</option>
            <option value="meter">Meter</option>
            <option value="cm">Centimeter</option>
            <option value="inch">Inch</option>
            <option value="yard">Yard</option>
            <option value="roll">Roll</option>
        </select>

        <label for="price">Price:</label>
        <input type="text" id="price" name="price" required>

        <label for="threshold">Alert Threshold:</label>
        <input type="number" id="threshold" name="threshold" value="10" required>

        <input type="submit" name="add_product" value="Add Product">
    </form>

    <div style="overflow-x:auto;">
        <table>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Actions</th>
                <th>Stock Alert</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['product_name'] . "</td>";
                    echo "<td>" . $row['quantity'] . "</td>";
                    echo "<td>" . $row['unit'] . "</td>";
                    echo "<td>" . $row['price'] . "</td>";
                    echo "<td>
                        <a href='hng_products.php?delete=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a> | 
                        <a href='update.php?id=" . $row['id'] . "'>Update</a>
                    </td>";

                    // Stock alert: Check if quantity is less than or equal to threshold
                    if ($row['quantity'] <= $row['threshold']) {
                        echo "<td class='low-stock'>Low Stock!</td>";
                    } else {
                        echo "<td class='in-stock'>In Stock</td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No HNG products found</td></tr>";
            }
            ?>
        </table>
    </div>
</div>

<!-- Low Stock Notification for HNG Products -->
<div id="notification-hng">
    <span id="notification-message-hng">Low stock alert!</span>
    <span class="close-btn" onclick="closeNotification('HNG')">×</span>
</div>

<script>
    function closeSuccessMessage() {
        document.getElementById('success-message').style.display = 'none';
    }

    // Display success message if product added
    <?php if ($productAdded): ?>
        document.getElementById('success-message').style.display = 'block';
        setTimeout(closeSuccessMessage, 3000); // Automatically close after 3 seconds
    <?php endif; ?>

    let dismissedHNGProducts = [];

    function checkLowStock(category) {
        console.log("Checking low stock for category:", category);

        fetch(`check_low_stock.php?category=${category}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log("Received data:", data);

                let productsToNotify = data.filter(product => !dismissedHNGProducts.includes(product.id));

                if (productsToNotify.length > 0) {
                    let message = 'Low stock alert for: ';
                    productsToNotify.forEach(product => {
                        message += `${product.product_name} (Qty: ${product.quantity} ${product.unit}), `;
                    });

                    document.getElementById('notification-message-hng').innerText = message.slice(0, -2);
                    document.getElementById('notification-hng').style.display = 'block';

                    console.log("Showing alert for:", productsToNotify);
                } else {
                    console.log("No low stock items found.");
                }
            })
            .catch(error => console.error('Error fetching low stock data:', error));
    }

    function closeNotification(category) {
        fetch(`check_low_stock.php?category=${category}`)
            .then(response => response.json())
            .then(data => {
                dismissedHNGProducts = data.map(product => product.id);
            });

        document.getElementById('notification-hng').style.display = 'none';
    }

    // Initially check for low stock when the page loads
    checkLowStock('HNG');

    // Continue checking every 10 seconds
    setInterval(() => checkLowStock('HNG'), 10000); // Check every 10 seconds
</script>
</section>
</body>
</html>

<?php
$conn->close();
?>
