<?php
include 'db/config.php';
session_start();

// Secure session settings
session_regenerate_id(true);

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        header("Location: home.php");
        exit();
    }
}

$error_message = '';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Hardcoded admin credentials
    $admin_email = "admin@hospital.com";
    $admin_password = "admin123";

    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['user_id'] = 1; // Hardcoded admin ID
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    }

    // Fetch user from database
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: home.php");
        exit();
    } else {
        $error_message = "Invalid email or password!";
    }
}

// Include the login form
include 'login_form.html';
?>
