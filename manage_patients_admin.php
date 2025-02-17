<?php 
session_start(); 
include 'db/config.php'; 

// Check if admin is logged in 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { 
    header("Location: index.php"); 
    exit(); 
} 

// Fetch patients with search and pagination
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$where_clause = $search ? "AND (name LIKE ? OR email LIKE ? OR phone_number LIKE ?)" : '';
$count_sql = "SELECT COUNT(*) as total FROM users WHERE role = 'patient' $where_clause";
$stmt = $conn->prepare($count_sql);

if ($search) {
    $search_param = "%$search%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
}

$stmt->execute();
$count_result = $stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

$patients_sql = "SELECT * FROM users WHERE role = 'patient' $where_clause ORDER BY name ASC LIMIT ?, ?";
$stmt = $conn->prepare($patients_sql);

if ($search) {
    $stmt->bind_param("sssii", $search_param, $search_param, $search_param, $offset, $per_page);
} else {
    $stmt->bind_param("ii", $offset, $per_page);
}

$stmt->execute();
$patients_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients | Care Compass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body>
<?php include 'nav.php'; ?>

<div class="container mt-4">
    <h2>Manage Patients</h2>
    
    <!-- Search Form -->
    <form class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search patients...">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <table id="patientsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Date of Birth</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($patient = $patients_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($patient['id']); ?></td>
                <td><?php echo htmlspecialchars($patient['name']); ?></td>
                <td><?php echo htmlspecialchars($patient['email']); ?></td>
                <td><?php echo htmlspecialchars($patient['phone_number']); ?></td>
                <td><?php echo htmlspecialchars($patient['address']); ?></td>
                <td><?php echo htmlspecialchars($patient['date_of_birth']); ?></td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick='editPatient(<?php echo json_encode($patient); ?>)'>Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deletePatient(<?php echo $patient['id']; ?>)">Delete</button>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>
        </ul>
    </nav>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editForm" action="update_patientadmin.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="patient_id" id="editPatientId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name:</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone:</label>
                        <input type="text" name="phone_number" id="editPhone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address:</label>
                        <input type="text" name="address" id="editAddress" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth:</label>
                        <input type="date" name="date_of_birth" id="editDob" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Picture:</label>
                        <input type="file" name="profile_picture" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Required Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let editModal;

document.addEventListener('DOMContentLoaded', function() {
    editModal = new bootstrap.Modal(document.getElementById('editModal'));
});

function editPatient(patient) {
    document.getElementById('editPatientId').value = patient.id;
    document.getElementById('editName').value = patient.name;
    document.getElementById('editEmail').value = patient.email;
    document.getElementById('editPhone').value = patient.phone_number;
    document.getElementById('editAddress').value = patient.address;
    document.getElementById('editDob').value = patient.date_of_birth;
    editModal.show();
}

function deletePatient(id) {
    if (confirm('Are you sure you want to delete this patient?')) {
        window.location.href = 'delete_patient.php?id=' + id;
    }
}
</script>

</body>
</html>