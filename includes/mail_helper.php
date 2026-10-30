<?php
// PHPMailer Central Dispatcher - Skill Grow Institution
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

require_once __DIR__ . '/settings_helper.php';

function getSystemSettingsForMail($pdo) {
    return getGlobalSettings();
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
    $settings = getSystemSettingsForMail($pdo);
    $siteTitle   = htmlspecialchars($settings['site_title'] ?: 'Skill Grow Institution');
    $sitePhone   = htmlspecialchars($settings['site_phone'] ?: '+91 9217431562');
    $siteEmail   = htmlspecialchars($settings['site_email'] ?: 'info@skillgrowinstitution.in');
    $siteAddress = htmlspecialchars($settings['site_address'] ?: 'Metro Plaza Meerut, 250002, India');
    
    $toEmail = $studentData['email'];
    $toName  = $studentData['full_name'];
    $appId   = $studentData['application_id'];
    $stuId   = $studentData['student_id'];
    $course  = $studentData['course_name'];
    $fee     = number_format((float)$studentData['fee_amount'], 2);
    $payId   = $studentData['razorpay_payment_id'] ?? 'RZP_VERIFIED';
    $year    = date('Y');
    $receiptUrl = "http://localhost:8000/download-receipt.php?app_id=" . urlencode($appId);
    $portalUrl  = "http://localhost:8000/my-enrollments.php";

    $subject = "Admission Confirmed! Application ID: {$appId} - {$siteTitle}";

    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #070e24; color: #e2e8f0; margin: 0; padding: 20px; }
    .email-container { max-width: 620px; margin: 0 auto; background: #0c1634; border: 1px solid #1a274e; border-radius: 16px; overflow: hidden; }
    .header { background: #002365; padding: 30px; text-align: center; border-bottom: 2px solid #e6a910; }
    .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
    .header p { color: #e6a910; margin: 5px 0 0; font-size: 13px; letter-spacing: 1px; text-transform: uppercase; }
    .content { padding: 35px 30px; }
    .success-alert { background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; border-radius: 10px; padding: 15px; text-align: center; color: #10b981; font-weight: 700; margin-bottom: 25px; }
    .deduction-notice { background: rgba(230, 169, 16, 0.1); border-left: 4px solid #e6a910; border-radius: 6px; padding: 12px 16px; margin-bottom: 22px; font-size: 13px; color: #f8fafc; line-height: 1.5; }
    .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
    .detail-table td { padding: 12px; border-bottom: 1px solid #1a274e; font-size: 14px; }
    .detail-table td.label { color: #94a3b8; width: 40%; font-weight: 600; }
    .detail-table td.value { color: #ffffff; font-weight: 700; }
    .btn-action { display: inline-block; background: #e6a910; color: #070e24; text-decoration: none; font-weight: 800; font-size: 14px; padding: 12px 24px; border-radius: 8px; margin: 10px 5px 0 0; }
    .btn-secondary { display: inline-block; background: #1e293b; color: #cbd5e1; text-decoration: none; font-weight: 700; font-size: 14px; padding: 12px 24px; border-radius: 8px; margin: 10px 5px 0 0; border: 1px solid #334155; }
    .footer { background: #060b1b; padding: 22px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #1a274e; line-height: 1.6; }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="header">
      <h1>{$siteTitle}</h1>
      <p>Admission & Seat Booking Confirmation</p>
    </div>
    <div class="content">
      <div class="success-alert">
        🎉 Congratulations! Your admission registration has been successfully confirmed.
      </div>

      <div class="deduction-notice">
        <strong>💡 Fee Deduction Guaranteed:</strong> The registration fee of <strong>₹{$fee}</strong> paid under this application will automatically be deducted from your final tuition fee when you enroll into your full program.
      </div>
      
      <p style="color: #cbd5e1; font-size: 14px; line-height: 1.6;">
        Dear <strong>{$toName}</strong>,<br>
        Your registration fee has been received and verified. Your candidate admission profile has been officially created in our academic database.
      </p>

      <table class="detail-table">
        <tr>
          <td class="label">Permanent Student ID</td>
          <td class="value" style="color: #e6a910; font-family: monospace;">{$stuId}</td>
        </tr>
        <tr>
          <td class="label">Application No.</td>
          <td class="value" style="font-family: monospace;">{$appId}</td>
        </tr>
        <tr>
          <td class="label">Academic Program</td>
          <td class="value">{$course}</td>
        </tr>
        <tr>
          <td class="label">Razorpay Transaction ID</td>
          <td class="value" style="font-family: monospace;">{$payId}</td>
        </tr>
        <tr>
          <td class="label">Registration Fee Paid</td>
          <td class="value" style="color: #10b981;">₹{$fee} (Verified)</td>
        </tr>
      </table>

      <div style="text-align: center; margin: 25px 0 10px;">
        <a href="{$receiptUrl}" class="btn-action" target="_blank">📄 Download Admission Slip (PDF)</a>
        <a href="{$portalUrl}" class="btn-secondary" target="_blank">🎓 Student Dashboard</a>
      </div>
    </div>
    <div class="footer">
      &copy; {$year} {$siteTitle}. Verified Student Credential.<br>
      {$siteAddress} &bull; Helpline: {$sitePhone} &bull; Email: {$siteEmail}
    </div>
  </div>
</body>
</html>
HTML;

    return sendSgiEmail($pdo, $toEmail, $toName, $subject, $html);
}

function sendCourseEnrollmentEmail($pdo, $enrollmentData) {
    $settings = getSystemSettingsForMail($pdo);
    $siteTitle   = htmlspecialchars($settings['site_title'] ?: 'Skill Grow Institution');
    $sitePhone   = htmlspecialchars($settings['site_phone'] ?: '+91 9217431562');
    $siteEmail   = htmlspecialchars($settings['site_email'] ?: 'info@skillgrowinstitution.in');
    $siteAddress = htmlspecialchars($settings['site_address'] ?: 'Metro Plaza Meerut, 250002, India');

    $toEmail      = $enrollmentData['student_email'];
    $toName       = $enrollmentData['student_name'];
    $enrollmentId = $enrollmentData['enrollment_id'];
    $stuId        = $enrollmentData['student_id'] ?: 'STUDENT';
    $courseName   = $enrollmentData['course_name'];
    $netPaid      = (float)$enrollmentData['fee_amount'];
    $origFee      = (float)($enrollmentData['original_fee'] ?? $netPaid);
    $regDeducted  = (float)($enrollmentData['reg_deducted'] ?? 0);
    $admissionId  = $enrollmentData['admission_id'] ?? '';
    $payId        = $enrollmentData['razorpay_payment_id'] ?? 'RZP_PAID';
    $year         = date('Y');
    $date         = date('d M, Y h:i A');

    $receiptUrl = "http://localhost:8000/enrollment-receipt.php?id=" . urlencode($enrollmentId);
    $portalUrl  = "http://localhost:8000/my-enrollments.php";

    $subject = "Course Enrolled: {$courseName} (ID: {$enrollmentId}) - {$siteTitle}";

    $deductionRowsHtml = '';
    if ($regDeducted > 0) {
        $origFeeFmt = number_format($origFee, 2);
        $regDedFmt  = number_format($regDeducted, 2);
        $admLabel   = !empty($admissionId) ? " (Admission Slip: {$admissionId})" : "";
        $deductionRowsHtml = <<<ROW
        <tr>
          <td class="label">Original Course Fee</td>
          <td class="value">₹{$origFeeFmt}</td>
        </tr>
        <tr>
          <td class="label">Less Registration Credit</td>
          <td class="value" style="color: #10b981;">- ₹{$regDedFmt}{$admLabel} [Deducted]</td>
        </tr>
ROW;
    }

    $netPaidFmt = number_format($netPaid, 2);

    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #070e24; color: #e2e8f0; margin: 0; padding: 20px; }
    .email-container { max-width: 620px; margin: 0 auto; background: #0c1634; border: 1px solid #1a274e; border-radius: 16px; overflow: hidden; }
    .header { background: #002365; padding: 30px; text-align: center; border-bottom: 2px solid #e6a910; }
    .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
    .header p { color: #e6a910; margin: 5px 0 0; font-size: 13px; letter-spacing: 1px; text-transform: uppercase; }
    .content { padding: 35px 30px; }
    .success-alert { background: rgba(230, 169, 16, 0.15); border: 1px solid #e6a910; border-radius: 10px; padding: 15px; text-align: center; color: #e6a910; font-weight: 700; margin-bottom: 25px; }
    .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
    .detail-table td { padding: 12px; border-bottom: 1px solid #1a274e; font-size: 14px; }
    .detail-table td.label { color: #94a3b8; width: 44%; font-weight: 600; }
    .detail-table td.value { color: #ffffff; font-weight: 700; }
    .btn-action { display: inline-block; background: #e6a910; color: #070e24; text-decoration: none; font-weight: 800; font-size: 14px; padding: 12px 24px; border-radius: 8px; margin: 10px 5px 0 0; }
    .btn-secondary { display: inline-block; background: #1e293b; color: #cbd5e1; text-decoration: none; font-weight: 700; font-size: 14px; padding: 12px 24px; border-radius: 8px; margin: 10px 5px 0 0; border: 1px solid #334155; }
    .footer { background: #060b1b; padding: 22px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #1a274e; line-height: 1.6; }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="header">
      <h1>{$siteTitle}</h1>
      <p>Course Enrollment & Tuition Fee Receipt</p>
    </div>
    <div class="content">
      <div class="success-alert">
        🎓 Enrollment Confirmed! Welcome to {$courseName}
      </div>
      
      <p style="color: #cbd5e1; font-size: 14px; line-height: 1.6;">
        Dear <strong>{$toName}</strong>,<br>
        Your official enrollment in <strong>{$courseName}</strong> has been successfully processed and verified. Your course credentials and receipt details are summarized below.
      </p>

      <table class="detail-table">
        <tr>
          <td class="label">Enrollment ID</td>
          <td class="value" style="color: #e6a910; font-size: 15px; font-family: monospace;">{$enrollmentId}</td>
        </tr>
        <tr>
          <td class="label">Course Program</td>
          <td class="value">{$courseName}</td>
        </tr>
        <tr>
          <td class="label">Student ID</td>
          <td class="value" style="font-family: monospace;">{$stuId}</td>
        </tr>
        {$deductionRowsHtml}
        <tr>
          <td class="label">Net Tuition Paid</td>
          <td class="value" style="color: #10b981; font-size: 16px;">₹{$netPaidFmt} (Paid in Full)</td>
        </tr>
        <tr>
          <td class="label">Razorpay Transaction ID</td>
          <td class="value" style="font-family: monospace;">{$payId}</td>
        </tr>
        <tr>
          <td class="label">Enrollment Date</td>
          <td class="value">{$date}</td>
        </tr>
      </table>

      <div style="text-align: center; margin: 25px 0 10px;">
        <a href="{$receiptUrl}" class="btn-action" target="_blank">📄 Print / Save Tuition Receipt (PDF)</a>
        <a href="{$portalUrl}" class="btn-secondary" target="_blank">🎓 Access Student Portal</a>
      </div>
    </div>
    <div class="footer">
      &copy; {$year} {$siteTitle}. Verified Student Enrollment.<br>
      {$siteAddress} &bull; Academic Helpline: {$sitePhone} &bull; Support: {$siteEmail}
    </div>
  </div>
</body>
</html>
HTML;

    return sendSgiEmail($pdo, $toEmail, $toName, $subject, $html);
}
