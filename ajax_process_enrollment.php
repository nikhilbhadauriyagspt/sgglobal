<?php
// Enhanced Process Course Enrollment & Fee Payment Endpoint with Admission Fee Deduction
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/mail_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Read input (Supports either $_POST or JSON payload)
$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true) ?: [];
}

$courseSlug   = trim($input['course_slug'] ?? '');
$courseId     = !empty($input['course_id']) ? (int)$input['course_id'] : 0;
$studentName  = trim($input['student_name'] ?? '');
$studentEmail = strtolower(trim($input['student_email'] ?? ''));
$studentPhone = trim($input['student_phone'] ?? '');
$paymentId    = trim($input['razorpay_payment_id'] ?? '');
$orderId      = trim($input['razorpay_order_id'] ?? ('ORD-' . time()));
$paymentMethod= trim($input['payment_method'] ?? 'Razorpay');

// If student is logged in, prioritize session values
$isLoggedIn = !empty($_SESSION['student_logged_in']);
if ($isLoggedIn) {
    $studentId    = $_SESSION['student_id'] ?? '';
    $studentName  = !empty($studentName) ? $studentName : ($_SESSION['student_name'] ?? '');
    $studentEmail = !empty($studentEmail) ? $studentEmail : ($_SESSION['student_email'] ?? '');
    $studentPhone = !empty($studentPhone) ? $studentPhone : ($_SESSION['student_phone'] ?? '');
} else {
    $studentId    = trim($input['student_id'] ?? '');
}

// Validation: Payment is strictly mandatory!
if (empty($paymentId)) {
    echo json_encode(['status' => 'error', 'message' => 'Payment transaction ID missing. Enrollment cannot be confirmed without verified payment.']);
    exit;
}

if (empty($studentName) || empty($studentEmail) || empty($studentPhone)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide complete student details (Name, Email, Phone).']);
    exit;
}

if (!filter_var($studentEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address provided.']);
    exit;
}

try {
    // 1. Fetch Course from DB
    if ($courseId > 0) {
        $cStmt = $pdo->prepare("SELECT * FROM courses WHERE id = :id AND status = 'active' LIMIT 1");
        $cStmt->execute(['id' => $courseId]);
    } else {
        $cStmt = $pdo->prepare("SELECT * FROM courses WHERE slug = :slug AND status = 'active' LIMIT 1");
        $cStmt->execute(['slug' => $courseSlug]);
    }
    $course = $cStmt->fetch();

    if (!$course) {
        echo json_encode(['status' => 'error', 'message' => 'Selected course is invalid or not active.']);
        exit;
    }

    $originalCourseFee = (float)($course['fee'] ?? 14999.00);
    $courseTitle       = $course['title'];
    $courseRealSlug    = $course['slug'];
    $courseRealId      = (int)$course['id'];

    // 2. Check if this student has a paid admission for this course to DEDUCT fee
    $regFeeDeducted    = 0.00;
    $admissionIdLinked = null;

    $admCheck = $pdo->prepare("
        SELECT a.id, a.application_id, a.student_id, a.fee_amount
        FROM admissions a
        WHERE (a.email = :em OR (a.student_id != '' AND a.student_id = :sid))
          AND a.payment_status = 'paid'
          AND (
            LOWER(TRIM(a.course_name)) = LOWER(TRIM(:cname))
            OR LOWER(REPLACE(a.course_name, ' ', '-')) LIKE LOWER(CONCAT('%', :cslug, '%'))
          )
          AND a.application_id NOT IN (
            SELECT COALESCE(admission_id_linked, '') FROM course_enrollments WHERE payment_status = 'paid' AND admission_id_linked IS NOT NULL
          )
        ORDER BY a.id DESC LIMIT 1
    ");
    $admCheck->execute([
        'em'    => $studentEmail,
        'sid'   => $studentId,
        'cname' => $courseTitle,
        'cslug' => $courseRealSlug
    ]);
    $matchedAdm = $admCheck->fetch();

    if ($matchedAdm) {
        $admissionIdLinked = $matchedAdm['application_id'];
        $regFeeDeducted    = (float)$matchedAdm['fee_amount'];
        if (empty($studentId) && !empty($matchedAdm['student_id'])) {
            $studentId = $matchedAdm['student_id'];
        }
    }

    // Net payable fee
    $finalFeePaid = max(0, $originalCourseFee - $regFeeDeducted);

    // 3. Check/Create Student Account if not existing
    if (empty($studentId)) {
        $sCheck = $pdo->prepare("SELECT student_id FROM students WHERE email = :email LIMIT 1");
        $sCheck->execute(['email' => $studentEmail]);
        $existing = $sCheck->fetch();

        if ($existing && !empty($existing['student_id'])) {
            $studentId = $existing['student_id'];
        } else {
            // Generate unique Student ID
            $studentId = 'STU-' . date('Y') . '-' . mt_rand(1000, 9999);
            $sIns = $pdo->prepare("
                INSERT INTO students (student_id, full_name, email, phone, course_name, status)
                VALUES (:sid, :fn, :em, :ph, :cn, 'active')
            ");
            $sIns->execute([
                'sid' => $studentId,
                'fn'  => $studentName,
                'em'  => $studentEmail,
                'ph'  => $studentPhone,
                'cn'  => $courseTitle
            ]);
        }
    }

    // Always maintain student session
    $_SESSION['student_logged_in'] = true;
    $_SESSION['student_id']        = $studentId;
    $_SESSION['student_name']      = $studentName;
    $_SESSION['student_email']     = $studentEmail;
    $_SESSION['student_phone']     = $studentPhone;

    // 4. Generate unique Enrollment ID for this specific course enrollment
    // Format: ENR-2026-XXXX-XXXX
    $slugCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $courseRealSlug), 0, 4));
    if (strlen($slugCode) < 3) { $slugCode = 'SGI'; }

    $uniqueFound = false;
    $enrollmentId = '';
    $attempts = 0;
    while (!$uniqueFound && $attempts < 10) {
        $attempts++;
        $enrollmentId = 'ENR-' . date('Y') . '-' . $slugCode . '-' . mt_rand(1000, 9999);
        $chkStmt = $pdo->prepare("SELECT id FROM course_enrollments WHERE enrollment_id = :eid LIMIT 1");
        $chkStmt->execute(['eid' => $enrollmentId]);
        if (!$chkStmt->fetch()) {
            $uniqueFound = true;
        }
    }

    // 5. Insert into course_enrollments with linked admission and deduction metadata
    $eStmt = $pdo->prepare("
        INSERT INTO course_enrollments (
            enrollment_id, student_id, course_id, course_name, course_slug,
            student_name, student_email, student_phone, fee_amount,
            original_course_fee, reg_fee_deducted, admission_id_linked,
            payment_status, payment_method, razorpay_payment_id, razorpay_order_id,
            status, enrolled_at
        ) VALUES (
            :eid, :sid, :cid, :cname, :cslug,
            :sname, :semail, :sphone, :fee,
            :orig_fee, :ded_fee, :adm_link,
            'paid', :pmethod, :payid, :orderid,
            'active', NOW()
        )
    ");

    $eStmt->execute([
        'eid'      => $enrollmentId,
        'sid'      => $studentId,
        'cid'      => $courseRealId,
        'cname'    => $courseTitle,
        'cslug'    => $courseRealSlug,
        'sname'    => $studentName,
        'semail'   => $studentEmail,
        'sphone'   => $studentPhone,
        'fee'      => $finalFeePaid,
        'orig_fee' => $originalCourseFee,
        'ded_fee'  => $regFeeDeducted,
        'adm_link' => $admissionIdLinked,
        'pmethod'  => $paymentMethod,
        'payid'    => $paymentId,
        'orderid'  => $orderId
    ]);

    // 6. Send Branded Email Confirmation
    $enrollmentData = [
        'student_email'       => $studentEmail,
        'student_name'        => $studentName,
        'student_id'          => $studentId,
        'enrollment_id'       => $enrollmentId,
        'course_name'         => $courseTitle,
        'fee_amount'          => $finalFeePaid,
        'original_fee'        => $originalCourseFee,
        'reg_deducted'        => $regFeeDeducted,
        'admission_id'        => $admissionIdLinked,
        'razorpay_payment_id' => $paymentId
    ];

    @sendCourseEnrollmentEmail($pdo, $enrollmentData);

    // 7. Return success response
    echo json_encode([
        'status'         => 'success',
        'enrollment_id'  => $enrollmentId,
        'student_id'     => $studentId,
        'course_name'    => $courseTitle,
        'fee_amount'     => $finalFeePaid,
        'reg_deducted'   => $regFeeDeducted,
        'admission_id'   => $admissionIdLinked,
        'payment_id'     => $paymentId,
        'redirect_url'   => 'my-enrollments.php?new=' . urlencode($enrollmentId),
        'message'        => 'Enrollment confirmed! Fee receipt has been emailed to you.'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error while saving enrollment: ' . $e->getMessage()
    ]);
}
