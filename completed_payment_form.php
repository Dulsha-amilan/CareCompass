<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$appointment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch appointment details - removed staff.fee
$sql = "SELECT 
            appointments.id,
            appointments.appointment_date,
            appointments.appointment_time,
            appointments.status,
            staff.name AS doctor_name
        FROM appointments 
        JOIN staff ON appointments.doctor_id = staff.id 
        WHERE appointments.id = ? AND appointments.patient_id = ? AND appointments.status = 'Completed'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $appointment_id, $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Invalid appointment or not authorized.";
    header("Location: appointments.php");
    exit();
}

$appointment = $result->fetch_assoc();

// Set default consultation fee
$consultation_fee = 75.00; // Standard consultation fee

// Check if payment already exists
$payment_check = $conn->prepare("SELECT id FROM payments WHERE appointment_id = ?");
$payment_check->bind_param("i", $appointment_id);
$payment_check->execute();
$payment_result = $payment_check->get_result();

$payment_exists = $payment_result->num_rows > 0;

// Process form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = trim($_POST['payment_method']);
    $transaction_id = isset($_POST['transaction_id']) ? trim($_POST['transaction_id']) : '';
    $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : $consultation_fee;
    
    // Validate input
    if (empty($payment_method)) {
        $error_message = "Payment method is required.";
    } else {
        // Insert payment record
        $stmt = $conn->prepare("INSERT INTO payments (appointment_id, patient_id, amount, payment_method, transaction_id, payment_date) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iidss", $appointment_id, $patient_id, $amount, $payment_method, $transaction_id);
        
        if ($stmt->execute()) {
            $success_message = "Payment recorded successfully!";
            // Redirect after 2 seconds
            header("refresh:2;url=appointments.php");
        } else {
            $error_message = "Error recording payment. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment for Completed Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .payment-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: #f9f9f9;
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
                    <a class="nav-link" href="home.php">HOME</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="appointments.php">Appointment</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="lab_reports.php">Lab Report</a>
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
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center mb-4">Add Payment for Completed Appointment</h2>
            
            <?php if ($payment_exists): ?>
                <div class="alert alert-info text-center">
                    <h4>Payment has already been recorded for this appointment.</h4>
                    <a href="appointments.php" class="btn btn-primary mt-3">Return to Appointments</a>
                </div>
            <?php else: ?>
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="payment-form">
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Appointment Details</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Doctor:</strong> <?php echo htmlspecialchars($appointment['doctor_name']); ?></p>
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
                            <p><strong>Time:</strong> <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
                            <p><strong>Standard Consultation Fee:</strong> $<?php echo number_format($consultation_fee, 2); ?></p>
                        </div>
                    </div>
                    
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Payment Amount ($)</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" value="<?php echo $consultation_fee; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">-- Select Payment Method --</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="PayPal">PayPal</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">Transaction ID (optional)</label>
                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="For electronic payments">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Submit Payment</button>
                            <a href="appointments.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>