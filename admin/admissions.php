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
    .doc-pill-btn {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 0.72rem;
      font-weight: 600;
      padding: 4px 8px;
      border-radius: 6px;
      text-decoration: none;
      transition: all 0.2s;
      white-space: nowrap;
    }
    .doc-pill-photo { background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); color: #60a5fa; }
    .doc-pill-aadhaar { background: rgba(230, 169, 16, 0.15); border: 1px solid rgba(230, 169, 16, 0.3); color: #fbbf24; }
    .doc-pill-marksheet { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; }
    .doc-pill-btn:hover { transform: translateY(-1px); color: #fff; }
    
    .status-select-sm {
      background: #060b1b !important;
      border: 1px solid #1a274e !important;
      color: #fff !important;
      font-size: 0.78rem;
      padding: 4px 8px;
      border-radius: 6px;
      width: auto;
      display: inline-block;
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
          <div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert" style="border-radius: 12px;">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <!-- Stat Cards Row -->
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="stat-metric-card">
              <div class="stat-metric-icon">
                <i class="fas fa-user-graduate"></i>
              </div>
              <div>
                <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">Total Registrations</div>
                <div class="text-white fw-bold" style="font-size: 1.5rem; font-family: var(--font-heading);"><?= number_format($statAdm['total_registered']) ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-metric-card">
              <div class="stat-metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="fas fa-id-badge"></i>
              </div>
              <div>
                <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">Verified Students</div>
                <div class="text-success fw-bold" style="font-size: 1.5rem; font-family: var(--font-heading);"><?= number_format($statAdm['total_verified']) ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-metric-card">
              <div class="stat-metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                <i class="fas fa-clock"></i>
              </div>
              <div>
                <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">Under Review</div>
                <div class="text-warning fw-bold" style="font-size: 1.5rem; font-family: var(--font-heading);"><?= number_format($statAdm['total_review']) ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="stat-metric-card">
              <div class="stat-metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                <i class="fas fa-indian-rupee-sign"></i>
              </div>
              <div>
                <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">Registration Fees</div>
                <div class="text-info fw-bold" style="font-size: 1.5rem; font-family: var(--font-heading);">₹<?= number_format((float)$statAdm['total_fee'], 0) ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bento-card mb-4 p-3">
          <form method="GET" action="admissions.php" class="row g-2 align-items-center">
            <div class="col-md-4">
              <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fas fa-search"></i></span>
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
          </div>

          <div class="table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Student Info</th>
                  <th>App ID & Student ID</th>
                  <th>Applied Course</th>
                  <th>Qualification & Aadhaar</th>
                  <th>Fee Paid</th>
                  <th>Uploaded Documents</th>
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
                            <a href="../<?= htmlspecialchars($adm['photo_doc']) ?>" target="_blank" title="View Full Photo">
                              <img src="../<?= htmlspecialchars($adm['photo_doc']) ?>" alt="Photo" class="student-avatar-thumb">
                            </a>
                          <?php else: ?>
                            <span class="student-avatar-thumb">
                              <?= strtoupper(substr($adm['full_name'], 0, 2)) ?>
                            </span>
                          <?php endif; ?>
                          <div>
                            <div class="fw-bold text-white"><?= htmlspecialchars($adm['full_name']) ?></div>
                            <small class="text-muted d-block"><?= htmlspecialchars($adm['email']) ?></small>
                            <small class="text-warning"><i class="fas fa-phone me-1"></i><?= htmlspecialchars($adm['phone']) ?></small>
                          </div>
                        </div>
                      </td>

                      <!-- App ID & Student ID -->
                      <td>
                        <div><span class="enrollment-badge"><?= htmlspecialchars($adm['application_id']) ?></span></div>
                        <?php if (!empty($adm['student_id'])): ?>
                          <div class="mt-1 font-monospace text-info" style="font-size: 0.78rem;">
                            ID: <?= htmlspecialchars($adm['student_id']) ?>
                          </div>
                        <?php else: ?>
                          <small class="text-muted">ID: Pending</small>
                        <?php endif; ?>
                      </td>

                      <!-- Applied Course -->
                      <td>
                        <div class="text-white fw-semibold" style="font-size: 0.88rem;">
                          <?= htmlspecialchars($adm['course_name']) ?>
                        </div>
                        <small class="text-muted"><?= date('d M, Y', strtotime($adm['created_at'])) ?></small>
                      </td>

                      <!-- Qualification & Aadhaar -->
                      <td>
                        <span class="badge bg-dark border border-secondary text-white"><?= htmlspecialchars($adm['qualification'] ?: '12th Pass') ?></span>
                        <div class="font-monospace text-muted mt-1" style="font-size: 0.75rem;">
                          <i class="fas fa-id-card me-1 text-warning"></i><?= htmlspecialchars($adm['aadhaar_number'] ?: 'N/A') ?>
                        </div>
                      </td>

                      <!-- Fee Paid -->
                      <td>
                        <div class="text-success fw-bold font-monospace">₹<?= number_format((float)$adm['fee_amount'], 2) ?></div>
                        <small class="text-muted font-monospace" style="font-size: 0.72rem;"><?= htmlspecialchars($adm['razorpay_payment_id'] ?: 'Razorpay') ?></small>
                      </td>

                      <!-- Uploaded Documents (Folder storage) -->
                      <td>
                        <div class="d-flex flex-column gap-1">
                          <?php if (!empty($adm['photo_doc'])): ?>
                            <a href="../<?= htmlspecialchars($adm['photo_doc']) ?>" target="_blank" class="doc-pill-btn doc-pill-photo">
                              <i class="fas fa-camera"></i> Photo
                            </a>
                          <?php else: ?>
                            <span class="text-muted" style="font-size: 0.72rem;">No Photo</span>
                          <?php endif; ?>

                          <?php if (!empty($adm['aadhaar_doc'])): ?>
                            <a href="../<?= htmlspecialchars($adm['aadhaar_doc']) ?>" target="_blank" class="doc-pill-btn doc-pill-aadhaar">
                              <i class="fas fa-id-card"></i> Aadhaar
                            </a>
                          <?php endif; ?>

                          <?php if (!empty($adm['marksheet_doc'])): ?>
                            <a href="../<?= htmlspecialchars($adm['marksheet_doc']) ?>" target="_blank" class="doc-pill-btn doc-pill-marksheet">
                              <i class="fas fa-file-alt"></i> Marksheet
                            </a>
                          <?php endif; ?>
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

                      <!-- Actions -->
                      <td>
                        <div class="d-flex gap-1 flex-wrap">
                          <button type="button" class="btn btn-sm btn-outline-info" 
                                  onclick="openDossierModal(<?= htmlspecialchars(json_encode($adm)) ?>)" 
                                  title="View Complete Student Dossier">
                            <i class="fas fa-eye"></i>
                          </button>
                          <a href="../download-receipt.php?app_id=<?= urlencode($adm['application_id']) ?>" target="_blank" class="btn btn-sm btn-outline-warning" title="Admission Slip / Receipt">
                            <i class="fas fa-file-invoice"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
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

  <!-- COMPLETE STUDENT DOSSIER MODAL -->
  <div class="modal fade" id="dossierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-monospace text-warning" id="dossierTitle">
            <i class="fas fa-id-card me-2"></i> Student Candidate Dossier
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-4">
            <!-- Left Profile Card -->
            <div class="col-md-4 text-center border-end border-secondary pe-md-4">
              <div id="modalPhotoContainer" class="mb-3">
                <img id="modalPhoto" src="" alt="Student Photo" class="img-fluid rounded-3 border border-secondary" style="max-height: 160px; object-fit: cover;">
              </div>
              <h4 id="modalName" class="text-white fw-bold mb-1" style="font-family: var(--font-heading); font-size: 1.2rem;"></h4>
              <div id="modalAppId" class="badge bg-warning text-dark font-monospace mb-2"></div>
              <div id="modalStuId" class="text-info font-monospace small"></div>
              <div id="modalStatusBadge" class="mt-2"></div>
            </div>

            <!-- Right Details Grid -->
            <div class="col-md-8">
              <div class="row g-3">
                <div class="col-sm-6">
                  <label class="text-muted small text-uppercase fw-bold">Email Address</label>
                  <div id="modalEmail" class="text-white fw-semibold"></div>
                </div>

                <div class="col-sm-6">
                  <label class="text-muted small text-uppercase fw-bold">Mobile Number</label>
                  <div id="modalPhone" class="text-white fw-semibold"></div>
                </div>

                <div class="col-12">
                  <label class="text-muted small text-uppercase fw-bold">Applied Academic Course</label>
                  <div id="modalCourse" class="text-warning fw-bold fs-6"></div>
                </div>

                <div class="col-sm-6">
                  <label class="text-muted small text-uppercase fw-bold">Educational Qualification</label>
                  <div id="modalQual" class="text-white"></div>
                </div>

                <div class="col-sm-6">
                  <label class="text-muted small text-uppercase fw-bold">Aadhaar Card No.</label>
                  <div id="modalAadhaar" class="text-white font-monospace"></div>
                </div>

                <div class="col-sm-6">
                  <label class="text-muted small text-uppercase fw-bold">Registration Fee Paid</label>
                  <div id="modalFee" class="text-success fw-bold"></div>
                </div>

                <div class="col-sm-6">
                  <label class="text-muted small text-uppercase fw-bold">Razorpay Tx ID</label>
                  <div id="modalTxId" class="text-white font-monospace small"></div>
                </div>

                <div class="col-12 border-top border-secondary pt-3 mt-3">
                  <label class="text-muted small text-uppercase fw-bold mb-2 d-block">Uploaded Verification Documents</label>
                  <div class="d-flex gap-2 flex-wrap" id="modalDocButtons"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a id="modalReceiptLink" href="#" target="_blank" class="btn btn-warning fw-bold">
            <i class="fas fa-print me-1"></i> View Admission Slip
          </a>
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/admin.js"></script>
  <script>
    function openDossierModal(data) {
      document.getElementById('modalName').innerText = data.full_name;
      document.getElementById('modalAppId').innerText = data.application_id;
      document.getElementById('modalStuId').innerText = data.student_id ? ('Student ID: ' + data.student_id) : 'Student ID: Pending';
      document.getElementById('modalEmail').innerText = data.email;
      document.getElementById('modalPhone').innerText = data.phone;
      document.getElementById('modalCourse').innerText = data.course_name;
      document.getElementById('modalQual').innerText = data.qualification || '12th Pass';
      document.getElementById('modalAadhaar').innerText = data.aadhaar_number || 'N/A';
      document.getElementById('modalFee').innerText = '₹' + parseFloat(data.fee_amount).toLocaleString('en-IN') + ' (Paid)';
      document.getElementById('modalTxId').innerText = data.razorpay_payment_id || 'RZP_VERIFIED';
      document.getElementById('modalStatusBadge').innerHTML = '<span class="badge bg-success">' + data.admission_status.toUpperCase() + '</span>';

      // Photo
      const photoImg = document.getElementById('modalPhoto');
      if (data.photo_doc) {
        photoImg.src = '../' + data.photo_doc;
        photoImg.style.display = 'block';
      } else {
        photoImg.style.display = 'none';
      }

      // Document buttons
      const docBox = document.getElementById('modalDocButtons');
      docBox.innerHTML = '';
      if (data.photo_doc) {
        docBox.innerHTML += `<a href="../${data.photo_doc}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-camera me-1"></i> Student Photo</a>`;
      }
      if (data.aadhaar_doc) {
        docBox.innerHTML += `<a href="../${data.aadhaar_doc}" target="_blank" class="btn btn-sm btn-outline-warning"><i class="fas fa-id-card me-1"></i> Aadhaar Card</a>`;
      }
      if (data.marksheet_doc) {
        docBox.innerHTML += `<a href="../${data.marksheet_doc}" target="_blank" class="btn btn-sm btn-outline-success"><i class="fas fa-file-alt me-1"></i> Academic Marksheet</a>`;
      }
      if (!data.photo_doc && !data.aadhaar_doc && !data.marksheet_doc) {
        docBox.innerHTML = '<span class="text-muted small">No digital documents attached.</span>';
      }

      // Receipt Link
      document.getElementById('modalReceiptLink').href = '../download-receipt.php?app_id=' + encodeURIComponent(data.application_id);

      // Show Modal
      const modal = new bootstrap.Modal(document.getElementById('dossierModal'));
      modal.show();
    }
  </script>
</body>
</html>
