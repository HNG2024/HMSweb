<?php
include('connection.php');

// Start the session securely
session_start();

if (!isset($_SESSION['login_user'])) {
    header("location: login71.php");
    exit;
}

// Establish the database connection
$conn = Connect();

$user_check = $_SESSION['login_user'];

// Split the session value into username, u_id, and role (assuming role is included in the session)
list($myusername, $u_id, $role) = explode('|', $user_check);

// Derive the database name based on the u_id
$u_id1 = substr($u_id, 0, -4);

// Select the appropriate database
$conn->select_db($u_id1);

// SQL Query to fetch complete information of the user
$query = "SELECT employeeName, u_id FROM adminlogin WHERE employeeName = ? AND u_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $myusername, $u_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$login_session = $row['employeeName'];
$login_u_id = $row['u_id'];

// If the session information doesn't match any record in the database, redirect to login
if (empty($login_session)) {
    header("location: login71.php");
    exit;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
