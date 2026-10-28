<?php
// Student Enrolled Courses & Profile History - Skill Grow Institution
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';

$isLoggedIn = !empty($_SESSION['student_logged_in']);
$studentId    = $_SESSION['student_id'] ?? '';
$studentEmail = $_SESSION['student_email'] ?? '';
$studentName  = $_SESSION['student_name'] ?? '';
$studentPhone = $_SESSION['student_phone'] ?? '';

$enrollments = [];
$totalFees = 0.0;

if ($isLoggedIn) {
    // Fetch all course enrollments for this student
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Enrolled Courses & Receipts | Skill Grow Institution</title>
  
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

    /* Header Dark Mode Precision Styles */
    .topbar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1030;
      background: #002365 !important;
    }

    .navbar {
      position: fixed !important;
      top: 44px !important;
      left: 0;
      width: 100% !important;
      z-index: 1025 !important;
      background: rgba(7, 14, 36, 0.94) !important;
      backdrop-filter: blur(12px) !important;
      -webkit-backdrop-filter: blur(12px) !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      padding: 6px 0;
      transition: background 0.3s ease;
    }

    .navbar.scrolled {
      background: #002365 !important;
      border-bottom: 1px solid rgba(230, 169, 16, 0.3);
    }

    .navbar .navbar-brand img {
      height: 55px;
      width: auto;
      object-fit: contain;
    }

    .navbar-nav .nav-link {
      color: #cbd5e1 !important;
      font-weight: 600;
      padding: 8px 14px !important;
    }

    .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active {
      color: var(--brand-primary) !important;
    }

    .navbar-toggler {
      border-color: rgba(255, 255, 255, 0.2);
    }

    .navbar-toggler-icon {
      filter: invert(1);
    }

    /* Page Header - High Top Clearance for Fixed Header */
    .profile-hero {
      background: radial-gradient(circle at 50% 0%, #10214d 0%, var(--brand-dark) 80%);
      border-bottom: 1px solid var(--brand-border);
      padding: 175px 0 35px;
    }

    @media (max-width: 991.98px) {
      .navbar {
        top: 0 !important;
        position: relative !important;
      }
      .topbar {
        position: relative !important;
      }
      .profile-hero {
        padding: 40px 0 30px;
      }
      .navbar-collapse {
        background: #070e24;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid var(--brand-border);
        margin-top: 10px;
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
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 24px;
    }

    /* Student Profile Header Box */
    .student-profile-strip {
      display: flex;
      align-items: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .student-avatar-big {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: rgba(230, 169, 16, 0.15);
      border: 2px solid var(--brand-primary);
      color: var(--brand-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
    }

    .student-stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 16px;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid var(--brand-border);
    }

    .stat-pill {
      background: #070e24;
      border: 1px solid var(--brand-border);
      border-radius: 12px;
      padding: 14px 18px;
    }

    .stat-pill .label {
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-muted-dark);
      margin-bottom: 4px;
    }

    .stat-pill .val {
      font-size: 1.25rem;
      font-weight: 800;
      color: #fff;
    }

    /* Enrolled Course Card */
    .enroll-item-card {
      background: #08112a;
      border: 1px solid var(--brand-border);
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 20px;
      transition: border-color 0.2s;
    }

    .enroll-item-card:hover {
      border-color: rgba(230, 169, 16, 0.4);
    }

    .course-banner-img {
      width: 140px;
      height: 100px;
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
      padding: 4px 12px;
      border-radius: 8px;
      font-size: 0.82rem;
      font-family: monospace;
      font-weight: 700;
    }

    .copy-btn {
      background: transparent;
      border: none;
      color: var(--brand-primary);
      cursor: pointer;
      padding: 0;
      font-size: 0.8rem;
    }

    .copy-btn:hover {
      color: #fff;
    }

    .fee-badge {
      font-size: 1.15rem;
      font-weight: 800;
      color: #10b981;
      font-family: var(--font-heading);
    }

    /* Empty state */
    .empty-courses-box {
      text-align: center;
      padding: 60px 20px;
      background: var(--brand-dark-card);
      border: 1px dashed var(--brand-border);
      border-radius: 16px;
    }
  </style>
</head>
<body>

  <!-- Global Header -->
  <?php require_once __DIR__ . '/header.php'; ?>

  <!-- Hero Banner -->
  <header class="profile-hero">
    <div class="container">
      <div class="profile-hero-inner">
        <div>
          <h1 class="hero-title">Student Profile & Enrolled Programs</h1>
          <p class="hero-subtitle">Review your active course enrollments, tuition fee records, and official downloadable receipts.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="enrollnow.php" class="btn btn-warning fw-bold px-3 py-2" style="border-radius: 10px;">
            <i class="fas fa-plus-circle me-1"></i> Enroll in New Course
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="py-5 flex-grow-1">
    <div class="container">
      
      <?php if (!$isLoggedIn): ?>
        <!-- Not Logged In View -->
        <div class="bento-card text-center py-5" style="max-width: 600px; margin: 40px auto;">
          <div style="font-size: 48px; color: var(--brand-primary); margin-bottom: 16px;">
            <i class="fas fa-lock"></i>
          </div>
          <h3 class="text-white" style="font-family: var(--font-heading); font-weight: 700;">Student Portal Sign In Required</h3>
          <p class="text-muted" style="font-size: 0.95rem; max-width: 440px; margin: 0 auto 24px;">
            Please sign in with your registered email or Student ID to view your enrolled courses, tuition fee history, and official receipts.
          </p>
          <a href="login.php?redirect=my-enrollments.php" class="btn btn-warning fw-bold px-4 py-2" style="border-radius: 10px;">
            <i class="fas fa-sign-in-alt me-1"></i> Student Sign In
          </a>
        </div>

      <?php else: ?>

        <!-- LOGGED IN STUDENT PROFILE CARD -->
        <div class="bento-card">
          <div class="student-profile-strip">
            <div class="student-avatar-big">
              <i class="fas fa-user-graduate"></i>
            </div>
            <div>
              <div class="d-flex align-items-center gap-3 flex-wrap">
                <h2 class="text-white mb-0" style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 800;">
                  <?= htmlspecialchars($studentName) ?>
                </h2>
                <span class="badge bg-success" style="font-size: 0.75rem; padding: 5px 10px;">Active Student</span>
              </div>
              <div class="d-flex align-items-center gap-3 mt-1 flex-wrap" style="font-size: 0.85rem; color: var(--text-muted-dark);">
                <span><i class="fas fa-id-card text-warning me-1"></i> ID: <strong class="text-white font-monospace"><?= htmlspecialchars($studentId) ?></strong></span>
                <span>&bull;</span>
                <span><i class="fas fa-envelope text-warning me-1"></i> <?= htmlspecialchars($studentEmail) ?></span>
                <span>&bull;</span>
                <span><i class="fas fa-phone text-warning me-1"></i> <?= htmlspecialchars($studentPhone) ?></span>
              </div>
            </div>
          </div>

          <!-- Quick Metrics -->
          <div class="student-stats-grid">
            <div class="stat-pill">
              <div class="label">Total Courses Enrolled</div>
              <div class="val text-warning"><?= count($enrollments) ?></div>
            </div>
            <div class="stat-pill">
              <div class="label">Total Tuition Fees Paid</div>
              <div class="val text-success">₹<?= number_format($totalFees, 0) ?></div>
            </div>
            <div class="stat-pill">
              <div class="label">Academic Standing</div>
              <div class="val text-info">Verified Candidate</div>
            </div>
          </div>
        </div>

        <!-- ENROLLED COURSES SECTION -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="text-white mb-0" style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700;">
            <i class="fas fa-graduation-cap text-warning me-2"></i> Enrolled Academic Courses (<?= count($enrollments) ?>)
          </h3>
          <a href="explore-course.php" class="text-warning text-decoration-none" style="font-size: 0.85rem;">
            Explore All Programs <i class="fas fa-arrow-right ms-1"></i>
          </a>
        </div>

        <?php if (!empty($enrollments)): ?>
          <?php foreach ($enrollments as $enr): ?>
            <div class="enroll-item-card">
              <div class="d-flex align-items-start gap-4 flex-wrap flex-md-nowrap">
                
                <!-- Course Banner -->
                <div class="course-banner-img d-none d-sm-block">
                  <img src="<?= htmlspecialchars($enr['image'] ?: 'assets/images/course/web-technology.webp') ?>" alt="Course" onerror="this.src='assets/images/course/web-technology.webp'">
                </div>

                <!-- Info & Metadata -->
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <div>
                      <span class="badge bg-dark border border-secondary text-warning me-2" style="font-size: 0.72rem;">
                        <?= htmlspecialchars($enr['category_name'] ?: 'Professional Studies') ?>
                      </span>
                      <h4 class="text-white d-inline-block mb-0" style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 700;">
                        <?= htmlspecialchars($enr['course_name']) ?>
                      </h4>
                    </div>
                    <div class="text-md-end">
                      <div class="fee-badge">₹<?= number_format((float)$enr['fee_amount'], 0) ?></div>
                      <small class="text-muted" style="font-size: 0.75rem;">Paid in Full</small>
                    </div>
                  </div>

                  <!-- Enrollment ID & Payment Metadata Strip -->
                  <div class="d-flex flex-wrap align-items-center gap-3 my-3 p-2 rounded-2" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                    <div>
                      <span class="text-muted" style="font-size: 0.75rem;">Enrollment ID:</span>
                      <div class="enrollment-id-badge">
                        <span id="eid-<?= $enr['id'] ?>"><?= htmlspecialchars($enr['enrollment_id']) ?></span>
                        <button class="copy-btn" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($enr['enrollment_id']) ?>'); alert('Enrollment ID copied!');" title="Copy ID">
                          <i class="far fa-copy"></i>
                        </button>
                      </div>
                    </div>

                    <div>
                      <span class="text-muted" style="font-size: 0.75rem;">Payment Tx ID:</span>
                      <div class="font-monospace text-white" style="font-size: 0.8rem;">
                        <?= htmlspecialchars($enr['razorpay_payment_id'] ?: 'RZP_VERIFIED') ?>
                      </div>
                    </div>

                    <div>
                      <span class="text-muted" style="font-size: 0.75rem;">Enrolled On:</span>
                      <div class="text-white" style="font-size: 0.8rem;">
                        <?= date('d M, Y', strtotime($enr['enrolled_at'])) ?>
                      </div>
                    </div>

                    <div class="ms-md-auto">
                      <span class="badge bg-success" style="font-size: 0.75rem;">
                        <i class="fas fa-check-circle me-1"></i> <?= ucfirst($enr['status']) ?>
                      </span>
                    </div>
                  </div>

                  <!-- Actions Strip -->
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="enrollment-receipt.php?id=<?= urlencode($enr['enrollment_id']) ?>" target="_blank" class="btn btn-sm btn-outline-warning" style="border-radius: 8px;">
                      <i class="fas fa-receipt me-1"></i> View Fee Receipt
                    </a>
                    <?php if (!empty($enr['course_slug'])): ?>
                      <a href="course-detail.php?course=<?= urlencode($enr['course_slug']) ?>" class="btn btn-sm btn-outline-light" style="border-radius: 8px;">
                        <i class="fas fa-book-open me-1"></i> Course Syllabus & Curriculum
                      </a>
                    <?php endif; ?>
                  </div>

                </div>

              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- No enrollments yet -->
          <div class="empty-courses-box">
            <div style="font-size: 40px; color: var(--text-muted-dark); margin-bottom: 12px;">
              <i class="fas fa-folder-open"></i>
            </div>
            <h4 class="text-white" style="font-family: var(--font-heading);">No Course Enrollments Found</h4>
            <p class="text-muted" style="font-size: 0.9rem; max-width: 400px; margin: 0 auto 20px;">
              You have not enrolled in any academic course yet. Browse our top industry programs and get started today!
            </p>
            <a href="explore-course.php" class="btn btn-warning fw-bold px-4 py-2" style="border-radius: 10px;">
              <i class="fas fa-graduation-cap me-1"></i> Browse Course Catalog
            </a>
          </div>
        <?php endif; ?>

      <?php endif; ?>

    </div>
  </main>

  <!-- Global Footer -->
  <?php require_once __DIR__ . '/footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
