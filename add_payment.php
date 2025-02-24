<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid appointment ID.");
}

$appointment_id = $_GET['id'];
$patient_id = $_SESSION['user_id'];
$message = "";

// Fetch appointment details
$sql = "SELECT appointments.id, appointments.appointment_date, staff.name AS doctor_name
        FROM appointments 
        JOIN staff ON appointments.doctor_id = staff.id 
        WHERE appointments.id = ? AND appointments.patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $appointment_id, $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Appointment not found.");
}

$appointment = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];

    if (empty($amount) || empty($payment_method)) {
        $message = "All fields are required.";
    } else {
        $insert_sql = "INSERT INTO payments (appointment_id, patient_id, amount, payment_method, payment_date) 
                       VALUES (?, ?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iiss", $appointment_id, $patient_id, $amount, $payment_method);

        if ($insert_stmt->execute()) {
            $message = "Payment added successfully!";
        } else {
            $message = "Error processing payment.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Add Payment</h2>
    <p><strong>Doctor:</strong> <?php echo htmlspecialchars($appointment['doctor_name']); ?></p>
    <p><strong>Date:</strong> <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Amount ($)</label>
            <input type="number" step="0.01" class="form-control" name="amount" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select class="form-control" name="payment_method" required>
                <option value="">Select Payment Method</option>
                <option value="Credit Card">Credit Card</option>
                <option value="PayPal">PayPal</option>
                <option value="Cash">Cash</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Submit Payment</button>
        <a href="appointments.php" class="btn btn-secondary">Back</a>
    </form>
</div>

</body>
</html>
