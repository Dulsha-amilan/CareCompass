<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['staff_id'])) {
    header('HTTP/1.1 403 Forbidden');
    exit(json_encode(['success' => false, 'error' => 'Access denied']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id']) && isset($_POST['new_status'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $new_status = $_POST['new_status'];
    
    // Validate status
    $valid_statuses = ['Pending', 'Approved', 'Cancelled', 'Completed'];
    if (!in_array($new_status, $valid_statuses)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
        exit();
    }
    
    $update_sql = "UPDATE appointments SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $appointment_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>