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
// Fetch all HNG Products
$sql_hng = "SELECT * FROM products WHERE category ='HNG'";
$hng_products = $conn->query($sql_hng);

// Fetch all Other Products
$sql_other = "SELECT * FROM products WHERE category ='Other'";
$other_products = $conn->query($sql_other);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Stock Management - All Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* General body styling */
        .stockmanagement {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        /* Center the content on the page */
        .stockmanagement .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Table styling */
        .stockmanagement table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        .stockmanagement th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .stockmanagement th {
            background-color: #f2f2f2;
        }
        .stockmanagement tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?> <!-- Your linked navbar -->

<section class="stockmanagement">
<div class="content">
    <h2 style="text-align: center;">All Products</h2>

    <h3>HNG Products</h3>
    <div style="overflow-x:auto;">
        <table>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Stock Alert</th>
            </tr>
            <?php
            if ($hng_products->num_rows > 0) {
                while($row = $hng_products->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['product_name'] . "</td>";
                    echo "<td>" . $row['quantity'] . "</td>";
                    echo "<td>" . $row['unit'] . "</td>";
                    echo "<td>" . $row['price'] . "</td>";

                    // Stock alert: Check if quantity is less than or equal to threshold
                    if ($row['quantity'] <= $row['threshold']) {
                        echo "<td style='color: red;'>Low Stock!</td>";
                    } else {
                        echo "<td>In Stock</td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No HNG products found</td></tr>";
            }
            ?>
        </table>
    </div>

    <h3>Other Products</h3>
    <div style="overflow-x:auto;">
        <table>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Stock Alert</th>
            </tr>
            <?php
            if ($other_products->num_rows > 0) {
                while($row = $other_products->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['product_name'] . "</td>";
                    echo "<td>" . $row['quantity'] . "</td>";
                    echo "<td>" . $row['unit'] . "</td>";
                    echo "<td>" . $row['price'] . "</td>";

                    // Stock alert: Check if quantity is less than or equal to threshold
                    if ($row['quantity'] <= $row['threshold']) {
                        echo "<td style='color: red;'>Low Stock!</td>";
                    } else {
                        echo "<td>In Stock</td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No Other products found</td></tr>";
            }
            ?>
        </table>
    </div>
</div>
</section>
</body>
</html>

<?php
$conn->close();
?>
