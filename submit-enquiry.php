<?php
// Enable error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'admin/includes/config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO course_enquiries (name, email, phone, course, message)
            VALUES ('$full_name', '$email', '$phone', '$course', '$message')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
            alert('✅ Enquiry submitted successfully!');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "<script>
            alert('❌ Error: " . mysqli_error($conn) . "');
            window.history.back();
        </script>";
    }

    mysqli_close($conn);
} else {
    echo "<script>
        alert('Invalid request.');
        window.history.back();
    </script>";
}
?>
