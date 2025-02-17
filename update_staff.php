<?php
session_start();
include 'db/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form data
    $staff_id = mysqli_real_escape_string($conn, $_POST['staff_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
        header("Location: admin_dashboard.php");
        exit();
    }

    // Check if email exists for other staff members
    $check_sql = "SELECT id FROM staff WHERE email = ? AND id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $email, $staff_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already exists for another staff member!";
    } else {
        // Check if trying to update an admin account
        $check_admin_sql = "SELECT role FROM staff WHERE id = ?";
        $check_admin_stmt = $conn->prepare($check_admin_sql);
        $check_admin_stmt->bind_param("i", $staff_id);
        $check_admin_stmt->execute();
        $admin_result = $check_admin_stmt->get_result();
        $staff_data = $admin_result->fetch_assoc();

        if ($staff_data && $staff_data['role'] === 'admin') {
            $_SESSION['error'] = "Cannot modify admin accounts!";
        } else {
            // Update staff member
            $update_sql = "UPDATE staff SET name = ?, email = ?, role = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sssi", $name, $email, $role, $staff_id);

            if ($update_stmt->execute()) {
                if ($update_stmt->affected_rows > 0) {
                    $_SESSION['success'] = "Staff member updated successfully!";
                } else {
                    $_SESSION['error'] = "No changes were made or staff member not found!";
                }
            } else {
                $_SESSION['error'] = "Error updating staff member: " . $conn->error;
            }
            $update_stmt->close();
        }
        $check_admin_stmt->close();
    }
    $check_stmt->close();
} else {
    $_SESSION['error'] = "Invalid request method!";
}

header("Location: admin_dashboard.php");
exit();
?>