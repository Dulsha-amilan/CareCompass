<?php
if (isset($_GET['file'])) {
    $file_name = basename($_GET['file']);
    $file_path = "uploads/" . $file_name;

    if (file_exists($file_path)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        readfile($file_path);
        exit;
    } else {
        die("Error: File not found.");
    }
} else {
    die("Error: Invalid request.");
}
?>
