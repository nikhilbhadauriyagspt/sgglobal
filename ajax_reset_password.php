<?php
// Ajax Handler for Student Password Reset with Live SMTP Dispatch
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/admin/config/db.php';
require_once __DIR__ . '/includes/mail_helper.php';

$action = trim($_POST['action'] ?? '');

if ($action === 'send_otp') {
    $identity = trim($_POST['identity'] ?? '');
    if (empty($identity)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter your registered Email or Student ID.']);
        exit;
    }

    // Check if student exists
    $stmt = $pdo->prepare("SELECT * FROM students WHERE email = :id1 OR student_id = :id2 LIMIT 1");
    $stmt->execute(['id1' => $identity, 'id2' => $identity]);
    $student = $stmt->fetch();

    if (!$student) {
        // Also check admissions table as fallback
        $stmtAdm = $pdo->prepare("SELECT * FROM admissions WHERE email = :id1 OR student_id = :id2 LIMIT 1");
        $stmtAdm->execute(['id1' => $identity, 'id2' => $identity]);
        $student = $stmtAdm->fetch();
    }

    if (!$student) {
        echo json_encode(['status' => 'error', 'message' => 'No registered student account found with this Email / ID.']);
        exit;
    }

    $email = $student['email'];
    $name  = $student['full_name'] ?: 'Student';
    $otp   = (string)mt_rand(100000, 999999);

    $_SESSION['reset_otp']        = $otp;
    $_SESSION['reset_email']      = $email;
    $_SESSION['reset_student_id'] = $student['student_id'] ?? '';
    $_SESSION['reset_time']       = time();
    $_SESSION['reset_verified']   = false;

    // Send email using SMTP dispatcher
    $settings  = getSystemSettingsForMail($pdo);
    $siteTitle = $settings['site_title'] ?: 'Skill Grow Institution';
    $subject   = "Password Reset OTP Code - {$siteTitle}";

    $htmlBody = "
    <div style='background-color:#070e24; padding:30px; font-family:Arial, sans-serif;'>
        <div style='max-width:560px; margin:0 auto; background:#0f1537; border:1px solid #1e2c52; border-top:4px solid #e6a910; border-radius:12px; padding:30px; color:#ffffff;'>
            <h2 style='color:#ffffff; margin-top:0; font-family:Georgia,serif;'>{$siteTitle}</h2>
            <p style='color:#cbd5e1; font-size:14px;'>Hello <strong>" . htmlspecialchars($name) . "</strong>,</p>
            <p style='color:#cbd5e1; font-size:14px; line-height:1.6;'>
                You requested a security verification code to reset your Student Portal account password. Please use the following 6-digit OTP passcode:
            </p>
            <div style='text-align:center; margin:25px 0;'>
                <div style='display:inline-block; background:#000c27; border:2px dashed #e6a910; padding:12px 32px; font-size:28px; font-weight:bold; letter-spacing:6px; color:#e6a910; border-radius:8px; font-family:monospace;'>
                    {$otp}
                </div>
            </div>
            <p style='color:#94a3b8; font-size:12px; line-height:1.5;'>
                This passcode will expire in 15 minutes. If you did not initiate this request, you can safely ignore this email.
            </p>
            <div style='border-top:1px solid #1e2c52; margin-top:20px; padding-top:14px; font-size:11px; color:#64748b; text-align:center;'>
                &copy; " . date('Y') . " {$siteTitle}. All rights reserved.
            </div>
        </div>
    </div>";

    $dispatch = sendSgiEmail($pdo, $email, $name, $subject, $htmlBody);

    if ($dispatch['mode'] === 'test') {
        echo json_encode([
            'status'   => 'success',
            'email'    => $email,
            'mode'     => 'test',
            'demo_otp' => $otp,
            'message'  => "Test Mode Active: Your verification code is {$otp}"
        ]);
    } elseif ($dispatch['success']) {
        echo json_encode([
            'status'  => 'success',
            'email'   => $email,
            'mode'    => 'live',
            'message' => "A 6-digit OTP has been dispatched to {$email}"
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Failed to send OTP email: ' . ($dispatch['message'] ?? ($dispatch['error'] ?? 'Please check SMTP settings in Admin.'))
        ]);
    }
    exit;
}

if ($action === 'verify_otp') {
    $code = trim($_POST['otp'] ?? '');
    $sessOtp = $_SESSION['reset_otp'] ?? '';
    $sessTime = $_SESSION['reset_otp_time'] ?? ($_SESSION['reset_time'] ?? 0);

    if (empty($code)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter the 6-digit verification code.']);
        exit;
    }

    if (time() - $sessTime > 900) {
        echo json_encode(['status' => 'error', 'message' => 'OTP has expired. Please request a new code.']);
        exit;
    }

    if ($code !== $sessOtp) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP code. Please enter the correct 6 digits.']);
        exit;
    }

    $_SESSION['reset_verified'] = true;
    echo json_encode(['status' => 'success', 'message' => 'OTP verified successfully!']);
    exit;
}

if ($action === 'reset_password') {
    $newPass = trim($_POST['password'] ?? '');
    $confirmPass = trim($_POST['confirm_password'] ?? '');
    $email = $_SESSION['reset_email'] ?? '';

    if (empty($_SESSION['reset_verified']) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired. Please restart the password reset process.']);
        exit;
    }

    if (strlen($newPass) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters long.']);
        exit;
    }

    if ($newPass !== $confirmPass) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }

    try {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $uStmt = $pdo->prepare("UPDATE students SET password = :p WHERE email = :em OR student_id = :sid");
        $uStmt->execute(['p' => $hash, 'em' => $email, 'sid' => $email]);

        // Clear reset session
        unset($_SESSION['reset_otp'], $_SESSION['reset_email'], $_SESSION['reset_verified'], $_SESSION['reset_time']);

        echo json_encode(['status' => 'success', 'message' => 'Password updated successfully!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
