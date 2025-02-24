<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['user_id'];

// Fetch recent appointments (limit to 3)
$sql = "SELECT appointments.*, staff.name AS doctor_name 
        FROM appointments 
        JOIN staff ON appointments.doctor_id = staff.id 
        WHERE appointments.patient_id = ? 
        ORDER BY appointments.appointment_date DESC 
        LIMIT 3";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$recent_appointments = $stmt->get_result();

// Fetch recent lab reports (limit to 3)
$lab_reports_sql = "SELECT * FROM lab_reports WHERE patient_id = ? ORDER BY uploaded_at DESC LIMIT 3";
$stmt2 = $conn->prepare($lab_reports_sql);
$stmt2->bind_param("i", $patient_id);
$stmt2->execute();
$recent_lab_reports = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>
    <style>
        .banner {
            width: 100%;
            max-height: 400px;
            overflow: hidden;
        }
        .banner img {
            width: 100%;
            height: auto;
        }
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
        .dashboard-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .section-heading {
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .section-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: #007bff;
        }
        .footer {
        background-color: #2c3e50;
        color: #fff;
        padding: 60px 0 30px;
        margin-top: 50px;
    }
    .footer h5 {
        color: #fff;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .footer-links li {
        margin-bottom: 10px;
    }
    .footer-links a {
        color: #ecf0f1;
        text-decoration: none;
        transition: color 0.3s;
    }
    .footer-links a:hover {
        color: #3498db;
    }
    .footer-contact i {
        margin-right: 10px;
        color: #3498db;
    }
    .footer-social a {
        display: inline-block;
        width: 35px;
        height: 35px;
        background-color: #34495e;
        color: #fff;
        text-align: center;
        line-height: 35px;
        border-radius: 50%;
        margin-right: 10px;
        transition: background-color 0.3s;
    }
    .footer-social a:hover {
        background-color: #3498db;
    }
    .footer-bottom {
        background-color: #233140;
        padding: 20px 0;
        margin-top: 40px;
    }
    .footer-bottom p {
        margin: 0;
        color: #bdc3c7;
    }
    </style>
</head>
<body>
<!-- This is just the navigation section to be added to home.php -->
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
                <li class="nav-item">
                    <a class="nav-link" href="submit_feedback.php">Feedback</a>
                </li>
            </ul>
            <div>
                <a class="btn btn-light me-2" href="profile.php">Profile</a>
                <a class="btn btn-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="banner">
    <div id="banner-slider" class="owl-carousel">
        <img src="assets/1.png" alt="Banner 1">
        <img src="assets/2.png" alt="Banner 2">
        <img src="assets/3.png" alt="Banner 3">
        <img src="assets/4.png" alt="Banner 4">
    </div>
</div>

<div class="container mt-5">
    <!-- Welcome Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-body text-center">
                    <h2>Welcome to Your Health Dashboard</h2>
                    <p class="lead">Manage your appointments and access your lab reports all in one place.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links Section -->
    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h3 class="section-heading">Appointments</h3>
                    <p>Schedule and manage your medical appointments with our healthcare professionals.</p>
                    <div class="d-grid gap-2">
                        <a href="appointments.php" class="btn btn-primary">View All Appointments</a>
                        <a href="book_appointment.php" class="btn btn-outline-primary">Book New Appointment</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h3 class="section-heading">Lab Reports</h3>
                    <p>Access and download your laboratory test results and medical reports.</p>
                    <div class="d-grid gap-2">
                        <a href="lab_reports.php" class="btn btn-primary">View All Lab Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Section -->
    <div class="row">
        <!-- Recent Appointments -->
        <div class="col-md-6 mb-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h3 class="section-heading">Recent Appointments</h3>
                    <?php if ($recent_appointments->num_rows > 0) { ?>
                        <div class="list-group">
                            <?php while ($appointment = $recent_appointments->fetch_assoc()) { ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></h5>
                                        <small class="text-muted"><?php echo htmlspecialchars($appointment['appointment_date']); ?></small>
                                    </div>
                                    <p class="mb-1">Time: <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
                                    <small class="text-muted">Status: <?php echo htmlspecialchars($appointment['status']); ?></small>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <p class="text-muted">No recent appointments found.</p>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Recent Lab Reports -->
        <div class="col-md-6 mb-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h3 class="section-heading">Recent Lab Reports</h3>
                    <?php if ($recent_lab_reports->num_rows > 0) { ?>
                        <div class="list-group">
                            <?php while ($report = $recent_lab_reports->fetch_assoc()) { ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($report['file_name']); ?></h5>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y', strtotime($report['uploaded_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="mt-2">
                                        <a href="<?php echo htmlspecialchars($report['report_file']); ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-info me-2">View</a>
                                        <a href="download.php?file=<?php echo urlencode($report['report_file']); ?>" 
                                           class="btn btn-sm btn-primary">Download</a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <p class="text-muted">No recent lab reports found.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    
</div>
<!-- Add this link to the head section for FontAwesome icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Add this footer section just before the closing body tag -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- Quick Links -->
            <div class="col-md-3 mb-4">
                <h5>Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="appointments.php">Appointments</a></li>
                    <li><a href="appoimentcomplited.php">Completed Appointments</a></li>
                    <li><a href="lab_reports.php">Lab Reports</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="col-md-3 mb-4">
                <h5>Our Services</h5>
                <ul class="footer-links">
                    <li><a href="#">General Checkup</a></li>
                    <li><a href="#">Laboratory Tests</a></li>
                    <li><a href="#">Specialist Consultation</a></li>
                    <li><a href="#">Emergency Services</a></li>
                    <li><a href="#">Vaccination</a></li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="col-md-3 mb-4">
                <h5>Contact Us</h5>
                <ul class="footer-links footer-contact">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        123 Healthcare Ave,<br>Medical District, City
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        +1 (555) 123-4567
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        info@healthcenter.com
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        Mon - Fri: 8:00 AM - 8:00 PM
                    </li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="col-md-3 mb-4">
                <h5>Newsletter</h5>
                <p>Subscribe to our newsletter for health tips and updates.</p>
                <form class="mt-3">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Enter your email" aria-label="Email">
                        <button class="btn btn-primary" type="submit">Subscribe</button>
                    </div>
                </form>
                
                <!-- Social Media Links -->
                <div class="footer-social mt-4">
                    <h5>Follow Us</h5>
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> Health Center. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>
                        <a href="#" class="text-white me-3">Privacy Policy</a>
                        <a href="#" class="text-white">Terms of Service</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
    $(document).ready(function() {
        $("#banner-slider").owlCarousel({
            autoPlay: 3000,
            items: 1,
            loop: true,
            nav: false,
            dots: true
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>