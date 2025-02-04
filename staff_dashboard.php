<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

$staff_name = $_SESSION['staff_name'];
$staff_role = $_SESSION['staff_role'] ?? 'Unknown Role';
$doctor_id = $_SESSION['staff_id']; // Assuming doctor_id corresponds to staff_id

// Fetch patients
$patients_sql = "SELECT * FROM users WHERE role = 'Patient'";
$patients_result = $conn->query($patients_sql);

// Fetch appointments for the logged-in doctor
$appointments_sql = "SELECT appointments.*, users.email, users.name AS patient_name 
                     FROM appointments 
                     JOIN users ON appointments.patient_id = users.id 
                     WHERE appointments.doctor_id = ?";
$stmt = $conn->prepare($appointments_sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$appointments_result = $stmt->get_result();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['lab_report'])) {
    $patient_id = $_POST['patient_id'];
    $file_name = $_FILES['lab_report']['name'];
    $file_tmp = $_FILES['lab_report']['tmp_name'];
    $upload_dir = "uploads/reports/";
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_path = $upload_dir . basename($file_name);
    
    if (move_uploaded_file($file_tmp, $file_path)) {
        $insert_sql = "INSERT INTO lab_reports (patient_id, report_file) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("is", $patient_id, $file_path);
        $stmt->execute();
    }
}
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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($patient = $patients_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $patient['id']; ?></td>
                    <td><?php echo $patient['name']; ?></td>
                    <td><?php echo $patient['email']; ?></td>
                    <td><?php echo $patient['phone_number']; ?></td>
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
                    <th>Patient Name</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($appointment = $appointments_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $appointment['patient_name']; ?></td>
                    <td><?php echo $appointment['email']; ?></td>
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

        <h4 class="mt-5">Upload Lab Reports</h4>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="patient_id" class="form-label">Select Patient</label>
                <select name="patient_id" id="patient_id" class="form-control" required>
                    <?php 
                    $patients_result->data_seek(0);
                    while ($patient = $patients_result->fetch_assoc()) { ?>
                        <option value="<?php echo $patient['id']; ?>"> <?php echo $patient['name']; ?> </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="lab_report" class="form-label">Upload Report</label>
                <input type="file" name="lab_report" id="lab_report" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>

        <h4 class="mt-5">View Lab Reports</h4>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Patient Name</th>
                    <th>Report</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $reports_sql = "SELECT lab_reports.*, users.name AS patient_name FROM lab_reports JOIN users ON lab_reports.patient_id = users.id";
                $reports_result = $conn->query($reports_sql);
                while ($report = $reports_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $report['patient_name']; ?></td>
                    <td><a href="<?php echo $report['report_file']; ?>" target="_blank">View Report</a></td>
                    <td>
                        <a href="delete_report.php?id=<?php echo $report['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <a href="staff_logout.php" class="btn btn-secondary mt-3">Logout</a>
    </div>
</body>
</html>
