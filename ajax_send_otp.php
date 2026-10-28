<?php
// Send Registration OTP Endpoint
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

$email = trim($_POST['email'] ?? '');
$name  = trim($_POST['name'] ?? 'Prospective Student');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit;
}

// Generate 6-digit OTP
$otp = (string)mt_rand(100000, 999999);
$_SESSION['reg_otp']        = $otp;
$_SESSION['reg_email']      = strtolower($email);
$_SESSION['reg_otp_time']   = time();
$_SESSION['email_verified'] = false;

// Attempt dispatch
$dispatch = sendRegistrationOtpMail($pdo, $email, $name, $otp);

if ($dispatch['mode'] === 'test') {
    echo json_encode([
        'status'   => 'success',
        'mode'     => 'test',
        'demo_otp' => $otp,
        'message'  => "Test Mode Active: Your verification code is {$otp}"
    ]);
} elseif ($dispatch['success']) {
    echo json_encode([
        'status'  => 'success',
        'mode'    => 'live',
        'message' => "Verification code has been dispatched to {$email}"
    ]);
} else {
    // If SMTP fails in live mode, provide fallback test OTP so testing isn't blocked
    echo json_encode([
        'status'   => 'success',
        'mode'     => 'fallback',
        'demo_otp' => $otp,
        'message'  => "SMTP notice: {$dispatch['message']}. Fallback Code: {$otp}"
    ]);
}
