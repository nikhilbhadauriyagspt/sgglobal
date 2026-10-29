<?php
// Admin Course Enrollments Management - Skill Grow Institution
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$msg = '';
$msgType = '';

// Handle Delete Enrollment Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_enrollment') {
    $enrId = (int)($_POST['enrollment_id'] ?? 0);
    if ($enrId > 0) {
        try {
            $stmt = $pdo->prepare("SELECT enrollment_id, student_name FROM course_enrollments WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $enrId]);
            $target = $stmt->fetch();

            if ($target) {
                $dStmt = $pdo->prepare("DELETE FROM course_enrollments WHERE id = :id");
                $dStmt->execute(['id' => $enrId]);

                $msg = "Enrollment record '" . htmlspecialchars($target['enrollment_id']) . "' for " . htmlspecialchars($target['student_name']) . " deleted successfully.";
                $msgType = "warning";
            }
        } catch (PDOException $e) {
            $msg = "Database error deleting enrollment: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// Filter & Search
$search = trim($_GET['search'] ?? '');
$filterCourse = trim($_GET['course'] ?? '');

$sql = "
    SELECT 
        e.*, 
        c.title as current_course_title, 
        c.image as course_image,
        s.qualification as student_qualification,
        s.aadhaar_number as student_aadhaar,
        s.avatar as student_avatar,
        adm.application_id,
        adm.admission_status,
        adm.photo_doc,
        adm.aadhaar_doc,
        adm.marksheet_doc,
        adm.id as admission_table_id
    FROM course_enrollments e
    LEFT JOIN courses c ON e.course_id = c.id
    LEFT JOIN (
        SELECT student_id, email, qualification, aadhaar_number, avatar, 
               ROW_NUMBER() OVER (PARTITION BY COALESCE(NULLIF(student_id,''), email) ORDER BY id DESC) as rn 
        FROM students
    ) s ON ((e.student_id IS NOT NULL AND e.student_id != '' AND e.student_id = s.student_id) OR (e.student_email = s.email)) AND s.rn = 1
    LEFT JOIN (
        SELECT id, student_id, email, application_id, qualification, aadhaar_number, photo_doc, aadhaar_doc, marksheet_doc, admission_status, 
               ROW_NUMBER() OVER (PARTITION BY COALESCE(NULLIF(student_id,''), email) ORDER BY id DESC) as rn 
        FROM admissions
    ) adm ON ((e.student_id IS NOT NULL AND e.student_id != '' AND e.student_id = adm.student_id) OR (e.student_email = adm.email)) AND adm.rn = 1
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $sql .= " AND (e.student_name LIKE :s1 OR e.student_email LIKE :s2 OR e.enrollment_id LIKE :s3 OR e.student_id LIKE :s4 OR e.student_phone LIKE :s5)";
    $params['s1'] = "%$search%";
    $params['s2'] = "%$search%";
    $params['s3'] = "%$search%";
    $params['s4'] = "%$search%";
    $params['s5'] = "%$search%";
}

if (!empty($filterCourse)) {
    $sql .= " AND (e.course_slug = :cslug OR e.course_name = :cname)";
    $params['cslug'] = $filterCourse;
    $params['cname']  = $filterCourse;
}

$sql .= " ORDER BY e.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$enrollments = $stmt->fetchAll();

// Metrics
$mStmt = $pdo->query("
    SELECT 
        COUNT(*) as total_count,
        COALESCE(SUM(fee_amount), 0) as total_revenue,
        COUNT(DISTINCT student_email) as unique_students
    FROM course_enrollments
");
$metrics = $mStmt->fetch() ?: ['total_count' => 0, 'total_revenue' => 0, 'unique_students' => 0];

// Active courses list for filter
$coursesList = $pdo->query("SELECT title, slug FROM courses ORDER BY title ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Enrollments & Tuition Fees | SG Admin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">
  <!-- Central Unified Admin Stylesheet -->
  <link rel="stylesheet" href="assets/css/admin.css">

  <style>
    /* Minimal & High-Contrast Typography */
    .stat-metric-card {
      padding: 14px 18px !important;
      border-radius: 12px !important;
      height: auto !important;
      background: var(--admin-card-bg);
      border: 1px solid var(--admin-border);
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .stat-metric-icon {
      width: 44px !important;
      height: 44px !important;
      font-size: 18px !important;
      border-radius: 10px !important;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .stat-metric-title {
      font-size: 0.72rem !important;
      text-transform: uppercase !important;
      font-weight: 800 !important;
      letter-spacing: 0.8px;
      margin-bottom: 2px;
      color: #94a3b8;
    }

    .stat-metric-val {
      font-size: 1.4rem !important;
      font-family: var(--font-heading);
      font-weight: 800;
      line-height: 1.2;
    }

    /* Minimal Compact Table */
    .admin-table th {
      padding: 10px 14px !important;
      font-size: 0.72rem !important;
      color: #cbd5e1 !important;
      background: rgba(4, 8, 20, 0.6) !important;
      letter-spacing: 0.8px;
      font-weight: 700 !important;
      border-bottom: 1px solid var(--admin-border) !important;
    }

    .admin-table td {
      padding: 10px 14px !important;
      font-size: 0.84rem !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
      vertical-align: middle;
    }

    /* High Visibility Text Classes */
    .text-light-contrast {
      color: #f1f5f9 !important;
      font-size: 0.82rem;
    }

    /* Candidate Avatar Thumbnail */
    .student-avatar-thumb {
      width: 38px;
      height: 38px;
      border-radius: 8px;
      object-fit: cover;
      border: 1.5px solid rgba(245, 158, 11, 0.4);
      background: #002365;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #fbbf24;
      font-weight: 700;
      font-size: 13px;
      flex-shrink: 0;
      cursor: pointer;
    }

    .student-btn-link {
      background: none;
      border: none;
      padding: 0;
      color: #ffffff !important;
      font-weight: 700;
      font-size: 0.90rem;
      text-align: left;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: color 0.18s ease;
    }
    .student-btn-link:hover {
      color: #fbbf24 !important;
      text-decoration: underline;
    }

    .enrollment-badge {
      font-family: monospace;
      font-size: 0.76rem;
      font-weight: 700;
      color: #fbbf24;
      background: rgba(245, 158, 11, 0.12);
      padding: 3px 8px;
      border-radius: 6px;
      border: 1px solid rgba(245, 158, 11, 0.3);
      display: inline-block;
    }

    .stu-id-badge {
      font-family: monospace;
      font-size: 0.72rem;
      font-weight: 700;
      color: #38bdf8;
      background: rgba(56, 189, 248, 0.12);
      padding: 2px 7px;
      border-radius: 5px;
      border: 1px solid rgba(56, 189, 248, 0.3);
      display: inline-block;
      cursor: pointer;
    }
    .stu-id-badge:hover {
      background: rgba(56, 189, 248, 0.22);
      color: #ffffff;
    }

    .app-id-badge-sm {
      font-family: monospace;
      font-size: 0.72rem;
      font-weight: 700;
      color: #10b981;
      background: rgba(16, 185, 129, 0.12);
      padding: 2px 7px;
      border-radius: 5px;
      border: 1px solid rgba(16, 185, 129, 0.3);
      display: inline-block;
    }

    /* Modal Dossier Luxury Dark Theme */
    .dossier-cell {
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 10px;
      padding: 9px 13px;
      transition: border-color 0.2s;
    }
    .dossier-cell:hover {
      border-color: rgba(230, 169, 16, 0.3);
    }
    .dossier-label {
      color: #fbbf24 !important;
      font-size: 0.70rem !important;
      text-transform: uppercase !important;
      font-weight: 800 !important;
      letter-spacing: 0.8px;
      margin-bottom: 3px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .dossier-val {
      color: #ffffff !important;
      font-size: 0.90rem;
      font-weight: 700;
      word-break: break-all;
    }

    /* Document Preview Cards */
    .doc-preview-card {
      background: #070e24;
      border: 1px solid #1e2c52;
      border-radius: 10px;
      padding: 12px;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .doc-preview-thumb-box {
      width: 100%;
      height: 150px;
      background: #040817;
      border-radius: 6px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px dashed #2a3c6d;
      margin-bottom: 8px;
      position: relative;
    }
    .doc-preview-thumb-box img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      cursor: pointer;
      transition: transform 0.2s;
      padding: 3px;
    }
    .doc-preview-thumb-box img:hover {
      transform: scale(1.03);
    }
    .doc-preview-thumb-box .pdf-placeholder {
      text-align: center;
      color: #ef4444;
      padding: 15px;
    }
    .doc-preview-thumb-box .empty-placeholder {
      text-align: center;
      color: #64748b;
      padding: 15px;
    }
    .doc-badge-pill {
      font-size: 0.70rem;
      font-weight: 800;
      padding: 3px 8px;
      border-radius: 50px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .admin-search-icon-box {
      background: #0b1736 !important;
      border-color: #233560 !important;
      color: #fbbf24 !important;
      font-size: 0.85rem !important;
    }
    .admin-search-input {
      background: #070e24 !important;
      border-color: #233560 !important;
      color: #ffffff !important;
    }
    .admin-search-input:focus {
      background: #091330 !important;
      border-color: #fbbf24 !important;
      color: #ffffff !important;
    }
    .admin-search-select {
      background: #070e24 !important;
      border-color: #233560 !important;
      color: #ffffff !important;
    }

    .student-avatar-initials {
      width: 82px;
      height: 82px;
      border-radius: 50%;
      background: linear-gradient(135deg, #002365 0%, #1e3a8a 100%);
      border: 2px solid #e6a910;
      color: #fbbf24;
      font-size: 1.8rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto;
      font-family: var(--font-heading);
    }
  </style>
</head>
<body>

  <div class="admin-layout-wrapper">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div class="admin-main">
      <header class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
          <button type="button" class="btn btn-outline-light d-lg-none" id="sidebarToggleBtn" aria-label="Toggle Navigation">
            <i class="fas fa-bars"></i>
          </button>
          <div>
            <h1 class="page-title-text" style="font-size: 1.25rem; font-family: var(--font-heading); margin: 0; font-weight: 700;">
              Course Enrollments & Tuition Fees
            </h1>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
                <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Admin</a></li>
                <li class="breadcrumb-item active text-warning">Enrollments</li>
              </ol>
            </nav>
          </div>
        </div>

        <div class="d-flex gap-2">
          <a href="../enrollnow.php" target="_blank" class="btn btn-outline-light btn-sm px-3 py-2 rounded-3">
            <i class="fas fa-external-link-alt me-1"></i> Live Enrollment Portal
          </a>
        </div>
      </header>

      <main class="admin-content">
        
        <?php if (!empty($msg)): ?>
          <div class="alert alert-<?= $msgType ?> alert-dismissible fade show mb-4 border-0" role="alert" style="border-radius: 10px;">
            <i class="fas fa-info-circle me-2"></i> <?= $msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <!-- Minimal Metrics Row -->
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="stat-metric-card" style="border-left: 4px solid var(--admin-primary);">
              <div class="stat-metric-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                <i class="fas fa-graduation-cap"></i>
              </div>
              <div>
                <div class="stat-metric-title">Total Enrollments</div>
                <div class="text-white stat-metric-val"><?= number_format($metrics['total_count']) ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-metric-card" style="border-left: 4px solid #10b981;">
              <div class="stat-metric-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                <i class="fas fa-indian-rupee-sign"></i>
              </div>
              <div>
                <div class="stat-metric-title text-success">Tuition Collected</div>
                <div class="text-success stat-metric-val">₹<?= number_format((float)$metrics['total_revenue'], 0) ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-metric-card" style="border-left: 4px solid #38bdf8;">
              <div class="stat-metric-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                <i class="fas fa-user-check"></i>
              </div>
              <div>
                <div class="stat-metric-title" style="color: #38bdf8;">Active Students</div>
                <div class="stat-metric-val" style="color: #38bdf8;"><?= number_format($metrics['unique_students']) ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-metric-card" style="border-left: 4px solid #a855f7;">
              <div class="stat-metric-icon" style="background: rgba(168, 85, 247, 0.15); color: #a855f7;">
                <i class="fas fa-shield-alt"></i>
              </div>
              <div>
                <div class="stat-metric-title" style="color: #c084fc;">Payment Status</div>
                <div class="stat-metric-val text-white" style="font-size: 1.15rem !important;">100% Paid</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bento-card mb-4 p-3" style="border-radius: 12px;">
          <form method="GET" action="enrollments.php" class="row g-2 align-items-center">
            <div class="col-md-5">
              <div class="input-group input-group-sm">
                <span class="input-group-text admin-search-icon-box"><i class="fas fa-search" style="color: #fbbf24;"></i></span>
                <input type="text" class="form-control form-control-sm admin-search-input" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search student name, email, phone, ID, or enrollment ID...">
              </div>
            </div>

            <div class="col-md-4">
              <select class="form-select form-select-sm admin-search-select" name="course">
                <option value="">All Academic Programs</option>
                <?php foreach ($coursesList as $cl): ?>
                  <option value="<?= htmlspecialchars($cl['slug']) ?>" <?= ($filterCourse === $cl['slug']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cl['title']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
              <button type="submit" class="btn btn-warning btn-sm fw-bold flex-grow-1">
                <i class="fas fa-filter me-1"></i> Filter
              </button>
              <?php if (!empty($search) || !empty($filterCourse)): ?>
                <a href="enrollments.php" class="btn btn-outline-light btn-sm px-3">Reset</a>
              <?php endif; ?>
            </div>
          </form>
        </div>

        <!-- Main Enrollments Table -->
        <div class="bento-card" style="border-radius: 12px; overflow: hidden;">
          <div class="bento-header d-flex justify-content-between align-items-center py-3 px-4">
            <h2 class="bento-title mb-0" style="font-size: 1.05rem;">
              <i class="fas fa-user-graduate text-warning me-2"></i> Verified Course Enrollments (<?= count($enrollments) ?>)
            </h2>
            <span class="badge bg-dark border border-secondary text-white-50 font-monospace">
              Live Gateway Sync
            </span>
          </div>

          <div class="table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Enrollment & Student ID</th>
                  <th>Enrolled Academic Course</th>
                  <th>Tuition Fee</th>
                  <th>Gateway</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($enrollments)): ?>
                  <?php foreach ($enrollments as $enr): 
                    // Prepare JSON payload for the Dossier Modal
                    $studentData = [
                        'id' => $enr['id'],
                        'enrollment_id' => $enr['enrollment_id'],
                        'student_id' => $enr['student_id'] ?: 'N/A',
                        'student_name' => $enr['student_name'],
                        'student_email' => $enr['student_email'],
                        'student_phone' => $enr['student_phone'],
                        'course_name' => $enr['course_name'],
                        'course_slug' => $enr['course_slug'],
                        'fee_amount' => number_format((float)$enr['fee_amount'], 2),
                        'payment_status' => $enr['payment_status'],
                        'payment_method' => $enr['payment_method'],
                        'razorpay_payment_id' => $enr['razorpay_payment_id'] ?: 'N/A',
                        'razorpay_order_id' => $enr['razorpay_order_id'] ?: 'N/A',
                        'enrolled_at' => date('d M, Y h:i A', strtotime($enr['enrolled_at'])),
                        'qualification' => !empty($enr['student_qualification']) ? $enr['student_qualification'] : 'Not Provided',
                        'aadhaar_number' => !empty($enr['student_aadhaar']) ? $enr['student_aadhaar'] : 'Not Provided',
                        'application_id' => !empty($enr['application_id']) ? $enr['application_id'] : '',
                        'admission_status' => !empty($enr['admission_status']) ? $enr['admission_status'] : '',
                        'photo_doc' => !empty($enr['student_avatar']) ? $enr['student_avatar'] : (!empty($enr['photo_doc']) ? $enr['photo_doc'] : ''),
                        'aadhaar_doc' => !empty($enr['aadhaar_doc']) ? $enr['aadhaar_doc'] : '',
                        'marksheet_doc' => !empty($enr['marksheet_doc']) ? $enr['marksheet_doc'] : ''
                    ];
                    $jsonPayload = htmlspecialchars(json_encode($studentData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8');
                  ?>
                    <tr>
                      <!-- 1. Student / Candidate (Avatar + Name + Contact) -->
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <?php 
                            $avatarImg = !empty($enr['student_avatar']) ? $enr['student_avatar'] : (!empty($enr['photo_doc']) ? $enr['photo_doc'] : '');
                          ?>
                          <?php if (!empty($avatarImg) && file_exists(__DIR__ . '/../' . $avatarImg)): ?>
                            <img src="../<?= htmlspecialchars($avatarImg) ?>" alt="Photo" class="student-avatar-thumb" onclick='openStudentDossier(<?= $jsonPayload ?>)' title="Click to view Dossier">
                          <?php else: ?>
                            <span class="student-avatar-thumb" onclick='openStudentDossier(<?= $jsonPayload ?>)' title="Click to view Dossier">
                              <?= strtoupper(substr($enr['student_name'], 0, 2)) ?>
                            </span>
                          <?php endif; ?>
                          <div>
                            <div>
                              <button type="button" class="student-btn-link" onclick='openStudentDossier(<?= $jsonPayload ?>)' title="View Complete Student Dossier">
                                <?= htmlspecialchars($enr['student_name']) ?>
                              </button>
                            </div>
                            <div class="text-light-contrast">
                              <i class="fas fa-envelope text-warning me-1"></i><?= htmlspecialchars($enr['student_email']) ?>
                              &bull; <i class="fas fa-phone text-success ms-1 me-1"></i><?= htmlspecialchars($enr['student_phone']) ?>
                            </div>
                          </div>
                        </div>
                      </td>

                      <!-- 2. Enrollment & Student ID -->
                      <td>
                        <div>
                          <span class="enrollment-badge"><?= htmlspecialchars($enr['enrollment_id']) ?></span>
                        </div>
                        <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                          <?php if (!empty($enr['student_id'])): ?>
                            <span class="stu-id-badge" onclick='openStudentDossier(<?= $jsonPayload ?>)' title="Click to view Dossier">
                              ID: <?= htmlspecialchars($enr['student_id']) ?>
                            </span>
                          <?php endif; ?>
                          <?php if (!empty($enr['application_id'])): ?>
                            <span class="app-id-badge-sm" title="Linked Admission Application ID">
                              App: <?= htmlspecialchars($enr['application_id']) ?>
                            </span>
                          <?php endif; ?>
                        </div>
                      </td>

                      <!-- 3. Enrolled Course -->
                      <td>
                        <div class="fw-bold text-white mb-1" style="font-size: 0.88rem;">
                          <?= htmlspecialchars($enr['course_name']) ?>
                        </div>
                        <div class="text-light-contrast">
                          <i class="far fa-calendar-alt text-warning me-1"></i><?= date('d M, Y', strtotime($enr['enrolled_at'])) ?>
                          &bull; <small class="text-muted"><?= date('h:i A', strtotime($enr['enrolled_at'])) ?></small>
                        </div>
                      </td>

                      <!-- 4. Tuition Fee -->
                      <td>
                        <div class="d-flex align-items-center gap-1">
                          <span class="text-success fw-bold font-monospace" style="font-size: 0.95rem;">
                            ₹<?= number_format((float)$enr['fee_amount'], 2) ?>
                          </span>
                        </div>
                        <span class="badge bg-success text-white px-2 py-0 mt-1" style="font-size: 0.68rem; font-weight: 800;">
                          PAID
                        </span>
                      </td>

                      <!-- 5. Payment Gateway -->
                      <td>
                        <div>
                          <span class="badge bg-dark border border-secondary text-white" style="font-size: 0.72rem;">
                            <i class="fas fa-bolt text-warning me-1"></i><?= htmlspecialchars($enr['payment_method']) ?>
                          </span>
                        </div>
                        <div class="font-monospace mt-1" style="font-size: 0.72rem; color: #38bdf8;">
                          <?= htmlspecialchars($enr['razorpay_payment_id'] ?: 'Direct') ?>
                        </div>
                      </td>

                      <!-- 6. Compact Actions -->
                      <td>
                        <div class="d-flex align-items-center gap-1">
                          <!-- Open Student Dossier Modal Button -->
                          <button type="button" class="btn btn-outline-info btn-sm px-2 py-1" onclick='openStudentDossier(<?= $jsonPayload ?>)' title="View Student Profile & Dossier" style="border-radius: 6px; font-size: 0.78rem;">
                            <i class="fas fa-id-card me-1"></i> Details
                          </button>

                          <!-- Official Receipt Link -->
                          <a href="../enrollment-receipt.php?id=<?= urlencode($enr['enrollment_id']) ?>" target="_blank" class="btn btn-outline-warning btn-sm px-2 py-1" title="View & Print Official Receipt" style="border-radius: 6px; font-size: 0.78rem;">
                            <i class="fas fa-file-invoice me-1"></i> Receipt
                          </a>

                          <!-- Delete Action Form -->
                          <form method="POST" action="enrollments.php" class="d-inline" onsubmit="return confirm('Permanently delete enrollment record <?= htmlspecialchars($enr['enrollment_id']) ?> for <?= htmlspecialchars($enr['student_name']) ?>?');">
                            <input type="hidden" name="action" value="delete_enrollment">
                            <input type="hidden" name="enrollment_id" value="<?= $enr['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm px-2 py-1" title="Delete Enrollment" style="border-radius: 6px; font-size: 0.78rem;">
                              <i class="fas fa-trash-alt"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                      <i class="fas fa-folder-open fa-2x mb-3 d-block text-secondary"></i>
                      No course enrollments match the selected filters.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- COMPLETE STUDENT ENROLLMENT DOSSIER MODAL -->
  <div class="modal fade" id="studentDossierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content" style="background: #08112a !important; border: 1.5px solid #223565 !important;">
        
        <!-- Modal Header -->
        <div class="modal-header" style="background: #050b1d; border-bottom: 1.5px solid #223565; padding: 16px 22px;">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-warning text-dark p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 18px;">
              <i class="fas fa-user-graduate"></i>
            </div>
            <div>
              <h5 class="modal-title font-monospace text-white fw-bold mb-0" style="font-size: 1.12rem;">
                Student Enrollment Dossier & Profile
              </h5>
              <div style="font-size: 0.78rem; color: #94a3b8;">
                Skill Grow Institution &bull; Verified Academic Enrollment Records
              </div>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body p-4" style="background: #08112a;">
          <div class="row g-4">
            
            <!-- LEFT COLUMN: STUDENT SUMMARY & PERSONAL INFO -->
            <div class="col-lg-5 border-end border-secondary pe-lg-4">
              
              <!-- Student Profile Card Box -->
              <div class="p-3 rounded-3 mb-3 text-center" style="background: #04091a; border: 1.5px solid #1a2a50;">
                <div id="modalAvatarBox" class="mb-2 d-flex justify-content-center">
                  <img id="modalPhotoImg" src="" alt="Student Photo" class="img-fluid rounded-3 border border-warning" style="max-height: 140px; width: 120px; object-fit: cover; display: none;">
                  <div id="modalInitialsAvatar" class="student-avatar-initials">STU</div>
                </div>
                
                <h3 id="modalStudentName" class="text-white fw-bold mb-1" style="font-family: var(--font-heading); font-size: 1.3rem;"></h3>
                
                <div class="d-flex justify-content-center gap-1 flex-wrap mt-2">
                  <span id="modalEnrollmentId" class="badge bg-warning text-dark font-monospace px-2 py-1" style="font-size: 0.76rem;"></span>
                  <span id="modalStudentId" class="badge bg-info text-dark font-monospace px-2 py-1" style="font-size: 0.76rem;"></span>
                  <span id="modalAppIdBadge" class="badge bg-success text-white font-monospace px-2 py-1" style="font-size: 0.76rem; display: none;"></span>
                </div>

                <div class="mt-2">
                  <span class="badge bg-success px-3 py-1 fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">ACTIVE ENROLLED STUDENT</span>
                </div>
              </div>

              <!-- Personal Credentials Grid -->
              <div class="p-3 rounded-3" style="background: #04091a; border: 1.5px solid #1a2a50;">
                <h6 class="text-warning fw-bold mb-3 d-flex align-items-center gap-2" style="font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.8px;">
                  <i class="fas fa-id-badge text-warning"></i> Student Identity & Contact
                </h6>
                
                <div class="row g-2">
                  <div class="col-12">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-envelope"></i> Email Address</div>
                      <div class="dossier-val font-monospace">
                        <a id="modalEmailLink" href="" class="text-info text-decoration-none" target="_blank"></a>
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-phone-alt"></i> Mobile Phone</div>
                      <div class="dossier-val font-monospace">
                        <a id="modalPhoneLink" href="" class="text-success text-decoration-none"></a>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-certificate"></i> Qualification</div>
                      <div id="modalQual" class="dossier-val text-white"></div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-id-card"></i> Aadhaar Number</div>
                      <div id="modalAadhaar" class="dossier-val font-monospace text-white"></div>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- RIGHT COLUMN: ENROLLMENT LEDGER & VERIFICATION DOCS -->
            <div class="col-lg-7">
              
              <!-- Enrollment Program & Fee Ledger -->
              <div class="p-3 rounded-3 mb-4" style="background: #04091a; border: 1.5px solid #1a2a50;">
                <h6 class="text-warning fw-bold mb-3 d-flex align-items-center gap-2" style="font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.8px;">
                  <i class="fas fa-file-invoice-dollar text-warning"></i> Enrolled Program & Fee Ledger
                </h6>

                <div class="row g-2">
                  <div class="col-12">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-book-reader"></i> Enrolled Academic Course</div>
                      <div id="modalCourseTitle" class="dossier-val text-warning fs-6"></div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-indian-rupee-sign"></i> Tuition Fee Amount</div>
                      <div id="modalFee" class="dossier-val text-success font-monospace fs-6"></div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-check-circle"></i> Payment Status</div>
                      <div class="dossier-val text-success">PAID (CONFIRMED)</div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-credit-card"></i> Payment Gateway</div>
                      <div id="modalPaymentMethod" class="dossier-val text-white"></div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-calendar-check"></i> Enrolled Timestamp</div>
                      <div id="modalEnrolledAt" class="dossier-val text-white" style="font-size: 0.82rem;"></div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-receipt"></i> Razorpay Payment ID</div>
                      <div id="modalTxId" class="dossier-val font-monospace text-info" style="font-size: 0.82rem;"></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Identity & Verification Documents (If Uploaded) -->
              <div>
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                  <h6 class="text-white fw-bold mb-0" style="font-family: var(--font-heading); font-size: 0.95rem;">
                    <i class="fas fa-folder-open text-warning me-2"></i> Verification Documents Preview
                  </h6>
                  <span id="modalDocStatusBadge" class="badge bg-dark border border-secondary text-white-50 px-2 py-1" style="font-size: 0.72rem;">
                    Dossier Repository
                  </span>
                </div>

                <!-- Three Preview Cards Container -->
                <div class="row g-2" id="docsContainer">
                  
                  <!-- 1. PASSPORT PHOTO PREVIEW CARD -->
                  <div class="col-md-4">
                    <div class="doc-preview-card">
                      <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <strong class="text-white" style="font-size: 0.80rem;">
                            <i class="fas fa-camera text-primary me-1"></i> Photo
                          </strong>
                          <span id="badgePhoto" class="doc-badge-pill bg-success text-white">Uploaded</span>
                        </div>
                        <div class="doc-preview-thumb-box">
                          <img id="imgDocPhoto" src="" alt="Photo" onclick="window.open(this.src, '_blank')" title="Click to open full photo">
                          <div id="emptyPhoto" class="empty-placeholder" style="display: none;">
                            <i class="fas fa-image fa-2x mb-1 text-secondary"></i>
                            <div style="font-size: 0.72rem;">No Photo</div>
                          </div>
                        </div>
                      </div>
                      <a id="btnPhotoOpen" href="#" target="_blank" class="btn btn-outline-light btn-sm w-100 py-1" style="font-size: 0.75rem;">
                        <i class="fas fa-external-link-alt me-1"></i> View Full
                      </a>
                    </div>
                  </div>

                  <!-- 2. AADHAAR CARD PREVIEW CARD -->
                  <div class="col-md-4">
                    <div class="doc-preview-card">
                      <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <strong class="text-white" style="font-size: 0.80rem;">
                            <i class="fas fa-address-card text-warning me-1"></i> Aadhaar
                          </strong>
                          <span id="badgeAadhaar" class="doc-badge-pill bg-warning text-dark">Uploaded</span>
                        </div>
                        <div class="doc-preview-thumb-box">
                          <img id="imgDocAadhaar" src="" alt="Aadhaar Document" onclick="window.open(this.src, '_blank')" title="Click to open Aadhaar">
                          <div id="pdfDocAadhaar" class="pdf-placeholder" style="display: none;">
                            <i class="fas fa-file-pdf fa-2x mb-1"></i>
                            <div class="fw-bold" style="font-size: 0.75rem;">PDF Document</div>
                          </div>
                          <div id="emptyAadhaar" class="empty-placeholder" style="display: none;">
                            <i class="fas fa-file-excel fa-2x mb-1 text-secondary"></i>
                            <div style="font-size: 0.72rem;">No Aadhaar</div>
                          </div>
                        </div>
                      </div>
                      <a id="btnAadhaarOpen" href="#" target="_blank" class="btn btn-outline-warning btn-sm w-100 py-1" style="font-size: 0.75rem;">
                        <i class="fas fa-external-link-alt me-1"></i> View Full
                      </a>
                    </div>
                  </div>

                  <!-- 3. MARKSHEET / CERTIFICATE PREVIEW CARD -->
                  <div class="col-md-4">
                    <div class="doc-preview-card">
                      <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <strong class="text-white" style="font-size: 0.80rem;">
                            <i class="fas fa-graduation-cap text-success me-1"></i> Marksheet
                          </strong>
                          <span id="badgeMarksheet" class="doc-badge-pill bg-success text-white">Uploaded</span>
                        </div>
                        <div class="doc-preview-thumb-box">
                          <img id="imgDocMarksheet" src="" alt="Marksheet" onclick="window.open(this.src, '_blank')" title="Click to open marksheet">
                          <div id="pdfDocMarksheet" class="pdf-placeholder" style="display: none;">
                            <i class="fas fa-file-pdf fa-2x mb-1"></i>
                            <div class="fw-bold" style="font-size: 0.75rem;">PDF Marksheet</div>
                          </div>
                          <div id="emptyMarksheet" class="empty-placeholder" style="display: none;">
                            <i class="fas fa-file-excel fa-2x mb-1 text-secondary"></i>
                            <div style="font-size: 0.72rem;">No Marksheet</div>
                          </div>
                        </div>
                      </div>
                      <a id="btnMarksheetOpen" href="#" target="_blank" class="btn btn-outline-success btn-sm w-100 py-1" style="font-size: 0.75rem;">
                        <i class="fas fa-external-link-alt me-1"></i> View Full
                      </a>
                    </div>
                  </div>

                </div>

                <!-- Fallback Notice When No Docs Uploaded -->
                <div id="noDocsNotice" class="p-3 rounded-3 mt-2 text-center" style="background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255, 255, 255, 0.12); display: none;">
                  <i class="fas fa-info-circle text-warning fa-lg mb-2 d-block"></i>
                  <div class="text-white fw-bold" style="font-size: 0.85rem;">Enrolled Directly via Online Portal</div>
                  <div class="text-muted" style="font-size: 0.78rem;">Candidate identity and payment authenticated via Razorpay payment gateway. Physical documents were not uploaded for this direct course purchase.</div>
                </div>

              </div>

            </div>

          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="modal-footer" style="background: #050b1d; border-top: 1.5px solid #223565; padding: 14px 22px;">
          <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
            <div>
              <a id="modalAdmLink" href="#" target="_blank" class="btn btn-outline-info btn-sm px-3" style="display: none;">
                <i class="fas fa-id-badge me-1"></i> View in Admissions Portal
              </a>
            </div>

            <div class="d-flex gap-2">
              <a id="modalReceiptLink" href="#" target="_blank" class="btn btn-warning btn-sm px-3 fw-bold">
                <i class="fas fa-file-invoice me-1"></i> Print Official Receipt
              </a>
              <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                Close
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/admin.js"></script>

  <script>
    // Open Comprehensive Student Dossier Modal
    function openStudentDossier(data) {
      if (!data) return;

      // 1. Text Fields
      document.getElementById('modalStudentName').innerText = data.student_name || 'Student';
      document.getElementById('modalEnrollmentId').innerText = data.enrollment_id || '';
      document.getElementById('modalStudentId').innerText = (data.student_id && data.student_id !== 'N/A') ? data.student_id : 'STU-ONLINE';
      
      const appIdBadge = document.getElementById('modalAppIdBadge');
      if (data.application_id) {
        appIdBadge.innerText = 'App: ' + data.application_id;
        appIdBadge.style.display = 'inline-block';
      } else {
        appIdBadge.style.display = 'none';
      }

      // Contact & Identity
      const emailLink = document.getElementById('modalEmailLink');
      emailLink.href = 'mailto:' + (data.student_email || '');
      emailLink.innerText = data.student_email || 'N/A';

      const phoneLink = document.getElementById('modalPhoneLink');
      phoneLink.href = 'tel:' + (data.student_phone || '');
      phoneLink.innerText = data.student_phone || 'N/A';

      document.getElementById('modalQual').innerText = data.qualification || 'Not Provided';
      document.getElementById('modalAadhaar').innerText = data.aadhaar_number || 'Not Provided';

      // Course & Ledger
      document.getElementById('modalCourseTitle').innerText = data.course_name + (data.course_slug ? ' (' + data.course_slug + ')' : '');
      document.getElementById('modalFee').innerText = '₹' + data.fee_amount;
      document.getElementById('modalPaymentMethod').innerText = data.payment_method || 'Razorpay';
      document.getElementById('modalEnrolledAt').innerText = data.enrolled_at || 'N/A';
      document.getElementById('modalTxId').innerText = (data.razorpay_payment_id && data.razorpay_payment_id !== 'N/A') ? data.razorpay_payment_id : 'Direct Verified';

      // Avatar & Photo Preview
      const photoImg = document.getElementById('modalPhotoImg');
      const initialsAvatar = document.getElementById('modalInitialsAvatar');

      if (data.photo_doc && data.photo_doc.trim() !== '') {
        let photoSrc = data.photo_doc;
        if (!photoSrc.startsWith('/') && !photoSrc.startsWith('http')) {
          photoSrc = '../' + photoSrc;
        }
        photoImg.src = photoSrc;
        photoImg.style.display = 'block';
        initialsAvatar.style.display = 'none';
      } else {
        photoImg.style.display = 'none';
        initialsAvatar.style.display = 'flex';
        // Compute Initials
        const names = (data.student_name || 'STU').trim().split(' ');
        let initials = names[0].charAt(0).toUpperCase();
        if (names.length > 1) {
          initials += names[names.length - 1].charAt(0).toUpperCase();
        }
        initialsAvatar.innerText = initials;
      }

      // Documents Previews
      const docsContainer = document.getElementById('docsContainer');
      const noDocsNotice = document.getElementById('noDocsNotice');
      const hasAnyDoc = (data.photo_doc && data.photo_doc.trim() !== '') || 
                        (data.aadhaar_doc && data.aadhaar_doc.trim() !== '') || 
                        (data.marksheet_doc && data.marksheet_doc.trim() !== '');

      if (hasAnyDoc) {
        docsContainer.style.display = 'flex';
        noDocsNotice.style.display = 'none';

        // 1. Photo
        const imgDocPhoto = document.getElementById('imgDocPhoto');
        const emptyPhoto = document.getElementById('emptyPhoto');
        const btnPhotoOpen = document.getElementById('btnPhotoOpen');
        const badgePhoto = document.getElementById('badgePhoto');

        if (data.photo_doc && data.photo_doc.trim() !== '') {
          let pSrc = data.photo_doc.startsWith('/') || data.photo_doc.startsWith('http') ? data.photo_doc : '../' + data.photo_doc;
          imgDocPhoto.src = pSrc;
          imgDocPhoto.style.display = 'block';
          emptyPhoto.style.display = 'none';
          btnPhotoOpen.href = pSrc;
          btnPhotoOpen.classList.remove('disabled');
          badgePhoto.className = 'doc-badge-pill bg-success text-white';
          badgePhoto.innerText = 'Uploaded';
        } else {
          imgDocPhoto.style.display = 'none';
          emptyPhoto.style.display = 'block';
          btnPhotoOpen.href = '#';
          btnPhotoOpen.classList.add('disabled');
          badgePhoto.className = 'doc-badge-pill bg-secondary text-white';
          badgePhoto.innerText = 'Not Uploaded';
        }

        // 2. Aadhaar
        const imgDocAadhaar = document.getElementById('imgDocAadhaar');
        const pdfDocAadhaar = document.getElementById('pdfDocAadhaar');
        const emptyAadhaar = document.getElementById('emptyAadhaar');
        const btnAadhaarOpen = document.getElementById('btnAadhaarOpen');
        const badgeAadhaar = document.getElementById('badgeAadhaar');

        if (data.aadhaar_doc && data.aadhaar_doc.trim() !== '') {
          let aSrc = data.aadhaar_doc.startsWith('/') || data.aadhaar_doc.startsWith('http') ? data.aadhaar_doc : '../' + data.aadhaar_doc;
          const isPdf = aSrc.toLowerCase().endsWith('.pdf');
          btnAadhaarOpen.href = aSrc;
          btnAadhaarOpen.classList.remove('disabled');
          badgeAadhaar.className = 'doc-badge-pill bg-warning text-dark';
          badgeAadhaar.innerText = 'Uploaded';
          emptyAadhaar.style.display = 'none';

          if (isPdf) {
            imgDocAadhaar.style.display = 'none';
            pdfDocAadhaar.style.display = 'block';
          } else {
            imgDocAadhaar.src = aSrc;
            imgDocAadhaar.style.display = 'block';
            pdfDocAadhaar.style.display = 'none';
          }
        } else {
          imgDocAadhaar.style.display = 'none';
          pdfDocAadhaar.style.display = 'none';
          emptyAadhaar.style.display = 'block';
          btnAadhaarOpen.href = '#';
          btnAadhaarOpen.classList.add('disabled');
          badgeAadhaar.className = 'doc-badge-pill bg-secondary text-white';
          badgeAadhaar.innerText = 'Not Uploaded';
        }

        // 3. Marksheet
        const imgDocMarksheet = document.getElementById('imgDocMarksheet');
        const pdfDocMarksheet = document.getElementById('pdfDocMarksheet');
        const emptyMarksheet = document.getElementById('emptyMarksheet');
        const btnMarksheetOpen = document.getElementById('btnMarksheetOpen');
        const badgeMarksheet = document.getElementById('badgeMarksheet');

        if (data.marksheet_doc && data.marksheet_doc.trim() !== '') {
          let mSrc = data.marksheet_doc.startsWith('/') || data.marksheet_doc.startsWith('http') ? data.marksheet_doc : '../' + data.marksheet_doc;
          const isPdf = mSrc.toLowerCase().endsWith('.pdf');
          btnMarksheetOpen.href = mSrc;
          btnMarksheetOpen.classList.remove('disabled');
          badgeMarksheet.className = 'doc-badge-pill bg-success text-white';
          badgeMarksheet.innerText = 'Uploaded';
          emptyMarksheet.style.display = 'none';

          if (isPdf) {
            imgDocMarksheet.style.display = 'none';
            pdfDocMarksheet.style.display = 'block';
          } else {
            imgDocMarksheet.src = mSrc;
            imgDocMarksheet.style.display = 'block';
            pdfDocMarksheet.style.display = 'none';
          }
        } else {
          imgDocMarksheet.style.display = 'none';
          pdfDocMarksheet.style.display = 'none';
          emptyMarksheet.style.display = 'block';
          btnMarksheetOpen.href = '#';
          btnMarksheetOpen.classList.add('disabled');
          badgeMarksheet.className = 'doc-badge-pill bg-secondary text-white';
          badgeMarksheet.innerText = 'Not Uploaded';
        }

      } else {
        docsContainer.style.display = 'none';
        noDocsNotice.style.display = 'block';
      }

      // Action Links
      document.getElementById('modalReceiptLink').href = '../enrollment-receipt.php?id=' + encodeURIComponent(data.enrollment_id);

      const admLink = document.getElementById('modalAdmLink');
      if (data.application_id || (data.student_id && data.student_id !== 'N/A')) {
        admLink.href = 'admissions.php?search=' + encodeURIComponent(data.application_id || data.student_id);
        admLink.style.display = 'inline-block';
      } else {
        admLink.style.display = 'none';
      }

      // Show Modal
      const modal = new bootstrap.Modal(document.getElementById('studentDossierModal'));
      modal.show();
    }
  </script>
</body>
</html>