<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];
    $sql = "DELETE FROM appointments WHERE id = '$appointment_id'";
    
    if ($conn->query($sql)) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
