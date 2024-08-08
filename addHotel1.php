<?php
session_start();
include('login_a_check.php');
// Database connection
require 'connection.php';
$conn = Connect();

// Retrieve form data and escape it to prevent SQL injection
$username = isset($_POST['Name']) ? $conn->real_escape_string($_POST['Name']) : '';
$companyName = isset($_POST['Hotel_Name']) ? $conn->real_escape_string($_POST['Hotel_Name']) : '';
$password = isset($_POST['Password']) ? $_POST['Password'] : '';
$contact = isset($_POST['phoneNumber']) ? $conn->real_escape_string($_POST['phoneNumber']) : '';
$address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
$age = isset($_POST['age']) ? $conn->real_escape_string($_POST['age']) : '';
$role = 'Hotel_admin';
$mail = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';

$randomNumber = mt_rand(1000, 9999); // Generates a random number between 1000 and 9999
$u_id = !empty($companyName) ? $companyName . $randomNumber : '';

// Create database if it doesn't exist
$createDatabaseQuery = "CREATE DATABASE IF NOT EXISTS $companyName";
$conn->query($createDatabaseQuery);

// Select the database
$conn->select_db($companyName);

// Create tables if they don't exist
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
$createTableQuery1 = "CREATE TABLE IF NOT EXISTS hotel_profile (
  id int AUTO_INCREMENT PRIMARY KEY,
  AdminName varchar(255) NOT NULL,
  Hotel_Name varchar(255) NOT NULL,
  phoneNumber varchar(15) NOT NULL,
  address text NOT NULL,
  u_id varchar(255) NOT NULL,
  startDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  endDate date NULL,
  OrderDetails varchar(100) NULL,
  email varchar(255) NOT NULL,
  plan varchar(255) NULL
)";
$conn->query($createTableQuery);
$conn->query($createTableQuery1);

// Encrypt the password using bcrypt
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
} else {
    die("Password is required.");
}

// Generate a unique u_id by concatenating employeeName with a random number
if (!empty($username) && !empty($hashedPassword) && !empty($contact) && !empty($address) && !empty($u_id) && !empty($companyName) && !empty($role) && !empty($mail)) {
    // Prepare the SQL statement for adminlogin table
    $stmt = $conn->prepare("INSERT INTO adminlogin (employeeName, Password, phoneNumber, address, u_id, companyName, age, role, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssiss", $username, $hashedPassword, $contact, $address, $u_id, $companyName, $age, $role, $mail);
    
    // Prepare the SQL statement for hotel_profile table
    $stmt1 = $conn->prepare("INSERT INTO hotel_profile (AdminName, phoneNumber, address, u_id, Hotel_Name, email) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt1->bind_param("ssssss", $username, $contact, $address, $u_id, $companyName, $mail);

    // Execute the statements
    if ($stmt->execute() && $stmt1->execute()) {
        
        $phoneNumber = '919360216792'; // Replace with the recipient's phone number
        $message = urlencode("Hello $username, your account has been successfully created with the ID $u_id and $password."); // URL-encode the message

        $whatsappLink = "https://wa.me/$phoneNumber?text=$message";

        // Redirect to WhatsApp link
        echo "<meta http-equiv='refresh' content='0;url=$whatsappLink'>";
        exit();
    } else {
        die("Couldn't enter data: " . $stmt->error);
    }

    $stmt->close();
    $stmt1->close();
} else {
    die("Please fill in all required fields.");
}

$conn->close();
?>
