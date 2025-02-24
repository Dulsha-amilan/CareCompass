<?php
session_start();
include 'db/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch staff members with all necessary fields including specialization
$staff_sql = "SELECT id, name, email, role, specialization, created_at, status FROM staff WHERE role != 'admin' ORDER BY created_at DESC";
$staff_result = $conn->query($staff_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff | Care Compass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables@1.10.18/media/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'nav.php'; ?>

    <div class="container">
        <!-- Alert Messages -->
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

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Staff Management</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                    <i class="fas fa-plus me-2"></i>Add New Staff
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="staffTable" class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Specialization</th>
                                <th>Status</th>
                                <th>Join Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($staff = $staff_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $staff['id']; ?></td>
                                <td><?php echo htmlspecialchars($staff['name']); ?></td>
                                <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $staff['role'] == 'doctor' ? 'primary' : 
                                            ($staff['role'] == 'nurse' ? 'success' : 'info'); 
                                    ?>">
                                        <?php echo ucfirst($staff['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($staff['specialization']); ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $staff['status'] == 'active' ? 'success' : 'danger'; 
                                    ?>">
                                        <?php echo ucfirst($staff['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($staff['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" 
                                            onclick="editStaff(<?php echo htmlspecialchars(json_encode($staff)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-<?php echo $staff['status'] == 'active' ? 'warning' : 'success'; ?> btn-sm" 
                                            onclick="toggleStatus(<?php echo $staff['id']; ?>, '<?php echo $staff['status']; ?>')">
                                        <i class="fas fa-<?php echo $staff['status'] == 'active' ? 'ban' : 'check'; ?>"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" 
                                            onclick="deleteStaff(<?php echo $staff['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Staff Modal -->
    <div class="modal fade" id="addStaffModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addStaffForm" action="add_staff.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" id="role_select" onchange="toggleSpecialization()" required>
                                <option value="">Select Role</option>
                                <option value="doctor">Doctor</option>
                                <option value="nurse">Nurse</option>
                                <option value="receptionist">Receptionist</option>
                            </select>
                        </div>
                        <div class="mb-3" id="specialization_div" style="display:none;">
                            <label class="form-label">Specialization</label>
                            <select class="form-select" name="specialization" id="specialization_select">
                                <option value="">Select Specialization</option>
                                <option value="Cardiologist">Cardiologist</option>
                                <option value="Neurologist">Neurologist</option>
                                <option value="Dermatologist">Dermatologist</option>
                                <option value="Orthopedic Surgeon">Orthopedic Surgeon</option>
                                <option value="Pediatrician">Pediatrician</option>
                                <option value="Oncologist">Oncologist</option>
                                <option value="Endocrinologist">Endocrinologist</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Staff</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editStaffModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editStaffForm" action="update_staff.php" method="POST">
                        <input type="hidden" name="staff_id" id="edit_staff_id">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" id="edit_role" onchange="toggleEditSpecialization()" required>
                                <option value="doctor">Doctor</option>
                                <option value="nurse">Nurse</option>
                                <option value="receptionist">Receptionist</option>
                            </select>
                        </div>
                        <div class="mb-3" id="edit_specialization_div">
                            <label class="form-label">Specialization</label>
                            <select class="form-select" name="specialization" id="edit_specialization_select">
                                <option value="">Select Specialization</option>
                                <option value="Cardiologist">Cardiologist</option>
                                <option value="Neurologist">Neurologist</option>
                                <option value="Dermatologist">Dermatologist</option>
                                <option value="Orthopedic Surgeon">Orthopedic Surgeon</option>
                                <option value="Pediatrician">Pediatrician</option>
                                <option value="Oncologist">Oncologist</option>
                                <option value="Endocrinologist">Endocrinologist</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password (leave blank to keep current)</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="new_password" id="new_password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Staff</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#staffTable').DataTable({
                order: [[6, 'desc']]
            });
        });

        function toggleSpecialization() {
            const roleSelect = document.getElementById('role_select');
            const specializationDiv = document.getElementById('specialization_div');
            
            if (roleSelect.value === 'doctor') {
                specializationDiv.style.display = 'block';
                document.getElementById('specialization_select').setAttribute('required', 'required');
            } else {
                specializationDiv.style.display = 'none';
                document.getElementById('specialization_select').removeAttribute('required');
                document.getElementById('specialization_select').value = '';
            }
        }

        function toggleEditSpecialization() {
            const roleSelect = document.getElementById('edit_role');
            const specializationDiv = document.getElementById('edit_specialization_div');
            
            if (roleSelect.value === 'doctor') {
                specializationDiv.style.display = 'block';
            } else {
                specializationDiv.style.display = 'none';
                document.getElementById('edit_specialization_select').value = '';
            }
        }

        function editStaff(staff) {
            document.getElementById('edit_staff_id').value = staff.id;
            document.getElementById('edit_name').value = staff.name;
            document.getElementById('edit_email').value = staff.email;
            document.getElementById('edit_role').value = staff.role;
            document.getElementById('new_password').value = '';
            
            // Handle specialization field
            const specializationDiv = document.getElementById('edit_specialization_div');
            const specializationSelect = document.getElementById('edit_specialization_select');
            
            if (staff.role === 'doctor') {
                specializationDiv.style.display = 'block';
                specializationSelect.value = staff.specialization || '';
            } else {
                specializationDiv.style.display = 'none';
                specializationSelect.value = '';
            }
            
            new bootstrap.Modal(document.getElementById('editStaffModal')).show();
        }

        function deleteStaff(staffId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `delete_staff.php?id=${staffId}`;
                }
            });
        }

        function toggleStatus(staffId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            const action = currentStatus === 'active' ? 'deactivate' : 'activate';
            
            Swal.fire({
                title: `Are you sure you want to ${action} this staff member?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: currentStatus === 'active' ? '#d33' : '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: `Yes, ${action} it!`
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `toggle_staff_status.php?id=${staffId}&status=${newStatus}`;
                }
            });
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
            
            const icon = event.currentTarget.querySelector('i');
            icon.className = `fas fa-${type === 'password' ? 'eye' : 'eye-slash'}`;
        }
    </script>
</body>
</html>