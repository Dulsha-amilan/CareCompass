<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$appointment_id = $_GET['id'];
$sql = "UPDATE appointments SET status='Cancelled' WHERE id='$appointment_id' AND patient_id='{$_SESSION['user_id']}'";

if ($conn->query($sql)) {
    header("Location: home.php");
    exit();
} else {
    echo "Failed to cancel appointment!";
}
?>
