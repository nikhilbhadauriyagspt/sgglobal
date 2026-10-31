<?php
// Certificate Verification & Request Portal - Skill Grow Institution
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/certificates_helper.php';

ensureCertificatesTable($pdo);

$isLoggedIn    = !empty($_SESSION['student_logged_in']);
$studentId     = $_SESSION['student_id'] ?? '';
$studentName   = $_SESSION['student_name'] ?? '';
$studentEmail  = $_SESSION['student_email'] ?? '';
$studentPhone  = $_SESSION['student_phone'] ?? '';

// Search / Verify logic
$searchCertId  = trim($_GET['verify'] ?? '');
$verifiedCert  = null;
$searched      = false;

if (!empty($searchCertId)) {
    $searched = true;
    $vStmt = $pdo->prepare("SELECT * FROM certificates WHERE certificate_number = :cnum LIMIT 1");
    $vStmt->execute(['cnum' => $searchCertId]);
    $verifiedCert = $vStmt->fetch();
}

// Fetch active courses for the dropdown
$coursesList = $pdo->query("SELECT title FROM courses WHERE status = 'active' ORDER BY title ASC")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Certificate Verification &amp; Request - Skill Grow Institution</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="icon" href="assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    body {
      background-color: #070e24;
      color: #cbd5e1;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .cert-hero {
      position: relative;
      padding: 190px 0 90px;
      background: linear-gradient(135deg, rgba(0, 12, 39, 0.95) 0%, rgba(0, 35, 101, 0.85) 100%), url('assets/images/banner/skill-aboutbg.jpg') no-repeat center/cover;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      text-align: center;
      color: #fff;
    }

    .cert-hero h1 {
      font-family: 'Roboto Slab', serif;
      font-size: clamp(2.1rem, 3.8vw, 3rem);
      font-weight: 700;
      margin-bottom: 14px;
    }

    .cert-hero p {
      font-size: 1.05rem;
      color: rgba(255, 255, 255, 0.78);
      max-width: 680px;
      margin: 0 auto;
    }

    .verify-box-card {
      background: #0f1537;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-top: 3px solid var(--primary-color);
      border-radius: 16px;
      padding: 34px 28px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
      margin-top: -45px;
      position: relative;
      z-index: 5;
    }

    .verify-input-group {
      display: flex;
      gap: 10px;
    }

    .verify-input {
      background: #000c27 !important;
      border: 1px solid rgba(255, 255, 255, 0.15) !important;
      color: #ffffff !important;
      font-family: monospace;
      font-size: 1rem;
      padding: 12px 18px;
      border-radius: 10px;
    }

    .verify-input:focus {
      border-color: var(--primary-color) !important;
      box-shadow: none !important;
    }

    .verified-result-card {
      background: #000c27;
      border: 1px solid #10b981;
      border-radius: 14px;
      padding: 24px;
      margin-top: 24px;
      text-align: left;
    }

    .cert-request-section {
      padding: 85px 0;
    }

    .request-card {
      background: #0f1537;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      padding: 38px 32px;
    }

    .request-card h3 {
      font-family: 'Roboto Slab', serif;
      font-size: 1.55rem;
      color: #ffffff;
      font-weight: 700;
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-label-custom {
      font-size: 0.84rem;
      font-weight: 600;
      color: #cbd5e1;
      margin-bottom: 6px;
    }

    .form-control-custom,
    .form-select-custom {
      background-color: #000c27 !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      color: #ffffff !important;
      padding: 11px 15px;
      border-radius: 10px;
      font-size: 0.92rem;
    }

    .form-control-custom:focus,
    .form-select-custom:focus {
      border-color: var(--primary-color) !important;
      box-shadow: none !important;
    }

    .features-card {
      background: #0f1537;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      padding: 32px 28px;
      height: 100%;
    }

    .features-card h4 {
      font-family: 'Roboto Slab', serif;
      font-size: 1.35rem;
      color: #ffffff;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .cert-features-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .cert-features-list li {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin-bottom: 18px;
      font-size: 0.92rem;
      line-height: 1.6;
      color: rgba(255, 255, 255, 0.78);
    }

    .cert-features-list li i {
      color: var(--primary-color);
      font-size: 1rem;
      margin-top: 4px;
      flex-shrink: 0;
    }

    .sample-preview-card {
      background: #000c27;
      border: 1px solid rgba(230, 169, 16, 0.3);
      border-radius: 12px;
      padding: 16px;
      text-align: center;
      margin-top: 24px;
    }
  </style>
</head>
<body>

  <!-- Unified Header -->
  <?php include 'header.php'; ?>

  <!-- Hero Banner -->
  <section class="cert-hero">
    <div class="container">
      <span class="sgi-about-eyebrow">Academic Examination Council</span>
      <h1>Official Certificate Verification &amp; Request</h1>
      <p>Verify official credentials issued by Skill Grow Institution or request your completion certificate with immediate digital validation.</p>
    </div>
  </section>

  <!-- Online Verification Section -->
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="verify-box-card">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fas fa-search text-warning"></i>
            <h4 class="text-white mb-0" style="font-family: 'Roboto Slab', serif; font-size: 1.25rem; font-weight: 700;">
              Verify Credential Online
            </h4>
          </div>
          <p class="text-secondary small mb-3">
            Enter the unique Certificate ID (e.g., <code>SGI-CERT-2026-XXXXX</code>) to verify student identity, course completion, and issuance records.
          </p>

          <form method="GET" action="certificate.php" class="verify-input-group">
            <input type="text" name="verify" class="form-control verify-input" placeholder="e.g. SGI-CERT-2026-8942" value="<?= htmlspecialchars($searchCertId) ?>" required>
            <button type="submit" class="btn btn-warning fw-bold px-4" style="border-radius: 10px; font-family: 'Roboto Slab', serif;">
              <i class="fas fa-check-circle me-1"></i> Verify
            </button>
          </form>

          <?php if ($searched): ?>
            <?php if ($verifiedCert): ?>
              <?php $vStatus = strtolower(trim($verifiedCert['status'] ?? 'pending')); ?>
              <div class="verified-result-card">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                  <div class="d-flex align-items-center gap-2">
                    <?php if ($vStatus === 'approved'): ?>
                      <span class="badge bg-success px-3 py-2"><i class="fas fa-shield-alt me-1"></i> OFFICIALLY VERIFIED &amp; ISSUED</span>
                    <?php elseif ($vStatus === 'pending'): ?>
                      <span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-clock me-1"></i> UNDER VERIFICATION (PENDING APPROVAL)</span>
                    <?php else: ?>
                      <span class="badge bg-danger px-3 py-2"><i class="fas fa-times-circle me-1"></i> NOT APPROVED</span>
                    <?php endif; ?>
                    <span class="text-warning font-monospace fw-bold"><?= htmlspecialchars($verifiedCert['certificate_number']) ?></span>
                  </div>
                  <a href="view-certificate.php?cert=<?= urlencode($verifiedCert['certificate_number']) ?>" target="_blank" class="btn btn-sm btn-outline-warning">
                    <?= $vStatus === 'approved' ? '<i class="fas fa-print me-1"></i> View Official Certificate' : '<i class="fas fa-clock me-1"></i> Check Application Status' ?>
                  </a>
                </div>
                <div class="row g-2 text-white small">
                  <div class="col-sm-6">
                    <span class="text-secondary">Student Name:</span>
                    <div class="fw-bold fs-6"><?= htmlspecialchars($verifiedCert['student_name']) ?></div>
                  </div>
                  <div class="col-sm-6">
                    <span class="text-secondary">Course Program:</span>
                    <div class="fw-bold text-info fs-6"><?= htmlspecialchars($verifiedCert['course_name']) ?></div>
                  </div>
                  <div class="col-sm-6 mt-2">
                    <span class="text-secondary">Performance Grade:</span>
                    <div class="fw-bold <?= $vStatus === 'approved' ? 'text-success' : 'text-warning' ?>"><?= htmlspecialchars($verifiedCert['grade'] ?: 'A (Distinction)') ?></div>
                  </div>
                  <div class="col-sm-6 mt-2">
                    <span class="text-secondary">Date of Application / Issue:</span>
                    <div><?= date('d M, Y', strtotime($verifiedCert['issue_date'])) ?></div>
                  </div>
                </div>
              </div>
            <?php else: ?>
              <div class="alert alert-danger mt-3 mb-0" style="border-radius: 10px;">
                <i class="fas fa-triangle-exclamation me-1"></i> No official certificate found with ID <strong><?= htmlspecialchars($searchCertId) ?></strong>. Please verify the credentials entered.
              </div>
            <?php endif; ?>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>

  <!-- Certificate Request Section -->
  <section class="cert-request-section">
    <div class="container">
      <div class="row g-4">
        
        <!-- Left: Certificate Request Form (7 cols) -->
        <div class="col-lg-7">
          <div class="request-card">
            <h3><i class="fas fa-certificate text-warning"></i> Request Your Completion Certificate</h3>
            <p class="text-secondary small mb-4">
              Upon submission, your credential will be verified by the academic controller and delivered to your email with instant printable access.
            </p>

            <form method="POST" action="certificate_request.php">
              <input type="hidden" name="student_id" value="<?= htmlspecialchars($studentId) ?>">

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label-custom">Full Legal Name *</label>
                  <input type="text" class="form-control form-control-custom" name="full_name" value="<?= htmlspecialchars($studentName) ?>" placeholder="As it should appear on certificate" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label-custom">Email Address *</label>
                  <input type="email" class="form-control form-control-custom" name="email" value="<?= htmlspecialchars($studentEmail) ?>" placeholder="Official registered email" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label-custom">Mobile Number *</label>
                  <input type="tel" class="form-control form-control-custom" name="phone" value="<?= htmlspecialchars($studentPhone) ?>" placeholder="10-digit mobile number" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label-custom">Course Completed *</label>
                  <?php if (!empty($coursesList)): ?>
                    <select class="form-select form-select-custom" name="course" required>
                      <option value="">-- Select Completed Course --</option>
                      <?php foreach ($coursesList as $cTitle): ?>
                        <option value="<?= htmlspecialchars($cTitle) ?>"><?= htmlspecialchars($cTitle) ?></option>
                      <?php endforeach; ?>
                    </select>
                  <?php else: ?>
                    <input type="text" class="form-control form-control-custom" name="course" placeholder="e.g. Advanced Management Studies" required>
                  <?php endif; ?>
                </div>

                <div class="col-12">
                  <label class="form-label-custom">Certificate Delivery Format *</label>
                  <select class="form-select form-select-custom" name="certificate_type">
                    <option value="Certificate of Completion" selected>Digital High-Res Certificate (PDF / Online Verified)</option>
                    <option value="Certificate of Professional Excellence">Certificate of Professional Excellence (Honors)</option>
                    <option value="Certificate of Industrial Training">Certificate of Industrial Training</option>
                  </select>
                </div>

                <div class="col-12">
                  <label class="form-label-custom">Additional Information / Enrollment ID (Optional)</label>
                  <textarea class="form-control form-control-custom" name="additional_info" rows="3" placeholder="Batch details, Student ID, or special honors request..."></textarea>
                </div>

                <div class="col-12 mt-4">
                  <button type="submit" class="btn btn-warning w-100 py-3 fw-bold" style="border-radius: 10px; font-family: 'Roboto Slab', serif; font-size: 1rem;">
                    <i class="fas fa-paper-plane me-1"></i> Submit &amp; Generate Certificate
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Right: Why Get Certified & Highlights (5 cols) -->
        <div class="col-lg-5">
          <div class="features-card">
            <h4><i class="fas fa-medal text-warning me-1"></i> Why Get Certified?</h4>
            <ul class="cert-features-list">
              <li>
                <i class="fas fa-check-circle"></i>
                <div>
                  <strong class="text-white d-block">Industry-Accredited Credential</strong>
                  <span>Recognized across top corporate sectors, IT enterprises, and aviation organizations.</span>
                </div>
              </li>
              <li>
                <i class="fas fa-check-circle"></i>
                <div>
                  <strong class="text-white d-block">Instant Public Verification</strong>
                  <span>Includes a unique Certificate ID verifiable directly by employers on our official portal.</span>
                </div>
              </li>
              <li>
                <i class="fas fa-check-circle"></i>
                <div>
                  <strong class="text-white d-block">Print-Ready High Resolution</strong>
                  <span>Formatted for pristine A4 landscape color printing with gold seal embellishments.</span>
                </div>
              </li>
              <li>
                <i class="fas fa-check-circle"></i>
                <div>
                  <strong class="text-white d-block">Permanent Student Portal Archive</strong>
                  <span>Access, view, and re-download your credentials anytime from your student dashboard.</span>
                </div>
              </li>
            </ul>

            <div class="sample-preview-card">
              <span class="badge bg-warning text-dark fw-bold mb-2">OFFICIAL SAMPLE PREVIEW</span>
              <img src="assets/images/certi/Certificate.png" alt="Sample Certificate" class="img-fluid rounded" onerror="this.style.display='none'">
              <div class="mt-2 text-secondary small">Skill Grow Institution Gold Embellished Certification</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
