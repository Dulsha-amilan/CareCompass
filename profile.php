<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT name, email, role, address, phone_number, date_of_birth FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
        .profile-header {
            background: linear-gradient(135deg, #0061f2 0%, #6900f2 100%);
            color: white;
            padding: 100px 0 30px;
            margin-bottom: -60px;
        }
        .profile-img-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background-color: #f8f9fa;
            padding: 3px;
        }
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 30px;
        }
        .info-label {
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .info-value {
            color: #2c3e50;
            font-size: 1.1em;
            margin-bottom: 20px;
        }
        .edit-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .edit-button:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }
        .modal-content {
            border-radius: 15px;
        }
        .modal-header {
            background: #007bff;
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .btn-close {
            filter: brightness(0) invert(1);
        }
        .profile-icon {
            font-size: 80px;
            color: #007bff;
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="home.php">Patient Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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

<!-- Profile Header -->
<div class="profile-header">
    <div class="container">
        <div class="text-center">
            <div class="profile-img-container">
                <div class="profile-img d-flex align-items-center justify-content-center">
                    <i class="fas fa-user-circle profile-icon"></i>
                </div>
            </div>
            <h2 class="mt-3"><?php echo htmlspecialchars($user['name']); ?></h2>
            <p class="lead"><?php echo htmlspecialchars($user['role']); ?></p>
        </div>
    </div>
</div>

<!-- Profile Content -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="profile-card">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <div class="info-label">Email</div>
                            <div class="info-value">
                                <i class="fas fa-envelope me-2"></i>
                                <?php echo htmlspecialchars($user['email']); ?>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value">
                                <i class="fas fa-phone me-2"></i>
                                <?php echo htmlspecialchars($user['phone_number']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <div class="info-label">Address</div>
                            <div class="info-value">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <?php echo htmlspecialchars($user['address']); ?>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="info-label">Date of Birth</div>
                            <div class="info-value">
                                <i class="fas fa-calendar me-2"></i>
                                <?php echo htmlspecialchars($user['date_of_birth']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button class="edit-button" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="edit-button">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){
    $("#editProfileForm").submit(function(event){
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "update_profile.php",
            data: $(this).serialize(),
            success: function(response) {
                alert(response);
                location.reload();
            },
            error: function() {
                alert("An error occurred while updating the profile.");
            }
        });
    });
});
</script>

</body>
</html>