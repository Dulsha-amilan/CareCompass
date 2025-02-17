<!-- Patients Section -->
<div class="card mb-4">
    <div class="card-header">
        <h4 class="mb-0">Manage Patients</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
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
                    <?php
                    // Update the SQL query to fetch all relevant patient information
                    $patients_sql = "SELECT id, name, email, phone_number, address, date_of_birth FROM users WHERE role = 'patient'";
                    $patients_result = $conn->query($patients_sql);
                    
                    while ($patient = $patients_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $patient['id']; ?></td>
                        <td><?php echo $patient['name']; ?></td>
                        <td><?php echo $patient['email']; ?></td>
                        <td><?php echo $patient['phone_number']; ?></td>
                        <td><?php echo $patient['address']; ?></td>
                        <td><?php echo $patient['date_of_birth']; ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" 
                                    onclick="editPatient(<?php echo htmlspecialchars(json_encode($patient)); ?>)">
                                Edit
                            </button>
                            <a href="delete_patient.php?id=<?php echo $patient['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this patient?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Patient Edit Modal -->
<div class="modal fade" id="editPatientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPatientForm" action="update_patient.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="patient_id" id="edit_patient_id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="edit_patient_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="edit_patient_email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone_number" id="edit_patient_phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" id="edit_patient_address"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" id="edit_patient_dob">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" name="profile_picture" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Patient</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editPatient(patient) {
    document.getElementById('edit_patient_id').value = patient.id;
    document.getElementById('edit_patient_name').value = patient.name;
    document.getElementById('edit_patient_email').value = patient.email;
    document.getElementById('edit_patient_phone').value = patient.phone_number;
    document.getElementById('edit_patient_address').value = patient.address;
    document.getElementById('edit_patient_dob').value = patient.date_of_birth;
    
    new bootstrap.Modal(document.getElementById('editPatientModal')).show();
}
</script>