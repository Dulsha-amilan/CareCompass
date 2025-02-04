<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $date_of_birth = $_POST['date_of_birth'];

    $sql = "UPDATE users SET name = ?, address = ?, phone_number = ?, date_of_birth = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $address, $phone_number, $date_of_birth, $user_id);

    if ($stmt->execute()) {
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile!";
    }
}
?>
