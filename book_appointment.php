<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch all registered doctors with their specializations
$doctors_sql = "SELECT id, name, specialization FROM staff WHERE role = 'Doctor'";
$doctors_result = $conn->query($doctors_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doctor_id']; // Get selected doctor ID
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    // Insert into appointments table
    $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time) 
            VALUES ('$patient_id', '$doctor_id', '$appointment_date', '$appointment_time')";

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
                <label class="form-label">Select Doctor</label>
                <select name="doctor_id" class="form-control" required>
                    <option value="">-- Select a Doctor --</option>
                    <?php while ($doctor = $doctors_result->fetch_assoc()) { ?>
                        <option value="<?php echo $doctor['id']; ?>">
                            Dr. <?php echo $doctor['name']; ?> - <?php echo $doctor['specialization']; ?>
                        </option>
                    <?php } ?>
                </select>
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