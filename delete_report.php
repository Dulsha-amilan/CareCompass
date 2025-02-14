<?php
session_start();
include 'db/config.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $report_id = $_GET['id'];
    
    // First get the file path
    $select_sql = "SELECT report_file FROM lab_reports WHERE id = ?";
    $stmt = $conn->prepare($select_sql);
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($report = $result->fetch_assoc()) {
        // Delete the file
        if (file_exists($report['report_file'])) {
            unlink($report['report_file']);
        }
        
        // Delete database record
        $delete_sql = "DELETE FROM lab_reports WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $report_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Report deleted successfully";
        } else {
            $_SESSION['error'] = "Error deleting report";
        }
    }
}

header("Location: upload_reports.php");
exit();
?>
//delete_report.php