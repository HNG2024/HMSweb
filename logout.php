<?php
session_start();
if(session_destroy()) // Destroying All Sessions
{
header("Location: login71.php"); // Redirecting To Home Page
}
?>