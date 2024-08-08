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
include('navbar.php');

// Fetch products
$sql = "SELECT id, product_name FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Product</title>
    <style>
        /* CSS styles for the page */
        .orderproduct {
            font-family: Arial, sans-serif;
            background-color: #f4f5f7;
            margin: 0;
            padding: 0;
        }
       .orderproduct .order-container {
            width: 80%;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
       .orderproduct .order-container h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }
      .orderproduct .orderproduct .order-container form {
            width: 100%;
            margin-bottom: 20px;
        }
       .orderproduct .order-container form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
       .orderproduct .order-container form select, 
       .orderproduct .order-container form input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            color: #333;
        }
       .orderproduct .order-container form input[type="button"] {
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
       .orderproduct .order-container form input[type="button"]:hover {
            background-color: #16a085;
        }
       .orderproduct .order-container .add-product-button {
            margin-top: 15px;
            background-color: #3498db;
            color: #fff;
        }
       .orderproduct .order-container .add-product-button:hover {
            background-color: #2980b9;
        }
       .orderproduct .preview-container {
            display: none;
            margin-top: 20px;
        }
       .orderproduct .preview-container table {
            width: 100%;
            border-collapse: collapse;
        }
       .orderproduct .preview-container th, .preview-container td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
       .orderproduct .preview-container th {
            background-color: #3498db;
            color: white;
        }
       .orderproduct .preview-container td {
            background-color: #ecf0f1;
        }
       .orderproduct .preview-buttons {
            margin-top: 20px;
            text-align: center;
        }
       .orderproduct .preview-buttons button {
            background-color: #1abc9c;
            color: #fff;
            padding: 10px 25px;
            border: none;
            border-radius: 25px;
            margin: 0 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
       .orderproduct .preview-buttons button:hover {
            background-color: #16a085;
        }
    </style>
    <script>
        function addProductRow() {
            const productRow = document.createElement('div');
            productRow.className = 'product-row';

            productRow.innerHTML = `
                <label for="product[]">Select Product:</label>
                <select name="product[]" required>
                    <option value="">Select a product</option>
                    <?php foreach ($products as $product) { ?>
                        <option value="<?php echo $product['product_name']; ?>"><?php echo $product['product_name']; ?></option>
                    <?php } ?>
                </select>

                <label for="quantity[]">Quantity:</label>
                <input type="number" name="quantity[]" required>

                <label for="unit[]">Unit of Measurement:</label>
                <select name="unit[]" required>
                    <option value="pcs">Pcs</option>
                    <option value="box">Box</option>
                    <option value="kg">Kg</option>
                    <option value="g">Gram</option>
                    <option value="liter">Liter</option>
                    <option value="ml">Milliliter</option>
                </select>

                <button type="button" class="remove-product-button" onclick="removeProductRow(this)">Remove Product</button>
                <hr>
            `;
            document.getElementById('productsContainer').appendChild(productRow);
        }

        function removeProductRow(button) {
            const productRow = button.parentElement;
            productRow.remove();
        }

        function previewOrder() {
            const products = document.getElementsByName('product[]');
            const quantities = document.getElementsByName('quantity[]');
            const units = document.getElementsByName('unit[]');

            let previewHtml = '';

            for (let i = 0; i < products.length; i++) {
                if (products[i].value && quantities[i].value && units[i].value) {
                    previewHtml += `
                        <tr>
                            <td>${products[i].value}</td>
                            <td>${quantities[i].value}</td>
                            <td>${units[i].value}</td>
                        </tr>
                    `;
                }
            }

            document.getElementById('previewTableBody').innerHTML = previewHtml;

            document.querySelector('.preview-container').style.display = 'block';
        }

        function sendOrder() {
            const products = document.getElementsByName('product[]');
            const quantities = document.getElementsByName('quantity[]');
            const units = document.getElementsByName('unit[]');

            let message = "Order Details:%0A";
            for (let i = 0; i < products.length; i++) {
                if (products[i].value && quantities[i].value && units[i].value) {
                    message += `${products[i].value}: ${quantities[i].value} ${units[i].value}%0A`;
                }
            }

            const phone = "919360216792"; // Replace with your WhatsApp number
            window.open("https://wa.me/" + phone + "?text=" + message, "_blank");
        }

        function downloadPDF() {
            const products = document.getElementsByName('product[]');
            const quantities = document.getElementsByName('quantity[]');
            const units = document.getElementsByName('unit[]');

            let pdfContent = "Order Details:\n";
            for (let i = 0; i < products.length; i++) {
                if (products[i].value && quantities[i].value && units[i].value) {
                    pdfContent += `${products[i].value}: ${quantities[i].value} ${units[i].value}\n`;
                }
            }

            const blob = new Blob([pdfContent], { type: "application/pdf" });
            const link = document.createElement("a");
            link.href = window.URL.createObjectURL(blob);
            link.download = "Order_Quotation.pdf";
            link.click();
        }
    </script>
</head>
<body>
    
    <section class="orderproduct">
<div class="order-container">
    <h2>Order Product</h2>

    <form id="orderForm">
        <div id="productsContainer">
            <!-- Product Rows will be added here dynamically -->
            <div class="product-row">
                <label for="product[]">Select Product:</label>
                <select name="product[]" required>
                    <option value="">Select a product</option>
                    <?php foreach ($products as $product) { ?>
                        <option value="<?php echo $product['product_name']; ?>"><?php echo $product['product_name']; ?></option>
                    <?php } ?>
                </select>

                <label for="quantity[]">Quantity:</label>
                <input type="number" name="quantity[]" required>

                <label for="unit[]">Unit of Measurement:</label>
                <select name="unit[]" required>
                    <option value="pcs">Pcs</option>
                    <option value="box">Box</option>
                    <option value="kg">Kg</option>
                    <option value="g">Gram</option>
                    <option value="liter">Liter</option>
                    <option value="ml">Milliliter</option>
                </select>
                <hr>
            </div>
        </div>

        <button type="button" class="add-product-button" onclick="addProductRow()">Add Another Product</button>
        <input type="button" value="Preview Order" onclick="previewOrder()">
    </form>

    <div class="preview-container">
        <h3>Order Preview</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody id="previewTableBody">
                <!-- Preview rows will be inserted here -->
            </tbody>
        </table>

        <div class="preview-buttons">
            <button onclick="sendOrder()">Send via WhatsApp</button>
            <button onclick="downloadPDF()">Download as PDF</button>
        </div>
    </div>
</div>
</section>
</body>
</html>

<?php
$conn->close();
?>
