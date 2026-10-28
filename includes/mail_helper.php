<?php
// PHPMailer Central Dispatcher - Skill Grow Institution
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

function getSystemSettingsForMail($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM system_settings WHERE id = 1 LIMIT 1");
        return $stmt->fetch() ?: [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Send an email using SMTP or simulated Test Mode
 */
function sendSgiEmail($pdo, $toEmail, $toName, $subject, $htmlBody) {
    $settings = getSystemSettingsForMail($pdo);
    $smtpMode = $settings['smtp_mode'] ?? 'test';

    // If Test Mode or credentials missing: return simulated success
    if ($smtpMode === 'test' || empty($settings['smtp_user']) || empty($settings['smtp_pass'])) {
        return [
            'success' => true,
            'mode'    => 'test',
            'message' => 'Simulated dispatch in Test Mode'
        ];
    }

    // Live Mode: Dispatch via PHPMailer SMTP
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $settings['smtp_host'] ?: 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $settings['smtp_user'];
        $mail->Password   = $settings['smtp_pass'];
        
        if (($settings['smtp_encryption'] ?? 'tls') === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->Port       = (int)($settings['smtp_port'] ?: 587);
        $mail->CharSet    = 'UTF-8';

        // Sender
        $fromEmail = $settings['smtp_from_email'] ?: 'admissions@skillgrowinstitution.in';
        $fromName  = $settings['smtp_from_name'] ?: 'Skill Grow Institution';
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail, $toName ?: $toEmail);
        $mail->addReplyTo($fromEmail, $fromName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $htmlBody));

        $mail->send();
        return [
            'success' => true,
            'mode'    => 'live',
            'message' => 'Email sent successfully via SMTP'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'mode'    => 'live_error',
            'message' => $mail->ErrorInfo ?: $e->getMessage()
        ];
    }
}

/**
 * Send 6-digit OTP verification email template
 */
function sendRegistrationOtpMail($pdo, $toEmail, $toName, $otpCode) {
    $subject = "Your Admission Verification Code: {$otpCode} - Skill Grow Institution";
    $year = date('Y');

    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #070e24; color: #e2e8f0; margin: 0; padding: 20px; }
    .email-container { max-width: 580px; margin: 0 auto; background: #0c1634; border: 1px solid #1a274e; border-radius: 16px; overflow: hidden; }
    .header { background: #002365; padding: 30px; text-align: center; border-bottom: 2px solid #e6a910; }
    .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
    .header p { color: #e6a910; margin: 5px 0 0; font-size: 13px; letter-spacing: 1px; text-transform: uppercase; }
    .content { padding: 35px 30px; text-align: center; }
    .greeting { font-size: 18px; color: #ffffff; margin-bottom: 15px; }
    .otp-badge { display: inline-block; background: #070e24; border: 2px solid #e6a910; color: #e6a910; font-size: 36px; font-weight: 800; letter-spacing: 8px; padding: 16px 36px; border-radius: 12px; margin: 25px 0; }
    .info-text { font-size: 14px; color: #94a3b8; line-height: 1.6; margin-bottom: 20px; }
    .footer { background: #060b1b; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #1a274e; }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="header">
      <h1>Skill Grow Institution</h1>
      <p>Student Admission Verification</p>
    </div>
    <div class="content">
      <div class="greeting">Hello {$toName},</div>
      <p class="info-text">Thank you for starting your admission process with Skill Grow Institution. Please use the verification code below to verify your email address:</p>
      
      <div class="otp-badge">{$otpCode}</div>
      
      <p class="info-text">This code is valid for 10 minutes. Please do not share this security code with anyone.</p>
    </div>
    <div class="footer">
      &copy; {$year} Skill Grow Institution. All Rights Reserved.<br>
      Need assistance? Contact us at info@skillgrowinstitution.in
    </div>
  </div>
</body>
</html>
HTML;

    return sendSgiEmail($pdo, $toEmail, $toName, $subject, $html);
}

/**
 * Send Admission & Payment Confirmation Email with details
 */
function sendAdmissionSuccessEmail($pdo, $studentData) {
    $toEmail = $studentData['email'];
    $toName  = $studentData['full_name'];
    $appId   = $studentData['application_id'];
    $stuId   = $studentData['student_id'];
    $course  = $studentData['course_name'];
    $fee     = number_format($studentData['fee_amount'], 2);
    $payId   = $studentData['razorpay_payment_id'] ?? 'RZP_VERIFIED';
    $year    = date('Y');

    $subject = "Admission Confirmed! Application ID: {$appId} - Skill Grow Institution";

    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #070e24; color: #e2e8f0; margin: 0; padding: 20px; }
    .email-container { max-width: 600px; margin: 0 auto; background: #0c1634; border: 1px solid #1a274e; border-radius: 16px; overflow: hidden; }
    .header { background: #002365; padding: 30px; text-align: center; border-bottom: 2px solid #e6a910; }
    .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
    .header p { color: #e6a910; margin: 5px 0 0; font-size: 13px; letter-spacing: 1px; text-transform: uppercase; }
    .content { padding: 35px 30px; }
    .success-alert { background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; border-radius: 10px; padding: 15px; text-align: center; color: #10b981; font-weight: 700; margin-bottom: 25px; }
    .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
    .detail-table td { padding: 12px; border-bottom: 1px solid #1a274e; font-size: 14px; }
    .detail-table td.label { color: #94a3b8; width: 40%; font-weight: 600; }
    .detail-table td.value { color: #ffffff; font-weight: 700; }
    .footer { background: #060b1b; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #1a274e; }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="header">
      <h1>Skill Grow Institution</h1>
      <p>Admission & Enrollment Confirmation</p>
    </div>
    <div class="content">
      <div class="success-alert">
        🎉 Congratulations! Your admission registration has been successfully confirmed.
      </div>
      
      <p style="color: #cbd5e1; font-size: 14px; line-height: 1.6;">
        Dear <strong>{$toName}</strong>,<br>
        Your registration fee of <strong>₹{$fee}</strong> has been received and verified. Your student records have been created in our academic database.
      </p>

      <table class="detail-table">
        <tr>
          <td class="label">Student ID</td>
          <td class="value" style="color: #e6a910;">{$stuId}</td>
        </tr>
        <tr>
          <td class="label">Application No.</td>
          <td class="value">{$appId}</td>
        </tr>
        <tr>
          <td class="label">Course Program</td>
          <td class="value">{$course}</td>
        </tr>
        <tr>
          <td class="label">Payment ID</td>
          <td class="value">{$payId}</td>
        </tr>
        <tr>
          <td class="label">Registration Fee</td>
          <td class="value" style="color: #10b981;">₹{$fee} (Paid)</td>
        </tr>
      </table>

      <p style="color: #94a3b8; font-size: 13px; line-height: 1.6;">
        You can log in to your student learning portal using your registered email address ({$toEmail}). Your academic counselor will contact you shortly with your batch orientation schedule.
      </p>
    </div>
    <div class="footer">
      &copy; {$year} Skill Grow Institution. Verified Student Credential.<br>
      Admissions Helpline: info@skillgrowinstitution.in
    </div>
  </div>
</body>
</html>
HTML;

    return sendSgiEmail($pdo, $toEmail, $toName, $subject, $html);
}


/**
 * Send Course Enrollment & Tuition Fee Receipt Email
 */
function sendCourseEnrollmentEmail($pdo, $enrollmentData) {
    $toEmail      = $enrollmentData['student_email'];
    $toName       = $enrollmentData['student_name'];
    $enrollmentId = $enrollmentData['enrollment_id'];
    $stuId        = $enrollmentData['student_id'] ?: 'STUDENT';
    $courseName   = $enrollmentData['course_name'];
    $fee          = number_format($enrollmentData['fee_amount'], 2);
    $payId        = $enrollmentData['razorpay_payment_id'] ?? 'RZP_PAID';
    $year         = date('Y');
    $date         = date('d M, Y h:i A');

    $subject = "Course Enrolled: {$courseName} (ID: {$enrollmentId}) - Skill Grow Institution";

    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #070e24; color: #e2e8f0; margin: 0; padding: 20px; }
    .email-container { max-width: 600px; margin: 0 auto; background: #0c1634; border: 1px solid #1a274e; border-radius: 16px; overflow: hidden; }
    .header { background: #002365; padding: 30px; text-align: center; border-bottom: 2px solid #e6a910; }
    .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
    .header p { color: #e6a910; margin: 5px 0 0; font-size: 13px; letter-spacing: 1px; text-transform: uppercase; }
    .content { padding: 35px 30px; }
    .success-alert { background: rgba(230, 169, 16, 0.15); border: 1px solid #e6a910; border-radius: 10px; padding: 15px; text-align: center; color: #e6a910; font-weight: 700; margin-bottom: 25px; }
    .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
    .detail-table td { padding: 12px; border-bottom: 1px solid #1a274e; font-size: 14px; }
    .detail-table td.label { color: #94a3b8; width: 42%; font-weight: 600; }
    .detail-table td.value { color: #ffffff; font-weight: 700; }
    .footer { background: #060b1b; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #1a274e; }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="header">
      <h1>Skill Grow Institution</h1>
      <p>Course Enrollment & Fee Receipt</p>
    </div>
    <div class="content">
      <div class="success-alert">
        🎓 Enrollment Confirmed! Welcome to {$courseName}
      </div>
      
      <p style="color: #cbd5e1; font-size: 14px; line-height: 1.6;">
        Dear <strong>{$toName}</strong>,<br>
        Your enrollment in <strong>{$courseName}</strong> has been successfully processed and confirmed. Your official Course Enrollment ID has been generated below.
      </p>

      <table class="detail-table">
        <tr>
          <td class="label">Course Enrollment ID</td>
          <td class="value" style="color: #e6a910; font-size: 15px; font-family: monospace;">{$enrollmentId}</td>
        </tr>
        <tr>
          <td class="label">Course Program</td>
          <td class="value">{$courseName}</td>
        </tr>
        <tr>
          <td class="label">Student ID</td>
          <td class="value">{$stuId}</td>
        </tr>
        <tr>
          <td class="label">Tuition Fee Paid</td>
          <td class="value" style="color: #10b981;">₹{$fee} (Paid in Full)</td>
        </tr>
        <tr>
          <td class="label">Razorpay Payment ID</td>
          <td class="value" style="font-family: monospace;">{$payId}</td>
        </tr>
        <tr>
          <td class="label">Enrollment Date</td>
          <td class="value">{$date}</td>
        </tr>
      </table>

      <p style="color: #94a3b8; font-size: 13px; line-height: 1.6;">
        You can review your enrolled courses, syllabus materials, and batch timings anytime in your student portal. If you have any questions, our admissions coordinator is here to support you.
      </p>
    </div>
    <div class="footer">
      &copy; {$year} Skill Grow Institution. Verified Student Enrollment.<br>
      Academic Support: info@skillgrowinstitution.in
    </div>
  </div>
</body>
</html>
HTML;

    return sendSgiEmail($pdo, $toEmail, $toName, $subject, $html);
}
