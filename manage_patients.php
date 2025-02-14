<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

// Fetch patients securely
$patients_sql = "SELECT * FROM users WHERE role = ?";
$stmt = $conn->prepare($patients_sql);
$role = 'Patient';
$stmt->bind_param("s", $role);
$stmt->execute();
$patients_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients | Care Compass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables@1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        .nav-link.active {
            background-color: #0d6efd;
            color: white !important;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar (Same as staff_dashboard.php) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="staff_dashboard.php">Care Compass</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="staff_dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_patients.php">Manage Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_appointments.php">Manage Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="upload_reports.php">Upload Lab Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_reports.php">View Lab Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about_us.php">About Us</a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <span class="nav-item">
                        <a class="nav-link text-danger" href="staff_logout.php">Logout</a>
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <h2>Manage Patients</h2>
        <div class="card">
            <div class="card-body">
                <table id="patientsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
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
                                <button class="btn btn-primary btn-sm" onclick="editPatient(<?php echo $patient['id']; ?>)">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deletePatient(<?php echo $patient['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#patientsTable').DataTable();
        });

        function editPatient(id) {
            // Add edit functionality
            alert('Edit patient ' + id);
        }

        function deletePatient(id) {
            if (confirm('Are you sure you want to delete this patient?')) {
                window.location.href = 'delete_patient.php?id=' + id;
            }
        }
    </script>
</body>
</html>