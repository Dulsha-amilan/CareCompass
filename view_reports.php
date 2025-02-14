<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

$staff_name = $_SESSION['staff_name'];
$staff_role = $_SESSION['staff_role'] ?? 'Unknown Role';

// Fetch reports with patient information
$reports_sql = "SELECT lr.*, u.name AS patient_name, u.email AS patient_email 
                FROM lab_reports lr 
                JOIN users u ON lr.patient_id = u.id 
                ORDER BY lr.upload_date DESC";
$reports_result = $conn->query($reports_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Lab Reports | Care Compass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables@1.10.18/media/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        .nav-link.active {
            background-color: #0d6efd;
            color: white !important;
            border-radius: 5px;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation Bar (same as staff_dashboard.php) -->
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
                        <span class="nav-link text-light">Welcome, <?php echo htmlspecialchars($staff_name); ?></span>
                    </span>
                    <li class="nav-item">
                        <a class="nav-link" href="staff_logout.php">Logout</a>
                    </li>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <h2 class="mb-4">Lab Reports</h2>
        
        <!-- Search and Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" id="patientSearch" class="form-control" placeholder="Search by patient name...">
                    </div>
                    <div class="col-md-4">
                        <input type="date" id="dateFilter" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary" onclick="resetFilters()">Reset Filters</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="reportsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>Patient Email</th>
                                <th>Report Type</th>
                                <th>Upload Date</th>
                                <th>File Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($report = $reports_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($report['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($report['patient_email']); ?></td>
                                <td><?php echo htmlspecialchars($report['report_type'] ?? 'General'); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($report['upload_date'])); ?></td>
                                <td><?php echo htmlspecialchars($report['file_name']); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo htmlspecialchars($report['report_file']); ?>" 
                                           class="btn btn-primary btn-sm" target="_blank">
                                            View
                                        </a>
                                        <a href="download_report.php?id=<?php echo $report['id']; ?>" 
                                           class="btn btn-success btn-sm">
                                            Download
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm"
                                                onclick="confirmDelete(<?php echo $report['id']; ?>)">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables@1.10.18/media/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#reportsTable').DataTable({
                order: [[3, 'desc']], // Sort by upload date by default
                pageLength: 10,
                language: {
                    search: "Search:"
                }
            });

            // Patient name search
            $('#patientSearch').on('keyup', function() {
                table.column(0).search(this.value).draw();
            });

            // Date filter
            $('#dateFilter').on('change', function() {
                table.column(3).search(this.value).draw();
            });
        });

        function resetFilters() {
            $('#patientSearch').val('');
            $('#dateFilter').val('');
            $('#reportsTable').DataTable().search('').columns().search('').draw();
        }

        function confirmDelete(reportId) {
            if (confirm('Are you sure you want to delete this report?')) {
                window.location.href = `delete_report.php?id=${reportId}`;
            }
        }

        // Highlight active nav item
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>