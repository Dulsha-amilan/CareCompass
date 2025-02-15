<?php
include 'db/config.php';
session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Hardcoded admin credentials
    $admin_email = "admin@hospital.com";
    $admin_password = "admin123";
    
    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['user_id'] = 1; // Hardcoded admin ID
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    }
    
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: home.php");
        exit();
    } else {
        $error_message = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Care Compass Hospitals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg" style="width: 400px;">
            <h3 class="text-center mb-3">Login</h3>
            
            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php } ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</body>
</html>
