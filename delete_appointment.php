<?php
session_start();
include 'db/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid appointment ID";
    header("Location: manage_appointments.php");
    exit();
}

$appointment_id = (int)$_GET['id'];

// Check if appointment exists
$check_sql = "SELECT id FROM appointments WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $appointment_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $_SESSION['error'] = "Appointment not found";
    header("Location: manage_appointments.php");
    exit();
}

// Delete the appointment
$delete_sql = "DELETE FROM appointments WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $appointment_id);

if ($delete_stmt->execute()) {
    $_SESSION['success'] = "Appointment successfully deleted";
} else {
    $_SESSION['error'] = "Failed to delete appointment: " . $conn->error;
}

// Close statements
$check_stmt->close();
$delete_stmt->close();

// Redirect back to manage appointments page
header("Location: manegappoiment admin.php");
exit();
?>