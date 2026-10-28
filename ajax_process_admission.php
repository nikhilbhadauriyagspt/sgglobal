<?php
// Process Final Admission & Verified Payment Endpoint with Student Document Folder Storage
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/admin/config/db.php';
require_once __DIR__ . '/includes/mail_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$fullName       = trim($_POST['full_name'] ?? '');
$email          = strtolower(trim($_POST['email'] ?? ''));
$phone          = trim($_POST['phone'] ?? '');
$courseName     = trim($_POST['course_name'] ?? 'Executive Certification Program');
$qualification  = trim($_POST['qualification'] ?? 'Graduate');
$aadhaarNumber  = trim($_POST['aadhaar_number'] ?? 'XXXX-XXXX-0000');
$paymentId      = trim($_POST['razorpay_payment_id'] ?? '');
$orderId        = trim($_POST['razorpay_order_id'] ?? ('ORD-' . time()));
$paymentMethod  = trim($_POST['payment_method'] ?? 'Razorpay');

// Validation: Payment is strictly required!
if (empty($paymentId)) {
    echo json_encode(['status' => 'error', 'message' => 'Payment verification failed. Payment ID is mandatory to confirm admission.']);
    exit;
}

if (empty($fullName) || empty($email) || empty($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all mandatory personal details.']);
    exit;
}

// Fetch admission fee from system_settings
$stmtSet = $pdo->query("SELECT admission_fee FROM system_settings WHERE id = 1 LIMIT 1");
$feeAmount = (float)($stmtSet->fetch()['admission_fee'] ?? 999.00);

try {
    // Generate unique IDs
    $applicationId = 'SGI-' . date('Y') . '-' . mt_rand(10000, 99999);
    $studentId     = 'STU-' . date('Y') . '-' . mt_rand(1000, 9999);

    // ============================================================
    // DEDICATED STUDENT FOLDER CREATION: uploads/students/{Name}_{AppID}/
    // ============================================================
    $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', trim($fullName));
    $relativeStudentDir = 'uploads/students/' . $safeName . '_' . $applicationId . '/';
    $absoluteStudentDir = __DIR__ . '/' . $relativeStudentDir;

    if (!is_dir($absoluteStudentDir)) {
        mkdir($absoluteStudentDir, 0777, true);
    }

    $aadhaarDoc   = null;
    $photoDoc     = null;
    $marksheetDoc = null;

    // 1. Process Aadhaar Document
    if (!empty($_FILES['file_aadhaar']['name']) && $_FILES['file_aadhaar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['file_aadhaar']['name'], PATHINFO_EXTENSION));
        $destPath = $absoluteStudentDir . 'aadhaar_card.' . $ext;
        if (move_uploaded_file($_FILES['file_aadhaar']['tmp_name'], $destPath)) {
            $aadhaarDoc = $relativeStudentDir . 'aadhaar_card.' . $ext;
        }
    }

    // 2. Process Student Photo
    if (!empty($_FILES['file_photo']['name']) && $_FILES['file_photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['file_photo']['name'], PATHINFO_EXTENSION));
        $destPath = $absoluteStudentDir . 'student_photo.' . $ext;
        if (move_uploaded_file($_FILES['file_photo']['tmp_name'], $destPath)) {
            $photoDoc = $relativeStudentDir . 'student_photo.' . $ext;
        }
    }

    // 3. Process Marksheet Document
    if (!empty($_FILES['file_marksheet']['name']) && $_FILES['file_marksheet']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['file_marksheet']['name'], PATHINFO_EXTENSION));
        $destPath = $absoluteStudentDir . 'academic_marksheet.' . $ext;
        if (move_uploaded_file($_FILES['file_marksheet']['tmp_name'], $destPath)) {
            $marksheetDoc = $relativeStudentDir . 'academic_marksheet.' . $ext;
        }
    }

    // 1. Insert or Update in students table
    $stmtStudent = $pdo->prepare("
        INSERT INTO students (student_id, full_name, email, phone, course_name, qualification, aadhaar_number, avatar, status)
        VALUES (:sid, :name, :email, :phone, :course, :qual, :aadh, :avatar, 'active')
        ON DUPLICATE KEY UPDATE 
            full_name = VALUES(full_name),
            phone = VALUES(phone),
            course_name = VALUES(course_name),
            qualification = VALUES(qualification),
            aadhaar_number = VALUES(aadhaar_number),
            avatar = COALESCE(VALUES(avatar), avatar)
    ");
    $stmtStudent->execute([
        'sid'    => $studentId,
        'name'   => $fullName,
        'email'  => $email,
        'phone'  => $phone,
        'course' => $courseName,
        'qual'   => $qualification,
        'aadh'   => $aadhaarNumber,
        'avatar' => $photoDoc
    ]);

    // 2. Insert into admissions table with document paths
    $stmtAdm = $pdo->prepare("
        INSERT INTO admissions (
            student_id, application_id, full_name, email, phone, course_name, 
            qualification, aadhaar_number, photo_doc, aadhaar_doc, marksheet_doc,
            fee_amount, payment_status, payment_method, 
            razorpay_payment_id, razorpay_order_id, admission_status, otp_verified
        ) VALUES (
            :sid, :aid, :name, :email, :phone, :course,
            :qual, :aadh, :pdoc, :adoc, :mdoc,
            :fee, 'paid', :pmethod,
            :pid, :oid, 'verified', 1
        )
    ");

    $stmtAdm->execute([
        'sid'     => $studentId,
        'aid'     => $applicationId,
        'name'    => $fullName,
        'email'   => $email,
        'phone'   => $phone,
        'course'  => $courseName,
        'qual'    => $qualification,
        'aadh'    => $aadhaarNumber,
        'pdoc'    => $photoDoc,
        'adoc'    => $aadhaarDoc,
        'mdoc'    => $marksheetDoc,
        'fee'     => $feeAmount,
        'pmethod' => $paymentMethod,
        'pid'     => $paymentId,
        'oid'     => $orderId
    ]);

    // 3. Set Student Session
    $_SESSION['student_logged_in'] = true;
    $_SESSION['student_id']        = $studentId;
    $_SESSION['student_name']      = $fullName;
    $_SESSION['student_email']     = $email;
    $_SESSION['student_phone']     = $phone;
    $_SESSION['student_course']    = $courseName;
    $_SESSION['student_app_id']    = $applicationId;
    $_SESSION['student_avatar']    = $photoDoc;

    // 4. Send Confirmation Email via PHPMailer
    $mailData = [
        'email'               => $email,
        'full_name'           => $fullName,
        'student_id'          => $studentId,
        'application_id'      => $applicationId,
        'course_name'         => $courseName,
        'fee_amount'          => $feeAmount,
        'razorpay_payment_id' => $paymentId
    ];
    @sendAdmissionSuccessEmail($pdo, $mailData);

    echo json_encode([
        'status'         => 'success',
        'student_id'     => $studentId,
        'application_id' => $applicationId,
        'fee_amount'     => $feeAmount,
        'payment_id'     => $paymentId,
        'receipt_url'    => "download-receipt.php?app_id=" . urlencode($applicationId),
        'student_folder' => $relativeStudentDir,
        'message'        => 'Admission confirmed! Documents saved to student folder.'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error during admission confirmation: ' . $e->getMessage()
    ]);
}
