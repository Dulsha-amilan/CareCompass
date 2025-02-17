<?php
session_start();
include 'db/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: admin_dashboard.php");
        exit();
    }

    // Check if email already exists
    $check_email = "SELECT id FROM staff WHERE email = ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already exists";
        header("Location: admin_dashboard.php");
        exit();
    }

    // Insert new staff member
    $insert_sql = "INSERT INTO staff (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff member added successfully";
    } else {
        $_SESSION['error'] = "Error adding staff member: " . $conn->error;
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid request method";
}

header("Location: admin_dashboard.php");
exit();