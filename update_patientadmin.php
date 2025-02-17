<?php
session_start();
include 'db/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $date_of_birth = $_POST['date_of_birth'];
    
    // Start with basic update SQL
    $update_sql = "UPDATE users SET 
                   name = ?, 
                   email = ?, 
                   phone_number = ?, 
                   address = ?, 
                   date_of_birth = ?
                   WHERE id = ? AND role = 'patient'";
    
    $params = array($name, $email, $phone_number, $address, $date_of_birth, $patient_id);
    $types = "sssssi";
    
    // Handle profile picture upload if provided
    if (!empty($_FILES['profile_picture']['name'])) {
        $upload_dir = 'uploads/profile_pictures/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $file_name = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $file_name;
        
        // Only allow certain file types
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($file_extension, $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                // Add profile picture to update query
                $update_sql = "UPDATE users SET 
                              name = ?, 
                              email = ?, 
                              phone_number = ?, 
                              address = ?, 
                              date_of_birth = ?,
                              profile_picture = ?
                              WHERE id = ? AND role = 'patient'";
                              
                array_push($params, $upload_path);
                array_push($params, $patient_id);
                $types = "ssssssi";
            }
        }
    }
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Patient updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating patient: " . $conn->error;
    }
    
    $stmt->close();
    header("Location: manage_patients_admin.php");
    exit();
}
?>