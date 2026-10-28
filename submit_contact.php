<?php
session_start();
include 'admin/includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO contact_submissions (name, phone, email, subject, message) 
            VALUES ('$name', '$phone', '$email', '$subject', '$message')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success", "message" => "Message submitted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
