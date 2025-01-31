<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

$staff_name = $_SESSION['staff_name'];
$staff_role = $_SESSION['staff_role'];

$patients_sql = "SELECT * FROM users";
$patients_result = $conn->query($patients_sql);

$appointments_sql = "SELECT appointments.*, users.email 
                     FROM appointments 
                     JOIN users ON appointments.patient_id = users.id";
$appointments_result = $conn->query($appointments_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | Care Compass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Staff Dashboard</h2>
        <h4>Welcome, <?php echo $staff_name; ?> (<?php echo $staff_role; ?>)</h4>

        <h4 class="mt-4">Manage Patients</h4>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($patient = $patients_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $patient['id']; ?></td>
                    <td><?php echo $patient['email']; ?></td>
                    <td>
                        <a href="delete_patient.php?id=<?php echo $patient['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <h4 class="mt-5">Manage Appointments</h4>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Patient Email</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($appointment = $appointments_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $appointment['email']; ?></td>
                    <td><?php echo $appointment['doctor_name']; ?></td>
                    <td><?php echo $appointment['appointment_date']; ?></td>
                    <td><?php echo $appointment['appointment_time']; ?></td>
                    <td><?php echo $appointment['status']; ?></td>
                    <td>
                        <a href="delete_appointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <a href="staff_logout.php" class="btn btn-secondary mt-3">Logout</a>
    </div>
</body>
</html>
