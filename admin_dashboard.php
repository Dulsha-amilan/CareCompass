<?php
session_start();
include 'db/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch patients
$patients_sql = "SELECT id, email FROM users WHERE role = 'patient'";
$patients_result = $conn->query($patients_sql);

// Fetch appointments
$appointments_sql = "SELECT a.id, u.email, a.doctor_name, 
                     a.appointment_date, a.appointment_time, a.status 
                     FROM appointments a
                     JOIN users u ON a.patient_id = u.id";
$appointments_result = $conn->query($appointments_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Care Compass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2 class="text-center mb-4">Admin Dashboard</h2>

        <h4>Manage Patients</h4>
        <table class="table table-bordered mt-3">
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

        <h4>Manage Appointments</h4>
        <table class="table table-bordered mt-3">
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
                        <a href="update_appointment.php?id=<?php echo $appointment['id']; ?>&status=Approved" class="btn btn-success btn-sm">Approve</a>
                        <a href="delete_appointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="admin_logout.php" class="btn btn-secondary mt-3">Logout</a>
    </div>
</body>
</html>
