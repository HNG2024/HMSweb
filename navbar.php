<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="imgs/logo.jpg" rel="icon">
    <title>Heal N Glow</title>
    <style>
        .Navbar{
            position: fixed;
           opacity: 9999;
           z-index: 9999;
        }
       .Navbar .navbar body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f4f4f4;
        }

       .Navbar .navbar {
            width: 60px;
            background-color: #fff;
            color: #000;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-right: 1px solid #ddd;
            transition: width 0.3s ease;
            padding-top: 100px;
            z-index: 1000;
        }

       .Navbar .navbar.expanded {
            width: 250px;
        }

       .Navbar .navbar a,.Navbar .navbar .dropdown-btn {
            padding: 10px 15px;
            text-decoration: none;
            color: #000;
            display: flex;
            align-items: center;
            width: 100%;
            transition: background 0.3s;
            box-sizing: border-box;
            position: relative;
            font-size: 14px;
        }

       .Navbar .navbar a:hover,.Navbar .navbar a.active,.Navbar .navbar .dropdown-btn:hover {
            background-color: #f1f3f4;
        }

       .Navbar .navbar a img,.Navbar .navbar .dropdown-btn img {
            width: 24px;
            height: 24px;
            margin-right: 15px;
            transition: transform 0.3s;
        }

       .Navbar .navbar.expanded a img,.Navbar .navbar.expanded .dropdown-btn img {
            transform: translateX(0);
        }

       .Navbar .navbar a span,.Navbar .navbar .dropdown-btn span {
            display: none;
            color: #202124;
            white-space: nowrap;
        }

       .Navbar .navbar.expanded a span,.Navbar .navbar.expanded .dropdown-btn span {
            display: inline;
        }

       .Navbar .navbar .logout {
            position: absolute;
            bottom: 110px; /* Always keep it at the bottom */
            width: 100%;
            background-color: #fff;
            border-top: 1px solid #ddd;
        }

        /* Scrollbar customization */
       .Navbar .navbar::-webkit-scrollbar {
            width: 5px;
        }

       .Navbar .navbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

       .Navbar .navbar::-webkit-scrollbar-thumb {
            background: #888;
        }

       .Navbar .navbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

       .Navbar .navbar .dropdown {
            width: 100%;
        }

       .Navbar .navbar .dropdown-content {
            display: none;
            background-color: #f4f4f4;
            width: 100%;
            padding-left: 30px; /* Indent the dropdown content */
            box-sizing: border-box;
        }

       .Navbar .navbar.expanded .dropdown-content {
            display: block;
        }

       .Navbar .navbar .dropdown-content a {
            padding: 10px 0;
            color: #000;
            text-decoration: none;
            display: block;
            text-align: left;
            font-size: 14px;
        }

       .Navbar .navbar .dropdown-content a:hover {
            background-color: #e7e7e7;
        }

        /* Toggle button outside of the navbar */
       .Navbar .toggle-btn-wrapper {
            position: fixed;
            top: 5px; /* Adjust this value to control vertical positioning */
            left: 5px; /* Adjust this value based on the navbar's width */
            z-index: 1100; /* Ensure it's above the navbar */
            transition: left 0.3s ease; /* Smooth transition when navbar expands/collapses */
            margin: 5px;
        }

       .Navbar .toggle-btn {
            background-color: #f4f4f4;
            color: #000;
            padding: 10px;
            cursor: pointer;
            text-align: center;
            width: 30px; /* Adjust the size of the toggle button */
            height: 30px; /* Make it a square button */
            border-radius: 20%; /* Make it circular */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Optional: Add a slight shadow for depth */
            display: flex;
            justify-content: center;
            align-items: center;
        }

       .Navbar .toggle-btn:hover {
            background-color: #e7e7e7;
        }

       .Navbar .toggle-btn img {
            width: 20px;
            height: 20px;
        }

       .Navbar .navbar.expanded ~ .toggle-btn-wrapper {
            left: 260px; /* Adjust this value to match the expanded navbar width */
        }

        @media (max-width: 768px) {
           .Navbar .navbar {
                width: 50px;
            }

           .Navbar .navbar.expanded {
                width: 200px;
            }

           .Navbar .toggle-btn-wrapper {
                left: 0px;
            }

           .Navbar .navbar.expanded ~ .toggle-btn-wrapper {
                left: 210px;
            }

           .Navbar .navbar a span,.Navbar .navbar .dropdown-btn span {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
<section class="Navbar">
    <div class="toggle-btn-wrapper" id="toggleBtnWrapper">
        <div class="toggle-btn" onclick="toggleNavbar()">
            <img id="toggleIcon" src="imgs/open.png" alt="Toggle Button">
        </div>
    </div>
    <div class="navbar" id="navbar">
        <a href="index.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="active"' : ''; ?>>
            <img src="imgs/hotel1.png" alt="Home">
            <span>Home</span>
        </a>
        
         <?php
        $allowed_roles = ['admin'];
        if (in_array($role, $allowed_roles)) {
        ?>
        <a href="addHotel.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'addHotel.php') ? 'class="active"' : ''; ?>>
            <img src="imgs/hotel1.png" alt="Add Hotel">
            <span>Add Hotel</span>
        </a>
        <?php } ?>
        
        <?php
        list($myusername, $u_id, $role) = explode('|', $_SESSION['login_user']);
        $allowed_roles = ['admin', 'hotel_admin', 'manager'];
        if (in_array($role, $allowed_roles)) {
        ?>
        <a href="room_booking.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'room_booking.php') ? 'class="active"' : ''; ?>>
            <img src="imgs/roombooking.png" alt="Booking Rooms">
            <span>Booking Rooms</span>
        </a>
        <a href="manage_rooms.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_rooms.php') ? 'class="active"' : ''; ?>>
            <img src="imgs/mr.png" alt="Manage Rooms">
            <span>Manage Rooms</span>
        </a>
        <?php } ?>

        <?php
        $allowed_roles = ['admin', 'hotel_admin'];
        if (in_array($role, $allowed_roles)) {
        ?>
        <a href="addRoom.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'addRoom.php') ? 'class="active"' : ''; ?>>
            <img src="imgs/addrooms.png" alt="Add Room">
            <span>Add Room</span>
        </a>
        <?php } ?>

       
        <div class="dropdown">
            <button class="dropdown-btn" onclick="toggleDropdown('stockDropdown')">
                <img src="imgs/sm.png" alt="Stock Management">
                <a href="https://hgstore.in/stockmanagement.php"><span>Stock Management</span></a>
            </button>
            <div class="dropdown-content" id="stockDropdown">
                <a href="hng_products.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'hng_products.php') ? 'class="active"' : ''; ?>>HNG Products</a>
                <a href="other_products.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'other_products.php') ? 'class="active"' : ''; ?>>Other Products</a>
                 <a href="OrderProduct.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'OrderProduct.php') ? 'class="active"' : ''; ?>>Order Products</a>
            </div>
        </div>
       

        <?php
        if (in_array($role, ['admin', 'hotel_admin', 'manager', 'employee'])) {
        ?>
        <a href="Housekeeping.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'housekeeping.php') ? 'class="active"' : ''; ?>>
            <img src="imgs/hk.png" alt="Housekeeping">
            <span>Housekeeping</span>
        </a>
        <?php } ?>

        <?php
        if (in_array($role, ['admin', 'hotel_admin', 'manager'])) {
        ?>
        <a href="invoice_list.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'invoice_list.php') ? 'class="active"' : ''; ?>>
            <img src="imgs/invoice.png" alt="Invoice">
            <span>Invoice</span>
        </a>
        <?php } ?>

        <a href="logout.php">
            <img src="imgs/logout.png" alt="Logout">
            <span>Logout</span>
        </a>
    </div>
</section>

<script>
    const navbar = document.getElementById('navbar');
    const toggleIcon = document.getElementById('toggleIcon');
    let isManuallyToggled = false; // Track if the navbar was manually toggled

    // Function to toggle the navbar open/close state
    function toggleNavbar() {
        isManuallyToggled = !isManuallyToggled; // Toggle the manual toggle flag
        const isExpanded = navbar.classList.toggle('expanded');
        toggleIcon.src = isExpanded ? "imgs/close.png" : "imgs/open.png";
        localStorage.setItem('sidebar-expanded', isExpanded);
    }

    // Restore the state of the sidebar on page load
    if (localStorage.getItem('sidebar-expanded') === 'true') {
        navbar.classList.add('expanded');
        toggleIcon.src = "imgs/close.png";
    } else {
        navbar.classList.remove('expanded');
        toggleIcon.src = "imgs/open.png";
    }

    // Add event listeners for dynamic sidebar
    navbar.addEventListener('mouseover', () => {
        if (!isManuallyToggled) {
            navbar.classList.add('expanded');
            toggleIcon.src = "imgs/close.png";
        }
    });

    navbar.addEventListener('mouseout', () => {
        if (!isManuallyToggled) {
            navbar.classList.remove('expanded');
            toggleIcon.src = "imgs/open.png";
        }
    });

    // Function to toggle dropdown menus
    function toggleDropdown(dropdownId) {
        document.getElementById(dropdownId).parentElement.classList.toggle('active');
    }

    // Auto close navbar if opened manually after mouseout
    navbar.addEventListener('mouseout', () => {
        if (isManuallyToggled && !navbar.classList.contains('expanded')) {
            isManuallyToggled = false;
        }
    });
</script>

</body>
</html>