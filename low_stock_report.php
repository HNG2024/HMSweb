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

// Get the category from the URL, default to 'HNG'
$category = isset($_GET['category']) ? $_GET['category'] : 'HNG';

// Fetch products with low stock based on the selected category
if ($category === 'HNG') {
    $sql = "SELECT * FROM products WHERE quantity < threshold &&  category ='HNG'";
} else {
    $sql = "SELECT * FROM products WHERE quantity < threshold &&  category ='Other'";
}
$low_stock = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Report - <?php echo $category; ?> Products</title>
    <style>
        /* General Styles */
        .low_stock {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f5f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .low_stock .container {
            width: 80%;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .low_stock h2 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        .low_stock a {
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

        .low_stock a:hover {
            background-color: #16a085;
        }

        .low_stock table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ecf0f1;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .low_stock th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .low_stock th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }

        .low_stock tr:nth-child(even) {
            background-color: #f2f3f4;
        }

        .low_stock tr:hover {
            background-color: #dcdde1;
        }

        .low_stock td {
            font-weight: 500;
        }
    </style>
</head>
<body>

<section class="low_stock">
    <div class="container">
        
        <h2>Low Stock Report - <?php echo $category; ?> Products</h2>
        <a href="javascript:history.back()">Back</a>
<br><br>
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
                    echo "<td style='color: red; font-weight: bold;'>Low Stock!</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No products with low stock in " . $category . "</td></tr>";
            }
            ?>
        </table>
    </div>
</section>
</body>
</html>

<?php
$conn->close();
?>
