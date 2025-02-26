<?php
session_start();
include 'db/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch summary statistics
$stats = [
    'total_patients' => $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'patient'")->fetch_assoc()['count'],
    'total_staff' => $conn->query("SELECT COUNT(*) as count FROM staff WHERE role != 'admin'")->fetch_assoc()['count'],
    'total_appointments' => $conn->query("SELECT COUNT(*) as count FROM appointments")->fetch_assoc()['count'],
    'pending_appointments' => $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'Pending'")->fetch_assoc()['count']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Care Compass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Add Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'nav.php'; ?>

    <div class="container">
        <!-- Alert Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <h2 class="text-center mb-4">Welcome to Admin Dashboard</h2>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Patients</h5>
                        <p class="card-text display-6"><?php echo $stats['total_patients']; ?></p>
                        <i class="fas fa-users position-absolute top-50 end-0 translate-middle-y opacity-25 fa-2x me-3"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Total Staff</h5>
                        <p class="card-text display-6"><?php echo $stats['total_staff']; ?></p>
                        <i class="fas fa-user-md position-absolute top-50 end-0 translate-middle-y opacity-25 fa-2x me-3"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Total Appointments</h5>
                        <p class="card-text display-6"><?php echo $stats['total_appointments']; ?></p>
                        <i class="fas fa-calendar-check position-absolute top-50 end-0 translate-middle-y opacity-25 fa-2x me-3"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Pending Appointments</h5>
                        <p class="card-text display-6"><?php echo $stats['pending_appointments']; ?></p>
                        <i class="fas fa-clock position-absolute top-50 end-0 translate-middle-y opacity-25 fa-2x me-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2">
                            <a href="manage_staff.php" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>Add New Staff
                            </a>
                            <a href="manage_appointments.php" class="btn btn-success">
                                <i class="fas fa-calendar-plus me-2"></i>View Appointments
                            </a>
                            <a href="manage_patients.php" class="btn btn-info">
                                <i class="fas fa-user-injured me-2"></i>Manage Patients
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.all.min.js"></script>
</body>
</html>
