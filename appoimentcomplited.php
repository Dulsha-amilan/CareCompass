<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$search_query = "";
$search_params = [];
$param_types = "i";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = trim($_GET['search']);
    $search_query = " AND (appointments.appointment_date LIKE ? OR staff.name LIKE ? OR appointments.status LIKE ? )";
    $search_params = array_fill(0, 3, "%$search_term%");
    $param_types .= "sss";
}

$sql = "SELECT 
            appointments.id,
            appointments.appointment_date,
            appointments.appointment_time,
            appointments.status,
            appointments.notes,
            staff.name AS doctor_name
        FROM appointments 
        JOIN staff ON appointments.doctor_id = staff.id 
        WHERE appointments.patient_id = ?" . $search_query . "
        ORDER BY appointments.appointment_date, appointments.appointment_time";

$stmt = $conn->prepare($sql);
$params = array_merge([$patient_id], $search_params);
$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-4">Your Appointments</h2>
    
    <form class="d-flex mb-4" action="" method="get">
        <input class="form-control me-2" type="text" name="search" placeholder="Search by Doctor, Date, or Status" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button class="btn btn-primary" type="submit">Search</button>
    </form>
    
    <h4>Upcoming Appointments</h4>
    <table class="table table-bordered table-hover">
        <thead class="table-primary">
            <tr>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Notes</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { 
                if ($row['status'] !== 'Completed') { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                    <td><span class="badge <?php echo $row['status'] == 'Scheduled' ? 'bg-success' : 'bg-secondary'; ?>"> <?php echo htmlspecialchars($row['status']); ?></span></td>
                    <td><?php echo htmlspecialchars($row['notes'] ?? 'No notes'); ?></td>
                    <td>
                        <?php if ($row['status'] == 'Scheduled') { ?>
                            <a href="cancel_appointment.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this appointment?')">Cancel</a>
                        <?php } else { echo "N/A"; } ?>
                    </td>
                </tr>
            <?php } } ?>
        </tbody>
    </table>
    
    <h4>Completed Appointments</h4>
    <table class="table table-bordered table-hover">
        <thead class="table-success">
            <tr>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) { 
                if ($row['status'] == 'Completed') { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                    <td><span class="badge bg-success">Completed</span></td>
                    <td><?php echo htmlspecialchars($row['notes'] ?? 'No notes'); ?></td>
                </tr>
            <?php } } ?>
        </tbody>
    </table>
    
    <div class="text-center mt-4">
        <a href="book_appointment.php" class="btn btn-success btn-lg">Book New Appointment</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
