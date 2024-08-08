<?php
include("connection.php");
session_start();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = Connect();

    // Escape special characters in form inputs
    $myusername = mysqli_real_escape_string($conn, $_POST['username']);
    $u_id = mysqli_real_escape_string($conn, $_POST['u_id']);
    $mypassword = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Remove the last 4 characters from the u_id to get u_id1
    $u_id1 = substr($u_id, 0, -4);
    
    // Use prepared statements to prevent SQL injection
    $conn->select_db($u_id1);
    $stmt = $conn->prepare("SELECT employeeName, Password, Role FROM adminlogin WHERE employeeName = ? AND u_id = ?");
    $stmt->bind_param("ss", $myusername, $u_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Compare the entered password with the hashed password in the database
        if (password_verify($mypassword, $row['Password'])) {
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            // Store the role in the session
            $_SESSION['login_user'] = $myusername . '|' . $u_id . '|' . $row['Role'];

            header("location: index.php");
            exit();
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

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
}
?>
