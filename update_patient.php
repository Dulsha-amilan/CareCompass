<?php
// update_patient.php
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
    
    // Start with basic update query
    $update_sql = "UPDATE users SET 
                   name = ?, 
                   email = ?, 
                   phone_number = ?, 
                   address = ?, 
                   date_of_birth = ?
                   WHERE id = ? AND role = 'patient'";
    
    // If a new profile picture is uploaded
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_picture']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $file_name = uniqid() . '_' . $_FILES['profile_picture']['name'];
            $upload_path = 'uploads/profile_pictures/' . $file_name;
            
            // Create directory if it doesn't exist
            if (!file_exists('uploads/profile_pictures')) {
                mkdir('uploads/profile_pictures', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                // Update query to include profile picture
                $update_sql = "UPDATE users SET 
                              name = ?, 
                              email = ?, 
                              phone_number = ?, 
                              address = ?, 
                              date_of_birth = ?,
                              profile_picture = ?
                              WHERE id = ? AND role = 'patient'";
            }
        }
    }
    
    $stmt = $conn->prepare($update_sql);
    
    if (isset($file_name)) {
        // If profile picture was uploaded successfully
        $stmt->bind_param("ssssssi", $name, $email, $phone_number, $address, $date_of_birth, $upload_path, $patient_id);
    } else {
        // Without profile picture update
        $stmt->bind_param("sssssi", $name, $email, $phone_number, $address, $date_of_birth, $patient_id);
    }
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Patient information updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating patient information: " . $conn->error;
    }
    
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}