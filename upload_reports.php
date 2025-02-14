<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

// CSRF Token Generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables
$error = null;
$success = null;

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['lab_report'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed!");
    }

    $patient_id = $_POST['patient_id'];
    $report_type = $_POST['report_type'];
    $report_date = $_POST['report_date'];
    $original_file_name = $_FILES['lab_report']['name'];
    $file_tmp = $_FILES['lab_report']['tmp_name'];
    $file_extension = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));
    $allowed_extensions = ['pdf', 'jpg', 'png', 'jpeg'];
    $max_file_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = "uploads/reports/";
    
    // Create upload directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Validate file
    if (!in_array($file_extension, $allowed_extensions)) {
        $error = "Invalid file format! Only PDF, JPG, PNG, and JPEG are allowed.";
    } elseif ($_FILES['lab_report']['size'] > $max_file_size) {
        $error = "File size too large! Maximum size is 5MB.";
    } else {
        $new_file_name = uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $new_file_name;
        
        if (move_uploaded_file($file_tmp, $file_path)) {
            try {
                $insert_sql = "INSERT INTO lab_reports (patient_id, file_name, report_file, report_type, report_date, uploaded_by) 
                              VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("issssi", $patient_id, $original_file_name, $file_path, $report_type, $report_date, $_SESSION['staff_id']);
                
                if ($stmt->execute()) {
                    $success = "Report uploaded successfully!";
                } else {
                    $error = "Error uploading report to database: " . $stmt->error;
                }
                $stmt->close();
            } catch (Exception $e) {
                $error = "Database error: " . $e->getMessage();
                // Delete the uploaded file if database insertion fails
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        } else {
            $error = "Error moving uploaded file!";
        }
    }
}

// Fetch patients with error handling
try {
    $patients_sql = "SELECT id, name FROM users WHERE role = 'Patient' ORDER BY name";
    $patients_result = $conn->query($patients_sql);
    if (!$patients_result) {
        throw new Exception($conn->error);
    }
} catch (Exception $e) {
    $error = "Error fetching patients: " . $e->getMessage();
    $patients_result = false;
}

// Fetch recent uploads with proper error handling
try {
    $recent_uploads_sql = "SELECT 
        lr.id,
        lr.file_name,
        lr.report_file,
        lr.report_type,
        lr.upload_date,
        p.name as patient_name,
        s.name as staff_name
    FROM lab_reports lr
    JOIN users p ON lr.patient_id = p.id
    JOIN users s ON lr.uploaded_by = s.id
    ORDER BY lr.upload_date DESC 
    LIMIT 10";
    
    $recent_uploads = $conn->query($recent_uploads_sql);
    if (!$recent_uploads) {
        throw new Exception($conn->error);
    }
} catch (Exception $e) {
    $error = "Error fetching recent uploads: " . $e->getMessage();
    $recent_uploads = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Lab Reports | Care Compass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .nav-link.active {
            background-color: #0d6efd;
            color: white !important;
            border-radius: 5px;
        }
        .upload-preview {
            max-width: 200px;
            max-height: 200px;
            display: none;
        }
        .recent-uploads {
            max-height: 400px;
            overflow-y: auto;
        }
        .list-group-item {
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .list-group-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="staff_dashboard.php">Care Compass</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="staff_dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_patients.php">Manage Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_appointments.php">Manage Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="upload_reports.php">Upload Lab Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_reports.php">View Lab Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about_us.php">About Us</a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <span class="nav-item">
                        <a class="nav-link text-danger" href="staff_logout.php">Logout</a>
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Upload Form -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Upload Lab Report</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="post" enctype="multipart/form-data" id="uploadForm">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="mb-3">
                                <label for="patient_id" class="form-label">Select Patient</label>
                                <select name="patient_id" id="patient_id" class="form-select" required>
                                    <option value="">Select Patient</option>
                                    <?php if ($patients_result && $patients_result->num_rows > 0): ?>
                                        <?php while ($patient = $patients_result->fetch_assoc()): ?>
                                            <option value="<?php echo htmlspecialchars($patient['id']); ?>">
                                                <?php echo htmlspecialchars($patient['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="report_type" class="form-label">Report Type</label>
                                <select name="report_type" id="report_type" class="form-select" required>
                                    <option value="">Select Report Type</option>
                                    <option value="Blood Test">Blood Test</option>
                                    <option value="X-Ray">X-Ray</option>
                                    <option value="MRI">MRI</option>
                                    <option value="CT Scan">CT Scan</option>
                                    <option value="Ultrasound">Ultrasound</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="report_date" class="form-label">Report Date</label>
                                <input type="date" name="report_date" id="report_date" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="lab_report" class="form-label">Upload Report</label>
                                <input type="file" name="lab_report" id="lab_report" class="form-control" required 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text">Max file size: 5MB. Allowed formats: PDF, JPG, PNG</div>
                                <img id="imagePreview" class="upload-preview mt-2 rounded">
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Uploads -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Recent Uploads</h4>
                    </div>
                    <div class="card-body recent-uploads">
                        <?php if ($recent_uploads && $recent_uploads->num_rows > 0): ?>
                            <div class="list-group">
                                <?php while ($upload = $recent_uploads->fetch_assoc()): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($upload['patient_name']); ?></h6>
                                            <small><?php echo date('M d, Y', strtotime($upload['upload_date'])); ?></small>
                                        </div>
                                        <p class="mb-1">
                                            <strong>Type:</strong> <?php echo htmlspecialchars($upload['report_type']); ?><br>
                                            <strong>File:</strong> <?php echo htmlspecialchars($upload['file_name']); ?>
                                        </p>
                                        <small class="text-muted">Uploaded by: <?php echo htmlspecialchars($upload['staff_name']); ?></small>
                                        <div class="mt-2">
                                            <a href="view_report.php?id=<?php echo urlencode($upload['id']); ?>" 
                                               class="btn btn-sm btn-primary" target="_blank">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="deleteReport(<?php echo $upload['id']; ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No recent uploads found.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview
        document.getElementById('lab_report').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Delete confirmation
        function deleteReport(reportId) {
            if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
                window.location.href = 'delete_report.php?id=' + reportId + '&csrf_token=<?php echo $_SESSION['csrf_token']; ?>';
            }
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    </script>
</body>
</html>