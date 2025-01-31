<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];
    $sql = "DELETE FROM users WHERE id = '$patient_id'";
    
    if ($conn->query($sql)) {
        header("Location: staff_dashboard.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
