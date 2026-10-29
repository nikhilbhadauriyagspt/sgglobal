<?php
// Skill Grow Institution - Student Admissions & Document Verification Management
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
    $admId  = (int)($_POST['admission_id'] ?? 0);
    $status = trim($_POST['status'] ?? 'verified');
    $allowed = ['verified', 'under_review', 'approved', 'rejected'];

    if ($admId > 0 && in_array($status, $allowed)) {
        try {
            $uStmt = $pdo->prepare("UPDATE admissions SET admission_status = :st WHERE id = :id");
            $uStmt->execute(['st' => $status, 'id' => $admId]);
            $msg = "Admission status successfully updated to " . ucfirst(str_replace('_', ' ', $status)) . "!";
            $msgType = "success";
        } catch (PDOException $e) {
            $msg = "Database error updating status: " . $e->getMessage();
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
            $stmt = $pdo->prepare("SELECT application_id, full_name, photo_doc, aadhaar_doc, marksheet_doc FROM admissions WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $admId]);
            $target = $stmt->fetch();

            if ($target) {
                // Delete record from admissions table
                $dStmt = $pdo->prepare("DELETE FROM admissions WHERE id = :id");
                $dStmt->execute(['id' => $admId]);

                $msg = "Admission record of '" . htmlspecialchars($target['full_name']) . "' (App ID: " . htmlspecialchars($target['application_id']) . ") has been deleted successfully.";
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
    $sql .= " AND course_name LIKE :cn";
    $params['cn'] = "%$filterCourse%";
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$admissions = $stmt->fetchAll();

// Global Stats
$statAdm = $pdo->query("SELECT 
    COUNT(*) as total_registered,
    COUNT(CASE WHEN admission_status = 'approved' OR admission_status = 'verified' THEN 1 END) as total_verified,
    COUNT(CASE WHEN admission_status = 'under_review' THEN 1 END) as total_review,
    COALESCE(SUM(fee_amount), 0) as total_fee
FROM admissions")->fetch() ?: ['total_registered' => 0, 'total_verified' => 0, 'total_review' => 0, 'total_fee' => 0];

// Active courses for filter
$coursesList = $pdo->query("SELECT title FROM courses ORDER BY title ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registered Students & Documents | SG Admin Console</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Central Unified Admin Stylesheet -->
  <link rel="stylesheet" href="assets/css/admin.css">

  <style>
    /* High-Contrast Readability Enhancements */
    .stat-metric-title {
      font-size: 0.82rem;
      text-transform: uppercase;
      font-weight: 700;
      letter-spacing: 0.6px;
      color: #e2e8f0;
      margin-bottom: 4px;
    }

    .text-light-contrast {
      color: #f1f5f9 !important;
      font-size: 0.88rem;
    }

    .status-select-sm {
      background: #09122c !important;
      border: 1px solid #2a3c6d !important;
      color: #ffffff !important;
      font-size: 0.82rem;
      font-weight: 600;
      padding: 6px 10px;
      border-radius: 8px;
      width: auto;
      display: inline-block;
      cursor: pointer;
    }

    /* Modal Dossier Luxury Dark Theme */
    .dossier-cell {
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 10px;
      padding: 10px 14px;
      transition: border-color 0.2s;
    }
    .dossier-cell:hover {
      border-color: rgba(230, 169, 16, 0.3);
    }
    .dossier-label {
      color: #fbbf24 !important;
      font-size: 0.72rem !important;
      text-transform: uppercase !important;
      font-weight: 800 !important;
      letter-spacing: 0.8px;
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .dossier-val {
      color: #ffffff !important;
      font-size: 0.95rem;
      font-weight: 700;
      word-break: break-all;
    }

    /* Document Preview Cards */
    .doc-preview-card {
      background: #070e24;
      border: 1px solid #1e2c52;
      border-radius: 12px;
      padding: 16px;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all 0.2s;
    }
    .doc-preview-card:hover {
      border-color: rgba(230, 169, 16, 0.4);
    }
    .doc-preview-thumb-box {
      width: 100%;
      height: 190px;
      background: #040817;
      border-radius: 8px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px dashed #2a3c6d;
      margin-bottom: 12px;
      position: relative;
    }
    .doc-preview-thumb-box img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      cursor: pointer;
      transition: transform 0.2s;
      padding: 4px;
    }
    .doc-preview-thumb-box img:hover {
      transform: scale(1.03);
    }
    .doc-preview-thumb-box .pdf-placeholder {
      text-align: center;
      color: #ef4444;
      padding: 20px;
    }
    .doc-preview-thumb-box .empty-placeholder {
      text-align: center;
      color: #64748b;
      padding: 20px;
    }
    .doc-badge-pill {
      font-size: 0.72rem;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: 50px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
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
            <h1 class="page-title-text">Registered Students & Admissions</h1>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
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
          <div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert" style="border-radius: 12px; font-weight: 600;">
            <i class="fas fa-info-circle me-2"></i><?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <!-- Stat Cards Row - High Contrast and Clear -->
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="stat-metric-card" style="border-left: 4px solid var(--admin-primary);">
              <div class="stat-metric-icon">
                <i class="fas fa-user-graduate"></i>
              </div>
              <div>
                <div class="stat-metric-title">Total Registrations</div>
                <div class="text-white fw-bold" style="font-size: 1.6rem; font-family: var(--font-heading);"><?= number_format($statAdm['total_registered']) ?></div>
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
                <div class="text-success fw-bold" style="font-size: 1.6rem; font-family: var(--font-heading);"><?= number_format($statAdm['total_verified']) ?></div>
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
                <div class="text-warning fw-bold" style="font-size: 1.6rem; font-family: var(--font-heading);"><?= number_format($statAdm['total_review']) ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-metric-card" style="border-left: 4px solid #38bdf8;">
              <div class="stat-metric-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                <i class="fas fa-indian-rupee-sign"></i>
              </div>
              <div>
                <div class="stat-metric-title text-info">Registration Fees</div>
                <div class="text-info fw-bold" style="font-size: 1.6rem; font-family: var(--font-heading);">₹<?= number_format((float)$statAdm['total_fee'], 0) ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bento-card mb-4 p-3">
          <form method="GET" action="admissions.php" class="row g-2 align-items-center">
            <div class="col-md-4">
              <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-warning"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search student name, email, phone, App ID, Student ID, Aadhaar...">
              </div>
            </div>

            <div class="col-md-3">
              <select class="form-select" name="course">
                <option value="">All Applied Programs</option>
                <?php foreach ($coursesList as $cl): ?>
                  <option value="<?= htmlspecialchars($cl['title']) ?>" <?= ($filterCourse === $cl['title']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cl['title']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-3">
              <select class="form-select" name="status">
                <option value="">All Verification Statuses</option>
                <option value="verified" <?= ($filterStatus === 'verified') ? 'selected' : '' ?>>Verified</option>
                <option value="approved" <?= ($filterStatus === 'approved') ? 'selected' : '' ?>>Approved</option>
                <option value="under_review" <?= ($filterStatus === 'under_review') ? 'selected' : '' ?>>Under Review</option>
                <option value="rejected" <?= ($filterStatus === 'rejected') ? 'selected' : '' ?>>Rejected</option>
              </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
              <button type="submit" class="btn btn-warning fw-bold flex-grow-1">Filter</button>
              <?php if (!empty($search) || !empty($filterStatus) || !empty($filterCourse)): ?>
                <a href="admissions.php" class="btn btn-outline-light">Reset</a>
              <?php endif; ?>
            </div>
          </form>
        </div>

        <!-- Registered Students Table -->
        <div class="bento-card">
          <div class="bento-header">
            <h2 class="bento-title">
              <i class="fas fa-users-gear text-warning me-2"></i> All Registered Candidates (<?= count($admissions) ?>)
            </h2>
            <div class="text-white-50" style="font-size: 0.85rem;">
              Click <strong class="text-warning">View Details</strong> for complete student dossier & document previews.
            </div>
          </div>

          <div class="table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Student Info</th>
                  <th>App ID & Student ID</th>
                  <th>Applied Course</th>
                  <th>Qualification & Aadhaar</th>
                  <th>Fee Paid & Tx ID</th>
                  <th>Admission Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($admissions)): ?>
                  <?php foreach ($admissions as $adm): ?>
                    <tr>
                      <!-- Student Info -->
                      <td>
                        <div class="d-flex align-items-center gap-3">
                          <?php if (!empty($adm['photo_doc']) && file_exists(__DIR__ . '/../' . $adm['photo_doc'])): ?>
                            <a href="javascript:void(0)" onclick="openDossierModal(<?= htmlspecialchars(json_encode($adm)) ?>)" title="View Student Profile">
                              <img src="../<?= htmlspecialchars($adm['photo_doc']) ?>" alt="Photo" class="student-avatar-thumb">
                            </a>
                          <?php else: ?>
                            <span class="student-avatar-thumb">
                              <?= strtoupper(substr($adm['full_name'], 0, 2)) ?>
                            </span>
                          <?php endif; ?>
                          <div>
                            <div class="fw-bold text-white fs-6"><?= htmlspecialchars($adm['full_name']) ?></div>
                            <div class="text-light-contrast mt-1">
                              <i class="fas fa-envelope text-warning me-1"></i><?= htmlspecialchars($adm['email']) ?>
                            </div>
                            <div class="text-light-contrast mt-1">
                              <i class="fas fa-phone text-success me-1"></i><?= htmlspecialchars($adm['phone']) ?>
                            </div>
                          </div>
                        </div>
                      </td>

                      <!-- App ID & Student ID -->
                      <td>
                        <div><span class="enrollment-badge"><?= htmlspecialchars($adm['application_id']) ?></span></div>
                        <?php if (!empty($adm['student_id'])): ?>
                          <div class="mt-1 font-monospace text-info fw-bold" style="font-size: 0.82rem;">
                            ID: <?= htmlspecialchars($adm['student_id']) ?>
                          </div>
                        <?php else: ?>
                          <div class="mt-1 font-monospace text-muted" style="font-size: 0.78rem;">ID: Pending</div>
                        <?php endif; ?>
                      </td>

                      <!-- Applied Course -->
                      <td>
                        <div class="text-white fw-bold" style="font-size: 0.92rem;">
                          <?= htmlspecialchars($adm['course_name']) ?>
                        </div>
                        <div class="text-light-contrast mt-1" style="font-size: 0.82rem;">
                          <i class="fas fa-calendar-alt text-warning me-1"></i><?= date('d M, Y', strtotime($adm['created_at'])) ?>
                        </div>
                      </td>

                      <!-- Qualification & Aadhaar -->
                      <td>
                        <span class="badge bg-dark border border-secondary text-warning fw-bold px-2 py-1" style="font-size: 0.8rem;">
                          <?= htmlspecialchars($adm['qualification'] ?: '12th Pass') ?>
                        </span>
                        <div class="font-monospace text-white fw-bold mt-1" style="font-size: 0.82rem; background: rgba(255,255,255,0.06); padding: 3px 8px; border-radius: 4px; display: inline-block;">
                          <i class="fas fa-id-card me-1 text-warning"></i><?= htmlspecialchars($adm['aadhaar_number'] ?: 'N/A') ?>
                        </div>
                      </td>

                      <!-- Fee Paid & Tx ID -->
                      <td>
                        <div class="text-success fw-bold font-monospace fs-6">₹<?= number_format((float)$adm['fee_amount'], 2) ?></div>
                        <div class="font-monospace text-info fw-semibold mt-1" style="font-size: 0.78rem;">
                          <i class="fas fa-receipt me-1 text-secondary"></i><?= htmlspecialchars($adm['razorpay_payment_id'] ?: 'Razorpay') ?>
                        </div>
                      </td>

                      <!-- Admission Status with Quick Changer -->
                      <td>
                        <form method="POST" action="admissions.php" class="d-inline">
                          <input type="hidden" name="action" value="update_status">
                          <input type="hidden" name="admission_id" value="<?= $adm['id'] ?>">
                          <select name="status" class="status-select-sm" onchange="this.form.submit()">
                            <option value="verified" <?= ($adm['admission_status'] === 'verified') ? 'selected' : '' ?>>Verified</option>
                            <option value="approved" <?= ($adm['admission_status'] === 'approved') ? 'selected' : '' ?>>Approved</option>
                            <option value="under_review" <?= ($adm['admission_status'] === 'under_review') ? 'selected' : '' ?>>Under Review</option>
                            <option value="rejected" <?= ($adm['admission_status'] === 'rejected') ? 'selected' : '' ?>>Rejected</option>
                          </select>
                        </form>
                      </td>

                      <!-- Actions: View Details, Receipt, and Delete -->
                      <td>
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                          <button type="button" class="btn btn-sm btn-info text-dark fw-bold px-2 py-1" 
                                  onclick="openDossierModal(<?= htmlspecialchars(json_encode($adm)) ?>)" 
                                  title="View Complete Student Dossier & Documents">
                            <i class="fas fa-eye me-1"></i> View Details
                          </button>
                          
                          <a href="../download-receipt.php?app_id=<?= urlencode($adm['application_id']) ?>" target="_blank" class="btn btn-sm btn-warning text-dark fw-bold px-2 py-1" title="View Official Admission Receipt">
                            <i class="fas fa-file-invoice"></i> Receipt
                          </a>

                          <!-- Delete Action Button -->
                          <form method="POST" action="admissions.php" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete admission record of <?= htmlspecialchars($adm['full_name']) ?> (App ID: <?= htmlspecialchars($adm['application_id']) ?>)?');">
                            <input type="hidden" name="action" value="delete_admission">
                            <input type="hidden" name="admission_id" value="<?= $adm['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Delete Admission Record">
                              <i class="fas fa-trash-alt"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="text-center py-5 text-white-50">
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
      <div class="modal-content" style="background: #08112a !important; border: 1.5px solid #223565 !important; box-shadow: 0 20px 50px rgba(0,0,0,0.7) !important;">
        
        <!-- Modal Header -->
        <div class="modal-header" style="background: #050b1d; border-bottom: 1.5px solid #223565; padding: 18px 24px;">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-warning text-dark p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; font-size: 20px;">
              <i class="fas fa-id-card"></i>
            </div>
            <div>
              <h5 class="modal-title font-monospace text-white fw-bold mb-0" style="font-size: 1.15rem;">
                Candidate Admission Dossier & Verification Portal
              </h5>
              <div style="font-size: 0.8rem; color: #94a3b8;">
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
              <div class="p-4 rounded-3 mb-4 text-center" style="background: #04091a; border: 1.5px solid #1a2a50;">
                <div id="modalPhotoContainer" class="mb-3 d-flex justify-content-center">
                  <img id="modalPhoto" src="" alt="Student Photo" class="img-fluid rounded-3 border border-warning shadow" style="max-height: 180px; width: 145px; object-fit: cover;">
                </div>
                <h3 id="modalName" class="text-white fw-bold mb-1" style="font-family: var(--font-heading); font-size: 1.4rem;"></h3>
                <div class="d-flex justify-content-center gap-2 flex-wrap mt-2">
                  <span id="modalAppId" class="badge bg-warning text-dark font-monospace fs-6 px-3 py-2"></span>
                  <span id="modalStuId" class="badge bg-info text-dark font-monospace fs-6 px-3 py-2"></span>
                </div>
                <div id="modalStatusBadge" class="mt-3"></div>
              </div>

              <!-- High Contrast Personal Details Grid -->
              <div class="p-3 rounded-3" style="background: #04091a; border: 1.5px solid #1a2a50;">
                <h6 class="text-warning fw-bold mb-3 d-flex align-items-center gap-2" style="font-size: 0.88rem; text-transform: uppercase; letter-spacing: 0.8px;">
                  <i class="fas fa-user-check text-warning"></i> Personal & Admission Details
                </h6>
                
                <div class="row g-3">
                  <div class="col-12">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-graduation-cap"></i> Applied Academic Course</div>
                      <div id="modalCourse" class="dossier-val text-warning fs-6"></div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-envelope"></i> Registered Email Address</div>
                      <div id="modalEmail" class="dossier-val font-monospace text-white"></div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-phone"></i> Mobile Number</div>
                      <div id="modalPhone" class="dossier-val font-monospace text-white"></div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="dossier-cell">
                      <div class="dossier-label"><i class="fas fa-certificate"></i> Qualification</div>
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
                <h5 class="text-white fw-bold mb-0" style="font-family: var(--font-heading); font-size: 1.15rem;">
                  <i class="fas fa-file-shield text-warning me-2"></i> Verification Documents Preview
                </h5>
                <span class="badge bg-dark border border-secondary text-white-50 px-3 py-2" style="font-size: 0.78rem;">
                  <i class="fas fa-folder-open text-warning me-1"></i> Stored in Student Folder
                </span>
              </div>

              <div class="row g-3">
                
                <!-- 1. PASSPORT PHOTO PREVIEW CARD -->
                <div class="col-md-6">
                  <div class="doc-preview-card">
                    <div>
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-white" style="font-size: 0.92rem;">
                          <i class="fas fa-camera text-primary me-1"></i> Passport Photo
                        </strong>
                        <span id="badgePhoto" class="doc-badge-pill bg-success text-white">Uploaded</span>
                      </div>
                      <div class="doc-preview-thumb-box" id="boxPhotoPreview">
                        <img id="imgDocPhoto" src="" alt="Passport Photo" onclick="window.open(this.src, '_blank')" title="Click to open full photo">
                        <div id="emptyPhoto" class="empty-placeholder" style="display: none;">
                          <i class="fas fa-image fa-2x mb-2 text-secondary"></i>
                          <div class="small fw-semibold text-white-50">No Photo Uploaded</div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex gap-2">
                      <a id="btnPhotoOpen" href="#" target="_blank" class="btn btn-sm btn-outline-primary fw-bold flex-grow-1">
                        <i class="fas fa-expand me-1"></i> Open Full Photo
                      </a>
                    </div>
                  </div>
                </div>

                <!-- 2. AADHAAR CARD PREVIEW CARD -->
                <div class="col-md-6">
                  <div class="doc-preview-card">
                    <div>
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-white" style="font-size: 0.92rem;">
                          <i class="fas fa-id-card text-warning me-1"></i> Aadhaar Card
                        </strong>
                        <span id="badgeAadhaar" class="doc-badge-pill bg-warning text-dark">Uploaded</span>
                      </div>
                      <div class="doc-preview-thumb-box" id="boxAadhaarPreview">
                        <img id="imgDocAadhaar" src="" alt="Aadhaar Card" onclick="window.open(this.src, '_blank')" title="Click to open Aadhaar Document">
                        <div id="pdfDocAadhaar" class="pdf-placeholder" style="display: none;">
                          <i class="fas fa-file-pdf fa-3x mb-2 text-danger"></i>
                          <div class="small fw-bold text-white">PDF Aadhaar Document</div>
                          <div class="text-white-50" style="font-size: 0.75rem;">Click Open button below</div>
                        </div>
                        <div id="emptyAadhaar" class="empty-placeholder" style="display: none;">
                          <i class="fas fa-id-card fa-2x mb-2 text-secondary"></i>
                          <div class="small fw-semibold text-white-50">No Aadhaar Uploaded</div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex gap-2">
                      <a id="btnAadhaarOpen" href="#" target="_blank" class="btn btn-sm btn-outline-warning fw-bold flex-grow-1">
                        <i class="fas fa-external-link-alt me-1"></i> Open Aadhaar Card
                      </a>
                    </div>
                  </div>
                </div>

                <!-- 3. MARKSHEET PREVIEW CARD -->
                <div class="col-12">
                  <div class="doc-preview-card">
                    <div>
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-white" style="font-size: 0.92rem;">
                          <i class="fas fa-file-alt text-success me-1"></i> Academic Marksheet / Degree Certificate
                        </strong>
                        <span id="badgeMarksheet" class="doc-badge-pill bg-success text-white">Uploaded</span>
                      </div>
                      <div class="doc-preview-thumb-box" id="boxMarksheetPreview" style="height: 220px;">
                        <img id="imgDocMarksheet" src="" alt="Marksheet Document" onclick="window.open(this.src, '_blank')" title="Click to open Marksheet">
                        <div id="pdfDocMarksheet" class="pdf-placeholder" style="display: none;">
                          <i class="fas fa-file-pdf fa-3x mb-2 text-danger"></i>
                          <div class="small fw-bold text-white">PDF Academic Certificate</div>
                          <div class="text-white-50" style="font-size: 0.75rem;">Click Open button below</div>
                        </div>
                        <div id="emptyMarksheet" class="empty-placeholder" style="display: none;">
                          <i class="fas fa-file-alt fa-2x mb-2 text-secondary"></i>
                          <div class="small fw-semibold text-white-50">No Marksheet Uploaded</div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex gap-2">
                      <a id="btnMarksheetOpen" href="#" target="_blank" class="btn btn-sm btn-outline-success fw-bold flex-grow-1">
                        <i class="fas fa-external-link-alt me-1"></i> Open Academic Marksheet
                      </a>
                    </div>
                  </div>
                </div>

              </div>
            </div>

          </div>
        </div>

        <!-- Modal Footer: Receipt, Delete & Close -->
        <div class="modal-footer d-flex justify-content-between align-items-center" style="background: #050b1d; border-top: 1.5px solid #223565; padding: 16px 24px;">
          
          <!-- Delete Candidate Button Inside Modal -->
          <form method="POST" action="admissions.php" id="modalDeleteForm" class="d-inline" onsubmit="return confirm('WARNING: Are you sure you want to permanently delete this candidate admission record? This action cannot be undone.');">
            <input type="hidden" name="action" value="delete_admission">
            <input type="hidden" name="admission_id" id="modalDeleteAdmId" value="0">
            <button type="submit" class="btn btn-danger fw-bold px-3">
              <i class="fas fa-trash-alt me-1"></i> Delete Candidate Record
            </button>
          </form>

          <div class="d-flex gap-2">
            <a id="modalReceiptLink" href="#" target="_blank" class="btn btn-warning text-dark fw-bold px-3">
              <i class="fas fa-file-invoice me-1"></i> Print Official Admission Slip
            </a>
            <button type="button" class="btn btn-outline-light px-4" data-bs-dismiss="modal">Close</button>
          </div>

        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/admin.js"></script>
  <script>
    function openDossierModal(data) {
      // Set delete form admission id
      document.getElementById('modalDeleteAdmId').value = data.id;

      // Personal & Admission details
      document.getElementById('modalName').innerText = data.full_name;
      document.getElementById('modalAppId').innerText = 'App: ' + data.application_id;
      document.getElementById('modalStuId').innerText = data.student_id ? ('Student: ' + data.student_id) : 'Student: Pending';
      document.getElementById('modalEmail').innerText = data.email;
      document.getElementById('modalPhone').innerText = data.phone;
      document.getElementById('modalCourse').innerText = data.course_name;
      document.getElementById('modalQual').innerText = data.qualification || '12th Pass / Graduate';
      document.getElementById('modalAadhaar').innerText = data.aadhaar_number || 'UIDAI Verified';
      document.getElementById('modalFee').innerText = '₹' + parseFloat(data.fee_amount).toLocaleString('en-IN') + ' (Paid in Full)';
      document.getElementById('modalTxId').innerText = data.razorpay_payment_id || 'RZP_VERIFIED';

      let statusColor = 'bg-success text-white';
      if (data.admission_status === 'under_review') statusColor = 'bg-warning text-dark';
      if (data.admission_status === 'rejected') statusColor = 'bg-danger text-white';
      document.getElementById('modalStatusBadge').innerHTML = '<span class="badge ' + statusColor + ' fs-6 px-3 py-2 fw-bold">' + data.admission_status.toUpperCase() + '</span>';

      // 1. Passport Photo Preview
      const photoImg = document.getElementById('modalPhoto');
      const imgDocPhoto = document.getElementById('imgDocPhoto');
      const emptyPhoto = document.getElementById('emptyPhoto');
      const btnPhotoOpen = document.getElementById('btnPhotoOpen');
      const badgePhoto = document.getElementById('badgePhoto');

      if (data.photo_doc) {
        photoImg.src = '../' + data.photo_doc;
        photoImg.style.display = 'block';
        imgDocPhoto.src = '../' + data.photo_doc;
        imgDocPhoto.style.display = 'block';
        emptyPhoto.style.display = 'none';
        btnPhotoOpen.href = '../' + data.photo_doc;
        btnPhotoOpen.classList.remove('disabled');
        badgePhoto.className = 'doc-badge-pill bg-success text-white';
        badgePhoto.innerText = 'Uploaded';
      } else {
        photoImg.style.display = 'none';
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

      if (data.aadhaar_doc) {
        const isPdf = data.aadhaar_doc.toLowerCase().endsWith('.pdf');
        btnAadhaarOpen.href = '../' + data.aadhaar_doc;
        btnAadhaarOpen.classList.remove('disabled');
        badgeAadhaar.className = 'doc-badge-pill bg-warning text-dark';
        badgeAadhaar.innerText = 'Uploaded';
        emptyAadhaar.style.display = 'none';

        if (isPdf) {
          imgDocAadhaar.style.display = 'none';
          pdfDocAadhaar.style.display = 'block';
        } else {
          imgDocAadhaar.src = '../' + data.aadhaar_doc;
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

      if (data.marksheet_doc) {
        const isPdf = data.marksheet_doc.toLowerCase().endsWith('.pdf');
        btnMarksheetOpen.href = '../' + data.marksheet_doc;
        btnMarksheetOpen.classList.remove('disabled');
        badgeMarksheet.className = 'doc-badge-pill bg-success text-white';
        badgeMarksheet.innerText = 'Uploaded';
        emptyMarksheet.style.display = 'none';

        if (isPdf) {
          imgDocMarksheet.style.display = 'none';
          pdfDocMarksheet.style.display = 'block';
        } else {
          imgDocMarksheet.src = '../' + data.marksheet_doc;
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
