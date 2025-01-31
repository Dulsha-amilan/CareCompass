<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_SESSION['user_id'];
    $doctor_name = $_POST['doctor_name'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    $sql = "INSERT INTO appointments (patient_id, doctor_name, appointment_date, appointment_time) 
            VALUES ('$patient_id', '$doctor_name', '$appointment_date', '$appointment_time')";
    if ($conn->query($sql)) {
        header("Location: home.php");
        exit();
    } else {
        $error_message = "Failed to book appointment!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Patient Dashboard</a>
            <a class="btn btn-danger" href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h3 class="text-center">Book an Appointment</h3>
        
        <?php if (!empty($error_message)) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Doctor's Name</label>
                <input type="text" name="doctor_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Appointment Date</label>
                <input type="date" name="appointment_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Appointment Time</label>
                <input type="time" name="appointment_time" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Book Appointment</button>
        </form>
    </div>
</body>
</html>
