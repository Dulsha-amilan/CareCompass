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

// Include the registration form
include 'register_form.html';
?>
