<?php
// Registered Candidates & Admissions Management - Skill Grow Institution
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$msg = '';
$msgType = '';

// Handle Status Update via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $admId = (int)($_POST['admission_id'] ?? 0);
    $newStatus = trim($_POST['status'] ?? '');
    
    $allowed = ['verified', 'under_review', 'approved', 'rejected'];
    if ($admId > 0 && in_array($newStatus, $allowed, true)) {
        try {
            $stmt = $pdo->prepare("UPDATE admissions SET admission_status = :st WHERE id = :id");
            $stmt->execute(['st' => $newStatus, 'id' => $admId]);
            $msg = "Admission status successfully updated to " . strtoupper($newStatus);
            $msgType = "success";
        } catch (PDOException $e) {
            $msg = "Database error: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// Handle Delete Admission via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_admission') {
    $admId = (int)($_POST['admission_id'] ?? 0);
    if ($admId > 0) {
        try {
            // Get student info for message
            $stmt = $pdo->prepare("SELECT application_id, full_name FROM admissions WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $admId]);
            $target = $stmt->fetch();

            if ($target) {
                // Delete record from admissions table
                $dStmt = $pdo->prepare("DELETE FROM admissions WHERE id = :id");
                $dStmt->execute(['id' => $admId]);

                $msg = "Admission record of '" . htmlspecialchars($target['full_name']) . "' (App ID: " . htmlspecialchars($target['application_id']) . ") deleted successfully.";
                $msgType = "warning";
            }
        } catch (PDOException $e) {
            $msg = "Database error deleting admission: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// Search & Filter
$search = trim($_GET['search'] ?? '');
$filterStatus = trim($_GET['status'] ?? '');
$filterCourse = trim($_GET['course'] ?? '');

$sql = "SELECT * FROM admissions WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (full_name LIKE :s1 OR email LIKE :s2 OR phone LIKE :s3 OR application_id LIKE :s4 OR student_id LIKE :s5 OR aadhaar_number LIKE :s6)";
    $params['s1'] = "%$search%";
    $params['s2'] = "%$search%";
    $params['s3'] = "%$search%";
    $params['s4'] = "%$search%";
    $params['s5'] = "%$search%";
    $params['s6'] = "%$search%";
}

if (!empty($filterStatus)) {
    $sql .= " AND admission_status = :st";
    $params['st'] = $filterStatus;
}

if (!empty($filterCourse)) {
    $sql .= " AND course_name = :cname";
    $params['cname'] = $filterCourse;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$admissions = $stmt->fetchAll();

// Statistics
$statAdm = [
    'total_registered' => $pdo->query("SELECT COUNT(*) FROM admissions")->fetchColumn() ?: 0,
    'total_verified'   => $pdo->query("SELECT COUNT(*) FROM admissions WHERE admission_status = 'verified'")->fetchColumn() ?: 0,
    'total_review'     => $pdo->query("SELECT COUNT(*) FROM admissions WHERE admission_status = 'under_review'")->fetchColumn() ?: 0,
    'total_fee'        => $pdo->query("SELECT SUM(fee_amount) FROM admissions WHERE payment_status = 'paid'")->fetchColumn() ?: 0,
];

// Lookup linked course enrollments
$linkedEnrollmentsMap = [];
try {
    $enrLookup = $pdo->query("SELECT enrollment_id, admission_id_linked, fee_amount, reg_fee_deducted, course_name FROM course_enrollments WHERE payment_status = 'paid' AND admission_id_linked IS NOT NULL")->fetchAll();
    foreach ($enrLookup as $erow) {
        if (!empty($erow['admission_id_linked'])) {
            $linkedEnrollmentsMap[$erow['admission_id_linked']] = $erow;
        }
    }
} catch (Exception $e) {}

// Active courses list for filter
$coursesList = $pdo->query("SELECT title FROM courses ORDER BY title ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registered Candidates & Admissions | SG Admin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Central Unified Admin Stylesheet -->
  <link rel="stylesheet" href="assets/css/admin.css">

  <style>
    /* Minimal & Sleek Executive Cards */
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

    .text-light-contrast {
      color: #cbd5e1 !important;
      font-size: 0.80rem;
    }

    .status-select-sm {
      background: #09122c !important;
      border: 1px solid #2a3c6d !important;
      color: #ffffff !important;
      font-size: 0.78rem;
      font-weight: 700;
      padding: 4px 8px;
      border-radius: 6px;
      width: auto;
      display: inline-block;
      cursor: pointer;
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
              Registered Candidates & Admissions
            </h1>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
                <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Admin</a></li>
                <li class="breadcrumb-item active text-warning">Admissions</li>
              </ol>
            </nav>
          </div>
        </div>

        <div class="d-flex gap-2">
          <a href="../register.php" target="_blank" class="btn btn-outline-light btn-sm px-3 py-2 rounded-3">
            <i class="fas fa-external-link-alt me-1"></i> Live Registration Form
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
                <i class="fas fa-user-graduate"></i>
              </div>
              <div>
                <div class="stat-metric-title">Total Registrations</div>
                <div class="text-white stat-metric-val"><?= number_format($statAdm['total_registered']) ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-metric-card" style="border-left: 4px solid #10b981;">
              <div class="stat-metric-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                <i class="fas fa-id-badge"></i>
              </div>
              <div>
                <div class="stat-metric-title text-success">Verified Students</div>
                <div class="text-success stat-metric-val"><?= number_format($statAdm['total_verified']) ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-metric-card" style="border-left: 4px solid #f59e0b;">
              <div class="stat-metric-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                <i class="fas fa-clock"></i>
              </div>
              <div>
                <div class="stat-metric-title text-warning">Under Review</div>
                <div class="text-warning stat-metric-val"><?= number_format($statAdm['total_review']) ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-metric-card" style="border-left: 4px solid #38bdf8;">
              <div class="stat-metric-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                <i class="fas fa-indian-rupee-sign"></i>
              </div>
              <div>
                <div class="stat-metric-title" style="color: #38bdf8;">Registration Fees</div>
                <div class="stat-metric-val" style="color: #38bdf8;">₹<?= number_format((float)$statAdm['total_fee'], 0) ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bento-card mb-4 p-3" style="border-radius: 12px;">
          <form method="GET" action="admissions.php" class="row g-2 align-items-center">
            <div class="col-md-5">
              <div class="input-group input-group-sm">
                <span class="input-group-text admin-search-icon-box"><i class="fas fa-search" style="color: #fbbf24;"></i></span>
                <input type="text" class="form-control form-control-sm admin-search-input" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search student name, email, phone, App ID, Student ID, Aadhaar...">
              </div>
            </div>

            <div class="col-md-3">
              <select class="form-select form-select-sm admin-search-select" name="course">
                <option value="">All Applied Programs</option>
                <?php foreach ($coursesList as $cl): ?>
                  <option value="<?= htmlspecialchars($cl['title']) ?>" <?= ($filterCourse === $cl['title']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cl['title']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-2">
              <select class="form-select form-select-sm admin-search-select" name="status">
                <option value="">All Statuses</option>
                <option value="verified" <?= ($filterStatus === 'verified') ? 'selected' : '' ?>>Verified</option>
                <option value="under_review" <?= ($filterStatus === 'under_review') ? 'selected' : '' ?>>Under Review</option>
                <option value="approved" <?= ($filterStatus === 'approved') ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= ($filterStatus === 'rejected') ? 'selected' : '' ?>>Rejected</option>
              </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
              <button type="submit" class="btn btn-warning btn-sm fw-bold flex-grow-1">
                <i class="fas fa-filter me-1"></i> Filter
              </button>
              <?php if (!empty($search) || !empty($filterStatus) || !empty($filterCourse)): ?>
                <a href="admissions.php" class="btn btn-outline-light btn-sm px-3">Reset</a>
              <?php endif; ?>
            </div>
          </form>
        </div>

        <!-- Main Admissions Table (Clean, Proper Basic Details) -->
        <div class="bento-card" style="border-radius: 12px; overflow: hidden;">
          <div class="bento-header d-flex justify-content-between align-items-center py-3 px-4">
            <h2 class="bento-title mb-0" style="font-size: 1.05rem;">
              <i class="fas fa-user-check text-warning me-2"></i> All Registered Candidates (<?= count($admissions) ?>)
            </h2>
            <span class="badge bg-dark border border-secondary text-white-50 font-monospace" style="font-size: 0.75rem;">
              Click Candidate or Details for Dossier
            </span>
          </div>

          <div class="table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Candidate</th>
                  <th>App & Student ID</th>
                  <th>Applied Course</th>
                  <th>Fee Paid</th>
                  <th>Admission Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($admissions)): ?>
                  <?php foreach ($admissions as $adm): 
                    $jsonPayload = htmlspecialchars(json_encode($adm, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8');
                  ?>
                    <tr>
                      <!-- 1. Candidate Info (Compact Basic Details) -->
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <?php if (!empty($adm['photo_doc']) && file_exists(__DIR__ . '/../' . $adm['photo_doc'])): ?>
                            <img src="../<?= htmlspecialchars($adm['photo_doc']) ?>" alt="Photo" class="student-avatar-thumb" onclick='openDossierModal(<?= $jsonPayload ?>)' title="Click to view Dossier">
                          <?php else: ?>
                            <span class="student-avatar-thumb" onclick='openDossierModal(<?= $jsonPayload ?>)' title="Click to view Dossier">
                              <?= strtoupper(substr($adm['full_name'], 0, 2)) ?>
                            </span>
                          <?php endif; ?>
                          <div>
                            <div>
                              <button type="button" class="student-btn-link" onclick='openDossierModal(<?= $jsonPayload ?>)' title="View Complete Student Dossier">
                                <?= htmlspecialchars($adm['full_name']) ?>
                              </button>
                            </div>
                            <div class="text-light-contrast">
                              <i class="fas fa-envelope text-warning me-1"></i><?= htmlspecialchars($adm['email']) ?>
                              &bull; <i class="fas fa-phone text-success ms-1 me-1"></i><?= htmlspecialchars($adm['phone']) ?>
                            </div>
                          </div>
                        </div>
                      </td>

                      <!-- 2. App ID & Student ID -->
                      <td>
                        <div>
                          <span class="enrollment-badge"><?= htmlspecialchars($adm['application_id']) ?></span>
                        </div>
                        <div class="mt-1">
                          <?php if (!empty($adm['student_id'])): ?>
                            <span class="stu-id-badge" onclick='openDossierModal(<?= $jsonPayload ?>)' title="Click to view Dossier">
                              ID: <?= htmlspecialchars($adm['student_id']) ?>
                            </span>
                          <?php else: ?>
                            <span class="text-muted font-monospace" style="font-size: 0.72rem;">ID: Pending</span>
                          <?php endif; ?>
                        </div>
                      </td>

                      <!-- 3. Applied Course -->
                      <td>
                        <div class="fw-bold text-white mb-1" style="font-size: 0.88rem;">
                          <?= htmlspecialchars($adm['course_name']) ?>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                          <span class="text-light-contrast">
                            <i class="far fa-calendar-alt text-warning me-1"></i><?= date('d M, Y', strtotime($adm['created_at'])) ?>
                          </span>
                          <?php if (isset($linkedEnrollmentsMap[$adm['application_id']])): ?>
                            <?php $lEnr = $linkedEnrollmentsMap[$adm['application_id']]; ?>
                            <a href="enrollments.php?search=<?= urlencode($lEnr['enrollment_id']) ?>" class="badge bg-success bg-opacity-25 text-success border border-success text-decoration-none" style="font-size: 0.68rem;" title="View Course Enrollment">
                              <i class="fas fa-graduation-cap me-1"></i> Enrolled: <?= htmlspecialchars($lEnr['enrollment_id']) ?>
                            </a>
                          <?php else: ?>
                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning" style="font-size: 0.68rem;">
                              <i class="fas fa-clock me-1"></i> Enrollment Pending
                            </span>
                          <?php endif; ?>
                        </div>
                      </td>

                      <!-- 4. Fee Paid -->
                      <td>
                        <div class="d-flex align-items-center gap-1">
                          <span class="text-success fw-bold font-monospace" style="font-size: 0.95rem;">
                            ₹<?= number_format((float)$adm['fee_amount'], 2) ?>
                          </span>
                        </div>
                        <span class="badge bg-success text-white px-2 py-0 mt-1" style="font-size: 0.68rem; font-weight: 800;">
                          PAID
                        </span>
                      </td>

                      <!-- 5. Admission Status with Quick Select -->
                      <td>
                        <form method="POST" action="admissions.php" class="d-inline">
                          <input type="hidden" name="action" value="update_status">
                          <input type="hidden" name="admission_id" value="<?= $adm['id'] ?>">
                          <select name="status" class="status-select-sm" onchange="this.form.submit()">
                            <option value="verified" <?= ($adm['admission_status'] === 'verified') ? 'selected' : '' ?>>Verified</option>
                            <option value="under_review" <?= ($adm['admission_status'] === 'under_review') ? 'selected' : '' ?>>Under Review</option>
                            <option value="approved" <?= ($adm['admission_status'] === 'approved') ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= ($adm['admission_status'] === 'rejected') ? 'selected' : '' ?>>Rejected</option>
                          </select>
                        </form>
                      </td>

                      <!-- 6. Compact Actions Group -->
                      <td>
                        <div class="d-flex align-items-center gap-1">
                          <!-- Open Dossier Modal Button -->
                          <button type="button" class="btn btn-outline-info btn-sm px-2 py-1" onclick='openDossierModal(<?= $jsonPayload ?>)' title="View Complete Student Dossier & Documents" style="border-radius: 6px; font-size: 0.78rem;">
                            <i class="fas fa-id-card me-1"></i> Details
                          </button>
                          
                          <!-- Official Receipt Link -->
                          <a href="../download-receipt.php?app_id=<?= urlencode($adm['application_id']) ?>" target="_blank" class="btn btn-outline-warning btn-sm px-2 py-1" title="View Official Admission Receipt" style="border-radius: 6px; font-size: 0.78rem;">
                            <i class="fas fa-file-invoice me-1"></i> Receipt
                          </a>

                          <!-- Delete Action Form -->
                          <form method="POST" action="admissions.php" class="d-inline" onsubmit="return confirm('Permanently delete admission record of <?= htmlspecialchars($adm['full_name']) ?> (App ID: <?= htmlspecialchars($adm['application_id']) ?>)?');">
                            <input type="hidden" name="action" value="delete_admission">
                            <input type="hidden" name="admission_id" value="<?= $adm['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm px-2 py-1" title="Delete Admission Record" style="border-radius: 6px; font-size: 0.78rem;">
                              <i class="fas fa-trash-alt"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center py-5 text-white-50">
                      <i class="fas fa-user-slash fa-2x mb-3 d-block text-secondary"></i>
                      No student registration records match the criteria.
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

  <!-- COMPLETE STUDENT DOSSIER & DOCUMENT PREVIEW MODAL -->
  <div class="modal fade" id="dossierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content" style="background: #08112a !important; border: 1.5px solid #223565 !important;">
        
        <!-- Modal Header -->
        <div class="modal-header" style="background: #050b1d; border-bottom: 1.5px solid #223565; padding: 16px 22px;">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-warning text-dark p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 18px;">
              <i class="fas fa-id-card"></i>
            </div>
            <div>
              <h5 class="modal-title font-monospace text-white fw-bold mb-0" style="font-size: 1.12rem;">
                Candidate Admission Dossier & Verification Portal
              </h5>
              <div style="font-size: 0.78rem; color: #94a3b8;">
                Skill Grow Institution &bull; Verified Academic Credentials & Stored Documents
              </div>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body p-4" style="background: #08112a;">
          <div class="row g-4">
            
            <!-- LEFT COLUMN: CANDIDATE SUMMARY & HIGH-CONTRAST INFO -->
            <div class="col-lg-5 border-end border-secondary pe-lg-4">
              
              <!-- Student Profile Card Box -->
              <div class="p-3 rounded-3 mb-3 text-center" style="background: #04091a; border: 1.5px solid #1a2a50;">
                <div id="modalPhotoContainer" class="mb-2 d-flex justify-content-center">
                  <img id="modalPhoto" src="" alt="Student Photo" class="img-fluid rounded-3 border border-warning" style="max-height: 140px; width: 120px; object-fit: cover;">
                  <div id="modalInitialsAvatar" class="student-avatar-initials" style="display: none;">STU</div>
                </div>
                <h3 id="modalName" class="text-white fw-bold mb-1" style="font-family: var(--font-heading); font-size: 1.3rem;"></h3>
                <div class="d-flex justify-content-center gap-1 flex-wrap mt-2">
                  <span id="modalAppId" class="badge bg-warning text-dark font-monospace px-2 py-1" style="font-size: 0.76rem;"></span>
                  <span id="modalStuId" class="badge bg-info text-dark font-monospace px-2 py-1" style="font-size: 0.76rem;"></span>
                </div>
                <div id="modalStatusBadge" class="mt-2"></div>
              </div>

              <!-- High Contrast Personal Details Grid -->
              <div class="p-3 rounded-3" style="background: #04091a; border: 1.5px solid #1a2a50;">
                <h6 class="text-warning fw-bold mb-3 d-flex align-items-center gap-2" style="font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.8px;">
                  <i class="fas fa-user-check text-warning"></i> Personal & Admission Details
                </h6>
                
                <div class="row g-2">
                  <div class="col-12">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-graduation-cap"></i> Applied Academic Course</div>
                      <div id="modalCourse" class="dossier-val text-warning fs-6"></div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-envelope"></i> Registered Email Address</div>
                      <div class="dossier-val font-monospace">
                        <a id="modalEmailLink" href="" class="text-info text-decoration-none" target="_blank"></a>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-phone"></i> Mobile Number</div>
                      <div class="dossier-val font-monospace">
                        <a id="modalPhoneLink" href="" class="text-success text-decoration-none"></a>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-certificate"></i> Highest Qualification</div>
                      <div id="modalQual" class="dossier-val text-white"></div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-id-card"></i> Aadhaar Identity Number</div>
                      <div id="modalAadhaar" class="dossier-val font-monospace text-white"></div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-indian-rupee-sign"></i> Admission Fee Paid</div>
                      <div id="modalFee" class="dossier-val text-success font-monospace fs-6"></div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-receipt"></i> Razorpay Payment ID</div>
                      <div id="modalTxId" class="dossier-val font-monospace text-info" style="font-size: 0.82rem;"></div>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- RIGHT COLUMN: PROPER DOCUMENT PREVIEWS -->
            <div class="col-lg-7">
              <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h6 class="text-white fw-bold mb-0" style="font-family: var(--font-heading); font-size: 0.95rem;">
                  <i class="fas fa-file-shield text-warning me-2"></i> Verification Documents Preview
                </h6>
                <span class="badge bg-dark border border-secondary text-white-50 px-2 py-1" style="font-size: 0.72rem;">
                  <i class="fas fa-folder-open text-warning me-1"></i> Stored in Student Folder
                </span>
              </div>

              <div class="row g-2">
                
                <!-- 1. PASSPORT PHOTO PREVIEW CARD -->
                <div class="col-md-4">
                  <div class="doc-preview-card">
                    <div>
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-white" style="font-size: 0.80rem;">
                          <i class="fas fa-camera text-primary me-1"></i> Passport Photo
                        </strong>
                        <span id="badgePhoto" class="doc-badge-pill bg-success text-white">Uploaded</span>
                      </div>
                      <div class="doc-preview-thumb-box" id="boxPhotoPreview">
                        <img id="imgDocPhoto" src="" alt="Passport Photo" onclick="window.open(this.src, '_blank')" title="Click to open full photo">
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
                          <i class="fas fa-address-card text-warning me-1"></i> Aadhaar Card
                        </strong>
                        <span id="badgeAadhaar" class="doc-badge-pill bg-warning text-dark">Uploaded</span>
                      </div>
                      <div class="doc-preview-thumb-box" id="boxAadhaarPreview">
                        <img id="imgDocAadhaar" src="" alt="Aadhaar Card" onclick="window.open(this.src, '_blank')" title="Click to open Aadhaar Document">
                        <div id="pdfDocAadhaar" class="pdf-placeholder" style="display: none;">
                          <i class="fas fa-file-pdf fa-2x mb-1"></i>
                          <div class="fw-bold" style="font-size: 0.75rem;">PDF Document</div>
                        </div>
                        <div id="emptyAadhaar" class="empty-placeholder" style="display: none;">
                          <i class="fas fa-id-card fa-2x mb-1 text-secondary"></i>
                          <div style="font-size: 0.72rem;">No Aadhaar</div>
                        </div>
                      </div>
                    </div>
                    <a id="btnAadhaarOpen" href="#" target="_blank" class="btn btn-outline-warning btn-sm w-100 py-1" style="font-size: 0.75rem;">
                      <i class="fas fa-external-link-alt me-1"></i> View Full
                    </a>
                  </div>
                </div>

                <!-- 3. MARKSHEET PREVIEW CARD -->
                <div class="col-md-4">
                  <div class="doc-preview-card">
                    <div>
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-white" style="font-size: 0.80rem;">
                          <i class="fas fa-graduation-cap text-success me-1"></i> Marksheet / Degree
                        </strong>
                        <span id="badgeMarksheet" class="doc-badge-pill bg-success text-white">Uploaded</span>
                      </div>
                      <div class="doc-preview-thumb-box" id="boxMarksheetPreview">
                        <img id="imgDocMarksheet" src="" alt="Marksheet Document" onclick="window.open(this.src, '_blank')" title="Click to open Marksheet">
                        <div id="pdfDocMarksheet" class="pdf-placeholder" style="display: none;">
                          <i class="fas fa-file-pdf fa-2x mb-1"></i>
                          <div class="fw-bold" style="font-size: 0.75rem;">PDF Document</div>
                        </div>
                        <div id="emptyMarksheet" class="empty-placeholder" style="display: none;">
                          <i class="fas fa-file-alt fa-2x mb-1 text-secondary"></i>
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
            </div>

          </div>
        </div>

        <!-- Modal Footer: Receipt, Delete & Close -->
        <div class="modal-footer" style="background: #050b1d; border-top: 1.5px solid #223565; padding: 14px 22px;">
          <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
            
            <!-- Delete Candidate Button Inside Modal -->
            <form method="POST" action="admissions.php" id="modalDeleteForm" class="d-inline" onsubmit="return confirm('WARNING: Are you sure you want to permanently delete this candidate admission record? This action cannot be undone.');">
              <input type="hidden" name="action" value="delete_admission">
              <input type="hidden" name="admission_id" id="modalDeleteAdmId" value="0">
              <button type="submit" class="btn btn-outline-danger btn-sm px-3">
                <i class="fas fa-trash-alt me-1"></i> Delete Candidate Record
              </button>
            </form>

            <div class="d-flex gap-2">
              <a id="modalReceiptLink" href="#" target="_blank" class="btn btn-warning btn-sm px-3 fw-bold">
                <i class="fas fa-file-invoice me-1"></i> Print Official Admission Slip
              </a>
              <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Close</button>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/admin.js"></script>
  <script>
    function openDossierModal(data) {
      if (!data) return;

      // Set delete form admission id
      document.getElementById('modalDeleteAdmId').value = data.id;

      // Personal & Admission details
      document.getElementById('modalName').innerText = data.full_name || 'Candidate';
      document.getElementById('modalAppId').innerText = 'App: ' + (data.application_id || '');
      document.getElementById('modalStuId').innerText = data.student_id ? ('Student: ' + data.student_id) : 'Student: Pending';
      
      const emailLink = document.getElementById('modalEmailLink');
      emailLink.href = 'mailto:' + (data.email || '');
      emailLink.innerText = data.email || 'N/A';

      const phoneLink = document.getElementById('modalPhoneLink');
      phoneLink.href = 'tel:' + (data.phone || '');
      phoneLink.innerText = data.phone || 'N/A';

      document.getElementById('modalCourse').innerText = data.course_name || 'N/A';
      document.getElementById('modalQual').innerText = data.qualification || '12th Pass / Graduate';
      document.getElementById('modalAadhaar').innerText = data.aadhaar_number || 'UIDAI Verified';
      document.getElementById('modalFee').innerText = '₹' + parseFloat(data.fee_amount || 0).toLocaleString('en-IN') + ' (Paid in Full)';
      document.getElementById('modalTxId').innerText = data.razorpay_payment_id || 'RZP_VERIFIED';

      let statusColor = 'bg-success text-white';
      if (data.admission_status === 'under_review') statusColor = 'bg-warning text-dark';
      if (data.admission_status === 'rejected') statusColor = 'bg-danger text-white';
      document.getElementById('modalStatusBadge').innerHTML = '<span class="badge ' + statusColor + ' fs-6 px-3 py-1 fw-bold">' + (data.admission_status || 'VERIFIED').toUpperCase() + '</span>';

      // 1. Passport Photo Preview
      const photoImg = document.getElementById('modalPhoto');
      const initialsAvatar = document.getElementById('modalInitialsAvatar');
      const imgDocPhoto = document.getElementById('imgDocPhoto');
      const emptyPhoto = document.getElementById('emptyPhoto');
      const btnPhotoOpen = document.getElementById('btnPhotoOpen');
      const badgePhoto = document.getElementById('badgePhoto');

      if (data.photo_doc && data.photo_doc.trim() !== '') {
        const pSrc = data.photo_doc.startsWith('/') || data.photo_doc.startsWith('http') ? data.photo_doc : '../' + data.photo_doc;
        photoImg.src = pSrc;
        photoImg.style.display = 'block';
        initialsAvatar.style.display = 'none';

        imgDocPhoto.src = pSrc;
        imgDocPhoto.style.display = 'block';
        emptyPhoto.style.display = 'none';
        btnPhotoOpen.href = pSrc;
        btnPhotoOpen.classList.remove('disabled');
        badgePhoto.className = 'doc-badge-pill bg-success text-white';
        badgePhoto.innerText = 'Uploaded';
      } else {
        photoImg.style.display = 'none';
        initialsAvatar.style.display = 'flex';
        const names = (data.full_name || 'STU').trim().split(' ');
        let initials = names[0].charAt(0).toUpperCase();
        if (names.length > 1) {
          initials += names[names.length - 1].charAt(0).toUpperCase();
        }
        initialsAvatar.innerText = initials;

        imgDocPhoto.style.display = 'none';
        emptyPhoto.style.display = 'block';
        btnPhotoOpen.href = '#';
        btnPhotoOpen.classList.add('disabled');
        badgePhoto.className = 'doc-badge-pill bg-secondary text-white';
        badgePhoto.innerText = 'Not Uploaded';
      }

      // 2. Aadhaar Card Preview
      const imgDocAadhaar = document.getElementById('imgDocAadhaar');
      const pdfDocAadhaar = document.getElementById('pdfDocAadhaar');
      const emptyAadhaar = document.getElementById('emptyAadhaar');
      const btnAadhaarOpen = document.getElementById('btnAadhaarOpen');
      const badgeAadhaar = document.getElementById('badgeAadhaar');

      if (data.aadhaar_doc && data.aadhaar_doc.trim() !== '') {
        const aSrc = data.aadhaar_doc.startsWith('/') || data.aadhaar_doc.startsWith('http') ? data.aadhaar_doc : '../' + data.aadhaar_doc;
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

      // 3. Marksheet Preview
      const imgDocMarksheet = document.getElementById('imgDocMarksheet');
      const pdfDocMarksheet = document.getElementById('pdfDocMarksheet');
      const emptyMarksheet = document.getElementById('emptyMarksheet');
      const btnMarksheetOpen = document.getElementById('btnMarksheetOpen');
      const badgeMarksheet = document.getElementById('badgeMarksheet');

      if (data.marksheet_doc && data.marksheet_doc.trim() !== '') {
        const mSrc = data.marksheet_doc.startsWith('/') || data.marksheet_doc.startsWith('http') ? data.marksheet_doc : '../' + data.marksheet_doc;
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

      // Official Receipt Link
      document.getElementById('modalReceiptLink').href = '../download-receipt.php?app_id=' + encodeURIComponent(data.application_id);

      // Open Modal
      const modal = new bootstrap.Modal(document.getElementById('dossierModal'));
      modal.show();
    }
  </script>
</body>
</html>