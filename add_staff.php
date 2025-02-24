<?php
session_start();
include 'db/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $specialization = isset($_POST['specialization']) ? trim($_POST['specialization']) : '';

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: manage_staff.php");
        exit();
    }

    // Check if email already exists
    $check_sql = "SELECT id FROM staff WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different email.";
        header("Location: manage_staff.php");
        exit();
    }

    // Add staff member
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO staff (name, email, password, role, specialization, status) VALUES (?, ?, ?, ?, ?, 'active')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $hashed_password, $role, $specialization);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff member added successfully.";
    } else {
        $_SESSION['error'] = "Error adding staff member: " . $conn->error;
    }
    
    header("Location: manage_staff.php");
    exit();
}