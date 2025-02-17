<?php
session_start();
include 'db/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Check if ID is provided
if (isset($_GET['id'])) {
    $staff_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Check if staff exists and is not an admin
    $check_sql = "SELECT role FROM staff WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $staff_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $staff = $result->fetch_assoc();
        
        // Prevent deletion of admin accounts
        if ($staff['role'] === 'admin') {
            $_SESSION['error'] = "Cannot delete admin accounts!";
            header("Location: admin_dashboard.php");
            exit();
        }
        
        // Delete the staff member
        $delete_sql = "DELETE FROM staff WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $staff_id);
        
        if ($delete_stmt->execute()) {
            // Check if any rows were actually deleted
            if ($delete_stmt->affected_rows > 0) {
                $_SESSION['success'] = "Staff member deleted successfully!";
            } else {
                $_SESSION['error'] = "Staff member could not be found!";
            }
        } else {
            $_SESSION['error'] = "Error deleting staff member: " . $conn->error;
        }
        
        $delete_stmt->close();
    } else {
        $_SESSION['error'] = "Staff member not found!";
    }
    
    $check_stmt->close();
} else {
    $_SESSION['error'] = "No staff ID provided!";
}

header("Location: admin_dashboard.php");
exit();
?>