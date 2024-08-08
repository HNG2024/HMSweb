<?php
session_start();

// If the user is already logged in, redirect them to the index page
if (isset($_SESSION['login_user'])) {
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="img/logo.png" rel="icon">
    <title>Login - Heal and Glow</title>
    <link rel="stylesheet" href="Style1.css">
</head>
<body>
    
    <section class="log-page">
        <div class="container">
            <h2>Login</h2>
            <form action="login7.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" name="username" class="box" required><br /><br />
                
                <label for="u_id">Hotel ID:</label>
                <input type="text" name="u_id" class="box" required><br /><br />
                
                <label for="password">Password:</label>
                <input type="password" name="password" class="box" required><br /><br />
                
                <input type="submit" name="submit" value="Submit"><br />
            </form>
            
            <div class="error-message">
                <?php
                // Display error message if exists
                if (isset($_SESSION['error'])) {
                    echo $_SESSION['error'];
                    unset($_SESSION['error']); // Clear the error message after displaying it
                }
                ?>
            </div>
        </div>
    </section>
</body>
</html>
