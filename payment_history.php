<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['user_id'];

// Fetch all payments for the patient with appointment details
$sql = "SELECT 
            payments.id,
            payments.amount,
            payments.payment_method,
            payments.transaction_id,
            payments.payment_date,
            payments.status,
            appointments.appointment_date,
            appointments.appointment_time,
            staff.name AS doctor_name
        FROM payments 
        JOIN appointments ON payments.appointment_id = appointments.id 
        JOIN staff ON appointments.doctor_id = staff.id 
        WHERE payments.patient_id = ?
        ORDER BY payments.payment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
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
                    <a class="nav-link" href="appointments.php">Appointment</a>
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
    <h2 class="text-center mb-4">Your Payment History</h2>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Summary</h4>
        </div>
        <div class="card-body">
            <?php
            $totalPayments = 0;
            $paymentCount = 0;
            
            if ($result->num_rows > 0) {
                $data = $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    $totalPayments += $row['amount'];
                    $paymentCount++;
                }
                $result->data_seek(0); // Reset the pointer
            }
            ?>
            <div class="row">
                <div class="col-md-6">
                    <h5>Total Payments</h5>
                    <p class="display-6 text-primary">$<?php echo number_format($totalPayments, 2); ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Number of Transactions</h5>
                    <p class="display-6 text-success"><?php echo $paymentCount; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Date</th>
                    <th>Doctor</th>
                    <th>Appointment Date</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Transaction ID</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M d, Y h:i A', strtotime($row['payment_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['appointment_date'])) . ' at ' . $row['appointment_time']; ?></td>
                            <td>$<?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                            <td><?php echo $row['transaction_id'] ? htmlspecialchars($row['transaction_id']) : 'N/A'; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $row['status'] == 'Completed' ? 'success' : ($row['status'] == 'Pending' ? 'warning' : ($row['status'] == 'Failed' ? 'danger' : 'info')); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No payment history found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="text-center mt-4">
        <a href="appointments.php" class="btn btn-primary">Back to Appointments</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>