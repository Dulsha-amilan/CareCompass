<?php 
session_start(); 
include 'db/config.php'; 

// Check if admin is logged in 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { 
    header("Location: index.php"); 
    exit(); 
} 

// Fetch appointments with pagination
$items_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

$total_sql = "SELECT COUNT(*) as count FROM appointments";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['count'];
$total_pages = ceil($total_rows / $items_per_page);

// Add search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_condition = '';
if ($search) {
    $search_condition = " WHERE u.email LIKE '%$search%' 
                         OR a.doctor_name LIKE '%$search%' 
                         OR a.status LIKE '%$search%'";
}

// Add sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'appointment_date';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$valid_sorts = ['email', 'doctor_name', 'appointment_date', 'appointment_time', 'status'];
if (!in_array($sort, $valid_sorts)) {
    $sort = 'appointment_date';
}

// Fetch appointments 
$appointments_sql = "SELECT a.id, u.email, a.doctor_name, 
                    a.appointment_date, a.appointment_time, a.status 
                    FROM appointments a 
                    JOIN users u ON a.patient_id = u.id
                    $search_condition
                    ORDER BY $sort $order
                    LIMIT $offset, $items_per_page";
$appointments_result = $conn->query($appointments_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Care Compass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .stats-card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .table-responsive {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
        }
        .action-buttons .btn {
            margin: 0 2px;
        }
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }
        .search-box .form-control {
            padding-left: 40px;
            border-radius: 20px;
        }
        .search-box i {
            position: absolute;
            left: 15px;
            top: 10px;
            color: #666;
        }
        .pagination {
            margin-top: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'nav.php'; ?>

    <div class="dashboard-header">
        <div class="container">
            <h2><i class="fas fa-clipboard-list"></i> Appointment Management</h2>
            <p class="mb-0">Manage and monitor all patient appointments</p>
        </div>
    </div>

    <div class="container">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <?php
            // Fetch quick stats
            $total_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments")->fetch_assoc()['count'];
            $pending_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status='Pending'")->fetch_assoc()['count'];
            $approved_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status='Approved'")->fetch_assoc()['count'];
            ?>
            <div class="col-md-4">
                <div class="stats-card card bg-primary text-white p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Appointments</h6>
                            <h2 class="mb-0"><?php echo $total_appointments; ?></h2>
                        </div>
                        <i class="fas fa-calendar fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card card bg-warning text-white p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Pending</h6>
                            <h2 class="mb-0"><?php echo $pending_appointments; ?></h2>
                        </div>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card card bg-success text-white p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Approved</h6>
                            <h2 class="mb-0"><?php echo $approved_appointments; ?></h2>
                        </div>
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert messages -->
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

        <!-- Search Box -->
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control" id="searchInput" placeholder="Search appointments..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>

        <!-- Appointments Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>
                            <a href="?sort=email&order=<?php echo $sort === 'email' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>" 
                               class="text-white text-decoration-none">
                                Patient Email 
                                <?php if($sort === 'email'): ?>
                                    <i class="fas fa-sort-<?php echo $order === 'ASC' ? 'up' : 'down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?sort=doctor_name&order=<?php echo $sort === 'doctor_name' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>" 
                               class="text-white text-decoration-none">
                                Doctor
                                <?php if($sort === 'doctor_name'): ?>
                                    <i class="fas fa-sort-<?php echo $order === 'ASC' ? 'up' : 'down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?sort=appointment_date&order=<?php echo $sort === 'appointment_date' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>" 
                               class="text-white text-decoration-none">
                                Date
                                <?php if($sort === 'appointment_date'): ?>
                                    <i class="fas fa-sort-<?php echo $order === 'ASC' ? 'up' : 'down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($appointment = $appointments_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['email']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                        <td>
                            <span class="status-badge bg-<?php 
                                echo $appointment['status'] === 'Approved' ? 'success' : 
                                    ($appointment['status'] === 'Pending' ? 'warning' : 'secondary'); 
                            ?>">
                                <?php echo $appointment['status']; ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <?php if ($appointment['status'] !== 'Approved'): ?>
                            <button onclick="approveAppointment(<?php echo $appointment['id']; ?>)" 
                                    class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i>
                            </button>
                            <?php endif; ?>
                            <button onclick="deleteAppointment(<?php echo $appointment['id']; ?>)" 
                                    class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                    </li>
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>

        <a href="admin_logout.php" class="btn btn-secondary mt-3">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.all.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchValue = e.target.value;
            window.location.href = `?search=${searchValue}&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>`;
        });

        // Approve appointment
        function approveAppointment(id) {
            Swal.fire({
                title: 'Confirm Approval',
                text: 'Are you sure you want to approve this appointment?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `update_appointment.php?id=${id}&status=Approved`;
                }
            });
        }

        // Delete appointment
        function deleteAppointment(id) {
            Swal.fire({
                title: 'Confirm Deletion',
                text: 'Are you sure you want to delete this appointment?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `delete_appointment.php?id=${id}`;
                }
            });
        }
    </script>
</body>
</html>