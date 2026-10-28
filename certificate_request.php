<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'admin/includes/config.php'; // DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $certificate_type = mysqli_real_escape_string($conn, $_POST['certificate_type']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $additional_info = mysqli_real_escape_string($conn, $_POST['additional_info']);

    $sql = "INSERT INTO certificate_requests (full_name, email, course, certificate_type, phone, additional_info) 
            VALUES ('$full_name', '$email', '$course', '$certificate_type', '$phone', '$additional_info')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Request submitted successfully!');
                window.location.href = 'index.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('Invalid request method.');
            window.history.back();
          </script>";
}
?>
