<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['user_id'];

// Fetch lab reports for the logged-in patient
$lab_reports_sql = "SELECT * FROM lab_reports WHERE patient_id = ? ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($lab_reports_sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$lab_reports_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .navbar-custom {
            background-color: #007bff;
            padding: 15px;
        }
        .navbar-custom .nav-link {
            color: white !important;
            font-size: 18px;
            transition: 0.3s;
        }
        .navbar-custom .nav-link:hover {
            color: #f8f9fa !important;
            text-decoration: underline;
        }
        .navbar-brand {
            font-size: 22px;
            font-weight: bold;
            color: white !important;
        }
        .report-card {
            transition: transform 0.2s;
        }
        .report-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="home.php">Patient Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="home.php">HOME</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="appointments.php">Appointment</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="lab_reports.php">Lab Report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="payment_history.php">Payment History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About Us</a>
                </li>
            </ul>
            <div>
                <a class="btn btn-light me-2" href="profile.php">Profile</a>
                <a class="btn btn-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center mb-4">Your Lab Reports</h2>

    <div class="row">
        <?php while ($report = $lab_reports_result->fetch_assoc()) { ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card report-card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-file-medical-fill text-primary me-2"></i>
                            <?php echo htmlspecialchars($report['file_name']); ?>
                        </h5>
                        <p class="card-text">
                            <small class="text-muted">
                                Uploaded: <?php echo date('F j, Y', strtotime($report['uploaded_at'])); ?>
                            </small>
                        </p>
                        <div class="d-flex justify-content-between mt-3">
                            <a href="<?php echo htmlspecialchars($report['report_file']); ?>" 
                               target="_blank" 
                               class="btn btn-info">
                                <i class="bi bi-eye-fill"></i> View
                            </a>
                            <a href="download.php?file=<?php echo urlencode($report['report_file']); ?>" 
                               class="btn btn-primary">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if ($lab_reports_result->num_rows === 0) { ?>
            <div class="col-12 text-center">
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    No lab reports available at this time.
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>