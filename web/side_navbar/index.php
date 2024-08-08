<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        .Navbar {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .Navbar .sidebar {
            width: 250px;
            background-color: #333;
            color: #fff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            transition: width 0.3s;
            overflow: hidden;
        }

        .Navbar .sidebar.closed {
            width: 0;
        }

        .Navbar .sidebar a {
            padding: 15px;
            text-decoration: none;
            color: #fff;
            display: block;
            text-align: left;
            transition: background 0.3s;
        }

        .Navbar .sidebar a:hover {
            background-color: #575757;
        }

        .Navbar .logout {
            margin-top: auto;
            background-color: #444;
        }

        .Navbar .toggle-btn {
            position: fixed;
            top: 10px;
            left: 10px;
            font-size: 20px;
            background-color: #333;
            color: #fff;
            padding: 10px;
            cursor: pointer;
            z-index: 1000;
        }

        .Navbar h1 {
            margin-top: 0;
        }

        /* Dropdown container */
        .Navbar .dropdown {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        /* Dropdown button */
        .Navbar .dropdown-btn {
            padding: 15px;
            width: 100%;
            text-align: left;
            background-color: #333;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .Navbar .dropdown-btn:hover {
            background-color: #575757;
        }

        /* Dropdown content */
        .Navbar .dropdown-content {
            display: none;
            position: absolute;
            background-color: #333;
            min-width: 100%;
            z-index: 1;
            left: 0;
        }

        .Navbar .dropdown-content a {
            padding: 15px;
            color: white;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .Navbar .dropdown-content a:hover {
            background-color: #575757;
        }

        /* Show dropdown when clicked */
        .Navbar .dropdown.active .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>
<section class="Navbar">
    <div class="sidebar" id="sidebar">
        <a href="javascript:void(0)" class="toggle-btn" onclick="toggleSidebar()">☰</a>
        <h2 style="text-align: center; padding: 20px;">Dashboard</h2>
        <a href="room_booking.php">Booking Rooms</a>
        <a href="manage_rooms.php">Manage Rooms</a>
        <a href="#">Add Room</a>

        <!-- Dropdown for Stock Management -->
        <div class="dropdown">
            <button class="dropdown-btn" onclick="toggleDropdown('stockDropdown')">Stock Management ▼</button>
            <div class="dropdown-content" id="stockDropdown">
                <a href="stockmanagement.php?category=HNG">HNG Products</a>
                <a href="stockmanagement.php?category=Other">Other Products</a>
            </div>
        </div>

        <a href="housekeeping.php">Housekeeping</a>
        <a href="#" class="logout">Logout</a>
    </div>
</section>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('closed');
    }

    function toggleDropdown(dropdownId) {
        document.getElementById(dropdownId).parentElement.classList.toggle('active');
    }
</script>

</body>
</html>
