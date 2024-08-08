<?php
// Include the database connection
include 'db_connection.php';

$productAdded = false; // Initialize a flag for success message

// Add product
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $price = $_POST['price'];
    $threshold = $_POST['threshold'];
    $category = $_POST['category'];

    $sql = "INSERT INTO products (product_name, quantity, unit, price, threshold, category) 
            VALUES ('$product_name', $quantity, '$unit', $price, $threshold, '$category')";
    if ($conn->query($sql) === TRUE) {
        $productAdded = true; // Set flag to true on success
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Delete product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM products WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Product deleted successfully";
        $sql_resequence = "
            SET @count = 0;
            UPDATE products SET id = @count:= @count + 1;
            ALTER TABLE products AUTO_INCREMENT = 1;
        ";
        $conn->multi_query($sql_resequence);
        header("Location: stockmanagement.php");
        exit();
    } else {
        echo "Error deleting product: " . $conn->error;
    }
}

// Fetch Products Based on Category
$category = isset($_GET['category']) ? $_GET['category'] : 'HNG';
$sql = "SELECT * FROM products WHERE category='$category'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Stock Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* General body styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        /* Center the content on the page */
        .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            transition: margin-left 0.3s ease; /* Smooth transition for content shifting */
        }

        /* Add margin-left to account for the navbar width */
        @media (min-width: 768px) {
            .content {
                margin-left: 250px; /* Adjust this value based on the width of the expanded navbar */
            }
        }

        /* Table styling */
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        a {
            text-decoration: none;
            color: #333;
        }
        a:hover {
            text-decoration: underline;
        }

        /* Responsive form styling */
        form {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        form input, form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Success Message Pop-up Box Styling */
        #success-message {
            display: none; /* Initially hidden */
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

        /* Notification styling */
        #notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #f44336;
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: none; /* Hide by default */
            z-index: 1000; /* Make sure it stays on top of other elements */
        }

        #notification .close-btn {
            margin-left: 15px;
            color: white;
            cursor: pointer;
            float: right;
        }

        /* Media Queries */
        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }
            form {
                padding: 10px;
            }
            form input, form select {
                padding: 8px;
            }
            #notification {
                width: 90%;
                right: 5%;
                bottom: 10px;
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .content {
                margin-left: 60px; /* Adjust content margin for small screens */
            }
            table {
                font-size: 10px;
            }
            form {
                padding: 5px;
            }
            form input, form select {
                padding: 6px;
            }
            #notification {
                width: 100%;
                right: 0;
                bottom: 0;
                border-radius: 0;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
<?php include 'index.php'; ?> <!-- Your linked navbar -->

<!-- Success Message Container -->
<div id="success-message">
    Product added successfully!
    <span class="close-btn" onclick="closeSuccessMessage()">×</span>
</div>

<div class="content">
    <h2 style="text-align: center;">Stock Management - <?php echo $category; ?> Products</h2>

    <div style="text-align: center;">
        <a href="sales.php">Record a usage</a> |
        <a href="sales_by_date.php">View usage by Date</a> |
        <a href="low_stock_report.php">View Low Stock Report</a>
    </div><br>

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

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="HNG">HNG Products</option>
            <option value="Other">Other Products</option>
        </select>

        <input type="submit" name="add_product" value="Add Product">
    </form>

    <h2 style="text-align: center;"><?php echo $category; ?> Products</h2>
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
                        <a href='stockmanagement.php?delete=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a> | 
                        <a href='update.php?id=" . $row['id'] . "'>Update</a>
                    </td>";

                    // Stock alert: Check if quantity is less than or equal to threshold
                    if ($row['quantity'] <= $row['threshold']) {
                        echo "<td style='color: red;'>Low Stock!</td>";
                    } else {
                        echo "<td>In Stock</td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No " . $category . " products found</td></tr>";
            }
            ?>
        </table>
    </div>
</div>

<div id="notification">
    <span id="notification-message">Low stock alert!</span>
    <span class="close-btn" onclick="closeNotification()">×</span>
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

    let dismissedProducts = [];

    function checkLowStock() {
        fetch('check_low_stock.php?category=<?php echo $category; ?>')
            .then(response => response.json())
            .then(data => {
                let productsToNotify = data.filter(product => !dismissedProducts.includes(product.id));

                if (productsToNotify.length > 0) {
                    let message = 'Low stock alert for: ';
                    productsToNotify.forEach(product => {
                        message += `${product.product_name} (Qty: ${product.quantity} ${product.unit}), `;
                    });
                    document.getElementById('notification-message').innerText = message.slice(0, -2); // Remove the last comma and space
                    document.getElementById('notification').style.display = 'block';
                }
            });
    }

    function closeNotification() {
        fetch('check_low_stock.php?category=<?php echo $category; ?>')
            .then(response => response.json())
            .then(data => {
                 dismissedProducts = data.map(product => product.id);
            }); 
        document.getElementById('notification').style.display = 'none';
    }

    // Initially check for low stock when the page loads
    checkLowStock();

    // Continue checking every 10 seconds
    setInterval(checkLowStock, 10000); // Check every 10 seconds
</script>
</body>
</html>

<?php
$conn->close();
?>