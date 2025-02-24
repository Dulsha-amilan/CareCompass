<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Health Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .about-header {
            background: linear-gradient(rgba(0,123,255,0.9), rgba(0,123,255,0.9)), url('assets/hospital-bg.jpg');
            background-size: cover;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .section-title {
            position: relative;
            margin-bottom: 30px;
            padding-bottom: 15px;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background-color: #007bff;
        }
        .team-member {
            text-align: center;
            margin-bottom: 30px;
            transition: transform 0.3s;
        }
        .team-member:hover {
            transform: translateY(-10px);
        }
        .team-member img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin-bottom: 20px;
            object-fit: cover;
        }
        .stat-card {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stat-card i {
            font-size: 40px;
            color: #007bff;
            margin-bottom: 15px;
        }
        .footer {
            background-color: #2c3e50;
            color: #fff;
            padding: 60px 0 30px;
            margin-top: 50px;
        }
        /* Include the rest of your footer styles from home.php */
    </style>
</head>
<body>

<!-- Navigation Bar -->
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

<!-- About Header -->
<div class="about-header">
    <div class="container">
        <h1>About Health Center</h1>
        <p class="lead">Providing Quality Healthcare Services Since 1995</p>
    </div>
</div>

<!-- Main Content -->
<div class="container mt-5">
    <!-- Our Story -->
    <section class="mb-5">
        <h2 class="section-title text-center">Our Story</h2>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <p class="text-center">Founded in 1995, Health Center has been at the forefront of providing exceptional healthcare services to our community. Our journey began with a simple mission: to make quality healthcare accessible to everyone. Over the years, we have grown into a comprehensive medical facility, equipped with state-of-the-art technology and staffed by dedicated healthcare professionals.</p>
            </div>
        </div>
    </section>

    <!-- Statistics -->
    <section class="mb-5">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-user-md"></i>
                    <h3>50+</h3>
                    <p>Expert Doctors</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-procedures"></i>
                    <h3>10,000+</h3>
                    <p>Patients Served</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-hospital"></i>
                    <h3>5</h3>
                    <p>Locations</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="fas fa-award"></i>
                    <h3>25+</h3>
                    <p>Years Experience</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission -->
    <section class="mb-5">
        <h2 class="section-title text-center">Our Mission</h2>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <p class="text-center">Our mission is to enhance the health and wellbeing of our community by providing accessible, high-quality healthcare services with compassion and excellence. We are committed to:</p>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5><i class="fas fa-heart text-primary me-2"></i> Patient-Centered Care</h5>
                                <p>Putting our patients first in everything we do.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5><i class="fas fa-star text-primary me-2"></i> Excellence</h5>
                                <p>Maintaining the highest standards in healthcare services.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Team -->
    <section class="mb-5">
        <h2 class="section-title text-center">Our Leadership Team</h2>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="team-member">
                    <img src="/api/placeholder/200/200" alt="Dr. Smith">
                    <h4>Dr. John Smith</h4>
                    <p class="text-muted">Chief Medical Officer</p>
                    <p>Over 20 years of experience in healthcare management and patient care.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="team-member">
                    <img src="/api/placeholder/200/200" alt="Dr. Johnson">
                    <h4>Dr. Sarah Johnson</h4>
                    <p class="text-muted">Medical Director</p>
                    <p>Specialist in internal medicine with extensive research experience.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="team-member">
                    <img src="/api/placeholder/200/200" alt="Dr. Davis">
                    <h4>Dr. Michael Davis</h4>
                    <p class="text-muted">Head of Patient Care</p>
                    <p>Dedicated to improving patient experience and care quality.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Footer -->
<footer class="footer">
    <!-- Include your footer content from home.php here -->
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>