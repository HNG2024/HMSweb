<?php
session_start();

// Database connection
require 'connection.php';
$conn = Connect();

// Create database if it doesn't exist
$createDatabaseQuery = "CREATE DATABASE IF NOT EXISTS hmsapp1";
$conn->query($createDatabaseQuery);

// Select the database
$conn->select_db('hmsapp1');

// Create table if it doesn't exist
$createTableQuery = "CREATE TABLE IF NOT EXISTS adminlogin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employeeName VARCHAR(255) NOT NULL,
    Password VARCHAR(255) NOT NULL,
    phoneNumber VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    u_id VARCHAR(255) NOT NULL,
    companyName VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    role VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL
)";
$conn->query($createTableQuery);

// Retrieve form data and escape it to prevent SQL injection
$username = isset($_POST['employeeName']) ? $conn->real_escape_string($_POST['employeeName']) : '';
$password = isset($_POST['Password']) ? $_POST['Password'] : '';
$contact = isset($_POST['phoneNumber']) ? $conn->real_escape_string($_POST['phoneNumber']) : '';
$address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
$companyName = isset($_POST['companyName']) ? $conn->real_escape_string($_POST['companyName']) : '';
$age = isset($_POST['age']) ? $conn->real_escape_string($_POST['age']) : '';
$role = isset($_POST['role']) ? $conn->real_escape_string($_POST['role']) : '';
$mail = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';

// Encrypt the password using bcrypt
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $key = bin2hex(random_bytes(12));

} else {
    die("Password is required.");
}

// Generate a unique u_id by concatenating employeeName with a random number
$randomNumber = mt_rand(1000, 9999); // Generates a random number between 1000 and 9999
$u_id = !empty($username) ? $username . $randomNumber : '';

if (!empty($username) && !empty($hashedPassword) && !empty($contact) && !empty($address) && !empty($u_id) && !empty($companyName) && !empty($age) && !empty($role) && !empty($mail)) {
    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO adminlogin (employeeName, Password, phoneNumber, address, u_id, companyName, age, role, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssiss", $username, $hashedPassword, $contact, $address, $u_id, $companyName, $age, $role, $mail);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to a success page
        header("Location: login71.php");
        exit();
    } else {
        die("Couldn't enter data: " . $stmt->error);
    }

    $stmt->close();
} else {
    die("Please fill in all required fields.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link href="images/hg.png" rel="icon">
   <title>Heal and Glow</title>
   <link rel="stylesheet" type="text/css" href="manager_registered_success.css">
</head>
<style>
    body {
        margin: 0;
        padding: 0;
    }
</style>
<body>
<div class="container">
    <div class="jumbotron" style="text-align: center;">
        <h2><?php echo "Welcome $username!" ?></h2>
        <h1>Your account has been created.</h1>
        <p>Login Now from <a href="login71.php">HERE</a></p>
    </div>
</div>
</body>
</html>
