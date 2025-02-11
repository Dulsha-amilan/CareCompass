<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 403 Forbidden");
    die("Error: Access denied.");
}

// Include database configuration
require_once 'db/config.php';

// Function to sanitize file path
function sanitizeFilePath($path) {
    // Remove any directory traversal attempts
    $path = str_replace(['..', '\\'], '', $path);
    // Remove any double forward slashes
    return preg_replace('#/+#', '/', $path);
}

// Ensure a file parameter exists
if (!isset($_GET['file']) || empty($_GET['file'])) {
    header("HTTP/1.1 400 Bad Request");
    die("Error: Invalid request.");
}

// Get the file path and sanitize it
$file_path = sanitizeFilePath($_GET['file']);

// Extract the file name from the path
$file_name = basename($file_path);

// Get patient_id from session
$patient_id = $_SESSION['user_id'];

// Verify that this file belongs to the logged-in patient
$stmt = $conn->prepare("SELECT * FROM lab_reports WHERE patient_id = ? AND report_file = ?");
$stmt->bind_param("is", $patient_id, $file_path);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("HTTP/1.1 403 Forbidden");
    die("Error: You don't have permission to access this file.");
}

// Construct the absolute file path
$absolute_path = __DIR__ . '/' . $file_path;

// Verify file exists and is readable
if (!file_exists($absolute_path) || !is_readable($absolute_path)) {
    header("HTTP/1.1 404 Not Found");
    die("Error: File not found or not readable.");
}

// Get file mime type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $absolute_path);
finfo_close($finfo);

// Set headers for file download
header('Content-Description: File Transfer');
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($absolute_path));

// Clear output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Output file
readfile($absolute_path);
exit();