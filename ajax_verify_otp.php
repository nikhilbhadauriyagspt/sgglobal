<?php
// Verify Registration OTP Endpoint
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$email = strtolower(trim($_POST['email'] ?? ''));
$otp   = trim($_POST['otp'] ?? '');

$sessOtp   = $_SESSION['reg_otp'] ?? '';
$sessEmail = $_SESSION['reg_email'] ?? '';
$sessTime  = $_SESSION['reg_otp_time'] ?? 0;

if (empty($otp)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter the 6-digit verification code.']);
    exit;
}

// Check expiry (15 mins)
if ((time() - $sessTime) > 900) {
    echo json_encode(['status' => 'error', 'message' => 'Verification code has expired. Please request a new one.']);
    exit;
}

// Compare OTP
if ($otp === $sessOtp && $email === $sessEmail) {
    $_SESSION['email_verified'] = true;
    echo json_encode(['status' => 'success', 'message' => 'Email verified successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect verification code. Please check and re-enter.']);
}
