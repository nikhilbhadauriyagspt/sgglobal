<?php
// Dedicated Course Enrollment & Fee Payment Portal - Skill Grow Institution
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/db.php';

// Fetch Live System Settings (Razorpay, SMTP)
$stmt = $pdo->query("SELECT * FROM system_settings WHERE id = 1 LIMIT 1");
$sysSettings = $stmt->fetch() ?: [];

$razorpayKeyId   = $sysSettings['razorpay_key_id'] ?? '';
$razorpayMode    = $sysSettings['razorpay_mode'] ?? 'test';
$razorpayStatus  = $sysSettings['razorpay_status'] ?? 'active';

// Fetch all active courses
$cStmt = $pdo->query("SELECT id, title, slug, fee, duration, mode, image, rating, reviews, tagline FROM courses WHERE status = 'active' ORDER BY id ASC");
$courses = $cStmt->fetchAll();

// Determine pre-selected course from GET parameter
$selectedCourse = null;
$reqCourse = trim($_GET['course'] ?? '');
$reqId = (int)($_GET['id'] ?? 0);

if (!empty($reqCourse)) {
    foreach ($courses as $c) {
        if ($c['slug'] === $reqCourse) {
            $selectedCourse = $c;
            break;
        }
    }
} elseif ($reqId > 0) {
    foreach ($courses as $c) {
        if ($c['id'] == $reqId) {
            $selectedCourse = $c;
            break;
        }
    }
}

// Fallback to first course if not found
if (!$selectedCourse && !empty($courses)) {
    $selectedCourse = $courses[0];
}

// Check logged in student
$isLoggedIn = !empty($_SESSION['student_logged_in']);
$studentName  = $_SESSION['student_name'] ?? '';
$studentEmail = $_SESSION['student_email'] ?? '';
$studentPhone = $_SESSION['student_phone'] ?? '';
$studentId    = $_SESSION['student_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enroll in Course | Skill Grow Institution</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <!-- Premium Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Core Theme Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css">

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
      flex-direction: column;
    }

    /* Page Hero Banner - High Top Clearance for Fixed Header */
    .enroll-hero {
      background: radial-gradient(circle at 50% 0%, #10214d 0%, var(--brand-dark) 80%);
      border-bottom: 1px solid var(--brand-border);
      padding: 175px 0 50px;
      text-align: center;
    }

    @media (max-width: 991.98px) {
      .navbar {
        top: 0 !important;
        position: relative !important;
      }
      .topbar {
        position: relative !important;
      }
      .enroll-hero {
        padding: 40px 0 35px;
      }
      .navbar-collapse {
        background: #070e24;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid var(--brand-border);
        margin-top: 10px;
      }
    }

    .enroll-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 16px;
      background: rgba(230, 169, 16, 0.12);
      border: 1px solid rgba(230, 169, 16, 0.35);
      border-radius: 999px;
      font-size: 0.8rem;
      font-weight: 700;
      color: var(--brand-primary);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 16px;
    }

    .enroll-hero h1 {
      font-family: var(--font-heading);
      font-weight: 800;
      font-size: 2.3rem;
      color: #fff;
      margin-bottom: 10px;
    }

    .enroll-hero p {
      color: var(--text-muted-dark);
      font-size: 1rem;
      max-width: 650px;
      margin: 0 auto;
    }

    /* Main Container */
    .enroll-main {
      padding: 40px 0 80px;
      flex: 1;
    }

    /* Bento Cards */
    .bento-card {
      background: var(--brand-dark-card);
      border: 1px solid var(--brand-border);
      border-radius: 16px;
      padding: 28px;
      margin-bottom: 24px;
    }

    .bento-title {
      font-family: var(--font-heading);
      font-size: 1.15rem;
      font-weight: 700;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
      padding-bottom: 14px;
      border-bottom: 1px solid var(--brand-border);
    }

    /* Form Inputs */
    .form-label {
      color: #cbd5e1;
      font-size: 0.85rem;
      font-weight: 600;
      margin-bottom: 6px;
    }

    .form-control, .form-select {
      background-color: #070e24 !important;
      border: 1px solid var(--brand-border) !important;
      color: #fff !important;
      border-radius: 10px;
      padding: 12px 14px;
      font-size: 0.95rem;
      transition: border-color 0.2s;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--brand-primary) !important;
      background-color: #070e24 !important;
      color: #fff !important;
    }

    .form-control::placeholder {
      color: #64748b;
    }

    .form-select option {
      background: #0b1736;
      color: #fff;
    }

    /* Course Preview Card */
    .course-summary-card {
      background: #08112a;
      border: 1px solid var(--brand-border);
      border-radius: 14px;
      padding: 20px;
      display: flex;
      gap: 18px;
      align-items: center;
      margin-bottom: 20px;
    }

    .course-thumb-box {
      width: 100px;
      height: 80px;
      border-radius: 10px;
      overflow: hidden;
      flex-shrink: 0;
      border: 1px solid var(--brand-border);
    }

    .course-thumb-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .course-info-box h4 {
      font-family: var(--font-heading);
      font-size: 1.05rem;
      font-weight: 700;
      color: #fff;
      margin: 0 0 6px;
    }

    .course-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .course-tag {
      font-size: 0.75rem;
      padding: 3px 8px;
      border-radius: 6px;
      background: rgba(230, 169, 16, 0.1);
      border: 1px solid rgba(230, 169, 16, 0.25);
      color: var(--brand-primary);
    }

    /* Fee Breakdown Sidebar */
    .fee-summary-sidebar {
      position: sticky;
      top: 100px;
    }

    .fee-line {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      font-size: 0.9rem;
      color: var(--text-muted-dark);
    }

    .fee-line.total {
      border-top: 1px solid var(--brand-border);
      border-bottom: none;
      padding-top: 16px;
      margin-top: 8px;
      font-size: 1.2rem;
      font-weight: 800;
      color: #fff;
    }

    .fee-line.total .price {
      color: var(--brand-primary);
      font-size: 1.45rem;
      font-family: var(--font-heading);
    }

    /* Checkout Button */
    .btn-pay-enroll {
      background: var(--brand-primary);
      color: #000;
      font-weight: 700;
      font-size: 1.05rem;
      border: none;
      border-radius: 12px;
      padding: 15px 24px;
      width: 100%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      cursor: pointer;
      transition: all 0.2s ease;
      margin-top: 16px;
    }

    .btn-pay-enroll:hover {
      background: var(--brand-primary-hover);
      color: #000;
      transform: translateY(-2px);
    }

    /* Secure strip */
    .secure-strip {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-top: 12px;
      font-size: 0.78rem;
      color: var(--text-muted-dark);
    }

    /* Verified Student Pill */
    .student-logged-pill {
      background: rgba(16, 185, 129, 0.1);
      border: 1px solid rgba(16, 185, 129, 0.3);
      border-radius: 12px;
      padding: 14px 18px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
    }

    .student-guest-banner {
      background: rgba(230, 169, 16, 0.1);
      border: 1px solid rgba(230, 169, 16, 0.3);
      border-radius: 12px;
      padding: 14px 18px;
      margin-bottom: 20px;
      font-size: 0.88rem;
    }

    /* Success Card Screen */
    .enroll-success-box {
      display: none;
      text-align: center;
      padding: 40px 20px;
    }

    .success-check-bubble {
      width: 76px;
      height: 76px;
      background: rgba(16, 185, 129, 0.15);
      border: 2px solid #10b981;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      color: #10b981;
      font-size: 32px;
    }

    .receipt-detail-table {
      width: 100%;
      max-width: 520px;
      margin: 25px auto;
      text-align: left;
      background: #08112a;
      border: 1px solid var(--brand-border);
      border-radius: 12px;
      overflow: hidden;
    }

    .receipt-detail-table tr td {
      padding: 12px 18px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      font-size: 0.88rem;
    }

    .receipt-detail-table tr:last-child td {
      border-bottom: none;
    }

    .receipt-label {
      color: var(--text-muted-dark);
      width: 45%;
    }

    .receipt-value {
      color: #fff;
      font-weight: 700;
    }
  </style>
</head>
<body>

  <!-- Global Header -->
  <?php require_once __DIR__ . '/header.php'; ?>

  <!-- Hero Section -->
  <header class="enroll-hero">
    <div class="container">
      <div class="enroll-badge">
        <i class="fas fa-certificate text-warning"></i> Official Course Enrollment
      </div>
      <h1>Direct Course Enrollment & Tuition Fee Portal</h1>
      <p>Secure your batch seat in our industry-recognized certification programs. Fast, instant enrollment with official enrollment ID generation and receipt.</p>
    </div>
  </header>

  <!-- Main Enrollment Form & Checkout -->
  <main class="enroll-main">
    <div class="container">
      
      <!-- SUCCESS SCREEN -->
      <div id="enrollSuccessScreen" class="enroll-success-box">
        <div class="bento-card" style="max-width: 650px; margin: 0 auto;">
          <div class="success-check-bubble">
            <i class="fas fa-check"></i>
          </div>
          <h2 class="text-white" style="font-family: var(--font-heading); font-size: 1.8rem; font-weight: 800;">Enrollment Confirmed!</h2>
          <p class="text-muted" style="font-size: 0.95rem;">
            Congratulations! You have been successfully enrolled in your chosen course. An official confirmation email with your fee receipt has been dispatched.
          </p>

          <table class="receipt-detail-table">
            <tr>
              <td class="receipt-label">Enrollment ID</td>
              <td class="receipt-value text-warning font-monospace" id="resEnrollId">ENR-2026-XXXX</td>
            </tr>
            <tr>
              <td class="receipt-label">Student ID</td>
              <td class="receipt-value text-white font-monospace" id="resStudentId">STU-2026-XXXX</td>
            </tr>
            <tr>
              <td class="receipt-label">Course Enrolled</td>
              <td class="receipt-value text-white" id="resCourseName">-</td>
            </tr>
            <tr>
              <td class="receipt-label">Fee Amount Paid</td>
              <td class="receipt-value text-success" id="resFeePaid">₹0.00</td>
            </tr>
            <tr>
              <td class="receipt-label">Payment ID</td>
              <td class="receipt-value font-monospace text-muted" id="resPayId">-</td>
            </tr>
            <tr>
              <td class="receipt-label">Status</td>
              <td class="receipt-value"><span class="badge bg-success">Active & Verified</span></td>
            </tr>
          </table>

          <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
            <a href="my-enrollments.php" class="btn btn-warning fw-bold px-4 py-2" style="border-radius: 10px;">
              <i class="fas fa-graduation-cap me-1"></i> View My Enrolled Courses
            </a>
            <a href="explore-course.php" class="btn btn-outline-light px-4 py-2" style="border-radius: 10px;">
              <i class="fas fa-book-open me-1"></i> Browse More Courses
            </a>
          </div>
        </div>
      </div>

      <!-- MAIN ENROLLMENT FORM (Step View) -->
      <div id="enrollFormScreen">
        <div class="row g-4">
          
          <!-- LEFT COLUMN: COURSE SELECTION & STUDENT DETAILS -->
          <div class="col-lg-7">
            
            <?php if ($isLoggedIn): ?>
              <!-- Logged-in Student Alert -->
              <div class="student-logged-pill">
                <div class="d-flex align-items-center gap-3">
                  <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 18px;">
                    <i class="fas fa-user-graduate"></i>
                  </div>
                  <div>
                    <div class="fw-bold text-white"><?= htmlspecialchars($studentName) ?></div>
                    <small class="text-success font-monospace">Student ID: <?= htmlspecialchars($studentId) ?> (Logged In)</small>
                  </div>
                </div>
                <div>
                  <a href="student-logout.php" class="btn btn-sm btn-outline-danger" style="border-radius: 8px; font-size: 0.78rem;">
                    Switch Account
                  </a>
                </div>
              </div>
            <?php else: ?>
              <!-- Guest Student Banner -->
              <div class="student-guest-banner d-flex align-items-center justify-content-between">
                <div>
                  <i class="fas fa-user-circle text-warning me-2"></i>
                  Already registered at SG Skill? 
                  <strong class="text-white">Sign in to auto-fill your details.</strong>
                </div>
                <a href="login.php?redirect=enrollnow.php" class="btn btn-sm btn-warning text-dark fw-bold ms-3" style="border-radius: 8px; white-space: nowrap;">
                  <i class="fas fa-sign-in-alt me-1"></i> Student Login
                </a>
              </div>
            <?php endif; ?>

            <!-- BENTO 1: COURSE SELECTION -->
            <div class="bento-card">
              <div class="bento-title">
                <i class="fas fa-book-reader text-warning"></i> 1. Select Academic Program
              </div>

              <div class="mb-3">
                <label class="form-label">Course to Enroll in *</label>
                <select class="form-select" id="courseSelect" onchange="onCourseSelected(this.value)">
                  <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['id'] ?>" 
                            data-slug="<?= htmlspecialchars($c['slug']) ?>"
                            data-title="<?= htmlspecialchars($c['title']) ?>"
                            data-fee="<?= (float)$c['fee'] ?>"
                            data-duration="<?= htmlspecialchars($c['duration']) ?>"
                            data-mode="<?= htmlspecialchars($c['mode']) ?>"
                            data-image="<?= htmlspecialchars($c['image']) ?>"
                            data-rating="<?= htmlspecialchars($c['rating']) ?>"
                            data-reviews="<?= htmlspecialchars($c['reviews']) ?>"
                            data-tagline="<?= htmlspecialchars($c['tagline']) ?>"
                            <?= ($selectedCourse && $selectedCourse['id'] == $c['id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($c['title']) ?> — ₹<?= number_format((float)$c['fee'], 0) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Selected Course Quick Summary Card -->
              <div class="course-summary-card" id="selectedCourseBox">
                <div class="course-thumb-box">
                  <img id="cardCourseImg" src="<?= htmlspecialchars($selectedCourse['image'] ?? 'assets/images/course/web-technology.webp') ?>" alt="Course" onerror="this.src='assets/images/course/web-technology.webp'">
                </div>
                <div class="course-info-box flex-grow-1">
                  <h4 id="cardCourseTitle"><?= htmlspecialchars($selectedCourse['title'] ?? 'Selected Program') ?></h4>
                  <div class="course-tags">
                    <span class="course-tag" id="cardCourseDuration"><i class="fas fa-clock me-1"></i> <?= htmlspecialchars($selectedCourse['duration'] ?? '6 Months') ?></span>
                    <span class="course-tag" id="cardCourseMode"><i class="fas fa-laptop-code me-1"></i> <?= htmlspecialchars($selectedCourse['mode'] ?? 'Hybrid') ?></span>
                    <span class="course-tag text-warning" id="cardCourseRating"><i class="fas fa-star me-1"></i> <?= htmlspecialchars($selectedCourse['rating'] ?? '4.9') ?> ★</span>
                  </div>
                </div>
              </div>

              <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: rgba(230,169,16,0.06); border: 1px dashed rgba(230,169,16,0.3);">
                <div style="font-size: 0.85rem; color: #cbd5e1;">
                  <i class="fas fa-shield-alt text-warning me-1"></i> 100% Placement Support & Live Mentorship Included
                </div>
                <a id="cardViewCourseLink" href="course-detail.php?course=<?= htmlspecialchars($selectedCourse['slug'] ?? '') ?>" target="_blank" class="text-warning text-decoration-none fw-bold" style="font-size: 0.82rem;">
                  View Syllabus <i class="fas fa-arrow-right ms-1"></i>
                </a>
              </div>
            </div>

            <!-- BENTO 2: STUDENT CANDIDATE INFORMATION -->
            <div class="bento-card">
              <div class="bento-title">
                <i class="fas fa-user-circle text-warning"></i> 2. Candidate Information
              </div>

              <form id="enrollmentForm" onsubmit="event.preventDefault(); startCheckout();">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Full Name *</label>
                    <input type="text" class="form-control" id="student_name" name="student_name" 
                           value="<?= htmlspecialchars($studentName) ?>" 
                           placeholder="e.g. Rahul Sharma" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Email Address * (For Confirmation & Receipts)</label>
                    <input type="email" class="form-control" id="student_email" name="student_email" 
                           value="<?= htmlspecialchars($studentEmail) ?>" 
                           placeholder="e.g. rahul@example.com" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Mobile Number * (For Batch WhatsApp Group)</label>
                    <input type="tel" class="form-control" id="student_phone" name="student_phone" 
                           value="<?= htmlspecialchars($studentPhone) ?>" 
                           placeholder="e.g. 9876543210" maxlength="15" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Student ID (Optional / Auto-linked)</label>
                    <input type="text" class="form-control font-monospace" id="student_id" name="student_id" 
                           value="<?= htmlspecialchars($studentId) ?>" 
                           placeholder="e.g. STU-2026-XXXX" <?= $isLoggedIn ? 'readonly' : '' ?>>
                  </div>

                  <div class="col-12">
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="checkbox" id="termsCheck" checked required>
                      <label class="form-check-label text-muted" for="termsCheck" style="font-size: 0.8rem;">
                        I agree to the admission policies, attendance requirements, and code of conduct of Skill Grow Institution.
                      </label>
                    </div>
                  </div>
                </div>
              </form>
            </div>

          </div>

          <!-- RIGHT COLUMN: TUITION FEE & RAZORPAY CHECKOUT -->
          <div class="col-lg-5">
            <div class="fee-summary-sidebar">
              
              <div class="bento-card">
                <div class="bento-title">
                  <i class="fas fa-file-invoice-dollar text-warning"></i> Tuition Fee Summary
                </div>

                <div class="fee-line">
                  <span>Program Fee (<span id="summaryCourseName"><?= htmlspecialchars($selectedCourse['title'] ?? '') ?></span>)</span>
                  <span class="text-white fw-bold" id="summaryBaseFee">₹<?= number_format((float)($selectedCourse['fee'] ?? 14999), 0) ?></span>
                </div>

                <div class="fee-line">
                  <span>Cloud Lab & Tooling Licenses</span>
                  <span class="text-success fw-bold">FREE (₹0)</span>
                </div>

                <div class="fee-line">
                  <span>Industry Capstone Project & Certificate</span>
                  <span class="text-success fw-bold">INCLUDED</span>
                </div>

                <div class="fee-line">
                  <span>Placement Portal & Resume Assessment</span>
                  <span class="text-success fw-bold">INCLUDED</span>
                </div>

                <div class="fee-line total">
                  <span>Total Payable:</span>
                  <span class="price" id="summaryTotalFee">₹<?= number_format((float)($selectedCourse['fee'] ?? 14999), 0) ?></span>
                </div>

                <button type="button" class="btn-pay-enroll" id="btnPayEnroll" onclick="startCheckout()">
                  <i class="fas fa-lock"></i>
                  <span>Pay <span id="btnFeeDisplay">₹<?= number_format((float)($selectedCourse['fee'] ?? 14999), 0) ?></span> via Razorpay</span>
                </button>

                <div class="secure-strip">
                  <i class="fas fa-shield-alt text-warning"></i>
                  <span>256-Bit SSL Encrypted Razorpay Gateway (UPI, Cards, NetBanking)</span>
                </div>

                <hr style="border-color: var(--brand-border); margin: 20px 0;">

                <div style="font-size: 0.78rem; color: #94a3b8; line-height: 1.5;">
                  <strong class="text-white"><i class="fas fa-info-circle text-warning me-1"></i> What happens next?</strong><br>
                  1. Instant generation of your distinct <strong>Course Enrollment ID</strong>.<br>
                  2. Immediate email confirmation with official fee receipt.<br>
                  3. Batch coordinator assignment and learning LMS activation.
                </div>
              </div>

              <!-- Assistance Bento -->
              <div class="bento-card p-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="bg-dark text-warning p-3 rounded-3 border border-secondary" style="font-size: 20px;">
                    <i class="fab fa-whatsapp"></i>
                  </div>
                  <div>
                    <div class="text-white fw-bold" style="font-size: 0.9rem;">Need Fee Concession or Guidance?</div>
                    <div class="text-muted" style="font-size: 0.78rem;">Talk to an Academic Counselor on WhatsApp.</div>
                  </div>
                  <a href="https://wa.me/919217431562?text=Hello%20SG%20Skill,%20I%20need%20help%20with%20course%20enrollment" target="_blank" class="btn btn-sm btn-outline-warning ms-auto" style="border-radius: 8px;">
                    Chat
                  </a>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>

    </div>
  </main>

  <!-- Global Footer -->
  <?php require_once __DIR__ . '/footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // System Settings injected from PHP
    const RAZORPAY_KEY_ID = '<?= htmlspecialchars($razorpayKeyId) ?>';
    const RAZORPAY_MODE   = '<?= htmlspecialchars($razorpayMode) ?>';

    // Current State
    let currentCourse = {
      id: <?= (int)($selectedCourse['id'] ?? 0) ?>,
      slug: '<?= htmlspecialchars($selectedCourse['slug'] ?? '') ?>',
      title: <?= json_encode($selectedCourse['title'] ?? '') ?>,
      fee: <?= (float)($selectedCourse['fee'] ?? 14999) ?>
    };

    // Format Indian Rupees
    function formatINR(val) {
      return '₹' + Math.round(val).toLocaleString('en-IN');
    }

    // When student changes course from dropdown
    function onCourseSelected(courseId) {
      const select = document.getElementById('courseSelect');
      const opt = select.options[select.selectedIndex];
      if (!opt) return;

      currentCourse.id       = parseInt(courseId);
      currentCourse.slug     = opt.getAttribute('data-slug');
      currentCourse.title    = opt.getAttribute('data-title');
      currentCourse.fee      = parseFloat(opt.getAttribute('data-fee')) || 14999;

      const duration = opt.getAttribute('data-duration') || '6 Months';
      const mode     = opt.getAttribute('data-mode') || 'Hybrid';
      const rating   = opt.getAttribute('data-rating') || '4.9';
      const image    = opt.getAttribute('data-image') || 'assets/images/course/web-technology.webp';

      // Update UI
      document.getElementById('cardCourseTitle').innerText = currentCourse.title;
      document.getElementById('cardCourseImg').src = image;
      document.getElementById('cardCourseDuration').innerHTML = '<i class="fas fa-clock me-1"></i> ' + duration;
      document.getElementById('cardCourseMode').innerHTML = '<i class="fas fa-laptop-code me-1"></i> ' + mode;
      document.getElementById('cardCourseRating').innerHTML = '<i class="fas fa-star me-1"></i> ' + rating + ' ★';
      document.getElementById('cardViewCourseLink').href = 'course-detail.php?course=' + encodeURIComponent(currentCourse.slug);

      // Update Summary & Fee
      document.getElementById('summaryCourseName').innerText = currentCourse.title;
      document.getElementById('summaryBaseFee').innerText    = formatINR(currentCourse.fee);
      document.getElementById('summaryTotalFee').innerText   = formatINR(currentCourse.fee);
      document.getElementById('btnFeeDisplay').innerText     = formatINR(currentCourse.fee);
    }

    // Checkout initiation
    function startCheckout() {
      const name = document.getElementById('student_name').value.trim();
      const email = document.getElementById('student_email').value.trim();
      const phone = document.getElementById('student_phone').value.trim();
      const terms = document.getElementById('termsCheck').checked;

      if (!name) {
        alert('Please enter your full name.');
        document.getElementById('student_name').focus();
        return;
      }
      if (!email || !email.includes('@')) {
        alert('Please enter a valid email address.');
        document.getElementById('student_email').focus();
        return;
      }
      if (!phone || phone.length < 10) {
        alert('Please enter a valid 10-digit mobile number.');
        document.getElementById('student_phone').focus();
        return;
      }
      if (!terms) {
        alert('Please agree to the admission policies.');
        return;
      }

      const btn = document.getElementById('btnPayEnroll');
      btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Launching Razorpay...';
      btn.disabled = true;

      // Check if real Razorpay Key is provided
      if (RAZORPAY_KEY_ID && RAZORPAY_KEY_ID.startsWith('rzp_')) {
        const options = {
          key: RAZORPAY_KEY_ID,
          amount: Math.round(currentCourse.fee * 100),
          currency: 'INR',
          name: 'Skill Grow Institution',
          description: 'Course Enrollment: ' + currentCourse.title,
          image: 'assets/images/logo/logopng.png',
          prefill: {
            name: name,
            email: email,
            contact: phone
          },
          theme: {
            color: '#002365'
          },
          handler: function (response) {
            submitEnrollmentRecord(response.razorpay_payment_id, response.razorpay_order_id || ('ORD-' + Date.now()), 'Razorpay');
          },
          modal: {
            ondismiss: function() {
              resetPayButton();
            }
          }
        };

        const rzp = new Razorpay(options);
        rzp.open();
      } else {
        // Fallback test mode simulation when no Razorpay live keys are active
        setTimeout(function() {
          const mockPaymentId = 'pay_sim_' + Math.floor(10000000 + Math.random() * 90000000);
          submitEnrollmentRecord(mockPaymentId, 'ORD-' + Date.now(), 'Razorpay (Test Simulation)');
        }, 1200);
      }
    }

    function resetPayButton() {
      const btn = document.getElementById('btnPayEnroll');
      btn.innerHTML = '<i class="fas fa-lock"></i> <span>Pay ' + formatINR(currentCourse.fee) + ' via Razorpay</span>';
      btn.disabled = false;
    }

    // Submit confirmed enrollment to server
    function submitEnrollmentRecord(paymentId, orderId, method) {
      const name = document.getElementById('student_name').value.trim();
      const email = document.getElementById('student_email').value.trim();
      const phone = document.getElementById('student_phone').value.trim();
      const studentId = document.getElementById('student_id').value.trim();

      const payload = new FormData();
      payload.append('course_id', currentCourse.id);
      payload.append('course_slug', currentCourse.slug);
      payload.append('student_name', name);
      payload.append('student_email', email);
      payload.append('student_phone', phone);
      payload.append('student_id', studentId);
      payload.append('fee_amount', currentCourse.fee);
      payload.append('razorpay_payment_id', paymentId);
      payload.append('razorpay_order_id', orderId);
      payload.append('payment_method', method);

      fetch('ajax_process_enrollment.php', {
        method: 'POST',
        body: payload
      })
      .then(res => res.json())
      .then(res => {
        if (res.status === 'success') {
          // Switch to Success Screen
          document.getElementById('enrollFormScreen').style.display = 'none';
          document.getElementById('enrollSuccessScreen').style.display = 'block';

          document.getElementById('resEnrollId').innerText   = res.enrollment_id;
          document.getElementById('resStudentId').innerText  = res.student_id;
          document.getElementById('resCourseName').innerText = res.course_name;
          document.getElementById('resFeePaid').innerText    = formatINR(res.fee_amount);
          document.getElementById('resPayId').innerText      = res.payment_id;

          // Scroll to top
          window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
          alert('Error processing enrollment: ' + res.message);
          resetPayButton();
        }
      })
      .catch(err => {
        alert('Server communication error. Please try again.');
        resetPayButton();
      });
    }

    // Header scroll dynamic effect
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
      if (navbar) {
        if ((window.pageYOffset || document.documentElement.scrollTop) > 40) {
          navbar.classList.add('scrolled');
        } else {
          navbar.classList.remove('scrolled');
        }
      }
    });
  </script>
</body>
</html>
