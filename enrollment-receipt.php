<?php
// Official Course Enrollment & Tuition Fee Receipt - Skill Grow Institution
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings_helper.php';
$siteSettings = getGlobalSettings();

$enrollmentId = trim($_GET['id'] ?? '');

if (empty($enrollmentId)) {
    die("Error: No enrollment ID specified.");
}

// Fetch enrollment details joined with course, student, and admission data
$stmt = $pdo->prepare("
    SELECT e.*, 
           c.title as current_course_title, c.duration, c.mode, c.image as course_image, c.tagline,
           s.avatar as student_avatar, s.qualification as student_qualification, s.aadhaar_number as student_aadhaar,
           adm.photo_doc, adm.application_id
    FROM course_enrollments e 
    LEFT JOIN courses c ON e.course_id = c.id 
    LEFT JOIN students s ON (e.student_id IS NOT NULL AND e.student_id != '' AND e.student_id = s.student_id) OR (e.student_email = s.email)
    LEFT JOIN admissions adm ON (e.student_id IS NOT NULL AND e.student_id != '' AND e.student_id = adm.student_id) OR (e.student_email = adm.email)
    WHERE e.enrollment_id = :eid 
    ORDER BY e.id DESC
    LIMIT 1
");
$stmt->execute(['eid' => $enrollmentId]);
$enr = $stmt->fetch();

if (!$enr) {
    die("Error: Enrollment record not found for ID: " . htmlspecialchars($enrollmentId));
}

// Determine student photo
$studentPhoto = '';
if (!empty($enr['student_avatar']) && file_exists(__DIR__ . '/' . $enr['student_avatar'])) {
    $studentPhoto = $enr['student_avatar'];
} elseif (!empty($enr['photo_doc']) && file_exists(__DIR__ . '/' . $enr['photo_doc'])) {
    $studentPhoto = $enr['photo_doc'];
}

// Compute Initials
$names = trim($enr['student_name'] ?? 'STU');
$parts = preg_split('/\s+/', $names);
$initials = strtoupper(substr($parts[0], 0, 1));
if (count($parts) > 1) {
    $initials .= strtoupper(substr(end($parts), 0, 1));
}

// Dynamic Site Settings
$brandLogo   = !empty($siteSettings['site_logo']) ? $siteSettings['site_logo'] : 'assets/images/logo/logopng.png';
$brandTitle  = $siteSettings['site_title'] ?: 'Skill Grow Institution';
$brandPhone  = $siteSettings['site_phone'] ?: '+91 9217431562';
$brandEmail  = $siteSettings['site_email'] ?: 'info@skillgrowinstitution.in';
$brandAddr   = $siteSettings['site_address'] ?: 'Metro Plaza Meerut, 250002, India';

// Receipt Serial Number
$receiptSerial = 'RCP-' . date('Y', strtotime($enr['enrolled_at'])) . '-' . substr(preg_replace('/[^0-9]/', '', $enr['enrollment_id']), -5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enrollment Receipt - <?= htmlspecialchars($enr['enrollment_id']) ?> | Skill Grow Institution</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <!-- Premium Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Roboto+Slab:wght@500;600;700;800;900&family=Cinzel:wght@600;700;800&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary-navy: #002365;
      --secondary-gold: #c88a04;
      --gold-light: #fef3c7;
      --dark-text: #0f172a;
      --muted-text: #475569;
      --border-color: #cbd5e1;
    }

    * {
      box-sizing: border-box;
      box-shadow: none !important;
    }

    body {
      background: #e2e8f0;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: var(--dark-text);
      padding: 30px 15px;
      margin: 0;
      -webkit-font-smoothing: antialiased;
    }

    /* Top Action Bar (hidden on print) */
    .receipt-action-bar {
      max-width: 840px;
      margin: 0 auto 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
    }

    /* Main A4 Receipt Sheet */
    .receipt-sheet {
      max-width: 840px;
      margin: 0 auto;
      background: #ffffff;
      border: 2px solid #002365;
      border-radius: 10px;
      padding: 35px 40px;
      position: relative;
      overflow: hidden;
    }

    /* Background Watermark Seal */
    .watermark-seal {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 440px;
      opacity: 0.035;
      pointer-events: none;
      z-index: 0;
    }

    .sheet-content {
      position: relative;
      z-index: 1;
    }

    /* Header Section */
    .sheet-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #002365;
      padding-bottom: 18px;
      margin-bottom: 20px;
      gap: 18px;
    }

    .brand-group {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .brand-logo-img {
      height: 65px;
      width: auto;
      object-fit: contain;
    }

    .institution-name {
      font-family: 'Roboto Slab', serif;
      font-size: 1.35rem;
      font-weight: 800;
      color: var(--primary-navy);
      line-height: 1.2;
      margin: 0 0 3px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .institution-tagline {
      font-size: 0.78rem;
      color: #64748b;
      font-weight: 600;
      margin: 0 0 3px;
    }

    .institution-meta {
      font-size: 0.72rem;
      color: #475569;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .receipt-title-box {
      text-align: right;
      flex-shrink: 0;
    }

    .receipt-badge-title {
      font-family: 'Roboto Slab', serif;
      font-size: 1.2rem;
      font-weight: 800;
      color: var(--secondary-gold);
      letter-spacing: 0.5px;
      margin-bottom: 3px;
    }

    .receipt-serial {
      font-family: monospace;
      font-size: 0.85rem;
      font-weight: 800;
      color: var(--primary-navy);
    }

    .receipt-date-text {
      font-size: 0.75rem;
      color: #64748b;
    }

    /* Student Bento Profile Card */
    .student-bento-card {
      background: #f8fafc;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 16px 20px;
      margin-bottom: 22px;
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .student-photo-frame {
      width: 78px;
      height: 78px;
      border-radius: 8px;
      overflow: hidden;
      border: 2px solid var(--secondary-gold);
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #002365;
    }

    .student-photo-frame img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .student-monogram-initials {
      font-family: 'Roboto Slab', serif;
      font-size: 1.6rem;
      font-weight: 800;
      color: #fbbf24;
      line-height: 1;
    }

    .student-info-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px 18px;
      flex-grow: 1;
    }

    .info-cell-label {
      font-size: 0.68rem;
      text-transform: uppercase;
      font-weight: 800;
      color: #64748b;
      letter-spacing: 0.6px;
      margin-bottom: 2px;
    }

    .info-cell-val {
      font-size: 0.86rem;
      font-weight: 700;
      color: var(--dark-text);
      word-break: break-word;
    }

    /* Course & Ledger Table */
    .ledger-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .ledger-table th {
      background: #002365;
      color: #ffffff;
      font-size: 0.74rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      padding: 10px 14px;
      border: none;
    }

    .ledger-table td {
      padding: 12px 14px;
      border-bottom: 1px solid #e2e8f0;
      font-size: 0.86rem;
      color: #1e293b;
      vertical-align: middle;
    }

    .ledger-total-row td {
      background: #f8fafc;
      font-weight: 800;
      font-size: 0.95rem;
      color: var(--primary-navy);
      border-top: 2px solid #002365;
      border-bottom: 2px solid #002365;
    }

    /* Gateway Verification Strip */
    .gateway-verify-box {
      background: #f1f5f9;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 12px 18px;
      margin-bottom: 22px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: gap;
    }

    .paid-badge-pill {
      background: #10b981;
      color: #ffffff;
      font-size: 0.72rem;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: 50px;
      letter-spacing: 0.5px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    /* Footer & Signatures */
    .sheet-footer {
      border-top: 1px solid var(--border-color);
      padding-top: 16px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      gap: 20px;
    }

    .terms-text {
      font-size: 0.72rem;
      color: #64748b;
      line-height: 1.45;
      max-width: 500px;
    }

    .signature-container {
      text-align: center;
      flex-shrink: 0;
      width: 190px;
    }

    .seal-circle-stamp {
      width: 62px;
      height: 62px;
      border: 2px dashed #002365;
      border-radius: 50%;
      margin: 0 auto 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      color: #002365;
      font-size: 0.56rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      line-height: 1.1;
      transform: rotate(-10deg);
    }

    .sig-line {
      border-top: 1px solid #0f172a;
      padding-top: 4px;
      font-size: 0.74rem;
      font-weight: 800;
      color: #0f172a;
    }

    /* Print Styles */
    @media print {
      body {
        background: #ffffff !important;
        padding: 0 !important;
      }
      .receipt-action-bar {
        display: none !important;
      }
      .receipt-sheet {
        border: none !important;
        padding: 10px !important;
        max-width: 100% !important;
      }
      .watermark-seal {
        opacity: 0.05 !important;
      }
    }
  </style>
</head>
<body>

  <!-- Top Action Toolbar (Screen Only) -->
  <div class="receipt-action-bar">
    <div>
      <a href="my-enrollments.php" class="btn btn-outline-secondary btn-sm px-3" style="border-radius: 6px;">
        <i class="fas fa-arrow-left me-1"></i> Back
      </a>
      <a href="admin/enrollments.php" class="btn btn-outline-dark btn-sm px-3 ms-1" style="border-radius: 6px;">
        <i class="fas fa-user-shield me-1"></i> Admin Portal
      </a>
    </div>

    <div class="d-flex gap-2">
      <button onclick="window.print()" class="btn btn-warning btn-sm px-4 fw-bold" style="border-radius: 6px;">
        <i class="fas fa-print me-1"></i> Print / Save PDF
      </button>
    </div>
  </div>

  <!-- Official Receipt Sheet (Print Ready A4) -->
  <div class="receipt-sheet">
    
    <!-- Watermark Seal -->
    <img src="<?= htmlspecialchars($brandLogo) ?>" alt="Seal" class="watermark-seal">

    <div class="sheet-content">

      <!-- 1. Header with High-Res Brand Logo & Receipt Metadata -->
      <div class="sheet-header">
        <div class="brand-group">
          <img src="<?= htmlspecialchars($brandLogo) ?>" alt="<?= htmlspecialchars($brandTitle) ?>" class="brand-logo-img">
          <div>
            <h1 class="institution-name"><?= htmlspecialchars($brandTitle) ?></h1>
            <div class="institution-tagline">Center for Advanced Professional Studies & Technology Training</div>
            <div class="institution-meta">
              <span><i class="fas fa-globe text-primary me-1"></i>fairrepairguide.com</span>
              <span>&bull;</span>
              <span><i class="fas fa-envelope text-primary me-1"></i><?= htmlspecialchars($brandEmail) ?></span>
              <span>&bull;</span>
              <span><i class="fas fa-phone-alt text-warning me-1"></i><?= htmlspecialchars($brandPhone) ?></span>
            </div>
          </div>
        </div>

        <div class="receipt-title-box">
          <div class="receipt-badge-title">OFFICIAL FEE RECEIPT</div>
          <div class="receipt-serial"><?= htmlspecialchars($receiptSerial) ?></div>
          <div class="receipt-date-text">
            Date: <?= date('d M, Y', strtotime($enr['enrolled_at'])) ?>
          </div>
        </div>
      </div>

      <!-- 2. Candidate & Enrollment Bento Box with Photo Thumbnail -->
      <div class="student-bento-card">
        <!-- Photo Thumbnail / Initials Avatar -->
        <div class="student-photo-frame">
          <?php if (!empty($studentPhoto)): ?>
            <img src="<?= htmlspecialchars($studentPhoto) ?>" alt="<?= htmlspecialchars($enr['student_name']) ?>">
          <?php else: ?>
            <span class="student-monogram-initials"><?= htmlspecialchars($initials) ?></span>
          <?php endif; ?>
        </div>

        <!-- Student Info Grid -->
        <div class="student-info-grid">
          <div>
            <div class="info-cell-label">Candidate Name</div>
            <div class="info-cell-val" style="color: var(--primary-navy); font-size: 0.95rem;">
              <?= htmlspecialchars($enr['student_name']) ?>
            </div>
          </div>

          <div>
            <div class="info-cell-label">Student ID</div>
            <div class="info-cell-val font-monospace" style="color: #0284c7;">
              <?= htmlspecialchars($enr['student_id'] ?: 'STU-ONLINE') ?>
            </div>
          </div>

          <div>
            <div class="info-cell-label">Enrollment ID</div>
            <div class="info-cell-val font-monospace" style="color: var(--secondary-gold);">
              <?= htmlspecialchars($enr['enrollment_id']) ?>
            </div>
          </div>

          <div>
            <div class="info-cell-label">Registered Email</div>
            <div class="info-cell-val font-monospace" style="font-size: 0.80rem;">
              <?= htmlspecialchars($enr['student_email']) ?>
            </div>
          </div>

          <div>
            <div class="info-cell-label">Contact Phone</div>
            <div class="info-cell-val font-monospace" style="font-size: 0.82rem;">
              <?= htmlspecialchars($enr['student_phone']) ?>
            </div>
          </div>

          <div>
            <div class="info-cell-label">Academic Program</div>
            <div class="info-cell-val" style="color: var(--primary-navy);">
              <?= htmlspecialchars($enr['course_name']) ?>
            </div>
          </div>
          <?php if (!empty($enr['admission_id_linked'])): ?>
            <div>
              <div class="info-cell-label">Linked Admission ID</div>
              <div class="info-cell-val font-monospace" style="color: #10b981; font-weight: 700;">
                <i class="fas fa-check-circle me-1"></i> <?= htmlspecialchars($enr['admission_id_linked']) ?> (Fee Credit Applied)
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- 3. Course Fee & Ledger Table -->
      <table class="ledger-table">
        <thead>
          <tr>
            <th>Description & Academic Inclusions</th>
            <th>Duration / Mode</th>
            <th class="text-end">Amount (INR)</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $origFee = !empty($enr['original_course_fee']) ? (float)$enr['original_course_fee'] : ((float)$enr['fee_amount'] + (float)($enr['reg_fee_deducted'] ?? 0));
          $regDed  = (float)($enr['reg_fee_deducted'] ?? 0);
          ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($enr['course_name']) ?></strong><br>
              <small class="text-muted">Comprehensive Program Tuition, Cloud Sandbox Lab, Live Mentor Support & Industry Certificate</small>
            </td>
            <td>
              <?= htmlspecialchars($enr['duration'] ?: '6 Months') ?><br>
              <small class="text-muted"><?= htmlspecialchars($enr['mode'] ?: 'Interactive Live Labs') ?></small>
            </td>
            <td class="text-end font-monospace fw-bold">
              ₹<?= number_format($origFee, 2) ?>
            </td>
          </tr>
          <?php if ($regDed > 0): ?>
            <tr style="background: rgba(16, 185, 129, 0.06);">
              <td>
                <strong class="text-success"><i class="fas fa-check-circle me-1"></i> Less: Admission Registration Credit (Auto-Deducted)</strong><br>
                <small class="text-muted">Verified admission fee credited under Application ID: <strong><?= htmlspecialchars($enr['admission_id_linked']) ?></strong></small>
              </td>
              <td><span class="badge bg-success bg-opacity-25 text-success border border-success">Credited</span></td>
              <td class="text-end font-monospace text-success fw-bold">
                - ₹<?= number_format($regDed, 2) ?>
              </td>
            </tr>
          <?php endif; ?>
          <tr>
            <td>
              <strong>E-Learning Portal Access & Project Code Repositories</strong>
            </td>
            <td>Complete Access</td>
            <td class="text-end font-monospace text-success">
              ₹0.00
            </td>
          </tr>
          <tr>
            <td>
              <strong>Verified Digital Credential & Career Advisory</strong>
            </td>
            <td>Included</td>
            <td class="text-end font-monospace text-success">
              ₹0.00
            </td>
          </tr>
          <tr class="ledger-total-row">
            <td colspan="2" class="text-end">Total Net Tuition Paid in Full:</td>
            <td class="text-end font-monospace" style="font-size: 1.15rem; color: #10b981;">
              ₹<?= number_format((float)$enr['fee_amount'], 2) ?>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- 4. Payment Gateway Verification Strip -->
      <div class="gateway-verify-box">
        <div class="d-flex align-items-center gap-2">
          <span class="paid-badge-pill">
            <i class="fas fa-check-circle"></i> PAID IN FULL
          </span>
          <span style="font-size: 0.80rem; color: #475569;">
            Gateway: <strong><?= htmlspecialchars($enr['payment_method']) ?></strong>
          </span>
        </div>

        <div class="d-flex align-items-center gap-3">
          <div class="font-monospace" style="font-size: 0.78rem; color: #475569;">
            Tx ID: <strong style="color: var(--primary-navy);"><?= htmlspecialchars($enr['razorpay_payment_id'] ?: 'Direct Verified') ?></strong>
          </div>
          <?php if (!empty($enr['razorpay_order_id'])): ?>
            <div class="font-monospace" style="font-size: 0.78rem; color: #475569;">
              Order: <strong><?= htmlspecialchars($enr['razorpay_order_id']) ?></strong>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- 5. Verification Notice & Authorized Seal Signatures -->
      <div class="sheet-footer">
        <div class="terms-text">
          <p class="mb-1 fw-bold text-dark" style="font-size: 0.76rem;">AUTHENTICATION & COMPLIANCE NOTICE:</p>
          <p class="mb-1">
            This document serves as an authentic, computer-generated tuition fee receipt issued by Skill Grow Institution. It certifies candidate course enrollment and full payment receipt.
          </p>
          <p class="mb-0">
            For online credential verification, visit <strong>fairrepairguide.com</strong> or contact <strong>admissions@fairrepairguide.com</strong>.
          </p>
        </div>

        <div class="signature-container">
          <div class="seal-circle-stamp">
            <span>OFFICIAL</span>
            <span>VERIFIED</span>
            <span style="color: #c88a04;">SGI 2026</span>
          </div>
          <div class="sig-line">
            Authorized Signatory
            <div style="font-size: 0.68rem; color: #64748b; font-weight: normal;">Registrar & Accounts Office</div>
          </div>
        </div>
      </div>

    </div>
  </div>

</body>
</html>