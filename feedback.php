<?php
include 'db/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $message = $_POST['message'];

    $sql = "INSERT INTO feedback (user_id, message) VALUES ('$user_id', '$message')";
    if ($conn->query($sql)) {
        echo "Feedback submitted!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="post">
    <textarea name="message" placeholder="Your feedback" required></textarea>
    <button type="submit">Submit</button>
</form>
