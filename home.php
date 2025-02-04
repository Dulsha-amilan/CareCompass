<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$search_query = "";

// Check if a search has been made
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $search_query = " AND (appointments.appointment_date LIKE '%$search_term%' OR staff.name LIKE '%$search_term%' OR appointments.status LIKE '%$search_term%')";
}

// Fetch appointments
$sql = "SELECT appointments.*, staff.name AS doctor_name 
        FROM appointments 
        JOIN staff ON appointments.doctor_id = staff.id 
        WHERE appointments.patient_id = ? $search_query
        ORDER BY appointments.appointment_date, appointments.appointment_time";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch lab reports for the logged-in patient
$lab_reports_sql = "SELECT * FROM lab_reports WHERE patient_id = ?";
$stmt2 = $conn->prepare($lab_reports_sql);
$stmt2->bind_param("i", $patient_id);
$stmt2->execute();
$lab_reports_result = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="home.php">Patient Dashboard</a>
        <div>
            <a class="btn btn-light me-2" href="profile.php">Profile</a>
            <a class="btn btn-danger" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h3 class="text-center">Your Appointments</h3>

    <!-- Search Form -->
    <form class="d-flex mb-4" action="" method="get">
        <input class="form-control me-2" type="text" name="search" placeholder="Search by Doctor, Date, or Status" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if ($row['status'] == 'Scheduled') { ?>
                            <a href="cancel_appointment.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Cancel</a>
                        <?php } else { echo "N/A"; } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="book_appointment.php" class="btn btn-success">Book New Appointment</a>

    <!-- Lab Reports Section -->
    <h3 class="text-center mt-5">Your Lab Reports</h3>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Report Name</th>
                <th>Uploaded Date</th>
                <th>View</th>
                <th>Download</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($report = $lab_reports_result->fetch_assoc()) { 
                $file_path = "uploads/" . htmlspecialchars($report['file_path']);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($report['file_name']); ?></td>
                    <td><?php echo htmlspecialchars($report['uploaded_at']); ?></td>
                    <td>
                        <a href="<?php echo $file_path; ?>" target="_blank" class="btn btn-info btn-sm">View</a>
                    </td>
                    <td>
                        <a href="download.php?file=<?php echo urlencode($report['file_path']); ?>" class="btn btn-primary btn-sm">Download</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
