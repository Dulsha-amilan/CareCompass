<?php
include 'db/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $doctor = $_POST['doctor'];
    $appointment_date = $_POST['appointment_date'];

    $sql = "INSERT INTO appointments (user_id, doctor, appointment_date) VALUES ('$user_id', '$doctor', '$appointment_date')";
    if ($conn->query($sql)) {
        echo "Appointment booked!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="post">
    <input type="text" name="doctor" placeholder="Doctor's Name" required>
    <input type="date" name="appointment_date" required>
    <button type="submit">Book Appointment</button>
</form>
