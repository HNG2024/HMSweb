<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $roomId = intval($_POST['roomId']);
    $value = $_POST['value'] ?? '';

    switch ($action) {
        case 'status':
            $query = "UPDATE rooms SET status = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $value, $roomId);
            break;
        case 'priority':
            $query = "UPDATE rooms SET priority = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $value, $roomId);
            break;
        case 'remark':
            $query = "UPDATE rooms SET remarks = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $value, $roomId);
            break;
        case 'deleteRemark':
            $query = "UPDATE rooms SET remarks = '' WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $roomId);
            break;
        case 'housekeeper':
            $query = "UPDATE rooms SET housekeeper = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $value, $roomId);
            break;
        case 'clear':
            $query = "DELETE FROM rooms WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $roomId);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Update successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
