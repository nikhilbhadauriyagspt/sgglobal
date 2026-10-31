<?php
// Admin Certificate Management: Issue, Approve, Mail & Configure Official Student Certificates
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/../includes/certificates_helper.php';
require_once __DIR__ . '/../includes/settings_helper.php';

ensureCertificatesTable($pdo);
ensureCertificateSettingsColumns($pdo);

$pageTitle = 'Certificate Issuance & Management';
$msg = '';
$msgType = '';

// Handle Actions: Issue New, Approve, Reject, Resend Email, Delete, Update Settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // 1. APPROVE PENDING CERTIFICATE
    if ($action === 'approve') {
        $id = (int)($_POST['id'] ?? 0);
        $sendEmail = !empty($_POST['send_email']);

        $stmt = $pdo->prepare("SELECT * FROM certificates WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $cert = $stmt->fetch();

        if ($cert) {
            $issueDate = date('Y-m-d');
            $up = $pdo->prepare("UPDATE certificates SET status = 'approved', issue_date = :idate WHERE id = :id");
            $up->execute(['idate' => $issueDate, 'id' => $id]);

            $cert['issue_date'] = $issueDate;
            $cert['status']     = 'approved';

            $mailNote = '';
            if ($sendEmail) {
                $mRes = sendCertificateApprovalEmail($pdo, $cert);
                if (!empty($mRes['success'])) {
                    $mailNote = " & Approval email dispatched to {$cert['student_email']}!";
                } else {
                    $mailNote = " (Email dispatch error: " . ($mRes['message'] ?? 'Check SMTP Settings') . ")";
                }
            }

            $msg = "Certificate {$cert['certificate_number']} approved and verified successfully{$mailNote}";
            $msgType = 'success';
        } else {
            $msg = 'Certificate record not found.';
            $msgType = 'danger';
        }
    }

    // 2. REJECT CERTIFICATE REQUEST
    elseif ($action === 'reject') {
        $id = (int)($_POST['id'] ?? 0);
        $notes = trim($_POST['admin_notes'] ?? 'Application rejected by administrative council.');

        $stmt = $pdo->prepare("UPDATE certificates SET status = 'rejected', admin_notes = :notes WHERE id = :id");
        $stmt->execute(['notes' => $notes, 'id' => $id]);

        $msg = 'Certificate request rejected.';
        $msgType = 'warning';
    }

    // 3. DIRECT ISSUE NEW CERTIFICATE
    elseif ($action === 'issue') {
        $studentEmail = trim($_POST['student_email'] ?? '');
        $studentName  = trim($_POST['student_name'] ?? '');
        $studentId    = trim($_POST['student_id'] ?? '');
        $courseName   = trim($_POST['course_name'] ?? '');
        $enrollmentId = trim($_POST['enrollment_id'] ?? '');
        $certType     = trim($_POST['certificate_type'] ?? 'Certificate of Completion');
        $grade        = trim($_POST['grade'] ?? 'A (Distinction)');
        $issueDate    = !empty($_POST['issue_date']) ? $_POST['issue_date'] : date('Y-m-d');
        $sendEmail    = !empty($_POST['send_email']);

        if (!empty($studentEmail) && !empty($studentName) && !empty($courseName)) {
            try {
                $certNum = generateCertificateNumber($pdo);

                $stmt = $pdo->prepare("
                    INSERT INTO certificates (certificate_number, student_id, student_name, student_email, course_name, enrollment_id, certificate_type, grade, issue_date, status)
                    VALUES (:cnum, :sid, :sname, :sem, :cname, :enrid, :ctype, :grade, :idate, 'approved')
                ");
                $stmt->execute([
                    'cnum'  => $certNum,
                    'sid'   => $studentId,
                    'sname' => $studentName,
                    'sem'   => $studentEmail,
                    'cname' => $courseName,
                    'enrid' => $enrollmentId,
                    'ctype' => $certType,
                    'grade' => $grade,
                    'idate' => $issueDate
                ]);

                $emailStatusNote = '';
                if ($sendEmail) {
                    $newCert = [
                        'certificate_number' => $certNum,
                        'student_name'       => $studentName,
                        'student_email'      => $studentEmail,
                        'course_name'        => $courseName,
                        'certificate_type'   => $certType,
                        'grade'              => $grade,
                        'issue_date'         => $issueDate
                    ];
                    $res = sendCertificateApprovalEmail($pdo, $newCert);
                    if (!empty($res['success'])) {
                        $emailStatusNote = " & Notification email dispatched to {$studentEmail}!";
                    } else {
                        $emailStatusNote = " (Email dispatch error: " . ($res['message'] ?? 'Check SMTP Settings') . ")";
                    }
                }

                $msg = "Certificate {$certNum} issued successfully{$emailStatusNote}!";
                $msgType = 'success';
            } catch (PDOException $e) {
                $msg = 'Error issuing certificate: ' . $e->getMessage();
                $msgType = 'danger';
            }
        } else {
            $msg = 'Student name, email, and course name are required.';
            $msgType = 'warning';
        }
    }

    // 4. RESEND CERTIFICATE EMAIL
    elseif ($action === 'resend_email') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM certificates WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $cert = $stmt->fetch();
        if ($cert) {
            $res = sendCertificateApprovalEmail($pdo, $cert);
            if (!empty($res['success'])) {
                $msg = "Certificate email resent to {$cert['student_email']}!";
                $msgType = 'success';
            } else {
                $msg = "Failed to resend email: " . ($res['message'] ?? 'Check SMTP Settings');
                $msgType = 'danger';
            }
        }
    }

    // 5. DELETE CERTIFICATE RECORD
    elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM certificates WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $msg = 'Certificate record removed.';
            $msgType = 'success';
        }
    }

    // 6. UPDATE CERTIFICATE SIGNATORIES & DESIGN SETTINGS
    elseif ($action === 'update_settings') {
        $sign1Name  = trim($_POST['cert_sign1_name'] ?? '');
        $sign1Title = trim($_POST['cert_sign1_title'] ?? '');
        $sign1Sub   = trim($_POST['cert_sign1_sub'] ?? '');
        $sign2Name  = trim($_POST['cert_sign2_name'] ?? '');
        $sign2Title = trim($_POST['cert_sign2_title'] ?? '');
        $sign2Sub   = trim($_POST['cert_sign2_sub'] ?? '');
        $subtitle   = trim($_POST['cert_subtitle'] ?? '');
        $sealText   = trim($_POST['cert_seal_text'] ?? '');
        $verText    = trim($_POST['cert_verified_text'] ?? '');

        // Fetch current to keep existing signature images if not uploaded
        $current = getCertificateSettings($pdo);
        $sign1Img = $current['cert_sign1_image'];
        $sign2Img = $current['cert_sign2_image'];

        $uploadDir = __DIR__ . '/../uploads/signatures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Upload Signatory 1 Signature Image
        if (!empty($_FILES['cert_sign1_image_file']['name']) && $_FILES['cert_sign1_image_file']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['cert_sign1_image_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'])) {
                $dest = $uploadDir . 'signatory_1_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['cert_sign1_image_file']['tmp_name'], $dest)) {
                    $sign1Img = 'uploads/signatures/signatory_1_' . time() . '.' . $ext;
                }
            }
        }

        // Upload Signatory 2 Signature Image
        if (!empty($_FILES['cert_sign2_image_file']['name']) && $_FILES['cert_sign2_image_file']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['cert_sign2_image_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'])) {
                $dest = $uploadDir . 'signatory_2_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['cert_sign2_image_file']['tmp_name'], $dest)) {
                    $sign2Img = 'uploads/signatures/signatory_2_' . time() . '.' . $ext;
                }
            }
        }

        try {
            $uStmt = $pdo->prepare("
                UPDATE system_settings SET
                    cert_sign1_name = :s1n,
                    cert_sign1_title = :s1t,
                    cert_sign1_sub = :s1s,
                    cert_sign1_image = :s1i,
                    cert_sign2_name = :s2n,
                    cert_sign2_title = :s2t,
                    cert_sign2_sub = :s2s,
                    cert_sign2_image = :s2i,
                    cert_subtitle = :csub,
                    cert_seal_text = :cseal,
                    cert_verified_text = :cver
                WHERE id = 1
            ");
            $uStmt->execute([
                's1n'   => $sign1Name,
                's1t'   => $sign1Title,
                's1s'   => $sign1Sub,
                's1i'   => $sign1Img,
                's2n'   => $sign2Name,
                's2t'   => $sign2Title,
                's2s'   => $sign2Sub,
                's2i'   => $sign2Img,
                'csub'  => $subtitle,
                'cseal' => $sealText,
                'cver'  => $verText
            ]);

            $msg = 'Certificate signatory and design settings saved successfully!';
            $msgType = 'success';
        } catch (PDOException $e) {
            $msg = 'Error updating settings: ' . $e->getMessage();
            $msgType = 'danger';
        }
    }
}

// Fetch lists
$pendingRequests     = $pdo->query("SELECT * FROM certificates WHERE status = 'pending' ORDER BY id DESC")->fetchAll();
$approvedCertificates = $pdo->query("SELECT * FROM certificates WHERE status = 'approved' ORDER BY id DESC")->fetchAll();
$allCertificates     = $pdo->query("SELECT * FROM certificates ORDER BY id DESC")->fetchAll();

$certSettings = getCertificateSettings($pdo);

// Fetch enrolled students who have paid tuition fees (convenient quick select dropdown)
$eligibleStudents = $pdo->query("
    SELECT e.student_name, e.student_email, e.student_id, e.course_name, e.enrollment_id, e.fee_amount
    FROM course_enrollments e
    WHERE e.payment_status = 'paid'
    ORDER BY e.id DESC
    LIMIT 100
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Certificate Management | Skill Grow Admin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/admin.css">
  <style>
    .nav-tabs-custom {
      border-bottom: 2px solid #1e2c52;
      gap: 8px;
    }
    .nav-tabs-custom .nav-link {
      color: #94a3b8;
      border: 1px solid transparent;
      border-radius: 8px 8px 0 0;
      padding: 10px 20px;
      font-weight: 600;
      font-size: 0.88rem;
      background: rgba(255, 255, 255, 0.02);
      transition: all 0.2s ease;
    }
    .nav-tabs-custom .nav-link:hover {
      color: #f8fafc;
      background: rgba(255, 255, 255, 0.05);
    }
    .nav-tabs-custom .nav-link.active {
      color: #e6a910;
      background: #0b1736;
      border-color: #1e2c52 #1e2c52 #0b1736;
      border-top: 3px solid #e6a910;
    }
    .stat-metric-card {
      background: #0b1736;
      border: 1px solid #1e2c52;
      border-radius: 14px;
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .stat-metric-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
    }
  </style>
</head>
<body>

  <div class="admin-layout-wrapper">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div class="admin-main">
      <header class="admin-topbar">
        <div>
          <h1 class="page-title-text" style="font-size: 1.25rem; font-family: var(--font-heading); margin: 0; font-weight: 700;">Certificate Issuance &amp; Verification</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
              <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Admin</a></li>
              <li class="breadcrumb-item active text-warning">Certificates</li>
            </ol>
          </nav>
        </div>
        <div class="d-flex gap-2">
          <span class="badge bg-dark border border-secondary px-3 py-2 text-warning d-flex align-items-center gap-2">
            <i class="fas fa-shield-alt"></i> Official Academic Board
          </span>
        </div>
      </header>

      <main class="admin-content">

        <?php if (!empty($msg)): ?>
          <div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert" style="border-radius: 12px;">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <!-- Stat Metrics -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="stat-metric-card" style="<?= !empty($pendingRequests) ? 'border-color: #e6a910; box-shadow: 0 0 15px rgba(230, 169, 16, 0.2);' : '' ?>">
              <div class="stat-metric-icon" style="background: rgba(230, 169, 16, 0.15); color: #e6a910;">
                <i class="fas fa-hourglass-half <?= !empty($pendingRequests) ? 'fa-spin' : '' ?>"></i>
              </div>
              <div>
                <div class="text-muted small" style="font-size: 0.78rem;">Pending Approval Requests</div>
                <div class="fs-4 fw-bold text-white"><?= count($pendingRequests) ?></div>
                <small class="<?= !empty($pendingRequests) ? 'text-warning fw-bold' : 'text-muted' ?>" style="font-size: 0.72rem;">
                  <?= !empty($pendingRequests) ? 'Action Required: Verify & Approve' : 'All requests processed' ?>
                </small>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="stat-metric-card">
              <div class="stat-metric-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
                <i class="fas fa-award"></i>
              </div>
              <div>
                <div class="text-muted small" style="font-size: 0.78rem;">Issued &amp; Active Certificates</div>
                <div class="fs-4 fw-bold text-white"><?= count($approvedCertificates) ?></div>
                <small class="text-success" style="font-size: 0.72rem;">Digitally Verified &amp; Signed</small>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="stat-metric-card">
              <div class="stat-metric-icon" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
                <i class="fas fa-signature"></i>
              </div>
              <div>
                <div class="text-muted small" style="font-size: 0.78rem;">Official Signatories Configured</div>
                <div class="fs-6 fw-bold text-white"><?= htmlspecialchars($certSettings['cert_sign1_name']) ?></div>
                <small class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars($certSettings['cert_sign1_title']) ?></small>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Navigation Tabs -->
        <ul class="nav nav-tabs nav-tabs-custom mb-4" id="certTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link <?= !empty($pendingRequests) ? 'active' : '' ?>" id="pending-tab" data-bs-toggle="tab" data-bs-target="#tabPending" type="button" role="tab">
              <i class="fas fa-clock text-warning me-1"></i> Pending Requests
              <?php if (!empty($pendingRequests)): ?>
                <span class="badge bg-warning text-dark ms-1"><?= count($pendingRequests) ?></span>
              <?php endif; ?>
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link <?= empty($pendingRequests) ? 'active' : '' ?>" id="issued-tab" data-bs-toggle="tab" data-bs-target="#tabIssued" type="button" role="tab">
              <i class="fas fa-check-circle text-success me-1"></i> Issued Archive (<?= count($approvedCertificates) ?>)
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="direct-tab" data-bs-toggle="tab" data-bs-target="#tabDirect" type="button" role="tab">
              <i class="fas fa-plus-circle text-info me-1"></i> Direct Issue
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#tabSettings" type="button" role="tab">
              <i class="fas fa-cog text-warning me-1"></i> Signatories &amp; Seal Settings
            </button>
          </li>
        </ul>

        <div class="tab-content" id="certTabContent">
          
          <!-- ================= TAB 1: PENDING REQUESTS ================= -->
          <div class="tab-pane fade <?= !empty($pendingRequests) ? 'show active' : '' ?>" id="tabPending" role="tabpanel">
            <div class="bento-card">
              <div class="bento-header d-flex justify-content-between align-items-center">
                <h3 class="bento-title">
                  <i class="fas fa-hourglass-half text-warning me-1"></i> Student Certificate Applications Awaiting Approval (<?= count($pendingRequests) ?>)
                </h3>
              </div>

              <div class="table-responsive">
                <table class="admin-table">
                  <thead>
                    <tr>
                      <th>Application Ref</th>
                      <th>Candidate Details</th>
                      <th>Course Applied</th>
                      <th>Request Date &amp; Notes</th>
                      <th class="text-end">Verification Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($pendingRequests)): ?>
                      <?php foreach ($pendingRequests as $pCert): ?>
                        <tr>
                          <td>
                            <div class="font-monospace text-warning fw-bold fs-6">
                              <?= htmlspecialchars($pCert['certificate_number']) ?>
                            </div>
                            <span class="badge bg-warning text-dark" style="font-size: 0.72rem;">
                              <i class="fas fa-clock me-1"></i> Pending Approval
                            </span>
                          </td>
                          <td>
                            <div class="fw-bold text-white fs-6"><?= htmlspecialchars($pCert['student_name']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($pCert['student_email']) ?></div>
                            <?php if (!empty($pCert['student_id'])): ?>
                              <small class="text-secondary font-monospace">Student ID: <?= htmlspecialchars($pCert['student_id']) ?></small>
                            <?php endif; ?>
                          </td>
                          <td>
                            <div class="text-info fw-bold">
                              <i class="fas fa-graduation-cap me-1"></i> <?= htmlspecialchars($pCert['course_name']) ?>
                            </div>
                            <small class="text-muted"><?= htmlspecialchars($pCert['certificate_type'] ?: 'Certificate of Completion') ?></small>
                          </td>
                          <td>
                            <div class="text-white small">
                              <?= date('d M, Y', strtotime($pCert['issue_date'])) ?>
                            </div>
                            <?php if (!empty($pCert['admin_notes'])): ?>
                              <small class="text-muted d-block" style="max-width: 200px; font-size: 0.72rem;">
                                <?= htmlspecialchars(mb_strimwidth($pCert['admin_notes'], 0, 70, '...')) ?>
                              </small>
                            <?php endif; ?>
                          </td>
                          <td class="text-end" style="white-space: nowrap;">
                            <!-- Approve & Dispatch Form -->
                            <form method="POST" action="certificates.php" class="d-inline" onsubmit="return confirm('Approve certificate <?= htmlspecialchars($pCert['certificate_number']) ?> and dispatch official notification to student?');">
                              <input type="hidden" name="action" value="approve">
                              <input type="hidden" name="id" value="<?= (int)$pCert['id'] ?>">
                              <input type="hidden" name="send_email" value="1">
                              <button type="submit" class="btn btn-sm btn-success fw-bold px-3 py-1 me-1">
                                <i class="fas fa-check-circle me-1"></i> Approve &amp; Dispatch Mail
                              </button>
                            </form>

                            <!-- View Tracking Link -->
                            <a href="../view-certificate.php?cert=<?= urlencode($pCert['certificate_number']) ?>" target="_blank" class="btn btn-sm btn-outline-warning py-1 px-2 me-1" title="View Current Status Screen">
                              <i class="fas fa-eye"></i>
                            </a>

                            <!-- Reject Form -->
                            <form method="POST" action="certificates.php" class="d-inline" onsubmit="return confirm('Reject this certificate request?');">
                              <input type="hidden" name="action" value="reject">
                              <input type="hidden" name="id" value="<?= (int)$pCert['id'] ?>">
                              <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Reject Request">
                                <i class="fas fa-times"></i>
                              </button>
                            </form>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                          <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                          No pending certificate applications! All student requests have been approved and processed.
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- ================= TAB 2: ISSUED ARCHIVE ================= -->
          <div class="tab-pane fade <?= empty($pendingRequests) ? 'show active' : '' ?>" id="tabIssued" role="tabpanel">
            <div class="bento-card">
              <div class="bento-header d-flex justify-content-between align-items-center">
                <h3 class="bento-title">
                  <i class="fas fa-award text-success me-1"></i> Issued &amp; Officially Verified Certificates (<?= count($approvedCertificates) ?>)
                </h3>
              </div>

              <div class="table-responsive">
                <table class="admin-table">
                  <thead>
                    <tr>
                      <th>Certificate ID</th>
                      <th>Student &amp; Course</th>
                      <th>Grade &amp; Issue Date</th>
                      <th class="text-end">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($approvedCertificates)): ?>
                      <?php foreach ($approvedCertificates as $certItem): ?>
                        <tr>
                          <td>
                            <div class="font-monospace text-warning fw-bold" style="font-size: 0.88rem;">
                              <?= htmlspecialchars($certItem['certificate_number']) ?>
                            </div>
                            <span class="badge bg-success" style="font-size: 0.72rem;">
                              <i class="fas fa-check-double me-1"></i> Approved &amp; Live
                            </span>
                          </td>
                          <td>
                            <div class="fw-bold text-white"><?= htmlspecialchars($certItem['student_name']) ?></div>
                            <div class="text-muted small" style="font-size: 0.76rem;"><?= htmlspecialchars($certItem['student_email']) ?></div>
                            <div class="text-info mt-1" style="font-size: 0.82rem; font-weight: 600;">
                              <i class="fas fa-graduation-cap me-1"></i> <?= htmlspecialchars($certItem['course_name']) ?>
                            </div>
                          </td>
                          <td>
                            <span class="badge bg-success mb-1" style="font-size: 0.72rem;">
                              Grade: <?= htmlspecialchars($certItem['grade'] ?: 'A') ?>
                            </span>
                            <div class="text-white small" style="font-size: 0.78rem;">
                              <?= date('d M, Y', strtotime($certItem['issue_date'])) ?>
                            </div>
                          </td>
                          <td class="text-end" style="white-space: nowrap;">
                            <a href="../view-certificate.php?cert=<?= urlencode($certItem['certificate_number']) ?>" target="_blank" class="btn btn-sm btn-outline-warning py-1 px-2 me-1" title="View & Print Official Certificate">
                              <i class="fas fa-print me-1"></i> View / Print
                            </a>
                            <form method="POST" action="certificates.php" class="d-inline">
                              <input type="hidden" name="action" value="resend_email">
                              <input type="hidden" name="id" value="<?= (int)$certItem['id'] ?>">
                              <button type="submit" class="btn btn-sm btn-outline-info py-1 px-2 me-1" title="Resend Certificate Email to Student">
                                <i class="fas fa-envelope"></i> Resend Mail
                              </button>
                            </form>
                            <form method="POST" action="certificates.php" class="d-inline" onsubmit="return confirm('Remove this certificate record?');">
                              <input type="hidden" name="action" value="delete">
                              <input type="hidden" name="id" value="<?= (int)$certItem['id'] ?>">
                              <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete Certificate">
                                <i class="fas fa-trash-alt"></i>
                              </button>
                            </form>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                          No approved certificates issued yet. You can approve student applications from the Pending Requests tab or issue directly from the Direct Issue tab.
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>

          <!-- ================= TAB 3: DIRECT ISSUE ================= -->
          <div class="tab-pane fade" id="tabDirect" role="tabpanel">
            <div class="row g-4">
              <div class="col-lg-6 mx-auto">
                <div class="bento-card">
                  <div class="bento-header">
                    <h3 class="bento-title"><i class="fas fa-award text-warning me-1"></i> Direct Official Certificate Issuance</h3>
                    <p class="text-muted small mb-0">Directly generate an approved certificate for any student and notify them via email.</p>
                  </div>

                  <!-- Quick Autofill from paid enrollment -->
                  <?php if (!empty($eligibleStudents)): ?>
                    <div class="mb-3">
                      <label class="form-label text-warning" style="font-size: 0.8rem; font-weight: 600;">⚡ Quick Select Enrolled Candidate:</label>
                      <select class="form-select" id="quickSelectStudent" onchange="autoFillStudent(this.value)" style="font-size: 0.83rem;">
                        <option value="">-- Choose From Paid Enrolled Candidates --</option>
                        <?php foreach ($eligibleStudents as $idx => $st): ?>
                          <option value="<?= $idx ?>">
                            <?= htmlspecialchars($st['student_name']) ?> &mdash; <?= htmlspecialchars($st['course_name']) ?> (<?= htmlspecialchars($st['enrollment_id']) ?>)
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  <?php endif; ?>

                  <form method="POST" action="certificates.php">
                    <input type="hidden" name="action" value="issue">
                    <input type="hidden" name="enrollment_id" id="cert_enr_id" value="">

                    <div class="mb-3">
                      <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Student Full Legal Name *</label>
                      <input type="text" class="form-control" name="student_name" id="cert_name" placeholder="Full legal name" required>
                    </div>

                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Student ID</label>
                        <input type="text" class="form-control font-monospace" name="student_id" id="cert_student_id" placeholder="e.g. STU-2026-6757">
                      </div>
                      <div class="col-6">
                        <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Performance Grade</label>
                        <select class="form-select" name="grade">
                          <option value="A (Distinction)" selected>A (Distinction)</option>
                          <option value="A+ (Outstanding)">A+ (Outstanding)</option>
                          <option value="A (Excellent)">A (Excellent)</option>
                          <option value="B+ (Very Good)">B+ (Very Good)</option>
                          <option value="B (Good)">B (Good)</option>
                        </select>
                      </div>
                    </div>

                    <div class="mb-3">
                      <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Candidate Email *</label>
                      <input type="email" class="form-control" name="student_email" id="cert_email" placeholder="student@example.com" required>
                    </div>

                    <div class="mb-3">
                      <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Course / Academic Program *</label>
                      <input type="text" class="form-control" name="course_name" id="cert_course" placeholder="e.g. Full Stack Web Technology" required>
                    </div>

                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Certificate Heading</label>
                        <select class="form-select" name="certificate_type">
                          <option value="Certificate of Completion" selected>Certificate of Completion</option>
                          <option value="Executive Diploma">Executive Diploma</option>
                          <option value="Advanced Master Certification">Advanced Master Certification</option>
                          <option value="Certificate of Excellence">Certificate of Excellence</option>
                        </select>
                      </div>
                      <div class="col-6">
                        <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Date of Issue</label>
                        <input type="date" class="form-control" name="issue_date" value="<?= date('Y-m-d') ?>">
                      </div>
                    </div>

                    <div class="form-check form-switch mb-3 p-2 rounded-2" style="background: rgba(230, 169, 16, 0.08); border: 1px dashed rgba(230, 169, 16, 0.3);">
                      <input class="form-check-input ms-0 me-2" type="checkbox" name="send_email" id="sendEmailSwitch" value="1" checked>
                      <label class="form-check-label text-warning fw-bold" for="sendEmailSwitch" style="font-size: 0.82rem;">
                        <i class="fas fa-paper-plane me-1"></i> Send Official Certificate Email to Student
                      </label>
                    </div>

                    <button type="submit" class="btn-gold w-100">
                      <i class="fas fa-stamp me-1"></i> Issue &amp; Generate Certificate
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- ================= TAB 4: SIGNATORIES & SEAL SETTINGS ================= -->
          <div class="tab-pane fade" id="tabSettings" role="tabpanel">
            <div class="row g-4">
              <div class="col-lg-8 mx-auto">
                <div class="bento-card">
                  <div class="bento-header">
                    <h3 class="bento-title"><i class="fas fa-signature text-warning me-1"></i> Certificate Signatories &amp; Seal Configuration</h3>
                    <p class="text-muted small mb-0">These details appear dynamically on all official student certificates generated by Skill Grow Institution.</p>
                  </div>

                  <form method="POST" action="certificates.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_settings">

                    <!-- Tagline & Seal Row -->
                    <div class="p-3 mb-4 rounded-3" style="background: rgba(255, 255, 255, 0.02); border: 1px solid #1e2c52;">
                      <h5 class="text-warning fw-bold fs-6 mb-3"><i class="fas fa-certificate me-1"></i> Certificate Title &amp; Official Seal</h5>
                      <div class="row g-3">
                        <div class="col-12">
                          <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Certificate Subtitle / Tagline</label>
                          <input type="text" class="form-control" name="cert_subtitle" value="<?= htmlspecialchars($certSettings['cert_subtitle']) ?>" placeholder="e.g. National Academy of Professional &amp; Industry Excellence" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Gold Seal Center Text</label>
                          <input type="text" class="form-control" name="cert_seal_text" value="<?= htmlspecialchars($certSettings['cert_seal_text']) ?>" placeholder="e.g. OFFICIAL SEAL" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Seal Verification Subtitle</label>
                          <input type="text" class="form-control" name="cert_verified_text" value="<?= htmlspecialchars($certSettings['cert_verified_text']) ?>" placeholder="e.g. VERIFIED CREDENTIAL" required>
                        </div>
                      </div>
                    </div>

                    <!-- Signatory 1 (Left) -->
                    <div class="p-3 mb-4 rounded-3" style="background: rgba(255, 255, 255, 0.02); border: 1px solid #1e2c52;">
                      <h5 class="text-info fw-bold fs-6 mb-3"><i class="fas fa-user-tie me-1"></i> Authorized Signatory 1 (Academic Director / Dean)</h5>
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Signatory Name *</label>
                          <input type="text" class="form-control" name="cert_sign1_name" value="<?= htmlspecialchars($certSettings['cert_sign1_name']) ?>" placeholder="e.g. Dr. R. K. Singhania" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Designation / Title *</label>
                          <input type="text" class="form-control" name="cert_sign1_title" value="<?= htmlspecialchars($certSettings['cert_sign1_title']) ?>" placeholder="e.g. Academic Director" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Institution / Department *</label>
                          <input type="text" class="form-control" name="cert_sign1_sub" value="<?= htmlspecialchars($certSettings['cert_sign1_sub']) ?>" placeholder="e.g. Skill Grow Institution" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Upload Signature Image (Optional)</label>
                          <input type="file" class="form-control" name="cert_sign1_image_file" accept="image/*">
                          <?php if (!empty($certSettings['cert_sign1_image'])): ?>
                            <small class="text-success mt-1 d-block"><i class="fas fa-check-circle"></i> Custom signature image active</small>
                          <?php else: ?>
                            <small class="text-muted mt-1 d-block">If no image uploaded, elegant script signature will be rendered.</small>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>

                    <!-- Signatory 2 (Right) -->
                    <div class="p-3 mb-4 rounded-3" style="background: rgba(255, 255, 255, 0.02); border: 1px solid #1e2c52;">
                      <h5 class="text-info fw-bold fs-6 mb-3"><i class="fas fa-user-check me-1"></i> Authorized Signatory 2 (Examination Controller / Registrar)</h5>
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Signatory Name *</label>
                          <input type="text" class="form-control" name="cert_sign2_name" value="<?= htmlspecialchars($certSettings['cert_sign2_name']) ?>" placeholder="e.g. Priya Sharma" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Designation / Title *</label>
                          <input type="text" class="form-control" name="cert_sign2_title" value="<?= htmlspecialchars($certSettings['cert_sign2_title']) ?>" placeholder="e.g. Controller of Examinations" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Council / Organization *</label>
                          <input type="text" class="form-control" name="cert_sign2_sub" value="<?= htmlspecialchars($certSettings['cert_sign2_sub']) ?>" placeholder="e.g. Academic Council" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Upload Signature Image (Optional)</label>
                          <input type="file" class="form-control" name="cert_sign2_image_file" accept="image/*">
                          <?php if (!empty($certSettings['cert_sign2_image'])): ?>
                            <small class="text-success mt-1 d-block"><i class="fas fa-check-circle"></i> Custom signature image active</small>
                          <?php else: ?>
                            <small class="text-muted mt-1 d-block">If no image uploaded, elegant script signature will be rendered.</small>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>

                    <button type="submit" class="btn-gold w-100 py-2 fs-6">
                      <i class="fas fa-save me-1"></i> Save Certificate Signatories &amp; Design Settings
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>

        </div>

      </main>
      <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const eligibleStudentsData = <?= json_encode($eligibleStudents, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    function autoFillStudent(idx) {
      if (idx === '' || !eligibleStudentsData[idx]) return;
      const st = eligibleStudentsData[idx];

      document.getElementById('cert_name').value = st.student_name || '';
      document.getElementById('cert_email').value = st.student_email || '';
      document.getElementById('cert_student_id').value = st.student_id || '';
      document.getElementById('cert_course').value = st.course_name || '';
      document.getElementById('cert_enr_id').value = st.enrollment_id || '';
    }
  </script>
</body>
</html>
