<?php
include 'db/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO staff (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";

    if ($conn->query($sql)) {
        header("Location: staff_login.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration | Care Compass</title>
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
        .register-container {
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
        <div class="register-container">
            <h2 class="text-center">Staff Registration</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Select Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="Doctor">Doctor</option>
                        <option value="Nurse">Nurse</option>
                        <option value="Receptionist">Receptionist</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
                <p class="mt-3 text-center">Already have an account? <a href="staff_login.php">Login here</a></p>
            </form>
        </div>
    </div>
    <script>
        var animation = lottie.loadAnimation({
            container: document.getElementById('lottie-animation'), // Animation container
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'assets/Animation - 1739479867797.json' // Lottie animation JSON file
        });
    </script>
</body>
</html>
