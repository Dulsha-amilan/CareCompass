<?php
include 'db/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $address = trim($_POST['address']);
    $phone_number = trim($_POST['phone_number']);
    $date_of_birth = $_POST['date_of_birth'];
    $role = "patient";

    $sql = "INSERT INTO users (name, email, password, address, phone_number, date_of_birth, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $name, $email, $password, $address, $phone_number, $date_of_birth, $role);

    if ($stmt->execute()) {
        $success_message = "Registration successful! You can now <a href='index.php'>Login</a>";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Care Compass Hospitals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets\css\register.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.9.6/lottie.min.js"></script>
</head>
<style>
        body {
            height: 100%;
            margin: 0;
            overflow: hidden;
            background: url('assets/pills-medical-tools-arrangement-flat-lay.jpg') no-repeat center center/cover;
            backdrop-filter: blur(8px);
        }
        </style>
<body>
    <div class="overlay"></div>

    <div class="split-container">
        <!-- Left Side (Animation) -->
        <div class="left-side">
            <div id="lottie-animation" style="width: 100%; height: 100%;"></div>
        </div>

        <!-- Right Side (Registration Form) -->
        <div class="right-side">
            <div class="register-box">
                <h3 class="text-center mb-3">Patient Registration</h3>

                <?php if (!empty($success_message)) { ?>
                    <div class="alert alert-success"> <?php echo $success_message; ?> </div>
                <?php } ?>

                <?php if (!empty($error_message)) { ?>
                    <div class="alert alert-danger"> <?php echo $error_message; ?> </div>
                <?php } ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>

                <p class="mt-3 text-center">
                    Already have an account? <a href="index.php">Login</a>
                </p>
            </div>
        </div>
    </div>

    <script src="assets\js\register.js"></script>
</body>
</html>