<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

$staff_name = $_SESSION['staff_name'];
$staff_role = $_SESSION['staff_role'] ?? 'Unknown Role';
$doctor_id = $_SESSION['staff_id'];

// CSRF Token Generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch patients securely
$patients_sql = "SELECT * FROM users WHERE role = ?";
$stmt = $conn->prepare($patients_sql);
$role = 'Patient';
$stmt->bind_param("s", $role);
$stmt->execute();
$patients_result = $stmt->get_result();

// Fetch appointments for the logged-in doctor
$appointments_sql = "SELECT appointments.*, users.email, users.name AS patient_name 
                     FROM appointments 
                     JOIN users ON appointments.patient_id = users.id 
                     WHERE appointments.doctor_id = ?";
$stmt = $conn->prepare($appointments_sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$appointments_result = $stmt->get_result();

// Handle file upload securely
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['lab_report'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed!");
    }

    $patient_id = $_POST['patient_id'];
    $original_file_name = $_FILES['lab_report']['name'];
    $file_tmp = $_FILES['lab_report']['tmp_name'];
    $file_extension = pathinfo($original_file_name, PATHINFO_EXTENSION);
    $allowed_extensions = ['pdf', 'jpg', 'png', 'jpeg'];
    $upload_dir = "uploads/reports/";
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if (in_array(strtolower($file_extension), $allowed_extensions)) {
        $new_file_name = uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $new_file_name;
        
        if (move_uploaded_file($file_tmp, $file_path)) {
            $insert_sql = "INSERT INTO lab_reports (patient_id, file_name, report_file) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("iss", $patient_id, $original_file_name, $file_path);
            
            if ($stmt->execute()) {
                echo "<script>alert('Report uploaded successfully!');</script>";
            } else {
                echo "<script>alert('Error uploading report to database!');</script>";
            }
        } else {
            echo "<script>alert('Error moving uploaded file!');</script>";
        }
    } else {
        echo "<script>alert('Invalid file format! Only PDF, JPG, PNG, and JPEG are allowed.');</script>";
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
        <h4>Welcome, <?php echo htmlspecialchars($staff_name); ?> (<?php echo htmlspecialchars($staff_role); ?>)</h4>

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
                    <td><?php echo htmlspecialchars($patient['id']); ?></td>
                    <td><?php echo htmlspecialchars($patient['name']); ?></td>
                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                    <td><?php echo htmlspecialchars($patient['phone_number']); ?></td>
                    <td>
                        <a href="delete_patient.php?id=<?php echo $patient['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <h4 class="mt-5">Upload Lab Reports</h4>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="mb-3">
                <label for="patient_id" class="form-label">Select Patient</label>
                <select name="patient_id" id="patient_id" class="form-control" required>
                    <?php 
                    $patients_result->data_seek(0);
                    while ($patient = $patients_result->fetch_assoc()) { ?>
                        <option value="<?php echo $patient['id']; ?>"> <?php echo htmlspecialchars($patient['name']); ?> </option>
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
                    <th>File Name</th>
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
                    <td><?php echo htmlspecialchars($report['patient_name']); ?></td>
                    <td><?php echo htmlspecialchars($report['file_name']); ?></td>
                    <td><a href="<?php echo htmlspecialchars($report['report_file']); ?>" target="_blank">View Report</a></td>
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