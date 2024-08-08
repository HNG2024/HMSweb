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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['deleteRoomId'])) {
        $roomId = $_POST['deleteRoomId'];

        $deleteQuery = "DELETE FROM rooms WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param('i', $roomId);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();
        exit;
    }

    if (isset($_POST['roomId']) && isset($_POST['housekeeper'])) {
        $roomId = $_POST['roomId'];
        $housekeeper = $_POST['housekeeper'];

        // Check if the housekeeper should be unassigned
        if ($housekeeper === 'Not Assigned') {
            $housekeeper = null;
        }

        $updateQuery = "UPDATE rooms SET housekeeper = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('si', $housekeeper, $roomId);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();
        exit;
    }

    if (isset($_POST['roomId']) && isset($_POST['remark'])) {
        $roomId = $_POST['roomId'];
        $remark = $_POST['remark'];

        $updateQuery = "UPDATE rooms SET remarks = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('si', $remark, $roomId);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();
        exit;
    }

    if (isset($_POST['deleteRemarkRoomId'])) {
        $roomId = $_POST['deleteRemarkRoomId'];

        $updateQuery = "UPDATE rooms SET remarks = '' WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('i', $roomId);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();
        exit;
    }

    if (isset($_POST['roomId']) && isset($_POST['status'])) {
        $roomId = $_POST['roomId'];
        $status = $_POST['status'];

        $updateQuery = "UPDATE rooms SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('si', $status, $roomId);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();
        exit;
    }
}

if ($roomsResult->num_rows > 0) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Housekeeping Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            color: #333;
            animation: slideInLeft 0.6s ease;
        }

        @keyframes slideInLeft {
            from { transform: translateX(-100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .header nav {
            font-size: 16px;
            color: #888;
        }

        .header nav a {
            text-decoration: none;
            color: #007bff;
            margin-left: 10px;
            transition: color 0.3s;
        }

        .header nav a:hover {
            color: #0056b3;
        }

        .header nav a.active {
            color: #000; /* Highlight color for the active page */
            font-weight: bold;
            text-decoration: underline;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 30px;
            animation: slideInUp 0.6s ease;
        }

        @keyframes slideInUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .filters select,
        .filters input[type="text"] {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: #fff;
            width: 100%;
            max-width: 220px;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: #333;
            box-sizing: border-box;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .filters select:focus,
        .filters input[type="text"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
            outline: none;
        }

        .room-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            overflow-x: auto;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .room-table th,
        .room-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s;
        }

        .room-table th {
            background-color: #f8fafc;
            font-weight: bold;
            color: #333;
        }

        .room-table td {
            color: #555;
        }

        .room-table tr:hover {
            background-color: #f1f5f9;
        }

        /* Custom Dropdown Styles */
      /* Custom Dropdown Styles */
.custom-dropdown {
    position: relative;
    display: inline-block;
    width: 100%;
    max-width: 200px;
    overflow: hidden;
    border-radius: 8px;
    background: linear-gradient(135deg, #72EDF2 10%, #5151E5 100%);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.custom-dropdown select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background-color: transparent; /* Now the background of the dropdown will be transparent */
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: white;
    width: 100%;
    cursor: pointer;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
    transition: background-color 0.3s ease, color 0.3s ease;
}

.custom-dropdown::after {
    content: '▼';
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    pointer-events: none;
    font-size: 18px;
    color: white;
    transition: transform 0.3s ease;
}

.custom-dropdown:hover::after {
    transform: translateY(-50%) rotate(180deg); /* Arrow rotates on hover */
}

/* Option Colors and Backgrounds */
.custom-dropdown select option[value="Ready To Book"] {
    background-color: #28a745; /* Green */
    color: white;
}

.custom-dropdown select option[value="Cleaning Process"] {
    background-color: #ffc107; /* Yellow */
    color: white;
}

.custom-dropdown select option[value="Unavailable"] {
    background-color: #dc3545; /* Red */
    color: white;
}

/* Dropdown Hover and Focus Styles */
.custom-dropdown select:focus {
    outline: none;
    box-shadow: 0 0 10px rgba(81, 81, 229, 0.7);
}

.custom-dropdown select:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

/* Animation for Dropdown Options */
.custom-dropdown select option {
    color: white;
    padding: 12px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Custom Hover Effect for Options */
.custom-dropdown select option:hover {
    background-color: rgba(255, 255, 255, 0.2);
    color: #333;
    transform: scale(1.05);
}

/* Adding an entry animation */
.custom-dropdown select {
    animation: dropdownFadeIn 0.6s ease both;
}

@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

        /* Add Remark Icon */
        .remark-text {
    display: none;
    color: #333;
    font-weight: bold;
    margin-left: 5px;
    transition: opacity 0.6s ease-in-out;
}


        /* Adjustments for remark-icon and more-icon */
        .remark-icon {
            display: inline-block;
            cursor: pointer;
            color: #888;
            transition: transform 0.3s ease-in-out;
            margin-right: 10px; /* Added more space between the remark and more icon */
        }

        .more-icon {
            cursor: pointer;
            font-size: 18px;
            color: #888;
            position: relative;
            display: inline-block;
            transition: color 0.3s;
            margin-left: 5px; /* Adjust this to position it closer to the remark icon */
        }

        .more-icon:hover {
            color: #333;
        }

        /* Positioning the action-menu closer to the remark-icon */
        .action-menu {
            display: none;
            position: absolute;
            background-color: #fff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            left: 0; /* Keep the left position at 0 to align with the remark icon */
            top: 30px; /* Adjust this value if needed to change the vertical position */
            z-index: 100;
            min-width: 180px;
            padding: 10px 0;
            margin: 0;
            overflow: hidden;
            animation: dropDown 0.3s ease-in-out;
        }
     .delete-room-btn {
    background-color: red; /* Default red background for the button */
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.delete-room-btn .delete-icon {
    width: 24px;
    height: 24px;
    background-image: url('image/bin.png'); /* Use the path where your image is saved */
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    opacity: 0;
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    transition: opacity 0.3s ease;
}

.delete-room-btn .delete-text {
    transition: opacity 0.3s ease;
}

.delete-room-btn:hover {
    background-color: transparent; /* Makes the button's background transparent on hover */
    color: transparent; /* Hides the "Delete" text */
}

.delete-room-btn:hover .delete-icon {
    opacity: 1; /* Show the bin icon */
}

.delete-room-btn:hover .delete-text {
    opacity: 0; /* Hide the text when hovering */
}

        @keyframes dropDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .action-menu button {
            display: block;
            width: 100%;
            padding: 12px 15px;
            border: none;
            background: none;
            text-align: left;
            cursor: pointer;
            color: #333;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .action-menu button:hover {
            background-color: #f0f0f0;
        }

        .more-icon {
            cursor: pointer;
            font-size: 18px;
            color: #888;
            position: relative;
            display: inline-block;
            transition: color 0.3s;
        }

        .more-icon:hover {
            color: #333;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 12px;
        }

        .modal-header {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
        }

        .modal-footer button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin-left: 10px;
        }

        .modal-footer .confirm-delete {
            background-color: #ff4d4d;
            color: white;
        }

        .modal-footer .cancel-delete {
            background-color: #ccc;
            color: black;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .filters select,
            .filters input[type="text"] {
                width: 100%;
                margin-bottom: 10px;
            }

            .room-table th, 
            .room-table td {
                padding: 12px;
            }

            .room-table {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 24px;
            }

            .filters select,
            .filters input[type="text"] {
                max-width: 100%;
                margin-bottom: 10px;
            }

            .room-table th, 
            .room-table td {
                padding: 10px;
            }

            .room-table {
                font-size: 12px;
            }

            .action-menu {
                min-width: 120px;
            }

            .action-menu button {
                font-size: 12px;
            }
            
        }
    </style>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    let deleteRoomId = null;
    let currentRoomId = null;

    const deleteModal = document.getElementById('deleteModal');
    const housekeeperModal = document.getElementById('housekeeperModal');
    const remarkModal = document.getElementById('remarkModal');

    const confirmDeleteButton = document.getElementById('confirmDelete');
    const cancelDeleteButton = document.getElementById('cancelDelete');

    const assignHousekeeperButton = document.getElementById('assignHousekeeper');
    const cancelHousekeeperButton = document.getElementById('cancelHousekeeper');

    const saveRemarkButton = document.getElementById('saveRemark');
    const cancelRemarkButton = document.getElementById('cancelRemark');

    function openDeleteModal(roomId, roomNumber) {
    deleteRoomId = roomId;
    document.getElementById('roomNumberToDelete').textContent = roomNumber; // Set room number in the modal
    deleteModal.style.display = 'block';
}


    function openHousekeeperModal(roomId) {
        currentRoomId = roomId;
        housekeeperModal.style.display = 'block';
    }

    function openRemarkModal(roomId, existingRemark) {
        currentRoomId = roomId;
        document.getElementById('remarkInput').value = existingRemark || '';
        remarkModal.style.display = 'block';
    }

    confirmDeleteButton.onclick = function() {
        if (deleteRoomId) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // Post to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText.trim() === 'success') {
                        const rowToDelete = document.querySelector(`tr[data-id="${deleteRoomId}"]`);
                        rowToDelete.remove();
                    } else {
                        alert('Error deleting room: ' + xhr.responseText);
                    }
                } else {
                    alert('Error deleting room.');
                }
            };
            xhr.send(`deleteRoomId=${deleteRoomId}`);
            deleteModal.style.display = 'none';
        }
    };

    cancelDeleteButton.onclick = function() {
        deleteModal.style.display = 'none';
    };

    assignHousekeeperButton.onclick = function() {
        const selectedHousekeeper = document.getElementById('housekeeperSelect').value;
        const typedHousekeeper = document.getElementById('housekeeperInput').value;

        // Prioritize the typed housekeeper name
        const housekeeper = typedHousekeeper.trim() !== "" ? typedHousekeeper : selectedHousekeeper;

        if (currentRoomId && housekeeper) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText.trim() === 'success') {
                        const rowToUpdate = document.querySelector(`tr[data-id="${currentRoomId}"]`);
                        rowToUpdate.querySelector('.housekeeper-name').textContent = housekeeper;
                        rowToUpdate.querySelector('.assign-housekeeper-btn').style.display = 'none';
                        rowToUpdate.querySelector('.unassign-housekeeper-btn').style.display = 'block';
                        showNotification('Housekeeper assigned successfully.');
                    } else {
                        alert('Error assigning housekeeper: ' + xhr.responseText);
                    }
                } else {
                    alert('Error assigning housekeeper.');
                }
            };
            xhr.send(`roomId=${currentRoomId}&housekeeper=${encodeURIComponent(housekeeper)}`);
            housekeeperModal.style.display = 'none';
        }
    };

    cancelHousekeeperButton.onclick = function() {
        housekeeperModal.style.display = 'none';
    };

    document.querySelectorAll('.unassign-housekeeper-btn').forEach(button => {
        button.addEventListener('click', function() {
            const currentRow = this.closest('tr');
            const roomId = currentRow.getAttribute('data-id');
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); 
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText.trim() === 'success') {
                        currentRow.querySelector('.housekeeper-name').textContent = 'Not Assigned';
                        currentRow.querySelector('.unassign-housekeeper-btn').style.display = 'none';
                        currentRow.querySelector('.assign-housekeeper-btn').style.display = 'block';
                        showNotification('Housekeeper unassigned successfully.');
                    } else {
                        alert('Error unassigning housekeeper: ' + xhr.responseText);
                    }
                } else {
                    alert('Error unassigning housekeeper.');
                }
            };
            xhr.send(`roomId=${roomId}&housekeeper=Not Assigned`);
        });
    });

    saveRemarkButton.onclick = function() {
        const remark = document.getElementById('remarkInput').value;
        if (currentRoomId) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText.trim() === 'success') {
                        const rowToUpdate = document.querySelector(`tr[data-id="${currentRoomId}"]`);
                        rowToUpdate.querySelector('.remark-text').textContent = remark;
                        rowToUpdate.querySelector('.edit-remark-btn').style.display = 'none';
                        rowToUpdate.querySelector('.delete-remark-btn').style.display = remark ? 'block' : 'none';
                        showNotification('Remark saved successfully.');
                    } else {
                        alert('Error saving remark: ' + xhr.responseText);
                    }
                } else {
                    alert('Error saving remark.');
                }
            };
            xhr.send(`roomId=${currentRoomId}&remark=${encodeURIComponent(remark)}`);
            remarkModal.style.display = 'none';
        }
    };

    cancelRemarkButton.onclick = function() {
        remarkModal.style.display = 'none';
    };

   document.querySelectorAll('.delete-room-btn').forEach(button => {
    button.addEventListener('click', function() {
        const roomId = this.closest('tr').getAttribute('data-id');
        const roomNumber = this.closest('tr').querySelector('td:first-child').textContent; // Get the room number
        openDeleteModal(roomId, roomNumber);
    });
    });

    document.querySelectorAll('.assign-housekeeper-btn').forEach(button => {
        button.addEventListener('click', function() {
            const roomId = this.closest('tr').getAttribute('data-id');
            openHousekeeperModal(roomId);
        });
    });

  document.querySelectorAll('.remark-icon').forEach(icon => {
    icon.addEventListener('mouseover', function() {
        const remarkText = this.nextElementSibling;
        remarkText.style.display = 'inline'; // Show the remark text when hovering
    });

    icon.addEventListener('mouseout', function() {
        const remarkText = this.nextElementSibling;
        remarkText.style.display = 'none'; // Hide the remark text when not hovering
    });
});

    document.querySelectorAll('.delete-remark-btn').forEach(button => {
        button.addEventListener('click', function() {
            const roomId = this.closest('tr').getAttribute('data-id');
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText.trim() === 'success') {
                        const rowToUpdate = document.querySelector(`tr[data-id="${roomId}"]`);
                        rowToUpdate.querySelector('.remark-text').textContent = '';
                        rowToUpdate.querySelector('.edit-remark-btn').style.display = 'block';
                        rowToUpdate.querySelector('.delete-remark-btn').style.display = 'none';
                        showNotification('Remark deleted successfully.');
                    } else {
                        alert('Error deleting remark: ' + xhr.responseText);
                    }
                } else {
                    alert('Error deleting remark.');
                }
            };
            xhr.send(`deleteRemarkRoomId=${roomId}`);
        });
    });

    document.querySelectorAll('.unassign-housekeeper-btn').forEach(button => {
        button.addEventListener('click', function() {
            const currentRow = this.closest('tr');
            const roomId = currentRow.getAttribute('data-id');
            currentRow.querySelector('.housekeeper-name').textContent = 'Not Assigned';
            updateDatabase('housekeeper', roomId, 'Not Assigned');
            currentRow.querySelector('.unassign-housekeeper-btn').style.display = 'none';
            currentRow.querySelector('.assign-housekeeper-btn').style.display = 'block';
        });
    });

    function updateDatabase(action, roomId, value = '') {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true); // Post to the same page
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

    function updateDropdownColor(selectElement) {
        const selectedValue = selectElement.value;
        selectElement.style.backgroundColor = {
            "Ready To Book": "#28a745",
            "Cleaning Process": "#ffc107",
            "Unavailable": "#dc3545"
        }[selectedValue];
    }

    document.querySelectorAll('.custom-dropdown select').forEach(select => {
        updateDropdownColor(select); // Initialize on page load
        select.addEventListener('change', function() {
            updateDropdownColor(this);
        });
    });

    function filterRooms() {
        const roomSearchInput = document.getElementById('room-search').value.toLowerCase();
        const roomTypeFilter = document.getElementById('room-type-filter').value.toLowerCase();
        const statusFilter = document.getElementById('housekeeping-status-filter').value.toLowerCase();
        const floorFilter = document.getElementById('floor-filter').value.toLowerCase();

        document.querySelectorAll('#room-list tr').forEach(row => {
            const roomNumber = row.cells[0].textContent.toLowerCase();
            const roomType = row.cells[1].textContent.toLowerCase();
            const status = row.querySelector('.status-dropdown').value.toLowerCase();
            const floor = row.cells[3].textContent.toLowerCase();

            const matchesRoomNumber = !roomSearchInput || roomNumber.includes(roomSearchInput);
            const matchesRoomType = !roomTypeFilter || roomType === roomTypeFilter;
            const matchesStatus = !statusFilter || status === statusFilter;
            const matchesFloor = !floorFilter || floor === floorFilter;

            if (matchesRoomNumber && matchesRoomType && matchesStatus && matchesFloor) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    document.getElementById('room-search').addEventListener('input', filterRooms);
    document.getElementById('room-type-filter').addEventListener('change', filterRooms);
    document.getElementById('housekeeping-status-filter').addEventListener('change', filterRooms);
    document.getElementById('floor-filter').addEventListener('change', filterRooms);

    document.querySelectorAll('.status-dropdown').forEach(select => {
        updateDropdownColor(select);
        select.addEventListener('change', function() {
            const roomId = select.closest('tr').getAttribute('data-id');
            const status = select.value;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log('Status updated successfully');
                    showNotification('Status updated successfully.');
                } else {
                    console.error('Error updating status:', xhr.statusText);
                }
            };
            xhr.send(`roomId=${roomId}&status=${encodeURIComponent(status)}`);
            updateDropdownColor(select);
        });
    });

    document.querySelectorAll('.remark-icon').forEach(icon => {
        icon.addEventListener('click', function() {
            const remarkText = this.nextElementSibling;
            remarkText.style.display = (remarkText.style.display === 'none' || remarkText.style.display === '') ? 'inline' : 'none';
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

    // Notification function
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }

    // Notification styles
    const style = document.createElement('style');
    style.innerHTML = `
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            opacity: 1;
            transition: opacity 0.5s ease-in-out;
        }
    `;
    document.head.appendChild(style);
});
    </script>
</head>
<body>
    <!-- Modal for delete confirmation -->
    <div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Confirm Deletion</div>
        <div class="modal-body">
            Are you sure you want to delete room <span id="roomNumberToDelete"></span>?
        </div>
        <div class="modal-footer">
            <button id="cancelDelete" class="cancel-delete">Cancel</button>
            <button id="confirmDelete" class="confirm-delete">Delete</button>
        </div>
    </div>
</div>


    <!-- Modal for assigning housekeeper -->
    <div id="housekeeperModal" class="modal">
    <div class="modal-content" style="border-radius: 10px; background: linear-gradient(to right, #f9f9f9, #e3e3e3); box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);">
        <div class="modal-header" style="padding: 15px; border-bottom: 1px solid #ddd; font-size: 20px; font-weight: bold; text-align: center; color: #333;">
            Assign Housekeeper
        </div>
        <div class="modal-body" style="padding: 20px;">
            <label for="housekeeperSelect" style="display: block; font-size: 16px; margin-bottom: 10px; color: #555;">Select Housekeeper</label>
            <select id="housekeeperSelect" style="width: 100%; padding: 12px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px; background-color: #fff; box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);">
                <option value="" disabled selected>Select Housekeeper</option>
                <option value="John Doe">John Doe</option>
                <option value="Jane Smith">Jane Smith</option>
                <option value="Michael Johnson">Michael Johnson</option>
            </select>

            <label for="housekeeperInput" style="display: block; font-size: 16px; margin-bottom: 10px; color: #555;">Or Type Housekeeper Name</label>
            <input type="text" id="housekeeperInput" placeholder="Enter housekeeper name" style="width: 100%; padding: 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px; background-color: #fff; box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);">
        </div>
        <div class="modal-footer" style="padding: 15px; border-top: 1px solid #ddd; text-align: center;">
            <button id="cancelHousekeeper" class="cancel-delete" style="padding: 10px 25px; border-radius: 5px; border: none; background-color: #bbb; color: white; font-size: 16px; margin-right: 10px; cursor: pointer; transition: background-color 0.3s;">Cancel</button>
            <button id="assignHousekeeper" class="confirm-delete" style="padding: 10px 25px; border-radius: 5px; border: none; background-color: #007bff; color: white; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">Assign</button>
        </div>
    </div>
</div>


    <!-- Modal for adding/editing remarks -->
  <div id="remarkModal" class="modal">
    <div class="modal-content" style="border-radius: 10px; background: linear-gradient(to right, #f9f9f9, #e3e3e3); box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);">
        <div class="modal-header" style="padding: 15px; border-bottom: 1px solid #ddd; font-size: 20px; font-weight: bold; text-align: center; color: #333;">
            Add/Edit Remark
        </div>
        <div class="modal-body" style="padding: 20px;">
            <input type="text" id="remarkInput" placeholder="Enter remark" style="width: 100%; padding: 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px; background-color: #fff; box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);">
        </div>
        <div class="modal-footer" style="padding: 15px; border-top: 1px solid #ddd; text-align: center;">
            <button id="cancelRemark" class="cancel-delete" style="padding: 10px 25px; border-radius: 5px; border: none; background-color: #bbb; color: white; font-size: 16px; margin-right: 10px; cursor: pointer; transition: background-color 0.3s;">Cancel</button>
            <button id="saveRemark" class="confirm-delete" style="padding: 10px 25px; border-radius: 5px; border: none; background-color: #28a745; color: white; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">Save</button>
        </div>
    </div>
</div>

    <div class="container">
        <div class="header">
            <h1>Housekeeping Management</h1>
            <nav>
    <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Housekeeping</a> / 
    <a href="amenity.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'amenity.php' ? 'active' : ''; ?>">Amenity</a> / 
    <a href="laundry.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'laundry.php' ? 'active' : ''; ?>">Laundry</a> / 
    <a href="report.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : ''; ?>">Report</a>
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
                <option value="Ready To Book">Ready To Book</option>
                <option value="Cleaning Process">Cleaning Process</option>
                <option value="Unavailable">Unavailable</option>
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
                    <th>Floor</th>
                    <th>Remarks</th>
                    <th>Housekeeper</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="room-list">
                <?php while($row = $roomsResult->fetch_assoc()): ?>
                <tr data-id="<?php echo $row['id']; ?>">
                    <td><?php echo $row['room_number']; ?></td>
                    <td><?php echo $row['room_type']; ?></td>
                    <td>
                        <div class="custom-dropdown">
                            <select class="status-dropdown">
                                <option value="Ready To Book" <?php if ($row['status'] == 'Ready To Book') echo 'selected'; ?>>Ready To Book</option>
                                <option value="Cleaning Process" <?php if ($row['status'] == 'Cleaning Process') echo 'selected'; ?>>Cleaning Process</option>
                                <option value="Unavailable" <?php if ($row['status'] == 'Unavailable') echo 'selected'; ?>>Unavailable</option>
                            </select>
                        </div>
                    </td>
                    <td><?php echo $row['floor']; ?></td>
                    <td>
    <div style="position: relative;">
      <span class="remark-icon">
    <img src="image/customer.png" alt="Remark Icon" title="<?php echo $row['remarks']; ?>" style="width: 25px; height: 25px;">
</span>
        <span class="remark-text"><?php echo $row['remarks']; ?></span>
        <span class="more-icon" onclick="toggleDropdown(this)">⋮</span> <!-- Vertical dots -->
        <div class="action-menu">
            <button class="action-btn assign-housekeeper-btn" style="display:<?php echo empty($row['housekeeper']) ? 'block' : 'none'; ?>;">Assign Housekeeper</button>
            <button class="action-btn unassign-housekeeper-btn" style="display:<?php echo empty($row['housekeeper']) ? 'none' : 'block'; ?>;">Unassign Housekeeper</button>
            <button class="action-btn edit-remark-btn">Add/Edit Remark</button>
            <button class="action-btn delete-remark-btn" style="display:<?php echo empty($row['remarks']) ? 'none' : 'block'; ?>;">Delete Remark</button>
        </div>
    </div>
</td>

                    <td class="housekeeper-name"><?php echo empty($row['housekeeper']) ? 'Not Assigned' : $row['housekeeper']; ?></td>
                    <td>
    <button class="delete-room-btn">
        <span class="delete-text">Delete</span>
        <span class="delete-icon"></span>
    </button>
</td>
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
