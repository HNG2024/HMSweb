<?php
$servername = "localhost";
$username = "root";  // Replace with your MySQL username
$password = "";      // Replace with your MySQL password
$dbname = "housekeeping_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch room data
$roomsQuery = "SELECT * FROM rooms";
$roomsResult = $conn->query($roomsQuery);

// Check if the query was successful
if (!$roomsResult) {
    die("Error retrieving rooms: " . $conn->error);
}

if ($roomsResult->num_rows > 0) {
    // We have results, proceed to display them
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Housekeeping Management</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header nav {
            font-size: 14px;
            color: #888;
        }

        .filters {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .filters select,
        .filters input[type="text"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #fff;
            width: 18%;
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            box-sizing: border-box;
        }

        .room-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .room-table th,
        .room-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .room-table th {
            background-color: #f4f7fc;
            font-weight: bold;
        }

.more-icon {
    cursor: pointer;
    font-size: 18px;
    color: #888;
    margin-left: 10px;
    position: relative;
    display: inline-block;
}

.action-menu {
    display: none;
    position: absolute;
    background-color: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    border-radius: 5px;
    left:10;
    z-index: 100;
    min-width: 150px; /* Reduce the minimum width for a more compact menu */
    padding: 0; /* Remove padding around the menu */
    margin: 0; /* Remove any margin */
}

.action-menu button {
    display: block;
    width: 100%;
    padding: 5px 10px; /* Reduce padding for a more compact button */
    border: none;
    background: none;
    text-align: left;
    cursor: pointer;
    color: #333;
    font-size: 12px; /* Reduce font size for a more compact look */
    margin: 0; /* Remove margin to eliminate extra space */
}

.action-menu button:hover {
    background-color: #f0f0f0;
}

.action-menu button img {
    vertical-align: middle; /* Align icons properly with text */
    margin-right: 5px; /* Reduce space between icon and text */
}


        /* Dropdowns for Status and Priority */
        .status-dropdown,
        .priority-dropdown {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #fff;
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }

        /* Color-coded selected values */
        .status-available { background-color: black; color: white; } /* Changed to black */
        .status-occupied { background-color: orange; color: white; } /* Changed to orange */
        .status-out-of-order { background-color: darkred; color: white; } /* Changed to dark red */
        .status-turnover { background-color: darkblue; color: white; } /* Changed to dark blue */

        .priority-low { background-color: green; color: white; } /* Changed to green */
        .priority-high { background-color: maroon; color: white; } /* Changed to maroon */

        /* To make the default selected color visible */
        .status-dropdown option:checked { 
            background-color: inherit;
        }

        .priority-dropdown option:checked { 
            background-color: inherit;
        }

        /* Add Remark Icon */
        .remark-icon {
            display: inline-block;
            cursor: pointer;
            color: #888;
            transition: transform 0.3s ease-in-out;
            margin-right: 5px;
        }

        .remark-icon:hover {
            transform: scale(1.2);
            color: #333;
        }

        .remark-text {
            display: inline-block;
            color: #333;
            font-weight: bold;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            function updateDatabase(action, roomId, value = '') {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'housekeeping_update.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log('Update successful:', xhr.responseText);
                    } else {
                        console.error('Update failed:', xhr.statusText);
                    }
                };
                xhr.send(`action=${action}&roomId=${roomId}&value=${encodeURIComponent(value)}`);
            }

            function updateStatusColor(selectElement) {
                // Remove previous class if exists
                selectElement.classList.remove('status-available', 'status-occupied', 'status-out-of-order', 'status-turnover');
                // Add the new class based on the selected option
                selectElement.classList.add(selectElement.options[selectElement.selectedIndex].className);
            }

            function updatePriorityColor(selectElement) {
                // Remove previous class if exists
                selectElement.classList.remove('priority-low', 'priority-high');
                // Add the new class based on the selected option
                selectElement.classList.add(selectElement.options[selectElement.selectedIndex].className);
            }

            function filterRooms() {
                const roomSearchInput = document.getElementById('room-search').value.toLowerCase();
                const roomTypeFilter = document.getElementById('room-type-filter').value.toLowerCase();
                const statusFilter = document.getElementById('housekeeping-status-filter').value.toLowerCase();
                const priorityFilter = document.getElementById('priority-filter').value.toLowerCase();
                const floorFilter = document.getElementById('floor-filter').value.toLowerCase();

                document.querySelectorAll('#room-list tr').forEach(row => {
                    const roomNumber = row.cells[0].textContent.toLowerCase();
                    const roomType = row.cells[1].textContent.toLowerCase();
                    const status = row.querySelector('.status-dropdown').value.toLowerCase();
                    const priority = row.querySelector('.priority-dropdown').value.toLowerCase();
                    const floor = row.cells[4].textContent.toLowerCase();

                    const matchesRoomNumber = !roomSearchInput || roomNumber.includes(roomSearchInput);
                    const matchesRoomType = !roomTypeFilter || roomType === roomTypeFilter;
                    const matchesStatus = !statusFilter || status === statusFilter;
                    const matchesPriority = !priorityFilter || priority === priorityFilter;
                    const matchesFloor = !floorFilter || floor === floorFilter;

                    if (matchesRoomNumber && matchesRoomType && matchesStatus && matchesPriority && matchesFloor) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            document.getElementById('room-search').addEventListener('input', filterRooms);
            document.getElementById('room-type-filter').addEventListener('change', filterRooms);
            document.getElementById('housekeeping-status-filter').addEventListener('change', filterRooms);
            document.getElementById('priority-filter').addEventListener('change', filterRooms);
            document.getElementById('floor-filter').addEventListener('change', filterRooms);

            document.querySelectorAll('.status-dropdown').forEach(select => {
                updateStatusColor(select);
                select.addEventListener('change', function() {
                    const roomId = select.closest('tr').getAttribute('data-id');
                    updateDatabase('status', roomId, select.value);
                    updateStatusColor(select); // Ensure the color updates immediately
                });
            });

            document.querySelectorAll('.priority-dropdown').forEach(select => {
                updatePriorityColor(select);
                select.addEventListener('change', function() {
                    const roomId = select.closest('tr').getAttribute('data-id');
                    updateDatabase('priority', roomId, select.value);
                    updatePriorityColor(select); // Ensure the color updates immediately
                });
            });

            document.querySelectorAll('.more-icon').forEach(icon => {
                icon.addEventListener('click', function(event) {
                    event.stopPropagation();
                    const actionMenu = this.nextElementSibling;
                    document.querySelectorAll('.action-menu').forEach(menu => {
                        if (menu !== actionMenu) {
                            menu.style.display = 'none';
                        }
                    });
                    actionMenu.style.display = actionMenu.style.display === 'block' ? 'none' : 'block';
                });
            });

            document.addEventListener('click', function() {
                document.querySelectorAll('.action-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
            });

            let currentRow;

            document.querySelectorAll('.clear-status-btn').forEach(button => {
                button.addEventListener('click', function() {
                    currentRow = this.closest('tr');
                    const roomId = currentRow.getAttribute('data-id');
                    const confirmation = confirm("Are you sure you want to clear this room's status?");
                    if (confirmation) {
                        currentRow.remove();
                        updateDatabase('clear', roomId);
                    }
                });
            });

            document.querySelectorAll('.assign-housekeeper-btn').forEach(button => {
    button.addEventListener('click', function() {
        const currentRow = this.closest('tr');
        const roomId = currentRow.getAttribute('data-id');
        const newHousekeeper = prompt('Enter the housekeeper\'s name:');
        if (newHousekeeper) {
            currentRow.querySelector('.housekeeper-name').textContent = newHousekeeper;
            updateDatabase('housekeeper', roomId, newHousekeeper);
        }
    });
});

function updateDatabase(action, roomId, value = '') {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log('Update successful:', xhr.responseText);
        } else {
            console.error('Update failed:', xhr.statusText);
        }
    };
    xhr.send(`action=${action}&roomId=${roomId}&value=${encodeURIComponent(value)}`);
}


            document.querySelectorAll('.unassign-housekeeper-btn').forEach(button => {
                button.addEventListener('click', function() {
                    currentRow = this.closest('tr');
                    const roomId = currentRow.getAttribute('data-id');
                    alert('Housekeeper unassigned.');
                    currentRow.querySelector('.housekeeper-name').textContent = 'Unassigned';
                    updateDatabase('housekeeper', roomId, '');
                });
            });

            document.querySelectorAll('.edit-remark-btn').forEach(button => {
                button.addEventListener('click', function() {
                    currentRow = this.closest('tr');
                    const remarkText = currentRow.querySelector('.remark-text');
                    const newRemark = prompt('Enter new remark:', remarkText.textContent);
                    if (newRemark !== null) {
                        const roomId = currentRow.getAttribute('data-id');
                        remarkText.textContent = newRemark;
                        currentRow.querySelector('.remark-icon').title = newRemark || "No remark";
                        updateDatabase('remark', roomId, newRemark);
                        if (newRemark.trim() !== "") {
                            currentRow.querySelector('.edit-remark-btn').style.display = 'none';
                            currentRow.querySelector('.delete-remark-btn').style.display = 'block';
                        } else {
                            currentRow.querySelector('.edit-remark-btn').style.display = 'block';
                            currentRow.querySelector('.delete-remark-btn').style.display = 'none';
                        }
                    }
                });
            });

            document.querySelectorAll('.delete-remark-btn').forEach(button => {
                button.addEventListener('click', function() {
                    currentRow = this.closest('tr');
                    const remarkText = currentRow.querySelector('.remark-text');
                    const roomId = currentRow.getAttribute('data-id');
                    const confirmation = confirm("Are you sure you want to delete this remark?");
                    if (confirmation) {
                        remarkText.textContent = "";
                        currentRow.querySelector('.remark-icon').title = "No remark";
                        this.style.display = 'none';
                        currentRow.querySelector('.edit-remark-btn').style.display = 'block';
                        updateDatabase('deleteRemark', roomId);
                    }
                });
            });

            document.querySelectorAll('.remark-icon').forEach(icon => {
                icon.addEventListener('click', function() {
                    const remarkText = this.nextElementSibling.textContent;
                    alert(`Remark: ${remarkText}`);
                });
            });

        });
    </script>
</head>
<body>
<?php include 'index.php'; ?>
    <div class="container">
        <div class="header">
            <h1>Housekeeping Management</h1>
            <nav>
                
            </nav>
        </div>

        <div class="filters">
            <input type="text" placeholder="Search Room" id="room-search">
            <select id="room-type-filter">
                <option value="">Room Type</option>
                <option value="Family">Family</option>
                <option value="Single">Single</option>
                <option value="Twin">Twin</option>
                <option value="Suite">Suite</option>
                <option value="King">King Size</option>
                <option value="Queen">Queen Size</option>
            </select>
            <select id="housekeeping-status-filter">
                <option value="">Housekeeping Status</option>
                <option value="Available" class="status-available">Available</option>
                <option value="Occupied" class="status-occupied">Occupied</option>
                <option value="Out of order" class="status-out-of-order">Out of order</option>
                <option value="Turnover" class="status-turnover">Turnover</option>
            </select>
            <select id="priority-filter">
                <option value="">Priority</option>
                <option value="Low" class="priority-low">Low</option>
                <option value="High" class="priority-high">High</option>
            </select>
            <select id="floor-filter">
                <option value="">Floor</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </div>

        <table class="room-table">
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Room Type</th>
                    <th>Housekeeping Status</th>
                    <th>Priority</th>
                    <th>Floor</th>
                    <th>Remarks</th>
                    <th>Housekeeper</th>
                </tr>
            </thead>
            <tbody id="room-list">
                <?php while($row = $roomsResult->fetch_assoc()): ?>
                <tr data-id="<?php echo $row['id']; ?>">
                    <td><?php echo $row['room_number']; ?></td>
                    <td><?php echo $row['room_type']; ?></td>
                    <td>
                        <select class="status-dropdown" onchange="updateStatusColor(this)">
                            <option value="Available" class="status-available" <?php if ($row['status'] == 'Available') echo 'selected'; ?>>Available</option>
                            <option value="Occupied" class="status-occupied" <?php if ($row['status'] == 'Occupied') echo 'selected'; ?>>Occupied</option>
                            <option value="Out of order" class="status-out-of-order" <?php if ($row['status'] == 'Out of order') echo 'selected'; ?>>Out of order</option>
                            <option value="Turnover" class="status-turnover" <?php if ($row['status'] == 'Turnover') echo 'selected'; ?>>Turnover</option>
                        </select>
                    </td>
                    <td>
                        <select class="priority-dropdown" onchange="updatePriorityColor(this)">
                            <option value="Low" class="priority-low" <?php if ($row['priority'] == 'Low') echo 'selected'; ?>>Low</option>
                            <option value="High" class="priority-high" <?php if ($row['priority'] == 'High') echo 'selected'; ?>>High</option>
                        </select>
                    </td>
                    <td><?php echo $row['floor']; ?></td>
                    <td>
                        <span class="remark-icon" title="<?php echo $row['remarks']; ?>">📝</span>
                        <span class="remark-text"><?php echo $row['remarks']; ?></span>
                        <span class="more-icon" onclick="toggleDropdown(this)">⋮</span> <!-- Vertical dots -->
                        <div class="action-menu">
                            <button class="action-btn assign-housekeeper-btn">Assign Housekeeper</button>
                            <button class="action-btn clear-status-btn">Clear Status</button>
                            <button class="action-btn unassign-housekeeper-btn">Unassign Housekeeper</button>
                            <button class="action-btn edit-remark-btn">Add/Edit Remark</button>
                            <button class="action-btn delete-remark-btn" style="display:<?php echo empty($row['remarks']) ? 'none' : 'block'; ?>;">Delete Remark</button>
                        </div>
                    </td>
                    <td class="housekeeper-name"><?php echo $row['housekeeper']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
} else {
    echo "<p>No rooms found.</p>";
}
?>
