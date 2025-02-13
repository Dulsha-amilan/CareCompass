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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Care Compass Hospitals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.9.6/lottie.min.js"></script>
    <style>
        body {
            height: 100%;
            margin: 0;
            overflow: hidden;
            backdrop-filter: blur(8px);
            background: url('assets/pills-medical-tools-arrangement-flat-lay.jpg') no-repeat center center/cover;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .split-container {
            display: flex;
            height: 100vh;
            position: relative;
            z-index: 1;
        }
        .left-side {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .right-side {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .staff-login-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .login-box {
            width: 500px;
            padding: 80px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            background: rgb(255, 255, 255);
            backdrop-filter: blur(10px);
            position: relative;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>

    <div class="split-container">
        <!-- Left Side (Animation) -->
        <div class="left-side">
            <div id="lottie-animation" style="width: 100%; height: 100%;"></div>
        </div>

        <!-- Right Side (Login Box) -->
        <div class="right-side">
            <div class="login-box">
                <a href="staff_login.php" class="btn btn-outline-primary staff-login-btn">Staff Login</a>
                <h3 class="text-center mb-3">Login</h3>

                <?php if (!empty($error_message)) { ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php } ?>

                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
    </div>

    <script>
        var animation = lottie.loadAnimation({
            container: document.getElementById('lottie-animation'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'assets/Animation - 1739479380501.json'//Animation - 1739477876197
        });
    </script>
</body>
</html>
