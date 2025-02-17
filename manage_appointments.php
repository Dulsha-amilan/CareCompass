<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];

// Update appointment status
if (isset($_POST['update_status']) && isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['new_status'];
    $update_sql = "UPDATE appointments SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $appointment_id);
    $stmt->execute();
}

// Modified SQL query to remove the notes column
$appointments_sql = "SELECT 
    a.id,
    a.appointment_date,
    a.appointment_time,
    a.status,
    p.name AS patient_name,
    p.email AS patient_email,
    p.phone_number AS patient_phone,
    a.doctor_name
    FROM appointments a
    JOIN users p ON a.patient_id = p.id
    ORDER BY a.appointment_date DESC, a.appointment_time ASC";

$appointments_result = $conn->query($appointments_sql);

// If you want to add the notes column to your database, run this SQL:
/*
ALTER TABLE appointments
ADD COLUMN notes TEXT;
*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments | Care Compass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .nav-link.active {
            background-color: #0d6efd;
            color: white !important;
            border-radius: 5px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }
        .status-pending { background-color: #ffd700; }
        .status-approved { background-color: #90EE90; }
        .status-cancelled { background-color: #ffcccb; }
        .status-completed { background-color: #87CEEB; }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Appointments</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
                <i class="fas fa-plus"></i> New Appointment
            </button>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="dateFilter" class="form-label">Filter by Date</label>
                        <input type="date" class="form-control" id="dateFilter">
                    </div>
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Filter by Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">All</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="card">
            <div class="card-body">
                <table id="appointmentsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Contact</th>
                          
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appointment = $appointments_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                            <td>
                                <div>Email: <?php echo htmlspecialchars($appointment['patient_email']); ?></div>
                                <div>Phone: <?php echo htmlspecialchars($appointment['patient_phone']); ?></div>
                            </td>
                          
                            <td>
                                <span class="status-badge status-<?php echo strtolower($appointment['status']); ?>">
                                    <?php echo htmlspecialchars($appointment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-success" onclick="updateStatus(<?php echo $appointment['id']; ?>, 'Approved')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="updateStatus(<?php echo $appointment['id']; ?>, 'Cancelled')">
                                        <i class="fas fa-times"></i>
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

    <!-- New Appointment Modal -->
    <div class="modal fade" id="newAppointmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="newAppointmentForm" action="create_appointment.php" method="POST">
                        <div class="mb-3">
                            <label for="patient" class="form-label">Patient</label>
                            <select class="form-select" id="patient" name="patient_id" required>
                                <?php
                                $patients_sql = "SELECT id, name FROM users WHERE role = 'Patient'";
                                $patients_result = $conn->query($patients_sql);
                                while ($patient = $patients_result->fetch_assoc()) {
                                    echo "<option value='" . $patient['id'] . "'>" . htmlspecialchars($patient['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="appointmentDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="appointmentDate" name="appointment_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="appointmentTime" class="form-label">Time</label>
                            <input type="time" class="form-control" id="appointmentTime" name="appointment_time" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Schedule Appointment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            const table = $('#appointmentsTable').DataTable({
                order: [[0, 'desc'], [1, 'asc']]
            });

            // Date filter
            $('#dateFilter').change(function() {
                table.draw();
            });

            // Status filter
            $('#statusFilter').change(function() {
                table.draw();
            });

            // Custom filtering function
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const dateFilter = $('#dateFilter').val();
                const statusFilter = $('#statusFilter').val();
                const appointmentDate = data[0]; // Date is in column 0
                const status = data[5]; // Status is in column 5

                if ((dateFilter === '' || appointmentDate === dateFilter) &&
                    (statusFilter === '' || status.includes(statusFilter))) {
                    return true;
                }
                return false;
            });
        });

        function updateStatus(appointmentId, newStatus) {
            if (confirm('Are you sure you want to update this appointment status?')) {
                $.post('update_appointment_status.php', {
                    appointment_id: appointmentId,
                    new_status: newStatus
                }, function(response) {
                    location.reload();
                });
            }
        }
    </script>
</body>
</html>
