<?php
// Printable Official Certificate Template & Status Verification - Skill Grow Institution
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/certificates_helper.php';
require_once __DIR__ . '/includes/settings_helper.php';

ensureCertificatesTable($pdo);
$settings = getGlobalSettings();
$certSettings = getCertificateSettings($pdo);

$certNumber = trim($_GET['cert'] ?? '');
if (empty($certNumber)) {
    die("Error: No certificate number specified.");
}

$stmt = $pdo->prepare("SELECT * FROM certificates WHERE certificate_number = :num LIMIT 1");
$stmt->execute(['num' => $certNumber]);
$cert = $stmt->fetch();

if (!$cert) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Certificate Not Found | <?= htmlspecialchars($settings['site_title'] ?: 'Skill Grow Institution') ?></title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </head>
    <body style="background: #070e24; color: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; padding: 20px;">
      <div style="max-width: 520px; width: 100%; background: #0f172a; border: 1px solid #1e293b; border-radius: 16px; padding: 36px; text-align: center;">
        <div style="font-size: 54px; color: #ef4444; margin-bottom: 16px;"><i class="fas fa-exclamation-triangle"></i></div>
        <h3 class="fw-bold mb-2">Certificate Record Not Found</h3>
        <p class="text-secondary small mb-4">No certificate record was found matching ID: <code><?= htmlspecialchars($certNumber) ?></code>. Please verify the certificate number and try again.</p>
        <a href="certificate.php" class="btn btn-warning fw-bold px-4">Search Certificate</a>
      </div>
    </body>
    </html>
    <?php
    exit;
}

$issueDate = date('F d, Y', strtotime($cert['issue_date'] ?: date('Y-m-d')));
$status = strtolower(trim($cert['status'] ?? 'pending'));

// IF CERTIFICATE IS NOT APPROVED (PENDING OR REJECTED), SHOW STATUS CARD INSTEAD OF CERTIFICATE!
if ($status !== 'approved'):
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Certificate Status &mdash; <?= htmlspecialchars($cert['certificate_number']) ?></title>
  <link rel="icon" href="assets/images/logo/logopng.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      background: radial-gradient(circle at 50% 0%, #0d1a3a 0%, #070e24 100%);
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px 15px;
      color: #e2e8f0;
    }
    .status-card {
      max-width: 650px;
      width: 100%;
      background: #0c1634;
      border: 1px solid #1a274e;
      border-radius: 18px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
      overflow: hidden;
    }
    .status-header {
      background: #002365;
      padding: 24px 30px;
      text-align: center;
      border-bottom: 2px solid #e6a910;
    }
    .status-body {
      padding: 34px 32px;
    }
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 18px;
      border-radius: 30px;
      font-weight: 700;
      font-size: 0.88rem;
      margin-bottom: 20px;
    }
    .badge-pending {
      background: rgba(230, 169, 16, 0.15);
      border: 1px solid #e6a910;
      color: #e6a910;
    }
    .badge-rejected {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid #ef4444;
      color: #ef4444;
    }
    .detail-table {
      width: 100%;
      border-collapse: collapse;
      margin: 22px 0;
    }
    .detail-table td {
      padding: 11px 14px;
      border-bottom: 1px solid #1a274e;
      font-size: 0.88rem;
    }
    .detail-table td.label {
      color: #94a3b8;
      width: 40%;
      font-weight: 600;
    }
    .detail-table td.val {
      color: #ffffff;
      font-weight: 700;
    }
    .btn-action-primary {
      background: linear-gradient(135deg, #f59e0b 0%, #e6a910 100%);
      color: #070e24;
      font-weight: 700;
      text-decoration: none;
      padding: 11px 24px;
      border-radius: 8px;
      display: inline-block;
      font-size: 0.9rem;
      transition: all 0.2s ease;
    }
    .btn-action-primary:hover {
      background: #f59e0b;
      color: #000;
      transform: translateY(-2px);
    }
    .btn-action-secondary {
      background: rgba(255, 255, 255, 0.08);
      color: #cbd5e1;
      border: 1px solid rgba(255, 255, 255, 0.15);
      font-weight: 600;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 8px;
      display: inline-block;
      font-size: 0.88rem;
    }
    .btn-action-secondary:hover {
      background: rgba(255, 255, 255, 0.15);
      color: #ffffff;
    }
  </style>
</head>
<body>

  <div class="status-card">
    <div class="status-header">
      <img src="<?= htmlspecialchars($settings['site_logo'] ?: 'assets/images/logo/logopng.png') ?>" alt="Skill Grow Logo" style="height: 48px; margin-bottom: 10px;" onerror="this.src='assets/images/logo/logopng.png'">
      <h4 class="text-white mb-1 fw-bold"><?= htmlspecialchars($settings['site_title'] ?: 'Skill Grow Institution') ?></h4>
      <p class="text-warning small mb-0 text-uppercase" style="letter-spacing: 1px;">Official Certification Verification Council</p>
    </div>

    <div class="status-body text-center">
      <?php if ($status === 'pending'): ?>
        <div class="status-badge badge-pending">
          <i class="fas fa-hourglass-half fa-spin"></i> Status: Verification Under Review / Pending Admin Approval
        </div>

        <h3 class="text-white fw-bold mb-2">Certificate Request In Progress</h3>
        <p class="text-secondary small mb-4" style="line-height: 1.6;">
          Your certificate request has been submitted and registered in our central academic database. The administration is actively verifying your academic completion and enrollment credentials.
        </p>

        <div class="text-start">
          <table class="detail-table">
            <tr>
              <td class="label">Certificate Application ID</td>
              <td class="val text-warning font-monospace"><?= htmlspecialchars($cert['certificate_number']) ?></td>
            </tr>
            <tr>
              <td class="label">Candidate Name</td>
              <td class="val"><?= htmlspecialchars($cert['student_name']) ?></td>
            </tr>
            <tr>
              <td class="label">Registered Email</td>
              <td class="val"><?= htmlspecialchars($cert['student_email']) ?></td>
            </tr>
            <tr>
              <td class="label">Academic Program</td>
              <td class="val text-info"><?= htmlspecialchars($cert['course_name']) ?></td>
            </tr>
            <tr>
              <td class="label">Submission Date</td>
              <td class="val"><?= htmlspecialchars($issueDate) ?></td>
            </tr>
            <tr>
              <td class="label">Current Stage</td>
              <td class="val text-warning">
                <i class="fas fa-clock me-1"></i> Awaiting Academic Administrator Verification
              </td>
            </tr>
          </table>
        </div>

        <div class="p-3 rounded-3 text-start mb-4" style="background: rgba(230, 169, 16, 0.08); border-left: 4px solid #e6a910; font-size: 0.82rem; color: #cbd5e1; line-height: 1.6;">
          <i class="fas fa-shield-alt text-warning me-1"></i> 
          <strong>Security Notice:</strong> The official digitally signed certificate with academic seal and QR verification will unlock automatically on this link immediately once approved by the administrator. An official notification with certificate link will also be dispatched to your email.
        </div>

      <?php else: ?>
        <div class="status-badge badge-rejected">
          <i class="fas fa-times-circle"></i> Status: Certificate Request Not Approved
        </div>

        <h3 class="text-white fw-bold mb-2">Request Not Verified</h3>
        <p class="text-secondary small mb-4" style="line-height: 1.6;">
          This certificate request could not be approved by the academic board. Please check with your course coordinator.
        </p>

        <?php if (!empty($cert['admin_notes'])): ?>
          <div class="alert alert-danger text-start small mb-4" style="border-radius: 10px;">
            <strong>Admin Remarks:</strong> <?= htmlspecialchars($cert['admin_notes']) ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
        <a href="my-enrollments.php" class="btn-action-primary">
          <i class="fas fa-user-graduate me-1"></i> Student Dashboard
        </a>
        <a href="certificate.php" class="btn-action-secondary">
          <i class="fas fa-search me-1"></i> Verify Another Certificate
        </a>
      </div>

    </div>
  </div>

</body>
</html>
<?php
exit;
endif;

// =========================================================================
// BELOW IS THE OFFICIAL PRINTABLE CERTIFICATE TEMPLATE (STATUS == 'APPROVED')
// =========================================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Official Certificate - <?= htmlspecialchars($cert['student_name']) ?> (<?= htmlspecialchars($cert['certificate_number']) ?>)</title>
  <link rel="icon" href="assets/images/logo/logopng.png" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800;900&family=Great+Vibes&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Roboto+Slab:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background: #0f172a;
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 30px 15px;
      color: #333;
    }

    .action-bar {
      width: 100%;
      max-width: 1060px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      background: #1e293b;
      padding: 14px 22px;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,0.1);
    }
    .action-bar-title {
      color: #f8fafc;
      font-weight: 700;
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .btn-action {
      background: linear-gradient(135deg, #f59e0b 0%, #e6a910 100%);
      color: #070e24;
      font-weight: 700;
      border: none;
      padding: 10px 22px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 0.9rem;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      box-shadow: 0 4px 12px rgba(230, 169, 16, 0.3);
      transition: all 0.2s ease;
    }
    .btn-action:hover {
      background: #f59e0b;
      transform: translateY(-2px);
    }
    .btn-back {
      background: rgba(255,255,255,0.08);
      color: #ffffff;
      border: 1px solid rgba(255,255,255,0.15);
      font-weight: 600;
      padding: 9px 18px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 0.88rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .btn-back:hover {
      background: rgba(255,255,255,0.15);
      color: #ffffff;
    }

    /* Certificate Outer Canvas */
    .cert-canvas {
      width: 1060px;
      min-height: 750px;
      background: #ffffff;
      background-image: 
        radial-gradient(circle at 50% 50%, rgba(254, 249, 231, 0.45) 0%, rgba(255, 255, 255, 1) 100%),
        repeating-linear-gradient(45deg, rgba(230, 169, 16, 0.015) 0px, rgba(230, 169, 16, 0.015) 2px, transparent 2px, transparent 8px);
      padding: 24px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
      position: relative;
      border-radius: 6px;
    }

    /* Double Ornate Border */
    .cert-border-outer {
      border: 6px double #002365;
      padding: 10px;
      height: 100%;
      position: relative;
    }
    .cert-border-inner {
      border: 2px solid #e6a910;
      padding: 40px 50px 30px;
      height: 100%;
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      text-align: center;
    }

    /* Corner Embellishments */
    .cert-corner {
      position: absolute;
      width: 40px;
      height: 40px;
      border: 3px solid #002365;
    }
    .cert-corner-tl { top: -6px; left: -6px; border-right: none; border-bottom: none; }
    .cert-corner-tr { top: -6px; right: -6px; border-left: none; border-bottom: none; }
    .cert-corner-bl { bottom: -6px; left: -6px; border-right: none; border-top: none; }
    .cert-corner-br { bottom: -6px; right: -6px; border-left: none; border-top: none; }

    /* Header */
    .cert-header {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
      margin-bottom: 8px;
    }
    .cert-logo {
      height: 64px;
      width: auto;
      object-fit: contain;
    }
    .cert-org-name {
      font-family: 'Cinzel', serif;
      font-size: 1.85rem;
      font-weight: 800;
      letter-spacing: 2px;
      color: #002365;
      text-transform: uppercase;
    }
    .cert-subtitle {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 0.76rem;
      font-weight: 600;
      letter-spacing: 3px;
      color: #b45309;
      text-transform: uppercase;
      margin-top: 2px;
    }

    /* Main Certificate Title */
    .cert-main-title {
      font-family: 'Cinzel', serif;
      font-size: 2.3rem;
      font-weight: 700;
      color: #002365;
      letter-spacing: 3px;
      margin: 18px 0 10px;
      text-transform: uppercase;
      position: relative;
      display: inline-block;
    }
    .cert-main-title::after {
      content: "";
      display: block;
      width: 140px;
      height: 2px;
      background: linear-gradient(90deg, transparent, #e6a910, transparent);
      margin: 8px auto 0;
    }

    .cert-presented {
      font-family: 'Great Vibes', cursive;
      font-size: 1.85rem;
      color: #64748b;
      margin-bottom: 6px;
    }

    .cert-student-name {
      font-family: 'Cinzel', serif;
      font-size: 2.35rem;
      font-weight: 800;
      color: #002365;
      letter-spacing: 1.5px;
      border-bottom: 2px solid #e6a910;
      display: inline-block;
      padding: 0 40px 6px;
      margin-bottom: 16px;
      text-transform: uppercase;
    }

    .cert-body-text {
      font-size: 1.02rem;
      line-height: 1.7;
      color: #334155;
      max-width: 820px;
      margin: 0 auto 16px;
    }
    .cert-course-name {
      font-family: 'Roboto Slab', serif;
      font-size: 1.35rem;
      font-weight: 700;
      color: #002365;
      display: block;
      margin: 6px 0;
    }

    /* Footer / Signatures / Seal */
    .cert-footer {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-top: 30px;
      padding-top: 15px;
    }

    .cert-seal-wrap {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .cert-gold-seal {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: radial-gradient(circle at 30% 30%, #fef08a 0%, #eab308 60%, #ca8a04 100%);
      border: 3px dashed #78350f;
      box-shadow: 0 6px 16px rgba(202, 138, 4, 0.4);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #451a03;
      position: relative;
    }
    .cert-gold-seal i {
      font-size: 28px;
      margin-bottom: 2px;
    }
    .cert-gold-seal span {
      font-size: 9px;
      font-weight: 800;
      letter-spacing: 1px;
      text-transform: uppercase;
      font-family: 'Cinzel', serif;
      text-align: center;
      padding: 0 6px;
    }

    .cert-sign-col {
      width: 250px;
      text-align: center;
    }
    .cert-sign-line {
      border-bottom: 1px solid #475569;
      margin-bottom: 8px;
      min-height: 48px;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      padding-bottom: 4px;
    }
    .cert-sign-img {
      font-family: 'Great Vibes', cursive;
      font-size: 1.95rem;
      color: #002365;
      line-height: 1;
    }
    .cert-sign-title {
      font-size: 0.82rem;
      font-weight: 700;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .cert-sign-sub {
      font-size: 0.72rem;
      color: #64748b;
    }

    /* Bottom Micro Bar */
    .cert-meta-bar {
      display: flex;
      justify-content: space-between;
      border-top: 1px solid rgba(230, 169, 16, 0.4);
      padding-top: 10px;
      margin-top: 20px;
      font-size: 0.74rem;
      color: #64748b;
      font-family: monospace;
    }

    @media print {
      body {
        background: #ffffff !important;
        padding: 0 !important;
      }
      .action-bar {
        display: none !important;
      }
      .cert-canvas {
        box-shadow: none !important;
        width: 100% !important;
        max-width: 100% !important;
        page-break-inside: avoid;
        margin: 0 !important;
      }
    }
  </style>
</head>
<body>

  <!-- Top Action Toolbar -->
  <div class="action-bar">
    <div class="action-bar-title">
      <i class="fas fa-award text-warning"></i>
      <span>Official Verified Certificate &mdash; <?= htmlspecialchars($cert['certificate_number']) ?></span>
    </div>
    <div class="d-flex gap-2">
      <a href="my-enrollments.php" class="btn-back">
        <i class="fas fa-arrow-left"></i> My Portal
      </a>
      <button type="button" class="btn-action" onclick="window.print()">
        <i class="fas fa-print"></i> Print / Save as PDF
      </button>
    </div>
  </div>

  <!-- Certificate Canvas -->
  <div class="cert-canvas" id="certificateNode">
    <div class="cert-border-outer">
      <div class="cert-corner cert-corner-tl"></div>
      <div class="cert-corner cert-corner-tr"></div>
      <div class="cert-corner cert-corner-bl"></div>
      <div class="cert-corner cert-corner-br"></div>

      <div class="cert-border-inner">
        <!-- Institution Header -->
        <div>
          <div class="cert-header">
            <img src="<?= htmlspecialchars($settings['site_logo'] ?: 'assets/images/logo/logopng.png') ?>" alt="Skill Grow Logo" class="cert-logo" onerror="this.src='assets/images/logo/logopng.png'">
            <div>
              <div class="cert-org-name"><?= htmlspecialchars($settings['site_title'] ?: 'Skill Grow Institution') ?></div>
              <div class="cert-subtitle"><?= htmlspecialchars($certSettings['cert_subtitle']) ?></div>
            </div>
          </div>

          <h2 class="cert-main-title"><?= htmlspecialchars($cert['certificate_type'] ?: 'Certificate of Completion') ?></h2>
          <div class="cert-presented">This is proudly presented to</div>
          <div class="cert-student-name"><?= htmlspecialchars($cert['student_name']) ?></div>

          <p class="cert-body-text">
            For successfully completing the comprehensive professional curriculum, real-world case projects, and academic assessment requirements for
            <span class="cert-course-name">&ldquo;<?= htmlspecialchars($cert['course_name']) ?>&rdquo;</span>
            with performance grade <strong style="color:#002365; font-weight:700;"><?= htmlspecialchars($cert['grade'] ?: 'A (Distinction)') ?></strong>, demonstrating excellence, proficiency, and industry readiness.
          </p>
        </div>

        <!-- Footer Signatures & Official Stamp (Dynamic from Admin Settings) -->
        <div>
          <div class="cert-footer">
            <!-- Signatory 1 -->
            <div class="cert-sign-col">
              <div class="cert-sign-line">
                <?php if (!empty($certSettings['cert_sign1_image']) && file_exists(__DIR__ . '/' . $certSettings['cert_sign1_image'])): ?>
                  <img src="<?= htmlspecialchars($certSettings['cert_sign1_image']) ?>" alt="Signatory 1" style="max-height: 44px; max-width: 150px; object-fit: contain;">
                <?php else: ?>
                  <span class="cert-sign-img"><?= htmlspecialchars($certSettings['cert_sign1_name']) ?></span>
                <?php endif; ?>
              </div>
              <div class="cert-sign-title"><?= htmlspecialchars($certSettings['cert_sign1_title']) ?></div>
              <div class="cert-sign-sub"><?= htmlspecialchars($certSettings['cert_sign1_sub']) ?></div>
            </div>

            <!-- Central Official Gold Seal -->
            <div class="cert-seal-wrap">
              <div class="cert-gold-seal">
                <i class="fas fa-stamp"></i>
                <span><?= htmlspecialchars($certSettings['cert_seal_text']) ?></span>
              </div>
              <span style="font-size: 10px; color: #475569; font-weight: bold; margin-top: 6px; letter-spacing: 0.5px;">
                <?= htmlspecialchars($certSettings['cert_verified_text']) ?>
              </span>
            </div>

            <!-- Signatory 2 -->
            <div class="cert-sign-col">
              <div class="cert-sign-line">
                <?php if (!empty($certSettings['cert_sign2_image']) && file_exists(__DIR__ . '/' . $certSettings['cert_sign2_image'])): ?>
                  <img src="<?= htmlspecialchars($certSettings['cert_sign2_image']) ?>" alt="Signatory 2" style="max-height: 44px; max-width: 150px; object-fit: contain;">
                <?php else: ?>
                  <span class="cert-sign-img"><?= htmlspecialchars($certSettings['cert_sign2_name']) ?></span>
                <?php endif; ?>
              </div>
              <div class="cert-sign-title"><?= htmlspecialchars($certSettings['cert_sign2_title']) ?></div>
              <div class="cert-sign-sub"><?= htmlspecialchars($certSettings['cert_sign2_sub']) ?></div>
            </div>
          </div>

          <!-- Micro Verification Footer -->
          <div class="cert-meta-bar">
            <span>CERTIFICATE ID: <strong><?= htmlspecialchars($cert['certificate_number']) ?></strong></span>
            <span>STUDENT ID: <?= htmlspecialchars($cert['student_id'] ?: 'SGI-STU') ?></span>
            <span>DATE OF ISSUE: <?= htmlspecialchars($issueDate) ?></span>
            <span>VERIFIED AT: SG-INSTITUTION PORTAL</span>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
