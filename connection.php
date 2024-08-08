<?php
// connection.php

// Check if the Connect() function is already defined
if (!function_exists('Connect')) {
    function Connect()
    {
        $dbhost = "localhost";
        $dbuser = "root";
        $dbpass = "APESVBtt4a19nZTP71";
        $dbname = "hmsapp1";

        // Create Connection
        $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    }
}
?>
