<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'];
$staff_role = $_SESSION['staff_role'] ?? 'Unknown Role';

// Fetch specialization from database if the role is doctor
$specialization = '';
if ($staff_role == 'doctor') {
    $spec_query = "SELECT specialization FROM staff WHERE id = ?";
    $stmt = $conn->prepare($spec_query);
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $specialization = $row['specialization'];
    }
    $stmt->close();
}

// CSRF Token Generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | Care Compass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <link rel="stylesheet" href="assets\css\staff.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>
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
                        <a class="nav-link" href="about_us1.php">About Us</a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <span class="nav-item">
                        <span class="nav-link text-light">Welcome, <?php echo htmlspecialchars($staff_name); ?></span>
                    </span>
                    <span class="nav-item">
                        <a class="nav-link text-danger" href="staff_logout.php">Logout</a>
                    </span>
                </div>
            </div>
        </div>
    </nav>
   
   <!-- Updated Banner Section -->
   <div class="banner">
        <div id="banner-slider" class="owl-carousel owl-theme">
            <div class="item">
                <img src="assets/1.png" alt="Banner 1">
            </div>
            <div class="item">
                <img src="assets/2.png" alt="Banner 2">
            </div>
            <div class="item">
                <img src="assets/3.png" alt="Banner 3">
            </div>
            <div class="item">
                <img src="assets/4.png" alt="Banner 4">
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="welcome-section">
            <h2>Welcome to Staff Dashboard</h2>
            <div class="staff-info">
                <span class="staff-badge role-badge">
                    <i class="fas fa-user-md me-1"></i> <?php echo htmlspecialchars(ucfirst($staff_role)); ?>
                </span>
                <?php if ($specialization): ?>
                <span class="staff-badge specialization-badge">
                    <i class="fas fa-stethoscope me-1"></i> <?php echo htmlspecialchars($specialization); ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Patients</h5>
                        <p class="card-text">View and manage patient information</p>
                        <a href="manage_patients.php" class="btn btn-primary">Go to Patients</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Appointments</h5>
                        <p class="card-text">Handle patient appointments</p>
                        <a href="manage_appointments.php" class="btn btn-primary">Go to Appointments</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Upload Reports</h5>
                        <p class="card-text">Upload patient lab reports</p>
                        <a href="upload_reports.php" class="btn btn-primary">Upload Reports</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">View Reports</h5>
                        <p class="card-text">Access patient lab reports</p>
                        <a href="view_reports.php" class="btn btn-primary">View Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets\js\staff.js"></script>
</body>
</html>