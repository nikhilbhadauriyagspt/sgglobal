<?php
// Student Portal: Complete Profile Management, Editable Personal Info, Password & Enrolled Courses
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';

$isLoggedIn   = !empty($_SESSION['student_logged_in']);
$studentId    = $_SESSION['student_id'] ?? '';
$studentEmail = $_SESSION['student_email'] ?? '';
$studentName  = $_SESSION['student_name'] ?? '';
$studentPhone = $_SESSION['student_phone'] ?? '';

$alertMsg  = isset($_GET['msg']) ? trim($_GET['msg']) : '';
$alertType = 'info';

$studentProfile = null;
$admissionsList = [];
$enrollments    = [];
$totalFees      = 0.0;

if ($isLoggedIn) {
    // 1. Handle Profile Update or Password Change POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profile_action'])) {
        $action = $_POST['profile_action'];

        if ($action === 'update_info') {
            $newName    = trim($_POST['full_name'] ?? '');
            $newPhone   = trim($_POST['phone'] ?? '');
            $newQual    = trim($_POST['qualification'] ?? '');
            $newAadhaar = trim($_POST['aadhaar_number'] ?? '');

            if (empty($newName) || empty($newPhone)) {
                $alertMsg  = 'Full Name and Mobile Number are required.';
                $alertType = 'danger';
            } else {
                // Fetch current student row first
                $curStmt = $pdo->prepare("SELECT * FROM students WHERE student_id = :sid OR email = :em LIMIT 1");
                $curStmt->execute(['sid' => $studentId, 'em' => $studentEmail]);
                $curStudent = $curStmt->fetch();

                $avatarPath = $curStudent['avatar'] ?? ($_SESSION['student_avatar'] ?? null);

                // Optional Profile Photo Upload
                if (!empty($_FILES['avatar_file']['name']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['avatar_file']['name'], PATHINFO_EXTENSION));
                    $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
                    if (in_array($ext, $allowedExts)) {
                        $uploadDir = __DIR__ . '/uploads/avatars/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $fileName = 'stu_' . preg_replace('/[^A-Za-z0-9]/', '', $studentId ?: 'user') . '_' . time() . '.' . $ext;
                        if (move_uploaded_file($_FILES['avatar_file']['tmp_name'], $uploadDir . $fileName)) {
                            $avatarPath = 'uploads/avatars/' . $fileName;
                        }
                    }
                }

                try {
                    // Update students table
                    $uStmt = $pdo->prepare("
                        UPDATE students 
                        SET full_name = :fn, phone = :ph, qualification = :ql, aadhaar_number = :ad, avatar = :av
                        WHERE student_id = :sid OR email = :em
                    ");
                    $uStmt->execute([
                        'fn'  => $newName,
                        'ph'  => $newPhone,
                        'ql'  => $newQual,
                        'ad'  => $newAadhaar,
                        'av'  => $avatarPath,
                        'sid' => $studentId,
                        'em'  => $studentEmail
                    ]);

                    // Keep admissions and course_enrollments synced with updated name & phone
                    $uAdm = $pdo->prepare("
                        UPDATE admissions 
                        SET full_name = :fn, phone = :ph, qualification = :ql, aadhaar_number = :ad
                        WHERE student_id = :sid OR email = :em
                    ");
                    $uAdm->execute([
                        'fn'  => $newName,
                        'ph'  => $newPhone,
                        'ql'  => $newQual,
                        'ad'  => $newAadhaar,
                        'sid' => $studentId,
                        'em'  => $studentEmail
                    ]);

                    $uEnr = $pdo->prepare("
                        UPDATE course_enrollments 
                        SET student_name = :fn, student_phone = :ph
                        WHERE student_id = :sid OR student_email = :em
                    ");
                    $uEnr->execute([
                        'fn'  => $newName,
                        'ph'  => $newPhone,
                        'sid' => $studentId,
                        'em'  => $studentEmail
                    ]);

                    // Update session variables
                    $_SESSION['student_name']   = $newName;
                    $_SESSION['student_phone']  = $newPhone;
                    $_SESSION['student_avatar'] = $avatarPath;
                    $studentName  = $newName;
                    $studentPhone = $newPhone;

                    $alertMsg  = 'Your student profile details have been updated successfully!';
                    $alertType = 'success';
                } catch (PDOException $e) {
                    $alertMsg  = 'Error updating profile: ' . $e->getMessage();
                    $alertType = 'danger';
                }
            }
        } elseif ($action === 'update_password') {
            $newPass     = trim($_POST['new_password'] ?? '');
            $confirmPass = trim($_POST['confirm_password'] ?? '');

            if (strlen($newPass) < 6) {
                $alertMsg  = 'New password must be at least 6 characters long.';
                $alertType = 'danger';
            } elseif ($newPass !== $confirmPass) {
                $alertMsg  = 'New password and confirm password do not match.';
                $alertType = 'danger';
            } else {
                try {
                    $hash = password_hash($newPass, PASSWORD_DEFAULT);
                    $pStmt = $pdo->prepare("UPDATE students SET password = :pw WHERE student_id = :sid OR email = :em");
                    $pStmt->execute([
                        'pw'  => $hash,
                        'sid' => $studentId,
                        'em'  => $studentEmail
                    ]);
                    $alertMsg  = 'Your account password has been updated successfully!';
                    $alertType = 'success';
                } catch (PDOException $e) {
                    $alertMsg  = 'Error updating password. Please try again.';
                    $alertType = 'danger';
                }
            }
        }
    }

    // 2. Fetch Complete Student Profile from `students` table
    $stuStmt = $pdo->prepare("SELECT * FROM students WHERE student_id = :sid OR email = :em LIMIT 1");
    $stuStmt->execute(['sid' => $studentId, 'em' => $studentEmail]);
    $studentProfile = $stuStmt->fetch();

    if ($studentProfile) {
        $studentId    = $studentProfile['student_id'];
        $studentName  = $studentProfile['full_name'];
        $studentEmail = $studentProfile['email'];
        $studentPhone = $studentProfile['phone'];
        $_SESSION['student_id']     = $studentId;
        $_SESSION['student_name']   = $studentName;
        $_SESSION['student_email']  = $studentEmail;
        $_SESSION['student_phone']  = $studentPhone;
        $_SESSION['student_avatar'] = $studentProfile['avatar'] ?? '';
    }

    // 3. Fetch Student Admissions Records (Registration Slips & Uploaded Docs)
    $admStmt = $pdo->prepare("
        SELECT * FROM admissions 
        WHERE student_id = :sid OR email = :em 
        ORDER BY id DESC
    ");
    $admStmt->execute(['sid' => $studentId, 'em' => $studentEmail]);
    $admissionsList = $admStmt->fetchAll();

    foreach ($admissionsList as $admItem) {
        if ($admItem['payment_status'] === 'paid') {
            $totalFees += (float)$admItem['fee_amount'];
        }
    }

    // 4. Fetch All Course Enrollments for this student
    $stmt = $pdo->prepare("
        SELECT e.*, c.duration, c.mode, c.image, c.tagline, cat.name as category_name
        FROM course_enrollments e
        LEFT JOIN courses c ON e.course_id = c.id
        LEFT JOIN course_categories cat ON c.category_id = cat.id
        WHERE e.student_id = :sid OR e.student_email = :email
        ORDER BY e.id DESC
    ");
    $stmt->execute([
        'sid'   => $studentId,
        'email' => $studentEmail
    ]);
    $enrollments = $stmt->fetchAll();

    foreach ($enrollments as $enr) {
        $totalFees += (float)$enr['fee_amount'];
    }
}

$qualValue    = $studentProfile['qualification'] ?? ($admissionsList[0]['qualification'] ?? '');
$aadhaarValue = $studentProfile['aadhaar_number'] ?? ($admissionsList[0]['aadhaar_number'] ?? '');
$primaryCourse= $studentProfile['course_name'] ?? ($admissionsList[0]['course_name'] ?? 'General Program');
$avatarUrl    = $studentProfile['avatar'] ?? ($admissionsList[0]['photo_doc'] ?? '');
$joinedDate   = !empty($studentProfile['created_at']) ? date('d M, Y', strtotime($studentProfile['created_at'])) : date('d M, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Portal & Profile | Skill Grow Institution</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Core Theme Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css">

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

    body {
      font-family: var(--font-body);
      background-color: var(--brand-dark);
      color: #e2e8f0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Hero Banner with Top Clearance for Global Fixed Header */
    .profile-hero {
      background: radial-gradient(circle at 50% 0%, #13285c 0%, var(--brand-dark) 80%);
      border-bottom: 1px solid var(--brand-border);
      padding: 165px 0 40px;
    }

    @media (max-width: 991.98px) {
      .profile-hero {
        padding: 130px 0 30px;
      }
    }

    .profile-hero-inner {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    .hero-title {
      font-family: var(--font-heading);
      font-size: 2rem;
      font-weight: 800;
      color: #fff;
      margin: 0 0 6px;
    }

    .hero-subtitle {
      color: var(--text-muted-dark);
      font-size: 0.95rem;
      margin: 0;
    }

    /* Bento Card */
    .bento-card {
      background: var(--brand-dark-card);
      border: 1px solid var(--brand-border);
      border-radius: 18px;
      padding: 26px;
      margin-bottom: 24px;
    }

    .bento-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-bottom: 16px;
      margin-bottom: 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      flex-wrap: wrap;
      gap: 12px;
    }

    .bento-card-title {
      font-family: var(--font-heading);
      font-size: 1.18rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* Student Identity Card (Left Column) */
    .student-avatar-xl {
      width: 96px;
      height: 96px;
      border-radius: 22px;
      background: linear-gradient(135deg, rgba(230, 169, 16, 0.2), rgba(230, 169, 16, 0.05));
      border: 2px solid var(--brand-primary);
      color: var(--brand-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 36px;
      font-weight: 800;
      overflow: hidden;
      margin: 0 auto 16px;
    }

    .student-avatar-xl img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .info-detail-list {
      list-style: none;
      padding: 0;
      margin: 18px 0 0;
      text-align: left;
    }

    .info-detail-list li {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 11px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      font-size: 0.86rem;
      gap: 10px;
    }

    .info-detail-list li:last-child {
      border-bottom: none;
    }

    .info-detail-label {
      color: var(--text-muted-dark);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .info-detail-val {
      color: #ffffff;
      font-weight: 600;
      text-align: right;
      word-break: break-word;
    }

    /* KPI Pills */
    .student-stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
      gap: 14px;
      margin-bottom: 24px;
    }

    .stat-pill {
      background: var(--brand-dark-card);
      border: 1px solid var(--brand-border);
      border-radius: 14px;
      padding: 16px 20px;
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .stat-pill-icon {
      width: 46px;
      height: 46px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      flex-shrink: 0;
    }

    .stat-pill .label {
      font-size: 0.74rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-muted-dark);
      margin-bottom: 2px;
      font-weight: 600;
    }

    .stat-pill .val {
      font-size: 1.35rem;
      font-weight: 800;
      color: #fff;
      font-family: var(--font-heading);
      line-height: 1.2;
    }

    /* Portal Tabs */
    .portal-nav-pills {
      display: flex;
      gap: 10px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }

    .portal-nav-pills .nav-link {
      background: var(--brand-dark-card);
      border: 1px solid var(--brand-border);
      color: #cbd5e1;
      font-weight: 700;
      font-size: 0.88rem;
      padding: 11px 20px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
    }

    .portal-nav-pills .nav-link:hover {
      border-color: rgba(230, 169, 16, 0.4);
      color: #ffffff;
    }

    .portal-nav-pills .nav-link.active {
      background: linear-gradient(135deg, #f59e0b 0%, #e6a910 100%);
      border-color: #e6a910;
      color: #070e24;
    }

    /* Form Controls */
    .portal-label {
      font-size: 0.83rem;
      font-weight: 600;
      color: #cbd5e1;
      margin-bottom: 6px;
    }

    .portal-input {
      background: #060d21 !important;
      border: 1px solid var(--brand-border) !important;
      color: #ffffff !important;
      border-radius: 10px;
      padding: 10px 14px;
      font-size: 0.9rem;
    }

    .portal-input:focus {
      border-color: var(--brand-primary) !important;
      box-shadow: none !important;
    }

    .portal-input[readonly] {
      opacity: 0.65;
      cursor: not-allowed;
    }

    /* Enrolled Course Card */
    .enroll-item-card {
      background: #08112a;
      border: 1px solid var(--brand-border);
      border-radius: 16px;
      padding: 22px;
      margin-bottom: 18px;
      transition: border-color 0.2s;
    }

    .enroll-item-card:hover {
      border-color: rgba(230, 169, 16, 0.4);
    }

    .course-banner-img {
      width: 135px;
      height: 95px;
      border-radius: 10px;
      overflow: hidden;
      flex-shrink: 0;
      border: 1px solid var(--brand-border);
    }

    .course-banner-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .enrollment-id-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(230, 169, 16, 0.12);
      border: 1px solid rgba(230, 169, 16, 0.3);
      color: var(--brand-primary);
      padding: 3px 10px;
      border-radius: 7px;
      font-size: 0.8rem;
      font-family: monospace;
      font-weight: 700;
    }

    .fee-badge {
      font-size: 1.15rem;
      font-weight: 800;
      color: #10b981;
      font-family: var(--font-heading);
    }
  </style>
</head>
<body>

  <!-- Unified Global Header -->
  <?php require_once __DIR__ . '/header.php'; ?>

  <!-- Hero Banner -->
  <header class="profile-hero">
    <div class="container">
      <div class="profile-hero-inner">
        <div>
          <span class="badge bg-warning text-dark fw-bold mb-2 px-3 py-1 rounded-pill" style="font-size: 0.75rem;">
            <i class="fas fa-user-shield me-1"></i> OFFICIAL STUDENT PORTAL
          </span>
          <h1 class="hero-title">My Profile, Admissions & Enrolled Courses</h1>
          <p class="hero-subtitle">Manage your personal details, academic documents, course batches, and official fee receipts.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <a href="enrollnow.php" class="btn btn-warning fw-bold px-3 py-2" style="border-radius: 10px;">
            <i class="fas fa-plus-circle me-1"></i> Enroll in New Course
          </a>
          <?php if ($isLoggedIn): ?>
            <a href="student-logout.php" class="btn btn-outline-danger fw-bold px-3 py-2" style="border-radius: 10px;">
              <i class="fas fa-power-off me-1"></i> Sign Out
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="py-5 flex-grow-1">
    <div class="container">
      
      <?php if (!empty($alertMsg)): ?>
        <div class="alert alert-<?= htmlspecialchars($alertType) ?> alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
          <i class="fas fa-info-circle me-2"></i> <?= htmlspecialchars($alertMsg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php if (!$isLoggedIn): ?>
        <!-- Not Logged In View -->
        <div class="bento-card text-center py-5" style="max-width: 600px; margin: 30px auto;">
          <div style="font-size: 48px; color: var(--brand-primary); margin-bottom: 16px;">
            <i class="fas fa-user-lock"></i>
          </div>
          <h3 class="text-white" style="font-family: var(--font-heading); font-weight: 700;">Student Portal Sign In Required</h3>
          <p class="text-secondary" style="font-size: 0.95rem; max-width: 440px; margin: 0 auto 24px;">
            Please sign in with your registered email or Student ID to view and edit your profile, check enrolled courses, and download fee receipts.
          </p>
          <div class="d-flex justify-content-center gap-3">
            <a href="login.php?redirect=my-enrollments.php" class="btn btn-warning fw-bold px-4 py-2" style="border-radius: 10px;">
              <i class="fas fa-sign-in-alt me-1"></i> Student Sign In
            </a>
            <a href="register.php" class="btn btn-outline-light fw-bold px-4 py-2" style="border-radius: 10px;">
              <i class="fas fa-user-plus me-1"></i> New Registration
            </a>
          </div>
        </div>

      <?php else: ?>

        <!-- Top Summary KPI Strip -->
        <div class="student-stats-grid">
          <div class="stat-pill">
            <div class="stat-pill-icon" style="background: rgba(245, 158, 11, 0.14); color: #f59e0b;">
              <i class="fas fa-id-card"></i>
            </div>
            <div>
              <div class="label">Student ID</div>
              <div class="val font-monospace" style="font-size: 1.05rem;"><?= htmlspecialchars($studentId ?: 'STU-VERIFIED') ?></div>
            </div>
          </div>

          <div class="stat-pill">
            <div class="stat-pill-icon" style="background: rgba(59, 130, 246, 0.14); color: #3b82f6;">
              <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
              <div class="label">Enrolled Batches</div>
              <div class="val"><?= count($enrollments) ?> Courses</div>
            </div>
          </div>

          <div class="stat-pill">
            <div class="stat-pill-icon" style="background: rgba(168, 85, 247, 0.14); color: #a855f7;">
              <i class="fas fa-file-signature"></i>
            </div>
            <div>
              <div class="label">Admission Applications</div>
              <div class="val"><?= count($admissionsList) ?> Verified</div>
            </div>
          </div>

          <div class="stat-pill">
            <div class="stat-pill-icon" style="background: rgba(16, 185, 129, 0.14); color: #10b981;">
              <i class="fas fa-indian-rupee-sign"></i>
            </div>
            <div>
              <div class="label">Total Fees Paid</div>
              <div class="val text-success">₹<?= number_format($totalFees, 0) ?></div>
            </div>
          </div>
        </div>

        <div class="row g-4">
          
          <!-- LEFT COLUMN: STUDENT IDENTITY CARD (4 Cols) -->
          <div class="col-lg-4">
            <div class="bento-card text-center">
              <div class="student-avatar-xl">
                <?php if (!empty($avatarUrl) && file_exists(__DIR__ . '/' . $avatarUrl)): ?>
                  <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Profile Photo">
                <?php else: ?>
                  <?= strtoupper(substr($studentName, 0, 1)) ?>
                <?php endif; ?>
              </div>

              <h3 class="text-white mb-1" style="font-family: var(--font-heading); font-size: 1.35rem; font-weight: 800;">
                <?= htmlspecialchars($studentName) ?>
              </h3>
              <p class="text-warning small fw-bold mb-2">
                <i class="fas fa-graduation-cap me-1"></i> <?= htmlspecialchars($primaryCourse) ?>
              </p>
              <span class="badge bg-success bg-opacity-25 text-success border border-success px-3 py-1 rounded-pill" style="font-size: 0.74rem;">
                <i class="fas fa-check-circle me-1"></i> Active Verified Student
              </span>

              <ul class="info-detail-list">
                <li>
                  <span class="info-detail-label"><i class="fas fa-id-badge text-warning"></i> Student ID</span>
                  <span class="info-detail-val font-monospace text-warning"><?= htmlspecialchars($studentId) ?></span>
                </li>
                <li>
                  <span class="info-detail-label"><i class="fas fa-envelope text-warning"></i> Email</span>
                  <span class="info-detail-val"><?= htmlspecialchars($studentEmail) ?></span>
                </li>
                <li>
                  <span class="info-detail-label"><i class="fas fa-phone text-warning"></i> Mobile</span>
                  <span class="info-detail-val"><?= htmlspecialchars($studentPhone) ?></span>
                </li>
                <li>
                  <span class="info-detail-label"><i class="fas fa-user-graduate text-warning"></i> Qualification</span>
                  <span class="info-detail-val"><?= htmlspecialchars($qualValue ?: 'Not specified') ?></span>
                </li>
                <li>
                  <span class="info-detail-label"><i class="fas fa-fingerprint text-warning"></i> Aadhaar No.</span>
                  <span class="info-detail-val font-monospace"><?= htmlspecialchars($aadhaarValue ?: 'Not specified') ?></span>
                </li>
                <li>
                  <span class="info-detail-label"><i class="fas fa-calendar-check text-warning"></i> Registered</span>
                  <span class="info-detail-val"><?= htmlspecialchars($joinedDate) ?></span>
                </li>
              </ul>

              <?php if (!empty($admissionsList)): ?>
                <div class="mt-4 pt-3 border-top border-secondary border-opacity-25 d-grid gap-2">
                  <a href="download-receipt.php?app_id=<?= urlencode($admissionsList[0]['application_id']) ?>" target="_blank" class="btn btn-outline-warning btn-sm py-2 fw-bold" style="border-radius: 10px;">
                    <i class="fas fa-file-invoice me-1"></i> Download Admission Slip (<?= htmlspecialchars($admissionsList[0]['application_id']) ?>)
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- RIGHT COLUMN: EDIT PROFILE, ENROLLED COURSES & ADMISSION DOCUMENTS (8 Cols) -->
          <div class="col-lg-8">

            <!-- Navigation Tabs -->
            <ul class="nav portal-nav-pills" id="studentPortalTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-profile-btn" data-bs-toggle="pill" data-bs-target="#tabProfileEdit" type="button" role="tab">
                  <i class="fas fa-user-pen"></i> View & Edit Profile
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-courses-btn" data-bs-toggle="pill" data-bs-target="#tabEnrolledCourses" type="button" role="tab">
                  <i class="fas fa-graduation-cap"></i> Enrolled Courses (<?= count($enrollments) ?>)
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-admissions-btn" data-bs-toggle="pill" data-bs-target="#tabAdmissionsDocs" type="button" role="tab">
                  <i class="fas fa-file-shield"></i> Admission & Documents (<?= count($admissionsList) ?>)
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-security-btn" data-bs-toggle="pill" data-bs-target="#tabSecurityPassword" type="button" role="tab">
                  <i class="fas fa-key"></i> Change Password
                </button>
              </li>
            </ul>

            <div class="tab-content">
              
              <!-- TAB 1: EDIT PERSONAL PROFILE DETAILS -->
              <div class="tab-pane fade show active" id="tabProfileEdit" role="tabpanel">
                <div class="bento-card">
                  <div class="bento-card-header">
                    <h3 class="bento-card-title">
                      <i class="fas fa-user-edit text-warning"></i> Edit Personal & Academic Profile
                    </h3>
                    <span class="badge bg-dark border border-secondary text-secondary">Instant Sync</span>
                  </div>

                  <form method="POST" action="my-enrollments.php" enctype="multipart/form-data">
                    <input type="hidden" name="profile_action" value="update_info">

                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="portal-label">Student ID (Immutable)</label>
                        <input type="text" class="form-control portal-input font-monospace" value="<?= htmlspecialchars($studentId) ?>" readonly>
                      </div>

                      <div class="col-md-6">
                        <label class="portal-label">Registered Email Address (Immutable)</label>
                        <input type="email" class="form-control portal-input" value="<?= htmlspecialchars($studentEmail) ?>" readonly>
                      </div>

                      <div class="col-md-6">
                        <label class="portal-label">Full Legal Name *</label>
                        <input type="text" name="full_name" class="form-control portal-input" value="<?= htmlspecialchars($studentName) ?>" required placeholder="Enter your full name">
                      </div>

                      <div class="col-md-6">
                        <label class="portal-label">Mobile / WhatsApp Number *</label>
                        <input type="text" name="phone" class="form-control portal-input" value="<?= htmlspecialchars($studentPhone) ?>" required placeholder="10-digit mobile number">
                      </div>

                      <div class="col-md-6">
                        <label class="portal-label">Highest Qualification</label>
                        <input type="text" name="qualification" class="form-control portal-input" value="<?= htmlspecialchars($qualValue) ?>" placeholder="e.g. 12th Pass, BCA, B.Tech, B.Com">
                      </div>

                      <div class="col-md-6">
                        <label class="portal-label">Aadhaar / ID Number</label>
                        <input type="text" name="aadhaar_number" class="form-control portal-input font-monospace" value="<?= htmlspecialchars($aadhaarValue) ?>" placeholder="XXXX-XXXX-XXXX">
                      </div>

                      <div class="col-12">
                        <label class="portal-label">Update Profile Photo (JPG, PNG, WEBP)</label>
                        <input type="file" name="avatar_file" class="form-control portal-input" accept=".jpg,.jpeg,.png,.webp">
                        <small class="text-secondary" style="font-size: 0.75rem;">Leave blank if you want to keep your current profile photograph.</small>
                      </div>

                      <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-warning fw-bold px-4 py-2" style="border-radius: 10px;">
                          <i class="fas fa-save me-1"></i> Save Profile Changes
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- TAB 2: ENROLLED COURSES & FEE RECEIPTS -->
              <div class="tab-pane fade" id="tabEnrolledCourses" role="tabpanel">
                <div class="bento-card" id="enrolledCoursesSection">
                  <div class="bento-card-header">
                    <h3 class="bento-card-title">
                      <i class="fas fa-graduation-cap text-warning"></i> My Enrolled Courses & Fee Receipts (<?= count($enrollments) ?>)
                    </h3>
                    <a href="enrollnow.php" class="btn btn-sm btn-warning fw-bold rounded-pill px-3">
                      <i class="fas fa-plus me-1"></i> Enroll New
                    </a>
                  </div>

                  <?php if (!empty($enrollments)): ?>
                    <?php foreach ($enrollments as $enr): ?>
                      <div class="enroll-item-card">
                        <div class="d-flex align-items-start gap-4 flex-wrap flex-md-nowrap">
                          <div class="course-banner-img d-none d-sm-block">
                            <img src="<?= htmlspecialchars($enr['image'] ?: 'assets/images/course/web-technology.webp') ?>" alt="Course" onerror="this.src='assets/images/course/web-technology.webp'">
                          </div>

                          <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                              <div>
                                <span class="badge bg-dark border border-secondary text-warning me-2" style="font-size: 0.72rem;">
                                  <?= htmlspecialchars($enr['category_name'] ?: 'Professional Program') ?>
                                </span>
                                <h4 class="text-white d-inline-block mb-0" style="font-family: var(--font-heading); font-size: 1.12rem; font-weight: 700;">
                                  <?= htmlspecialchars($enr['course_name']) ?>
                                </h4>
                              </div>
                              <div class="text-md-end">
                                <div class="fee-badge">₹<?= number_format((float)$enr['fee_amount'], 0) ?></div>
                                <small class="text-secondary" style="font-size: 0.74rem;">Tuition Paid</small>
                              </div>
                            </div>

                            <div class="d-flex flex-wrap align-items-center gap-3 my-3 p-2 rounded-2" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                              <div>
                                <span class="text-secondary" style="font-size: 0.74rem;">Enrollment ID:</span>
                                <div class="enrollment-id-badge">
                                  <span><?= htmlspecialchars($enr['enrollment_id']) ?></span>
                                </div>
                              </div>

                              <div>
                                <span class="text-secondary" style="font-size: 0.74rem;">Transaction ID:</span>
                                <div class="font-monospace text-white" style="font-size: 0.8rem;">
                                  <?= htmlspecialchars($enr['razorpay_payment_id'] ?: 'RZP_VERIFIED') ?>
                                </div>
                              </div>

                              <div>
                                <span class="text-secondary" style="font-size: 0.74rem;">Enrolled On:</span>
                                <div class="text-white" style="font-size: 0.8rem;">
                                  <?= date('d M, Y', strtotime($enr['enrolled_at'])) ?>
                                </div>
                              </div>

                              <div class="ms-md-auto">
                                <span class="badge bg-success" style="font-size: 0.74rem;">
                                  <i class="fas fa-check-circle me-1"></i> <?= ucfirst($enr['status']) ?>
                                </span>
                              </div>
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-wrap">
                              <a href="enrollment-receipt.php?id=<?= urlencode($enr['enrollment_id']) ?>" target="_blank" class="btn btn-sm btn-outline-warning" style="border-radius: 8px;">
                                <i class="fas fa-receipt me-1"></i> Download Tuition Receipt
                              </a>
                              <?php if (!empty($enr['course_slug'])): ?>
                                <a href="course-detail.php?course=<?= urlencode($enr['course_slug']) ?>" class="btn btn-sm btn-outline-light" style="border-radius: 8px;">
                                  <i class="fas fa-book-open me-1"></i> View Curriculum
                                </a>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <div class="text-center py-4">
                      <i class="fas fa-folder-open fa-2x text-secondary mb-2"></i>
                      <h5 class="text-white">No Direct Batch Enrollments Yet</h5>
                      <p class="text-secondary small">You can enroll in any flagship course program and download your instant fee receipt.</p>
                      <a href="explore-course.php" class="btn btn-warning btn-sm fw-bold px-3">Browse Courses</a>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- TAB 3: ADMISSION APPLICATIONS & UPLOADED KYC DOCUMENTS -->
              <div class="tab-pane fade" id="tabAdmissionsDocs" role="tabpanel">
                <div class="bento-card">
                  <div class="bento-card-header">
                    <h3 class="bento-card-title">
                      <i class="fas fa-file-shield text-warning"></i> Admission Applications & Uploaded KYC Documents
                    </h3>
                  </div>

                  <?php if (!empty($admissionsList)): ?>
                    <?php foreach ($admissionsList as $adm): ?>
                      <div class="enroll-item-card">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                          <div>
                            <span class="enrollment-id-badge me-2"><?= htmlspecialchars($adm['application_id']) ?></span>
                            <h5 class="text-white d-inline-block mb-0" style="font-family: var(--font-heading); font-weight: 700;">
                              <?= htmlspecialchars($adm['course_name']) ?>
                            </h5>
                          </div>
                          <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i> <?= ucfirst(str_replace('_', ' ', $adm['admission_status'])) ?>
                          </span>
                        </div>

                        <div class="row g-2 my-2 small text-secondary">
                          <div class="col-sm-4">Registration Fee: <strong class="text-success">₹<?= number_format((float)$adm['fee_amount'], 0) ?> (Paid)</strong></div>
                          <div class="col-sm-4">Payment ID: <strong class="text-white font-monospace"><?= htmlspecialchars($adm['razorpay_payment_id'] ?: 'Verified') ?></strong></div>
                          <div class="col-sm-4">Date: <strong class="text-white"><?= date('d M Y', strtotime($adm['created_at'])) ?></strong></div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-3 pt-2 border-top border-secondary border-opacity-25">
                          <a href="download-receipt.php?app_id=<?= urlencode($adm['application_id']) ?>" target="_blank" class="btn btn-sm btn-warning fw-bold">
                            <i class="fas fa-print me-1"></i> Print Admission Slip
                          </a>
                          <?php if (!empty($adm['photo_doc'])): ?>
                            <a href="<?= htmlspecialchars($adm['photo_doc']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                              <i class="fas fa-camera me-1"></i> Uploaded Photo
                            </a>
                          <?php endif; ?>
                          <?php if (!empty($adm['aadhaar_doc'])): ?>
                            <a href="<?= htmlspecialchars($adm['aadhaar_doc']) ?>" target="_blank" class="btn btn-sm btn-outline-light">
                              <i class="fas fa-id-card me-1"></i> Aadhaar Document
                            </a>
                          <?php endif; ?>
                          <?php if (!empty($adm['marksheet_doc'])): ?>
                            <a href="<?= htmlspecialchars($adm['marksheet_doc']) ?>" target="_blank" class="btn btn-sm btn-outline-success">
                              <i class="fas fa-file-lines me-1"></i> Marksheet Document
                            </a>
                          <?php endif; ?>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <div class="text-center py-4">
                      <p class="text-secondary mb-3">No admission application records found for your account.</p>
                      <a href="register.php" class="btn btn-outline-warning btn-sm">Submit Admission Form</a>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- TAB 4: CHANGE ACCOUNT PASSWORD -->
              <div class="tab-pane fade" id="tabSecurityPassword" role="tabpanel">
                <div class="bento-card">
                  <div class="bento-card-header">
                    <h3 class="bento-card-title">
                      <i class="fas fa-key text-warning"></i> Update Portal Password
                    </h3>
                  </div>

                  <form method="POST" action="my-enrollments.php">
                    <input type="hidden" name="profile_action" value="update_password">

                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="portal-label">New Password *</label>
                        <input type="password" name="new_password" class="form-control portal-input" required minlength="6" placeholder="Minimum 6 characters">
                      </div>

                      <div class="col-md-6">
                        <label class="portal-label">Confirm New Password *</label>
                        <input type="password" name="confirm_password" class="form-control portal-input" required minlength="6" placeholder="Re-enter new password">
                      </div>

                      <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-warning fw-bold px-4 py-2" style="border-radius: 10px;">
                          <i class="fas fa-lock me-1"></i> Update Password
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

            </div><!-- End Tab Content -->

          </div>

        </div>

      <?php endif; ?>

    </div>
  </main>

  <!-- Global Footer -->
  <?php require_once __DIR__ . '/footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Auto-switch to Enrolled Courses tab if URL hash is #enrolledCoursesSection
    document.addEventListener('DOMContentLoaded', function () {
      if (window.location.hash === '#enrolledCoursesSection') {
        const coursesTabBtn = document.getElementById('tab-courses-btn');
        if (coursesTabBtn && typeof bootstrap !== 'undefined') {
          const tab = new bootstrap.Tab(coursesTabBtn);
          tab.show();
        }
      }
    });
  </script>
</body>
</html>
