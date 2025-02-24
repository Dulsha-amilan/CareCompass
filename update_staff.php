<?php
session_start();
include 'db/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = $_POST['staff_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $new_password = $_POST['new_password'];
    $specialization = isset($_POST['specialization']) ? trim($_POST['specialization']) : '';

    // Validate inputs
    if (empty($staff_id) || empty($name) || empty($email) || empty($role)) {
        $_SESSION['error'] = "Required fields cannot be empty.";
        header("Location: manage_staff.php");
        exit();
    }

    // Check if email already exists (except for the current staff member)
    $check_sql = "SELECT id FROM staff WHERE email = ? AND id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $email, $staff_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different email.";
        header("Location: manage_staff.php");
        exit();
    }

    // Update staff member
    if (!empty($new_password)) {
        // Update with new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE staff SET name = ?, email = ?, password = ?, role = ?, specialization = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $hashed_password, $role, $specialization, $staff_id);
    } else {
        // Update without changing password
        $sql = "UPDATE staff SET name = ?, email = ?, role = ?, specialization = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $role, $specialization, $staff_id);
    }
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff member updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating staff member: " . $conn->error;
    }
    
    header("Location: manage_staff.php");
    exit();
}