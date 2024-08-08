<?php
include('session.php'); // Ensure session initialization and connection code is included

// Check if the session is started and the user is logged in
if (!isset($_SESSION['login_user'])) {
    header('Location: login71.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f4f5f7;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #2a9d8f;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 3em;
            font-weight: 700;
        }

        nav {
            display: flex;
            justify-content: center;
            background-color: #264653;
            padding: 15px 0;
        }

        nav a {
            text-decoration: none;
            color: #ffffff;
            padding: 10px 25px;
            margin: 0 15px;
            border-radius: 25px;
            transition: background-color 0.3s, transform 0.3s;
            font-weight: 700;
        }

        nav a:hover {
            background-color: #e76f51;
            transform: translateY(-3px);
        }

        main {
            flex-grow: 1;
            padding: 50px 20px;
            text-align: center;
        }

        .welcome {
            font-size: 1.8em;
            margin-bottom: 30px;
            color: #264653;
        }

        .intro {
            font-size: 1.2em;
            margin-bottom: 50px;
            color: #666;
            line-height: 1.6;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .features {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .feature {
            background-color: #e9c46a;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
            width: 30%;
            margin: 20px;
            transition: transform 0.3s;
        }

        .feature:hover {
            transform: scale(1.05);
        }

        .feature h2 {
            margin-top: 0;
            color: #264653;
            font-size: 1.8em;
        }

        .feature p {
            color: #333;
            font-size: 1.1em;
        }

        .cta {
            margin: 50px 0;
        }

        .cta a {
            background-color: #264653;
            color: white;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 5px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, transform 0.3s;
            font-size: 1.2em;
            font-weight: 700;
        }

        .cta a:hover {
            background-color: #2a9d8f;
            transform: translateY(-3px);
        }

        footer {
            background-color: #264653;
            color: white;
            text-align: center;
            padding: 20px 0;
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.1);
        }

        footer p {
            margin: 0;
            font-size: 1em;
        }
    </style>
</head>
<body>
    <header>
        <h1>Hotel Management System</h1>
    </header>

    <?php include('navbar.php'); ?>

    <main>
        <h1 class="welcome">Welcome, <?php echo "$myusername"; ?></h1>

        <div class="intro">
            <p>Our Hotel Management System is designed to streamline your operations, enhance guest experiences, and simplify your day-to-day tasks. Whether you are managing a small boutique hotel or a large chain, our system offers all the tools you need to ensure smooth and efficient management.</p>
        </div>

        <div class="features">
            <div class="feature">
                <h2>Room Management</h2>
                <p>Easily manage room bookings, availability, and room types. Our system provides a real-time overview of room occupancy and availability, allowing you to optimize room usage and enhance guest satisfaction.</p>
            </div>
            <div class="feature">
                <h2>Guest Services</h2>
                <p>Deliver exceptional guest services with our integrated solutions. From check-in to check-out, our system ensures a seamless guest experience, complete with personalized service options and real-time updates.</p>
            </div>
            <div class="feature">
                <h2>Billing & Invoicing</h2>
                <p>Handle all your billing and invoicing needs effortlessly. Our system automates billing processes, generates detailed invoices, and integrates with your payment systems to ensure a smooth financial operation.</p>
            </div>
            <div class="feature">
                <h2>Housekeeping</h2>
                <p>Optimize your housekeeping operations with real-time updates and task management. Our system allows you to assign tasks, track progress, and ensure that rooms are always ready for your guests.</p>
            </div>
            <div class="feature">
                <h2>Inventory Management</h2>
                <p>Keep track of your hotel’s inventory with ease. Our system provides real-time inventory updates, allowing you to manage supplies, monitor usage, and reduce waste effectively.</p>
            </div>
            <div class="feature">
                <h2>Reporting & Analytics</h2>
                <p>Gain insights into your hotel's performance with our comprehensive reporting and analytics tools. Monitor key metrics, track trends, and make informed decisions to drive your business forward.</p>
            </div>
        </div>

        <div class="cta">
            <a href="room_booking.php">Get Started</a>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> HNG. All rights reserved.</p>
    </footer>
</body>
</html>
