<?php
// Modern Interactive Multi-Step Student Admission & Registration - Skill Grow Institution
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/admin/config/db.php';

// Fetch Live System Settings (Fee, Razorpay, SMTP)
$stmt = $pdo->query("SELECT * FROM system_settings WHERE id = 1 LIMIT 1");
$sysSettings = $stmt->fetch() ?: [];

$admissionFee    = (float)($sysSettings['admission_fee'] ?? 999.00);
$razorpayKeyId   = $sysSettings['razorpay_key_id'] ?? '';
$razorpayMode    = $sysSettings['razorpay_mode'] ?? 'test';
$razorpayStatus  = $sysSettings['razorpay_status'] ?? 'active';
$smtpMode        = $sysSettings['smtp_mode'] ?? 'test';

// Fetch active courses from database
$selectedCourseParam = trim($_GET["course"] ?? ($_GET["course_id"] ?? ""));
$dbCourses = $pdo->query("SELECT id, title, slug, duration, fee FROM courses WHERE status = 'active' ORDER BY title ASC")->fetchAll();

// Determine initial selected course title
$initialSelectedCourseTitle = 'Full Stack Web Technology';
if (!empty($dbCourses)) {
    $initialSelectedCourseTitle = $dbCourses[0]['title'];
    foreach ($dbCourses as $dc) {
        if (!empty($selectedCourseParam) && (
            strtolower($selectedCourseParam) === strtolower($dc['slug'] ?? '') ||
            (string)$selectedCourseParam === (string)$dc['id'] ||
            strtolower($selectedCourseParam) === strtolower($dc['title'])
        )) {
            $initialSelectedCourseTitle = $dc['title'];
            break;
        }
    }
}

// Check if student is already logged in for Direct Fast-Track Seat Booking
$isLoggedIn      = !empty($_SESSION['student_logged_in']);
$loggedStudentId = $_SESSION['student_id'] ?? '';
$loggedName      = $_SESSION['student_name'] ?? '';
$loggedEmail     = $_SESSION['student_email'] ?? '';
$loggedPhone     = $_SESSION['student_phone'] ?? '';
$loggedQual      = '12th Standard';
$loggedAadhaar   = '';

if ($isLoggedIn && (!empty($loggedEmail) || !empty($loggedStudentId))) {
    try {
        $stStmt = $pdo->prepare("SELECT * FROM students WHERE email = :em OR (student_id != '' AND student_id = :sid) ORDER BY id DESC LIMIT 1");
        $stStmt->execute(['em' => $loggedEmail, 'sid' => $loggedStudentId]);
        $stRow = $stStmt->fetch();
        if ($stRow) {
            $loggedStudentId = $stRow['student_id'] ?: $loggedStudentId;
            $loggedName      = $stRow['full_name'] ?: $loggedName;
            $loggedEmail     = $stRow['email'] ?: $loggedEmail;
            $loggedPhone     = $stRow['phone'] ?: $loggedPhone;
            $loggedQual      = $stRow['qualification'] ?: $loggedQual;
            $loggedAadhaar   = $stRow['aadhaar_number'] ?: $loggedAadhaar;
        }

        $admStmt = $pdo->prepare("SELECT * FROM admissions WHERE email = :em OR (student_id != '' AND student_id = :sid) ORDER BY id DESC LIMIT 1");
        $admStmt->execute(['em' => $loggedEmail, 'sid' => $loggedStudentId]);
        $admRow = $admStmt->fetch();
        if ($admRow) {
            if (empty($loggedStudentId)) $loggedStudentId = $admRow['student_id'] ?? '';
            if (empty($loggedName))      $loggedName      = $admRow['full_name'] ?? '';
            if (empty($loggedEmail))     $loggedEmail     = $admRow['email'] ?? '';
            if (empty($loggedPhone))     $loggedPhone     = $admRow['phone'] ?? '';
            if (!empty($admRow['qualification']))  $loggedQual    = $admRow['qualification'];
            if (!empty($admRow['aadhaar_number'])) $loggedAadhaar = $admRow['aadhaar_number'];
        }
        $loggedPhone = substr(preg_replace('/\D/', '', $loggedPhone), -10);
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Admission & Enrollment Registration | Skill Grow Institution</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <!-- Premium Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Razorpay Official Checkout SDK -->
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

  <style>
    :root {
      --brand-primary: #e6a910;
      --brand-primary-hover: #cf9507;
      --brand-navy: #002365;
      --brand-dark: #070e24;
      --brand-dark-card: #0b1736;
      --brand-border: #1e2c52;
      --text-muted-dark: #94a3b8;
      --font-heading: 'Roboto Slab', serif;
      --font-body: 'Plus Jakarta Sans', sans-serif;
    }

    * {
      box-sizing: border-box;
      box-shadow: none !important;
    }

    body {
      font-family: var(--font-body);
      background-color: var(--brand-dark);
      color: #e2e8f0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 15px;
      position: relative;
    }

    /* Ambient Background Atmosphere */
    .reg-glow-1 {
      position: absolute;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(230, 169, 16, 0.1) 0%, rgba(0, 35, 101, 0) 70%);
      top: -100px;
      left: -100px;
      pointer-events: none;
      filter: blur(90px);
      z-index: 0;
    }

    .reg-glow-2 {
      position: absolute;
      width: 450px;
      height: 450px;
      background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, rgba(0, 35, 101, 0) 70%);
      bottom: -100px;
      right: -100px;
      pointer-events: none;
      filter: blur(90px);
      z-index: 0;
    }

    /* Main Container */
    .reg-wrapper {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 1140px;
      background: var(--brand-dark-card);
      border: 1px solid var(--brand-border);
      border-radius: 20px;
      overflow: hidden;
    }

    /* Left Stepper Sidebar */
    .reg-visual-panel {
      position: relative;
      background: linear-gradient(180deg, rgba(7, 14, 36, 0.85) 0%, rgba(7, 14, 36, 0.98) 100%),
                  url('assets/images/about/classroom.jpg') no-repeat center center / cover;
      padding: 45px 35px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 100%;
      border-right: 1px solid var(--brand-border);
    }

    .reg-brand-logo {
      height: 58px;
      width: auto;
      object-fit: contain;
    }

    /* Stepper Navigation Visual */
    .stepper-nav {
      list-style: none;
      padding: 0;
      margin: 35px 0;
      position: relative;
    }

    .stepper-nav::before {
      content: '';
      position: absolute;
      left: 19px;
      top: 15px;
      bottom: 25px;
      width: 2px;
      background: var(--brand-border);
      z-index: 1;
    }

    .stepper-item {
      display: flex;
      align-items: flex-start;
      margin-bottom: 28px;
      position: relative;
      z-index: 2;
    }

    .stepper-item:last-child {
      margin-bottom: 0;
    }

    .stepper-icon-bubble {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #070e24;
      border: 2px solid var(--brand-border);
      color: #64748b;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 0.9rem;
      margin-right: 15px;
      flex-shrink: 0;
      transition: all 0.3s ease;
    }

    .stepper-item.active-step .stepper-icon-bubble {
      background: var(--brand-primary);
      border-color: var(--brand-primary);
      color: #070e24;
    }

    .stepper-item.completed-step .stepper-icon-bubble {
      background: #10b981;
      border-color: #10b981;
      color: #ffffff;
    }

    .stepper-content h4 {
      font-family: var(--font-heading);
      font-size: 1rem;
      font-weight: 700;
      color: #cbd5e1;
      margin: 0 0 3px;
      transition: color 0.3s;
    }

    .stepper-item.active-step .stepper-content h4 {
      color: #ffffff;
    }

    .stepper-content p {
      font-size: 0.78rem;
      color: #64748b;
      margin: 0;
    }

    /* Right Form Area */
    .reg-form-panel {
      padding: 45px 40px;
      background: var(--brand-dark-card);
    }

    .form-step-screen {
      display: none;
      animation: fadeInStep 0.35s ease forwards;
    }

    .form-step-screen.active {
      display: block;
    }

    @keyframes fadeInStep {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .step-header-box {
      margin-bottom: 24px;
      border-bottom: 1px solid var(--brand-border);
      padding-bottom: 16px;
    }

    .step-badge {
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: var(--brand-primary);
      background: rgba(230, 169, 16, 0.12);
      border: 1px solid rgba(230, 169, 16, 0.3);
      padding: 3px 10px;
      border-radius: 50px;
      display: inline-block;
      margin-bottom: 8px;
    }

    .step-header-box h3 {
      font-family: var(--font-heading);
      font-size: 1.65rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0 0 6px;
    }

    .step-header-box p {
      color: var(--text-muted-dark);
      font-size: 0.88rem;
      margin: 0;
    }

    /* Form Fields */
    .form-group-item {
      margin-bottom: 16px;
    }

    .form-group-item label {
      display: block;
      font-size: 0.82rem;
      font-weight: 600;
      color: #cbd5e1;
      margin-bottom: 6px;
    }

    .form-group-item label span {
      color: #ef4444;
    }

    .input-icon-box {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-icon-box i {
      position: absolute;
      left: 14px;
      color: #64748b;
      font-size: 0.95rem;
      pointer-events: none;
    }

    .input-icon-box .form-control, 
    .input-icon-box .form-select {
      background: rgba(7, 14, 36, 0.65);
      border: 1px solid var(--brand-border);
      border-radius: 8px;
      padding: 11px 14px 11px 40px;
      color: #ffffff;
      font-size: 0.9rem;
      width: 100%;
      outline: none;
      transition: all 0.2s ease;
    }

    .input-icon-box .form-control:focus, 
    .input-icon-box .form-select:focus {
      background: rgba(7, 14, 36, 0.95);
      border-color: var(--brand-primary);
      color: #ffffff;
    }

    .input-icon-box .form-select option {
      background: #070e24;
      color: #ffffff;
    }

    /* Inline OTP Box */
    .otp-inline-container {
      background: rgba(7, 14, 36, 0.8);
      border: 1px dashed var(--brand-border);
      border-radius: 10px;
      padding: 14px 16px;
      margin-top: 10px;
      display: none;
    }

    .btn-send-otp {
      background: rgba(230, 169, 16, 0.15);
      border: 1px solid var(--brand-primary);
      color: var(--brand-primary);
      font-size: 0.82rem;
      font-weight: 700;
      padding: 10px 14px;
      border-radius: 8px;
      cursor: pointer;
      white-space: nowrap;
      transition: all 0.2s;
    }

    .btn-send-otp:hover {
      background: var(--brand-primary);
      color: #070e24;
    }

    .btn-verify-otp {
      background: #10b981;
      border: none;
      color: #ffffff;
      font-size: 0.85rem;
      font-weight: 700;
      padding: 8px 16px;
      border-radius: 6px;
      cursor: pointer;
    }

    /* Documents Upload Box */
    .doc-upload-box {
      background: rgba(7, 14, 36, 0.5);
      border: 1.5px dashed var(--brand-border);
      border-radius: 10px;
      padding: 18px;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s ease;
      position: relative;
    }

    .doc-upload-box:hover {
      border-color: var(--brand-primary);
      background: rgba(230, 169, 16, 0.03);
    }

    .doc-upload-box i {
      font-size: 1.6rem;
      color: var(--brand-primary);
      margin-bottom: 8px;
    }

    .doc-upload-box .title {
      font-size: 0.86rem;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 3px;
    }

    .doc-upload-box .desc {
      font-size: 0.75rem;
      color: #64748b;
    }

    .doc-upload-box input[type="file"] {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
    }

    .uploaded-file-name {
      font-size: 0.78rem;
      color: #10b981;
      font-weight: 600;
      margin-top: 6px;
      display: none;
    }

    /* Step 3: Fee Summary */
    .fee-summary-table {
      background: rgba(7, 14, 36, 0.6);
      border: 1px solid var(--brand-border);
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
    }

    .fee-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      font-size: 0.9rem;
    }

    .fee-row:last-child {
      border-bottom: none;
      padding-top: 14px;
      margin-top: 4px;
      border-top: 1.5px solid var(--brand-border);
    }

    .fee-row span:first-child {
      color: #94a3b8;
    }

    .fee-row .fee-amount {
      font-family: var(--font-heading);
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--brand-primary);
    }

    /* Razorpay Box */
    .razorpay-secure-badge {
      background: rgba(0, 35, 101, 0.3);
      border: 1px solid rgba(230, 169, 16, 0.3);
      border-radius: 10px;
      padding: 12px 16px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .razorpay-logo-strip {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .razorpay-logo-strip span {
      font-size: 0.85rem;
      font-weight: 700;
      color: #ffffff;
    }

    /* Action Buttons */
    .step-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      margin-top: 24px;
      padding-top: 18px;
      border-top: 1px solid var(--brand-border);
    }

    .btn-step-next {
      background: linear-gradient(135deg, #f59e0b, #e6a910);
      color: #070e24;
      font-weight: 700;
      font-size: 0.95rem;
      padding: 12px 28px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
    }

    .btn-step-next:hover {
      background: #fbbf24;
      transform: translateY(-1px);
    }

    .btn-step-prev {
      background: transparent;
      border: 1px solid var(--brand-border);
      color: #94a3b8;
      font-weight: 600;
      font-size: 0.95rem;
      padding: 12px 22px;
      border-radius: 8px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
    }

    .btn-step-prev:hover {
      border-color: rgba(255, 255, 255, 0.3);
      color: #ffffff;
    }

    /* Step 4: Success Screen */
    .success-celebration-box {
      text-align: center;
      padding: 40px 20px;
    }

    .success-icon-bubble {
      width: 76px;
      height: 76px;
      border-radius: 50%;
      background: rgba(16, 185, 129, 0.15);
      border: 2px solid #10b981;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 2.2rem;
      color: #10b981;
      margin-bottom: 20px;
    }

    .success-celebration-box h2 {
      font-family: var(--font-heading);
      font-size: 2rem;
      color: #ffffff;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .success-celebration-box p {
      color: #94a3b8;
      font-size: 0.98rem;
      max-width: 500px;
      margin: 0 auto 24px;
    }

    .application-receipt-card {
      background: rgba(7, 14, 36, 0.6);
      border: 1px solid var(--brand-border);
      border-radius: 12px;
      padding: 20px;
      max-width: 480px;
      margin: 0 auto 28px;
      text-align: left;
    }

    .receipt-item {
      display: flex;
      justify-content: space-between;
      padding: 6px 0;
      font-size: 0.88rem;
    }

    .receipt-item span:first-child {
      color: #94a3b8;
    }

    .receipt-item span:last-child {
      color: #ffffff;
      font-weight: 600;
    }

    @media (max-width: 991px) {
      .reg-visual-panel {
        display: none;
      }
      .reg-form-panel {
        padding: 30px 20px;
      }
    }
  </style>
</head>

<body>

  <div class="reg-glow-1"></div>
  <div class="reg-glow-2"></div>

  <div class="reg-wrapper">
    <div class="row g-0">
      
      <!-- Left Visual Panel with Step Progress -->
      <div class="col-lg-4 d-none d-lg-block">
        <div class="reg-visual-panel">
          <div>
            <a href="index.php">
              <img src="assets/images/logo/logopng.png" alt="Skill Grow Logo" class="reg-brand-logo">
            </a>
          </div>

          <div>
            <ul class="stepper-nav">
              <li class="stepper-item <?= $isLoggedIn ? 'completed-step' : 'active-step' ?>" id="navStep1">
                <div class="stepper-icon-bubble">1</div>
                <div class="stepper-content">
                  <h4>Personal & Email OTP</h4>
                  <p>Candidate details & verification</p>
                </div>
              </li>
              <li class="stepper-item <?= $isLoggedIn ? 'completed-step' : '' ?>" id="navStep2">
                <div class="stepper-icon-bubble">2</div>
                <div class="stepper-content">
                  <h4>Aadhaar & Documents</h4>
                  <p>Identity proof & qualification</p>
                </div>
              </li>
              <li class="stepper-item <?= $isLoggedIn ? 'active-step' : '' ?>" id="navStep3">
                <div class="stepper-icon-bubble">3</div>
                <div class="stepper-content">
                  <h4>Razorpay Checkout</h4>
                  <p>Registration fee payment</p>
                </div>
              </li>
              <li class="stepper-item" id="navStep4">
                <div class="stepper-icon-bubble"><i class="fas fa-check"></i></div>
                <div class="stepper-content">
                  <h4>Admission Slip</h4>
                  <p>Student ID & confirmation</p>
                </div>
              </li>
            </ul>
          </div>

          <div style="font-size: 0.8rem; color: #64748b;">
            <i class="fas fa-lock text-warning me-1"></i> 256-Bit SSL Encrypted Admission System
          </div>
        </div>
      </div>

      <!-- Right Multi-Step Form Container -->
      <div class="col-lg-8">
        <div class="reg-form-panel">
          
          <form id="admissionMasterForm">
            
            <!-- ================= STEP 1: PERSONAL DETAILS & EMAIL OTP ================= -->
            <div class="form-step-screen <?= !$isLoggedIn ? 'active' : '' ?>" id="stepScreen1">
              
              <div class="step-header-box">
                <span class="step-badge"><i class="fas fa-user-edit me-1"></i> Step 1 of 3</span>
                <h3>Personal Information & Email Verification</h3>
                <p>Fill in candidate personal details and verify email address via OTP.</p>
              </div>

              <div class="row g-3">
                <!-- Full Name -->
                <div class="col-md-6">
                  <div class="form-group-item">
                    <label>Candidate Full Name <span>*</span></label>
                    <div class="input-icon-box">
                      <i class="fas fa-user"></i>
                      <input type="text" class="form-control" id="candName" value="<?= htmlspecialchars($loggedName) ?>" placeholder="As per official ID" required>
                    </div>
                  </div>
                </div>

                <!-- Father's Name -->
                <div class="col-md-6">
                  <div class="form-group-item">
                    <label>Father's / Guardian's Name <span>*</span></label>
                    <div class="input-icon-box">
                      <i class="fas fa-user-friends"></i>
                      <input type="text" class="form-control" id="fatherName" placeholder="Enter father's name" required>
                    </div>
                  </div>
                </div>

                <!-- Phone -->
                <div class="col-md-6">
                  <div class="form-group-item">
                    <label>Phone / WhatsApp No. <span>*</span></label>
                    <div class="input-icon-box">
                      <i class="fas fa-phone"></i>
                      <input type="tel" class="form-control" id="phone" value="<?= htmlspecialchars($loggedPhone) ?>" placeholder="10-digit mobile number" maxlength="10" minlength="10" pattern="[0-9]{10}" inputmode="numeric" autocomplete="tel" required>
                    </div>
                    <small id="phoneErrorHint" class="text-danger mt-1" style="display: none; font-size: 0.76rem; font-weight: 600;">
                      <i class="fas fa-exclamation-circle me-1"></i> Phone number must be exactly 10 digits.
                    </small>
                  </div>
                </div>

                <!-- Selected Course -->
                <div class="col-md-6">
                  <div class="form-group-item">
                    <label>Select Course Program <span>*</span></label>
                    <div class="input-icon-box">
                      <i class="fas fa-graduation-cap"></i>
                      <select class="form-select" id="course" onchange="syncCourseSelection(this.value)" required>
                        <?php if (!empty($dbCourses)): ?>
                          <?php 
                          $matchFound = false;
                          foreach ($dbCourses as $dc) {
                              if (!empty($selectedCourseParam) && (strtolower($selectedCourseParam) === strtolower($dc['slug'] ?? '') || (string)$selectedCourseParam === (string)$dc['id'] || strtolower($selectedCourseParam) === strtolower($dc['title']))) {
                                  $matchFound = true;
                                  break;
                              }
                          }
                          foreach ($dbCourses as $idx => $dc): 
                              $isSelected = false;
                              if (!empty($selectedCourseParam) && (strtolower($selectedCourseParam) === strtolower($dc['slug'] ?? '') || (string)$selectedCourseParam === (string)$dc['id'] || strtolower($selectedCourseParam) === strtolower($dc['title']))) {
                                  $isSelected = true;
                              } elseif (!$matchFound && $idx === 0) {
                                  $isSelected = true;
                              }
                          ?>
                            <option value="<?= htmlspecialchars($dc['title']) ?>" <?= $isSelected ? 'selected' : '' ?>>
                              <?= htmlspecialchars($dc['title']) ?> (<?= htmlspecialchars($dc['duration']) ?>)
                            </option>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <option value="Full Stack Web Technology" selected>Full Stack Web Technology (6 Months)</option>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Email with Send OTP Button -->
                <div class="col-12">
                  <div class="form-group-item">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <label class="mb-0">Candidate Email Address <span>*</span></label>
                      <span id="emailVerifiedBadge" class="badge bg-success" style="display: <?= $isLoggedIn ? 'inline-block' : 'none' ?>;">
                        <i class="fas fa-check-circle me-1"></i> Email Verified
                      </span>
                    </div>

                    <div class="d-flex gap-2">
                      <div class="input-icon-box flex-grow-1">
                        <i class="fas fa-envelope"></i>
                        <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($loggedEmail) ?>" placeholder="student@example.com" <?= $isLoggedIn ? 'readonly' : '' ?> required>
                      </div>
                      <button type="button" class="btn-send-otp" id="btnSendOtp" onclick="triggerSendOtp()" style="display: <?= $isLoggedIn ? 'none' : 'inline-flex' ?>;">
                        <i class="fas fa-paper-plane me-1"></i> Send OTP
                      </button>
                    </div>

                    <!-- Inline OTP Verification Box -->
                    <div class="otp-inline-container" id="otpVerifyBox">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size: 0.82rem; color: #cbd5e1;">
                          <i class="fas fa-key text-warning me-1"></i> Enter 6-digit OTP sent to your email:
                        </span>
                        <small id="otpStatusNotice" class="text-warning" style="font-size: 0.78rem;"></small>
                      </div>
                      <div class="d-flex gap-2">
                        <input type="text" maxlength="6" class="form-control text-center font-monospace fw-bold" id="inputOtpCode" placeholder="••••••" style="max-width: 160px; font-size: 1.1rem; letter-spacing: 4px;">
                        <button type="button" class="btn-verify-otp" id="btnVerifyOtp" onclick="triggerVerifyOtp()">
                          <i class="fas fa-check-circle me-1"></i> Verify Code
                        </button>
                      </div>
                    </div>

                  </div>
                </div>

                <!-- Highest Qualification -->
                <div class="col-md-6">
                  <div class="form-group-item">
                    <label>Highest Qualification <span>*</span></label>
                    <div class="input-icon-box">
                      <i class="fas fa-award"></i>
                      <select class="form-select" id="qualification" required>
                        <option value="12th Standard" selected>12th Standard (Higher Secondary)</option>
                        <option value="Bachelor's Degree">Bachelor's Degree (Graduation)</option>
                        <option value="Master's Degree">Master's Degree (Post Graduation)</option>
                        <option value="Diploma / Polytechnic">Diploma / Polytechnic</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- City -->
                <div class="col-md-6">
                  <div class="form-group-item">
                    <label>City & State <span>*</span></label>
                    <div class="input-icon-box">
                      <i class="fas fa-map-marker-alt"></i>
                      <input type="text" class="form-control" id="cityState" placeholder="e.g. New Delhi, Delhi" required>
                    </div>
                  </div>
                </div>

                <!-- Create Student Portal Password -->
                <div class="col-md-6" id="fieldPasswordWrap" style="<?= $isLoggedIn ? 'display:none;' : '' ?>">
                  <div class="form-group-item">
                    <label>Create Portal Password <span>*</span></label>
                    <div class="input-icon-box position-relative">
                      <i class="fas fa-lock"></i>
                      <input type="password" class="form-control" id="portalPassword" placeholder="Min. 6 characters" autocomplete="new-password" style="padding-right: 40px;">
                      <button type="button" class="btn btn-sm text-muted position-absolute end-0 top-50 translate-middle-y me-2 border-0 bg-transparent" onclick="toggleRegPassword('portalPassword', this)" tabindex="-1" style="z-index: 5;">
                        <i class="fas fa-eye"></i>
                      </button>
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">Password to access your student portal dashboard.</small>
                  </div>
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6" id="fieldConfirmPasswordWrap" style="<?= $isLoggedIn ? 'display:none;' : '' ?>">
                  <div class="form-group-item">
                    <label>Confirm Password <span>*</span></label>
                    <div class="input-icon-box position-relative">
                      <i class="fas fa-shield-alt"></i>
                      <input type="password" class="form-control" id="confirmPassword" placeholder="Re-enter your password" autocomplete="new-password" style="padding-right: 40px;">
                      <button type="button" class="btn btn-sm text-muted position-absolute end-0 top-50 translate-middle-y me-2 border-0 bg-transparent" onclick="toggleRegPassword('confirmPassword', this)" tabindex="-1" style="z-index: 5;">
                        <i class="fas fa-eye"></i>
                      </button>
                    </div>
                    <small id="regPassMatchHint" class="text-muted" style="font-size: 0.75rem;">Must match the portal password.</small>
                  </div>
                </div>

              </div>

              <!-- Step 1 Actions -->
              <div class="step-actions">
                <a href="login.php" class="btn-step-prev" style="text-decoration: none;">
                  <i class="fas fa-user"></i> Already Registered?
                </a>
                <button type="button" class="btn-step-next" onclick="validateStep1AndProceed()">
                  Next: Documents <i class="fas fa-arrow-right"></i>
                </button>
              </div>

            </div>


            <!-- ================= STEP 2: DOCUMENTS & AADHAAR ================= -->
            <div class="form-step-screen" id="stepScreen2">
              
              <div class="step-header-box">
                <span class="step-badge"><i class="fas fa-id-card me-1"></i> Step 2 of 3</span>
                <h3>Identity Verification & Document Upload</h3>
                <p>Provide your government ID details and academic documents.</p>
              </div>

              <!-- 12-Digit Aadhaar Input -->
              <div class="form-group-item mb-4">
                <label>12-Digit Aadhaar Number <span>*</span></label>
                <div class="input-icon-box">
                  <i class="fas fa-fingerprint"></i>
                  <input type="text" class="form-control font-monospace" id="aadhaarNum" value="<?= htmlspecialchars($loggedAadhaar) ?>" placeholder="XXXX - XXXX - XXXX" maxlength="19" required>
                </div>
                <small class="text-muted" style="font-size: 0.75rem;">Government registered identity number is required for official certificate issuing.</small>
              </div>

              <div class="row g-3">
                <!-- Aadhaar Front/Back -->
                <div class="col-md-6">
                  <div class="doc-upload-box">
                    <i class="fas fa-id-badge"></i>
                    <div class="title">Upload Aadhaar Card (PDF / Image)</div>
                    <div class="desc">Front & back clear photo (Max 5MB)</div>
                    <div class="uploaded-file-name" id="nameDocAadhaar"></div>
                    <input type="file" id="fileAadhaar" accept="image/*,application/pdf" onchange="showUploadedName(this, 'nameDocAadhaar')">
                  </div>
                </div>

                <!-- Passport Photo -->
                <div class="col-md-6">
                  <div class="doc-upload-box">
                    <i class="fas fa-user-circle"></i>
                    <div class="title">Upload Student Photo</div>
                    <div class="desc">Recent passport size photo (Max 2MB)</div>
                    <div class="uploaded-file-name" id="nameDocPhoto"></div>
                    <input type="file" id="filePhoto" accept="image/*" onchange="showUploadedName(this, 'nameDocPhoto')">
                  </div>
                </div>

                <!-- Marksheet -->
                <div class="col-12">
                  <div class="doc-upload-box">
                    <i class="fas fa-file-invoice"></i>
                    <div class="title">Upload 10th / 12th / Graduation Marksheet</div>
                    <div class="desc">Academic qualification proof for enrollment verification</div>
                    <div class="uploaded-file-name" id="nameDocMarksheet"></div>
                    <input type="file" id="fileMarksheet" accept="image/*,application/pdf" onchange="showUploadedName(this, 'nameDocMarksheet')">
                  </div>
                </div>
              </div>

              <!-- Step 2 Actions -->
              <div class="step-actions">
                <button type="button" class="btn-step-prev" onclick="goToStep(1)">
                  <i class="fas fa-arrow-left"></i> Back to Step 1
                </button>
                <button type="button" class="btn-step-next" onclick="validateStep2AndProceed()">
                  Next: Payment <i class="fas fa-arrow-right"></i>
                </button>
              </div>

            </div>


            <!-- ================= STEP 3: MANDATORY RAZORPAY PAYMENT ================= -->
            <div class="form-step-screen <?= $isLoggedIn ? 'active' : '' ?>" id="stepScreen3">
              
              <div class="step-header-box">
                <span class="step-badge">
                  <i class="fas <?= $isLoggedIn ? 'fa-bolt' : 'fa-credit-card' ?> me-1"></i>
                  <?= $isLoggedIn ? 'Direct Seat Booking Checkout (Logged-In Student)' : 'Step 3 of 3 (Mandatory)' ?>
                </span>
                <h3><?= $isLoggedIn ? 'Confirm Course & Pay Seat Booking Fee' : 'Admission Fee Checkout' ?></h3>
                <p>
                  <?= $isLoggedIn
                      ? 'Aap pehle se logged-in hain! Neeche apni verified details aur course check karein aur seedha seat book karein.'
                      : 'Payment is strictly required to confirm your student admission and batch allotment.' ?>
                </p>
              </div>

              <?php if ($isLoggedIn): ?>
                <!-- Logged-In Student Verified Details & Course Selector Box -->
                <div style="background: rgba(16, 185, 129, 0.07); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 14px; padding: 18px 20px; margin-bottom: 20px;">
                  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <div class="d-flex align-items-center gap-2">
                      <span class="badge bg-success"><i class="fas fa-user-check me-1"></i> Verified Student Profile</span>
                      <?php if (!empty($loggedStudentId)): ?>
                        <span class="badge bg-dark border border-secondary font-monospace text-warning"><?= htmlspecialchars($loggedStudentId) ?></span>
                      <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-light" style="font-size: 0.75rem; padding: 3px 10px; border-radius: 6px;" onclick="goToStep(1)">
                      <i class="fas fa-pen me-1"></i> Edit Personal / Doc Details
                    </button>
                  </div>

                  <div class="row g-2 mb-3" style="font-size: 0.86rem;">
                    <div class="col-sm-6">
                      <span class="text-muted d-block" style="font-size: 0.74rem;">Candidate Name</span>
                      <strong class="text-white"><?= htmlspecialchars($loggedName) ?></strong>
                    </div>
                    <div class="col-sm-6">
                      <span class="text-muted d-block" style="font-size: 0.74rem;">Registered Email</span>
                      <strong class="text-white"><?= htmlspecialchars($loggedEmail) ?></strong>
                    </div>
                    <div class="col-sm-6">
                      <span class="text-muted d-block" style="font-size: 0.74rem;">Phone / WhatsApp No.</span>
                      <strong class="text-white font-monospace"><?= htmlspecialchars($loggedPhone) ?></strong>
                    </div>
                    <div class="col-sm-6">
                      <span class="text-muted d-block" style="font-size: 0.74rem;">Qualification & ID</span>
                      <strong class="text-success"><i class="fas fa-check-circle me-1"></i> <?= htmlspecialchars($loggedQual) ?> (KYC Linked)</strong>
                    </div>
                  </div>

                  <!-- Direct Course Selector for Seat Booking -->
                  <div class="pt-2" style="border-top: 1px dashed rgba(255,255,255,0.12);">
                    <label class="form-label text-warning fw-bold mb-1" style="font-size: 0.82rem;">
                      <i class="fas fa-graduation-cap me-1"></i> Course Program Jiske Liye Aap Seat Book Kar Rahe Hain:
                    </label>
                    <select class="form-select" id="step3CourseSelect" onchange="syncCourseSelection(this.value)" style="background: #070e24; color: #fff; border-color: #e6a910; font-weight: 600;">
                      <?php foreach ($dbCourses as $dc): ?>
                        <option value="<?= htmlspecialchars($dc['title']) ?>" <?= ($dc['title'] === $initialSelectedCourseTitle) ? 'selected' : '' ?>>
                          <?= htmlspecialchars($dc['title']) ?> (<?= htmlspecialchars($dc['duration']) ?> — Tuition: ₹<?= number_format((float)($dc['fee'] ?? 14999), 0) ?>)
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Fee Breakdown -->
              <div class="fee-summary-table">
                <div class="fee-row">
                  <span>Candidate Name:</span>
                  <span id="summaryStudentName" style="color: #ffffff; font-weight: 700;"><?= htmlspecialchars($loggedName ?: 'Candidate') ?></span>
                </div>
                <div class="fee-row">
                  <span>Selected Course Program:</span>
                  <span id="summaryCourseName" style="color: #e6a910; font-weight: 700;"><?= htmlspecialchars($initialSelectedCourseTitle) ?></span>
                </div>
                <div class="fee-row">
                  <span>Seat Booking & Registration Fee:</span>
                  <span style="color: #ffffff;">₹<?= number_format($admissionFee, 2) ?></span>
                </div>
                <div class="fee-row">
                  <span style="font-weight: 700; color: #ffffff;">Total Payable Now:</span>
                  <span class="fee-amount">₹<?= number_format($admissionFee, 2) ?></span>
                </div>
              </div>

              <div class="p-2 px-3 mb-3 rounded-3" style="background: rgba(230, 169, 16, 0.08); border-left: 3px solid #e6a910; font-size: 0.8rem; color: #cbd5e1;">
                <i class="fas fa-info-circle text-warning me-1"></i>
                Yeh <strong>₹<?= number_format($admissionFee, 0) ?></strong> seat booking amount aapke is course ki final enrollment tuition fee me se automatically <strong>minus (deduct)</strong> ho jayega.
              </div>

              <!-- Razorpay Secure Badge -->
              <div class="razorpay-secure-badge">
                <div class="razorpay-logo-strip">
                  <i class="fas fa-shield-alt text-warning fa-lg"></i>
                  <div>
                    <span>Razorpay Secure Payment Gateway</span><br>
                    <small  style="font-size: 0.75rem;">Supports UPI (GPay/PhonePe), Credit/Debit Cards, Net Banking & Wallets</small>
                  </div>
                </div>
                <span class="badge bg-warning text-dark font-weight-bold">256-Bit SSL</span>
              </div>

              <!-- Step 3 Actions -->
              <div class="step-actions">
                <?php if ($isLoggedIn): ?>
                  <a href="my-enrollments.php" class="btn-step-prev" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> My Dashboard
                  </a>
                <?php else: ?>
                  <button type="button" class="btn-step-prev" onclick="goToStep(2)">
                    <i class="fas fa-arrow-left"></i> Back to Documents
                  </button>
                <?php endif; ?>
                <button type="button" class="btn-step-next" id="btnPayRazorpay" onclick="initiateRazorpayPayment()">
                  <i class="fas fa-lock"></i> Pay ₹<?= number_format($admissionFee, 0) ?> & Book Seat
                </button>
              </div>

            </div>


            <!-- ================= STEP 4: SUCCESS CONFIRMATION & ADMISSION SLIP ================= -->
            <div class="form-step-screen" id="stepScreen4">
              <div class="success-celebration-box">
                <div class="success-icon-bubble">
                  <i class="fas fa-check"></i>
                </div>
                <h2>Admission Confirmed! 🎉</h2>
                <p>
                  Your registration fee has been received and verified. Your student records are now live at Skill Grow Institution.
                </p>

                <!-- Receipt Card -->
                <div class="application-receipt-card">
                  <div class="receipt-item">
                    <span>Student ID:</span>
                    <span id="receiptStudentId" style="color: #10b981; font-weight: 800;">STU-2026-0000</span>
                  </div>
                  <div class="receipt-item">
                    <span>Application Number:</span>
                    <span id="receiptAppId" style="color: var(--brand-primary); font-weight: 800;">SGI-2026-00000</span>
                  </div>
                  <div class="receipt-item">
                    <span>Candidate Name:</span>
                    <span id="receiptName">Student Name</span>
                  </div>
                  <div class="receipt-item">
                    <span>Enrolled Course:</span>
                    <span id="receiptCourse">Full Stack Web Technology</span>
                  </div>
                  <div class="receipt-item">
                    <span>Razorpay Payment ID:</span>
                    <span id="receiptPayId" class="font-monospace text-warning">pay_verified</span>
                  </div>
                  <div class="receipt-item">
                    <span>Registration Fee:</span>
                    <span class="text-success"><i class="fas fa-check-circle me-1"></i> ₹<?= number_format($admissionFee, 2) ?> Paid</span>
                  </div>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                  <a id="downloadSlipBtn" href="download-receipt.php" target="_blank" class="btn-step-next" style="text-decoration: none;">
                    <i class="fas fa-file-pdf me-1"></i> Download Admission Slip (PDF)
                  </a>
                  <a href="my-enrollments.php" class="btn-step-prev" style="text-decoration: none; border-color: #e6a910; color: #e6a910;">
                    <i class="fas fa-graduation-cap me-1"></i> Go to My Enrollments
                  </a>
                </div>

              </div>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // System Config injected from PHP
    const RAZORPAY_KEY_ID = '<?= htmlspecialchars($razorpayKeyId) ?>';
    const ADMISSION_FEE   = <?= (float)$admissionFee ?>;
    const SMTP_MODE       = '<?= htmlspecialchars($smtpMode) ?>';

    let isEmailVerified = <?= $isLoggedIn ? 'true' : 'false' ?>;

    // Sync course dropdowns between Step 1 and Step 3 Fast-Track
    function syncCourseSelection(courseTitle) {
      const step1Select = document.getElementById('course');
      const step3Select = document.getElementById('step3CourseSelect');
      const summaryCourse = document.getElementById('summaryCourseName');

      if (step1Select && step1Select.value !== courseTitle) {
        step1Select.value = courseTitle;
      }
      if (step3Select && step3Select.value !== courseTitle) {
        step3Select.value = courseTitle;
      }
      if (summaryCourse) {
        summaryCourse.textContent = courseTitle;
      }
    }

    // Strict 10-digit numeric validation for Phone / WhatsApp No.
    const phoneInputEl = document.getElementById('phone');
    const phoneErrorHint = document.getElementById('phoneErrorHint');
    if (phoneInputEl) {
      phoneInputEl.addEventListener('input', function() {
        const digitsOnly = this.value.replace(/\D/g, '').slice(0, 10);
        if (this.value !== digitsOnly) {
          this.value = digitsOnly;
        }
        if (digitsOnly.length > 0 && digitsOnly.length < 10) {
          if (phoneErrorHint) phoneErrorHint.style.display = 'block';
          this.style.borderColor = '#ef4444';
        } else if (digitsOnly.length === 10) {
          if (phoneErrorHint) phoneErrorHint.style.display = 'none';
          this.style.borderColor = '#10b981';
        } else {
          if (phoneErrorHint) phoneErrorHint.style.display = 'none';
          this.style.borderColor = '';
        }
      });

      phoneInputEl.addEventListener('blur', function() {
        const digitsOnly = this.value.replace(/\D/g, '');
        if (digitsOnly.length > 0 && digitsOnly.length !== 10) {
          if (phoneErrorHint) phoneErrorHint.style.display = 'block';
          this.style.borderColor = '#ef4444';
        }
      });
    }

    // Strict 6-digit numeric filter for OTP input
    const otpInputEl = document.getElementById('inputOtpCode');
    if (otpInputEl) {
      otpInputEl.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
      });
    }

    // Trigger Send OTP via AJAX
    function triggerSendOtp() {
      const phone = document.getElementById('phone').value.replace(/\D/g, '');
      const email = document.getElementById('email').value.trim();
      const name = document.getElementById('candName').value.trim() || 'Student Candidate';

      if (phone.length !== 10) {
        if (phoneErrorHint) phoneErrorHint.style.display = 'block';
        alert('Please enter a valid 10-digit Phone / WhatsApp number first.');
        document.getElementById('phone').focus();
        return;
      }

      if (!email || !email.includes('@')) {
        alert('Please enter a valid email address first.');
        document.getElementById('email').focus();
        return;
      }

      const btn = document.getElementById('btnSendOtp');
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
      btn.disabled = true;

      const formData = new FormData();
      formData.append('email', email);
      formData.append('name', name);

      fetch('ajax_send_otp.php', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Resend OTP';
        btn.disabled = false;

        const box = document.getElementById('otpVerifyBox');
        const notice = document.getElementById('otpStatusNotice');
        const otpInput = document.getElementById('inputOtpCode');

        if (data.status === 'success') {
          box.style.display = 'block';
          if (data.mode === 'test' && data.demo_otp) {
            notice.className = 'text-warning';
            notice.textContent = 'Test Mode: Use Code ' + data.demo_otp;
            otpInput.value = data.demo_otp;
          } else {
            notice.className = 'text-success fw-semibold';
            notice.innerHTML = '<i class="fas fa-check-circle me-1"></i> OTP sent to ' + email;
            otpInput.value = '';
            otpInput.focus();
          }
        } else {
          box.style.display = 'none';
          alert('Error: ' + data.message);
        }
      })
      .catch(err => {
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send OTP';
        btn.disabled = false;
        alert('Server connection error. Please try again.');
      });
    }

    // Trigger Verify OTP via AJAX
    function triggerVerifyOtp() {
      const email = document.getElementById('email').value.trim();
      const otp = document.getElementById('inputOtpCode').value.trim();

      if (!otp || otp.length !== 6) {
        alert('Please enter the complete 6-digit verification code.');
        return;
      }

      const btn = document.getElementById('btnVerifyOtp');
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
      btn.disabled = true;

      const formData = new FormData();
      formData.append('email', email);
      formData.append('otp', otp);

      fetch('ajax_verify_otp.php', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Verify Code';
        btn.disabled = false;

        if (data.status === 'success') {
          isEmailVerified = true;
          document.getElementById('otpVerifyBox').style.display = 'none';
          document.getElementById('btnSendOtp').style.display = 'none';
          document.getElementById('email').readOnly = true;
          document.getElementById('emailVerifiedBadge').style.display = 'inline-block';
          alert('Email successfully verified! You may now proceed.');
        } else {
          alert('Verification Failed: ' + data.message);
        }
      })
      .catch(err => {
        btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Verify Code';
        btn.disabled = false;
        alert('Server error verifying OTP.');
      });
    }

    // Show / Hide Password
    function toggleRegPassword(fieldId, btn) {
      const inp = document.getElementById(fieldId);
      if (!inp) return;
      const icon = btn.querySelector('i');
      if (inp.type === 'password') {
        inp.type = 'text';
        if (icon) {
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
          btn.classList.add('text-warning');
        }
      } else {
        inp.type = 'password';
        if (icon) {
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
          btn.classList.remove('text-warning');
        }
      }
    }

    // Validate Step 1
    function validateStep1AndProceed() {
      const name = document.getElementById('candName').value.trim();
      const phone = document.getElementById('phone').value.replace(/\D/g, '');
      const email = document.getElementById('email').value.trim();

      if (!name || !phone || !email) {
        alert('Please fill out Candidate Name, Phone Number, and Email.');
        return;
      }

      if (phone.length !== 10) {
        if (phoneErrorHint) phoneErrorHint.style.display = 'block';
        alert('Phone / WhatsApp No. must be exactly 10 digits.');
        document.getElementById('phone').focus();
        return;
      }

      if (!isEmailVerified) {
        alert('Email verification is required! Please click "Send OTP" to verify your email address.');
        document.getElementById('email').focus();
        return;
      }

      // Validate password if field is active/visible
      const passEl = document.getElementById('portalPassword');
      const confEl = document.getElementById('confirmPassword');
      if (passEl && passEl.offsetParent !== null) {
        const passVal = passEl.value;
        const confVal = confEl ? confEl.value : '';
        if (!passVal || passVal.length < 6) {
          alert('Please create a secure portal password (at least 6 characters).');
          passEl.focus();
          return;
        }
        if (passVal !== confVal) {
          alert('Passwords do not match. Please verify your confirm password.');
          if (confEl) confEl.focus();
          return;
        }
      }

      goToStep(2);
    }

    // Validate Step 2
    function validateStep2AndProceed() {
      const rawAadhaar = document.getElementById('aadhaarNum').value.replace(/\D/g, '');
      if (rawAadhaar.length !== 12) {
        alert('Please enter a valid 12-digit Aadhaar Card number.');
        document.getElementById('aadhaarNum').focus();
        return;
      }

      // Update Step 3 Summary
      const studentName = document.getElementById('candName').value.trim();
      const courseSelect = document.getElementById('course');
      const courseTitle = courseSelect.options[courseSelect.selectedIndex].text;

      document.getElementById('summaryStudentName').textContent = studentName;
      document.getElementById('summaryCourseName').textContent = courseTitle;

      goToStep(3);
    }

    // Step navigation controller
    function goToStep(stepNum) {
      document.querySelectorAll('.form-step-screen').forEach(el => el.classList.remove('active'));
      const targetScreen = document.getElementById('stepScreen' + stepNum);
      if (targetScreen) {
        targetScreen.classList.add('active');
      }

      for (let i = 1; i <= 4; i++) {
        const navEl = document.getElementById('navStep' + i);
        if (!navEl) continue;
        navEl.classList.remove('active-step', 'completed-step');
        if (i < stepNum) {
          navEl.classList.add('completed-step');
        } else if (i === stepNum) {
          navEl.classList.add('active-step');
        }
      }

      window.scrollTo({ top: 50, behavior: 'smooth' });
    }

    // Aadhaar number auto-hyphenation
    const aadhaarInput = document.getElementById('aadhaarNum');
    if (aadhaarInput) {
      aadhaarInput.addEventListener('input', function() {
        let val = this.value.replace(/\D/g, '');
        if (val.length > 12) val = val.substring(0, 12);
        let formatted = val.match(/.{1,4}/g)?.join(' - ') || val;
        this.value = formatted;
      });
    }

    // Show uploaded file name
    function showUploadedName(input, displayId) {
      const displayEl = document.getElementById(displayId);
      if (input.files && input.files[0]) {
        displayEl.textContent = '✓ ' + input.files[0].name;
        displayEl.style.display = 'block';
      }
    }

    // Initiate Razorpay Payment (Live or Test Mode)
    function initiateRazorpayPayment() {
      const btn = document.getElementById('btnPayRazorpay');
      btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Launching Gateway...';
      btn.disabled = true;

      const candName    = document.getElementById('candName').value.trim();
      const candEmail   = document.getElementById('email').value.trim();
      const candPhone   = document.getElementById('phone').value.trim();
      const courseTitle = document.getElementById('course').value;

      // If Razorpay Key is configured: Launch Razorpay Standard Checkout
      if (RAZORPAY_KEY_ID && RAZORPAY_KEY_ID.startsWith('rzp_')) {
        const options = {
          key: RAZORPAY_KEY_ID,
          amount: Math.round(ADMISSION_FEE * 100),
          currency: 'INR',
          name: 'Skill Grow Institution',
          description: 'Admission Registration Fee',
          image: 'assets/images/logo/logopng.png',
          prefill: {
            name: candName,
            email: candEmail,
            contact: candPhone
          },
          theme: {
            color: '#002365'
          },
          handler: function (response) {
            confirmAdmissionRecord(response.razorpay_payment_id, response.razorpay_order_id || ('ORD-' + Date.now()), 'Razorpay');
          },
          modal: {
            ondismiss: function() {
              btn.innerHTML = '<i class="fas fa-lock"></i> Pay ₹' + ADMISSION_FEE + ' via Razorpay';
              btn.disabled = false;
            }
          }
        };

        const rzp = new Razorpay(options);
        rzp.open();
      } else {
        // Fallback Test Mode (When Razorpay key is not yet set in Admin)
        setTimeout(function() {
          const mockPaymentId = 'pay_sim_' + Math.floor(10000000 + Math.random() * 90000000);
          confirmAdmissionRecord(mockPaymentId, 'ORD-' + Date.now(), 'Razorpay (Test Simulation)');
        }, 1200);
      }
    }

    // Send payload to backend to confirm admission & generate slip
    function confirmAdmissionRecord(paymentId, orderId, paymentMethod) {
      const candName      = document.getElementById('candName').value.trim();
      const candEmail     = document.getElementById('email').value.trim();
      const candPhone     = document.getElementById('phone').value.trim();
      const courseTitle   = document.getElementById('course').value;
      const qualification = document.getElementById('qualification').value;
      const rawAadhaar    = document.getElementById('aadhaarNum').value.trim();

      const payload = new FormData();
      payload.append('full_name', candName);
      payload.append('email', candEmail);
      payload.append('phone', candPhone);
      payload.append('course_name', courseTitle);
      payload.append('qualification', qualification);
      payload.append('aadhaar_number', rawAadhaar);
      payload.append('razorpay_payment_id', paymentId);
      payload.append('razorpay_order_id', orderId);
      payload.append('payment_method', paymentMethod);

      const passField = document.getElementById('portalPassword');
      if (passField && passField.value) {
        payload.append('password', passField.value);
      }

      // Attach student uploaded files to store in dedicated folder
      const fileAadhaar = document.getElementById('fileAadhaar');
      if (fileAadhaar && fileAadhaar.files[0]) {
        payload.append('file_aadhaar', fileAadhaar.files[0]);
      }

      const filePhoto = document.getElementById('filePhoto');
      if (filePhoto && filePhoto.files[0]) {
        payload.append('file_photo', filePhoto.files[0]);
      }

      const fileMarksheet = document.getElementById('fileMarksheet');
      if (fileMarksheet && fileMarksheet.files[0]) {
        payload.append('file_marksheet', fileMarksheet.files[0]);
      }

      fetch('ajax_process_admission.php', {
        method: 'POST',
        body: payload
      })
      .then(r => r.json())
      .then(res => {
        if (res.status === 'success') {
          // Update Success Screen
          document.getElementById('receiptStudentId').textContent = res.student_id;
          document.getElementById('receiptAppId').textContent = res.application_id;
          document.getElementById('receiptName').textContent = candName;
          document.getElementById('receiptCourse').textContent = courseTitle;
          document.getElementById('receiptPayId').textContent = res.payment_id;
          document.getElementById('downloadSlipBtn').href = res.receipt_url;

          goToStep(4);
        } else {
          alert('Error confirming admission: ' + res.message);
          const btn = document.getElementById('btnPayRazorpay');
          btn.innerHTML = '<i class="fas fa-lock"></i> Pay ₹' + ADMISSION_FEE + ' via Razorpay';
          btn.disabled = false;
        }
      })
      .catch(err => {
        alert('Server error saving admission details.');
        const btn = document.getElementById('btnPayRazorpay');
        btn.innerHTML = '<i class="fas fa-lock"></i> Pay ₹' + ADMISSION_FEE + ' via Razorpay';
        btn.disabled = false;
      });
    }
  </script>
</body>
</html>
