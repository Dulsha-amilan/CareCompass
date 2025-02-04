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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="home.php">Patient Dashboard</a>
            <a class="btn btn-danger" href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h3 class="text-center">User Profile</h3>
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['date_of_birth']); ?></p>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileLabel" aria-hidden="true">
        <div class="modal-dialog">
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
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JavaScript -->
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
                    }
                });
            });
        });
    </script>
</body>
</html>
