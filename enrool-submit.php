<?php
include 'admin/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName     = $_POST['full_name'];
    $fatherName   = $_POST['father_name'];
    $dob          = $_POST['dob'];
    $gender       = $_POST['gender'];
    $email        = $_POST['email'];
    $phone        = $_POST['phone'];
    $address      = $_POST['address'];
    $city         = $_POST['city'];
    $state        = $_POST['state'];
    $pinCode      = $_POST['pin_code'];
    $qualification = $_POST['qualification'];
    $course       = $_POST['course'];
    $message      = $_POST['message'];

    try {
        $sql = "INSERT INTO enrollment_submissions
                (full_name, father_name, dob, gender, email, phone, address, city, state, pin_code, qualification, course, message) 
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $fullName, $fatherName, $dob, $gender, $email,
            $phone, $address, $city, $state, $pinCode,
            $qualification, $course, $message
        ]);

        // Success: Show alert and redirect to index.php
        echo "<script>
                alert('Application submitted successfully!');
                window.location.href = 'index.php';
              </script>";
    } catch (PDOException $e) {
        // Error: Show alert and stay on the same page or redirect as needed
        $errorMsg = $e->getMessage();
        echo "<script>
                alert('Error submitting application: " . addslashes($errorMsg) . "');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('Invalid request');
            window.location.href = 'index.php';
          </script>";
}
?>
