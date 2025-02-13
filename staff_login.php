<?php
include 'db/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM staff WHERE email='$email'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['staff_id'] = $user['id'];
        $_SESSION['staff_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        header("Location: staff_dashboard.php");
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
    <title>Staff Login | Care Compass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.9.6/lottie.min.js"></script>
    <style>
        body {
            background: url('assets/10456897.jpg') no-repeat center center fixed;
            background-size: cover;
            backdrop-filter: blur(8px);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 80%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .animation-container {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            width: 50%;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-wrapper">
        <div class="animation-container">
            <div id="lottie-animation" style="width: 100%; height: 400px;"></div>
        </div>
        <div class="login-container">
            <h2 class="text-center">Staff Login</h2>

            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger"> <?php echo $error_message; ?> </div>
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
            <p class="mt-3 text-center">Don't have an account? <a href="staff_register.php">Register here</a></p>
        </div>
    </div>
    <script>
        var animation = lottie.loadAnimation({
            container: document.getElementById('lottie-animation'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'assets/Animation - 1739476869573.json' // Lottie JSON file path
        });
    </script>
</body>
</html>
