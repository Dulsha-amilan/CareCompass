<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$sql = "SELECT * FROM appointments WHERE patient_id = '$patient_id' ORDER BY appointment_date, appointment_time";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
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
        <h3 class="text-center">Your Appointments</h3>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td><?php echo $row['appointment_date']; ?></td>
                        <td><?php echo $row['appointment_time']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <?php if ($row['status'] == 'Scheduled') { ?>
                                <a href="cancel_appointment.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Cancel</a>
                            <?php } else { echo "N/A"; } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="book_appointment.php" class="btn btn-success">Book New Appointment</a>
    </div>
</body>
</html>
