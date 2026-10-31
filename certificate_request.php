<?php
// Certificate Request Submission & Auto-Notification
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/certificates_helper.php';

ensureCertificatesTable($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $course   = trim($_POST['course'] ?? '');
    $type     = trim($_POST['certificate_type'] ?? 'Certificate of Completion');
    $notes    = trim($_POST['additional_info'] ?? '');
    $studentId= trim($_POST['student_id'] ?? ($_SESSION['student_id'] ?? ''));

    if (empty($fullName) || empty($email) || empty($course)) {
        echo "<script>alert('Please fill all required fields.'); window.history.back();</script>";
        exit;
    }

    try {
        // Check if student has an existing certificate for this course
        $chk = $pdo->prepare("SELECT certificate_number, status FROM certificates WHERE student_email = :em AND course_name = :cn ORDER BY id DESC LIMIT 1");
        $chk->execute(['em' => $email, 'cn' => $course]);
        $existing = $chk->fetch();

        if ($existing) {
            if ($existing['status'] === 'approved') {
                echo "<script>
                        alert('You already have an approved & issued certificate (" . htmlspecialchars($existing['certificate_number']) . ") for this course!');
                        window.location.href = 'view-certificate.php?cert=" . urlencode($existing['certificate_number']) . "';
                      </script>";
                exit;
            } elseif ($existing['status'] === 'pending') {
                echo "<script>
                        alert('Your certificate application (" . htmlspecialchars($existing['certificate_number']) . ") is already under verification by the Academic Council!');
                        window.location.href = 'view-certificate.php?cert=" . urlencode($existing['certificate_number']) . "';
                      </script>";
                exit;
            }
        }

        // Generate unique certificate ID
        $certNumber = generateCertificateNumber($pdo);
        $issueDate  = date('Y-m-d');

        $ins = $pdo->prepare("
            INSERT INTO certificates (certificate_number, student_id, student_name, student_email, course_name, certificate_type, grade, issue_date, status, admin_notes)
            VALUES (:cnum, :sid, :sname, :sem, :cname, :ctype, 'A (Distinction)', :idate, 'pending', :notes)
        ");
        $ins->execute([
            'cnum'  => $certNumber,
            'sid'   => $studentId,
            'sname' => $fullName,
            'sem'   => $email,
            'cname' => $course,
            'ctype' => $type,
            'idate' => $issueDate,
            'notes' => "Submitted online via Certificate Request Form. Phone: {$phone}. Additional: {$notes}"
        ]);

        echo "<script>
                alert('Certificate application submitted successfully! Application ID: " . $certNumber . ". It is now under verification by the administration. Once approved, your official certificate will be generated and dispatched.');
                window.location.href = 'view-certificate.php?cert=" . urlencode($certNumber) . "';
              </script>";
        exit;

    } catch (PDOException $e) {
        echo "<script>alert('Database Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit;
    }
} else {
    header("Location: certificate.php");
    exit;
}
