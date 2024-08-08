<?php
include("connection.php");
session_start();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish connection
    $conn = Connect();

    // Escape special characters in form inputs
    $myusername = mysqli_real_escape_string($conn, $_POST['username']);
    $u_id = mysqli_real_escape_string($conn, $_POST['u_id']);
    $mypassword = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to fetch the user with matching username and u_id
    $sql = "SELECT employeeName, Password FROM adminlogin WHERE employeeName = '$myusername' AND u_id = '$u_id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // Compare the entered password with the hashed password in the database
        if (password_verify($mypassword, $row['Password'])) {
            $_SESSION['login_user'] = $myusername;
            header("location: index.php");
            exit(); // Make sure to exit after redirecting
        } else {
            $error = "Your Login Name or Password is invalid";
        }
    } else {
        $error = "Your Login Name or Password is invalid";
    }

    // If there's an error, store it in the session and redirect back to login page
    if (!empty($error)) {
        $_SESSION['error'] = $error;
        header("location: login71.php");
        exit();
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
