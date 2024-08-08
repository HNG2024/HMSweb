<?php
include 'housekeeping_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = $_POST['room_id'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    $remark = $_POST['remark'];

    $query = "UPDATE rooms SET status = ?, priority = ?, remarks = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssi', $status, $priority, $remark, $roomId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Room updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update room']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
