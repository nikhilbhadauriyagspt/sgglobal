<?php
session_start();
include 'admin/includes/config.php';

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$note = $_POST['note'];

$stmt = $conn->prepare("INSERT INTO enrollments (name, email, phone, note) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $note);

if ($stmt->execute()) {
    echo "<script>
            alert('Enrollment successful!');
            window.location.href = 'index.php';
          </script>";
} else {
    echo "<script>
            alert('Something went wrong. Please try again.');
            window.location.href = 'index.php';
          </script>";
}

$stmt->close();
$conn->close();
?>