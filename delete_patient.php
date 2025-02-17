<?php
// delete_patient.php
session_start();
include 'db/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];
    
    // First, get the patient's profile picture path
    $select_sql = "SELECT profile_picture FROM users WHERE id = ? AND role = 'patient'";
    $stmt = $conn->prepare($select_sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Delete the profile picture if it exists
        if (!empty($row['profile_picture']) && file_exists($row['profile_picture'])) {
            unlink($row['profile_picture']);
        }
    }
    
    // Delete the patient record
    $delete_sql = "DELETE FROM users WHERE id = ? AND role = 'patient'";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $patient_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Patient deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting patient: " . $conn->error;
    }
    
    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid patient ID!";
}

header("Location: admin_dashboard.php");
exit();