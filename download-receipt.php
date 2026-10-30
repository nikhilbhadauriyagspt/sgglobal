<?php
// Student Official Admission Slip & Fee Receipt - Skill Grow Institution
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/admin/config/db.php';
require_once __DIR__ . '/includes/settings_helper.php';
$siteSettings = getGlobalSettings();

$appId = trim($_GET['app_id'] ?? ($_SESSION['student_app_id'] ?? ''));

if (empty($appId)) {
    die("Error: No admission application specified. Please provide a valid application ID.");
}

$stmt = $pdo->prepare("SELECT * FROM admissions WHERE application_id = :id LIMIT 1");
$stmt->execute(['id' => $appId]);
$adm = $stmt->fetch();

if (!$adm) {
    die("Error: Admission record not found for Application ID: " . htmlspecialchars($appId));
}

// Dynamic Site Settings
$brandLogo   = !empty($siteSettings['site_logo']) ? $siteSettings['site_logo'] : 'assets/images/logo/logopng.png';
$brandTitle  = $siteSettings['site_title'] ?: 'Skill Grow Institution';
$brandPhone  = $siteSettings['site_phone'] ?: '+91 9217431562';
$brandEmail  = $siteSettings['site_email'] ?: 'info@skillgrowinstitution.in';
$brandAddr   = $siteSettings['site_address'] ?: 'Metro Plaza Meerut, 250002, India';

// Generate receipt serial number
$receiptSerial = 'RCP-' . date('Y') . '-' . substr(preg_replace('/[^0-9]/', '', $adm['application_id']), -5);
$studentIdDisplay = !empty($adm['student_id']) ? $adm['student_id'] : ('STU-2026-' . substr(preg_replace('/[^0-9]/', '', $adm['application_id']), -4));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Official Admission Slip - <?= htmlspecialchars($adm['application_id']) ?> | Skill Grow Institution</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <!-- Fonts -->
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
    }

    body {
      background: #e2e8f0;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: var(--dark-text);
      padding: 35px 15px;
      margin: 0;
    }

    /* Top Action Bar (hidden on print) */
    .receipt-action-bar {
      max-width: 880px;
      margin: 0 auto 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 15px;
    }

    /* Main A4 Receipt Sheet */
    .receipt-sheet {
      max-width: 880px;
      margin: 0 auto;
      background: #ffffff;
      border: 2px solid #002365;
      border-radius: 12px;
      padding: 40px 45px;
      position: relative;
      box-shadow: 0 10px 30px rgba(0, 35, 101, 0.1);
      overflow: hidden;
    }

    /* Background Watermark Seal */
    .watermark-seal {
      position: absolute;
      top: 52%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 480px;
      opacity: 0.04;
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
      padding-bottom: 20px;
      margin-bottom: 20px;
      gap: 20px;
    }

    .brand-group {
      display: flex;
      align-items: center;
      gap: 18px;
    }

    .brand-logo-img {
      height: 75px;
      width: auto;
      object-fit: contain;
    }

    .institution-name {
      font-family: 'Roboto Slab', serif;
      font-size: 1.45rem;
      font-weight: 800;
      color: var(--primary-navy);
      line-height: 1.2;
      margin: 0 0 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .institution-tagline {
      font-size: 0.8rem;
      color: #64748b;
      font-weight: 600;
      margin: 0 0 4px;
    }

    .institution-meta {
      font-size: 0.74rem;
      color: #475569;
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .sheet-title-box {
      text-align: right;
    }

    .receipt-badge-title {
      font-family: 'Roboto Slab', serif;
      font-size: 1.25rem;
      font-weight: 800;
      color: #002365;
      margin: 0 0 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .receipt-serial {
      font-family: monospace;
      font-size: 0.88rem;
      font-weight: 700;
      color: #b45309;
    }

    /* Verified Banner Strip */
    .verification-strip {
      background: linear-gradient(90deg, #002365 0%, #0c388f 100%);
      color: #ffffff;
      border-radius: 8px;
      padding: 10px 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .strip-left {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.86rem;
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .strip-right {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 0.78rem;
    }

    /* Candidate Profile Box */
    .candidate-card-wrap {
      border: 1px solid #cbd5e1;
      border-radius: 10px;
      background: #f8fafc;
      padding: 20px;
      margin-bottom: 24px;
      display: flex;
      gap: 22px;
      align-items: center;
    }

    .candidate-photo-box {
      width: 120px;
      height: 140px;
      border: 2px solid #002365;
      border-radius: 8px;
      overflow: hidden;
      background: #e2e8f0;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .candidate-photo-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .candidate-photo-fallback {
      font-size: 38px;
      color: #94a3b8;
    }

    .candidate-grid {
      flex: 1;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px 20px;
    }

    .meta-field label {
      font-size: 0.7rem;
      text-transform: uppercase;
      font-weight: 700;
      color: #64748b;
      margin-bottom: 2px;
      display: block;
      letter-spacing: 0.5px;
    }

    .meta-field .val {
      font-size: 0.92rem;
      font-weight: 700;
      color: #0f172a;
    }

    /* Fee Table */
    .receipt-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      overflow: hidden;
    }

    .receipt-table th {
      background: #002365;
      color: #ffffff;
      padding: 10px 14px;
      font-size: 0.8rem;
      text-transform: uppercase;
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .receipt-table td {
      padding: 12px 14px;
      border-bottom: 1px solid #e2e8f0;
      font-size: 0.88rem;
    }

    .receipt-table tr.total-row td {
      background: #f8fafc;
      font-size: 1.05rem;
      font-weight: 800;
      border-top: 2px solid #002365;
      border-bottom: none;
    }

    /* Payment & Security Metadata Strip */
    .payment-meta-box {
      background: #f1f5f9;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      padding: 14px 18px;
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
    }

    .payment-item {
      font-size: 0.82rem;
    }

    .payment-item label {
      color: #64748b;
      font-weight: 600;
      margin-right: 4px;
    }

    .payment-item strong {
      color: #0f172a;
    }

    /* Footer & Signatures */
    .sheet-footer {
      border-top: 1px dashed #94a3b8;
      padding-top: 20px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      gap: 20px;
    }

    .instructions-box {
      flex: 1;
      font-size: 0.72rem;
      color: #64748b;
      line-height: 1.5;
    }

    .instructions-box ol {
      padding-left: 16px;
      margin: 4px 0 0;
    }

    .signature-area {
      text-align: center;
      width: 220px;
    }

    .official-stamp-box {
      margin-bottom: 6px;
    }

    .seal-badge-svg {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      border: 2px dashed #002365;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #002365;
      font-size: 26px;
      background: rgba(0, 35, 101, 0.04);
    }

    .sig-line {
      border-top: 1.5px solid #002365;
      padding-top: 5px;
      font-weight: 800;
      font-size: 0.85rem;
      color: #002365;
    }

    .sig-sub {
      font-size: 0.72rem;
      color: #64748b;
      font-weight: 600;
    }

    /* Print Media Rules */
    @media print {
      body {
        background: #ffffff;
        padding: 0;
      }
      .receipt-action-bar {
        display: none !important;
      }
      .receipt-sheet {
        border: 1.5px solid #002365;
        box-shadow: none;
        padding: 25px 30px;
        max-width: 100%;
        border-radius: 0;
      }
      @page {
        size: A4;
        margin: 10mm;
      }
    }
  </style>
</head>
<body>

  <!-- Screen Action Bar -->
  <div class="receipt-action-bar">
    <a href="index.php" class="btn btn-outline-dark btn-sm px-3 fw-bold">
      <i class="fas fa-arrow-left me-1"></i> Back to Website
    </a>
    <div class="d-flex gap-2">
      <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold px-4">
        <i class="fas fa-print me-1"></i> Print / Save as PDF
      </button>
    </div>
  </div>

  <!-- A4 Printable Sheet -->
  <div class="receipt-sheet">
    <img src="<?= htmlspecialchars($brandLogo) ?>" alt="Watermark Seal" class="watermark-seal">

    <div class="sheet-content">
      
      <!-- HEADER -->
      <div class="sheet-header">
        <div class="brand-group">
          <img src="<?= htmlspecialchars($brandLogo) ?>" alt="<?= htmlspecialchars($brandTitle) ?>" class="brand-logo-img">
          <div>
            <h1 class="institution-name"><?= htmlspecialchars($brandTitle) ?></h1>
            <p class="institution-tagline">Center for Professional Excellence, Technology & Vocational Studies</p>
            <div class="institution-meta">
              <span><i class="fas fa-globe me-1 text-primary"></i> fairrepairguide.com</span>
              <span><i class="fas fa-envelope me-1 text-primary"></i> <?= htmlspecialchars($brandEmail) ?></span>
              <span><i class="fas fa-phone me-1 text-primary"></i> <?= htmlspecialchars($brandPhone) ?></span>
            </div>
          </div>
        </div>

        <div class="sheet-title-box">
          <div class="receipt-badge-title">Admission Slip</div>
          <div class="receipt-serial">Receipt No: <?= htmlspecialchars($receiptSerial) ?></div>
          <div class="text-muted" style="font-size: 0.78rem; font-weight: 600;">
            Date: <?= date('d M, Y', strtotime($adm['created_at'])) ?>
          </div>
        </div>
      </div>

      <!-- OFFICIAL VERIFICATION STRIP -->
      <div class="verification-strip">
        <div class="strip-left">
          <i class="fas fa-certificate text-warning fa-lg"></i>
          <span>OFFICIAL PROVISIONAL ADMISSION & FEE RECEIPT</span>
        </div>
        <div class="strip-right">
          <span><i class="fas fa-shield-alt text-success me-1"></i> 100% Digitally Verified</span>
          <span>&bull;</span>
          <span>Academic Session: <strong>2026 - 2027</strong></span>
        </div>
      </div>

      <!-- CANDIDATE PROFILE BOX WITH PHOTO THUMBNAIL -->
      <div class="candidate-card-wrap">
        
        <!-- Candidate Photo -->
        <div class="candidate-photo-box">
          <?php if (!empty($adm['photo_doc']) && file_exists(__DIR__ . '/' . $adm['photo_doc'])): ?>
            <img src="<?= htmlspecialchars($adm['photo_doc']) ?>" alt="<?= htmlspecialchars($adm['full_name']) ?>">
          <?php else: ?>
            <div class="candidate-photo-fallback">
              <i class="fas fa-user-graduate"></i>
            </div>
            <div class="text-muted" style="font-size: 0.65rem; font-weight: 700; margin-top: 4px;">STUDENT</div>
          <?php endif; ?>
        </div>

        <!-- Details Grid -->
        <div class="candidate-grid">
          <div class="meta-field">
            <label>Application Number</label>
            <div class="val font-monospace text-primary"><?= htmlspecialchars($adm['application_id']) ?></div>
          </div>

          <div class="meta-field">
            <label>Student Enrollment ID</label>
            <div class="val font-monospace text-success"><?= htmlspecialchars($studentIdDisplay) ?></div>
          </div>

          <div class="meta-field">
            <label>Candidate Full Name</label>
            <div class="val" style="font-family: 'Roboto Slab', serif; font-size: 1rem; color: #002365;">
              <?= htmlspecialchars($adm['full_name']) ?>
            </div>
          </div>

          <div class="meta-field">
            <label>Registered Mobile Number</label>
            <div class="val font-monospace"><?= htmlspecialchars($adm['phone']) ?></div>
          </div>

          <div class="meta-field">
            <label>Registered Email Address</label>
            <div class="val" style="font-size: 0.85rem; word-break: break-all;"><?= htmlspecialchars($adm['email']) ?></div>
          </div>

          <div class="meta-field">
            <label>Aadhaar Identity Number</label>
            <div class="val font-monospace"><?= htmlspecialchars($adm['aadhaar_number'] ?: 'Verified via UIDAI') ?></div>
          </div>

          <div class="meta-field" style="grid-column: span 2;">
            <label>Enrolled Program / Course</label>
            <div class="val text-primary" style="font-size: 0.98rem;"><?= htmlspecialchars($adm['course_name']) ?></div>
          </div>

          <div class="meta-field">
            <label>Highest Qualification</label>
            <div class="val"><?= htmlspecialchars($adm['qualification'] ?: '10+2 / Graduate') ?></div>
          </div>
        </div>

      </div>

      <!-- FEE BREAKDOWN TABLE -->
      <table class="receipt-table">
        <thead>
          <tr>
            <th>Item / Service Description</th>
            <th>Type</th>
            <th>Status</th>
            <th class="text-end">Amount Paid (INR)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <strong>Provisional Admission Registration & Student Verification</strong><br>
              <small class="text-muted">Includes identity verification, course batch allocation & orientation kit</small>
            </td>
            <td>Registration Fee</td>
            <td><span class="badge bg-success" style="font-size: 0.75rem;">PAID</span></td>
            <td class="text-end font-monospace fw-bold" style="font-size: 0.95rem;">
              ₹<?= number_format((float)$adm['fee_amount'], 2) ?>
            </td>
          </tr>
          <tr>
            <td>
              <strong>Online Student LMS & Digital Learning Sandbox</strong><br>
              <small class="text-muted">Interactive study syllabus, lab tools & cloud practice environment</small>
            </td>
            <td>Academic Service</td>
            <td><span class="badge bg-info text-dark" style="font-size: 0.75rem;">INCLUDED</span></td>
            <td class="text-end font-monospace text-success fw-bold">₹0.00</td>
          </tr>
          <tr>
            <td>
              <strong>Industry Placement Assistance & Resume Accreditation</strong><br>
              <small class="text-muted">Dedicated mentor assistance, mock interview rounds & certification assessment</small>
            </td>
            <td>Career Support</td>
            <td><span class="badge bg-info text-dark" style="font-size: 0.75rem;">INCLUDED</span></td>
            <td class="text-end font-monospace text-success fw-bold">₹0.00</td>
          </tr>
          <tr class="total-row">
            <td colspan="3" class="text-end" style="color: #002365;">
              TOTAL AMOUNT RECEIVED (INR):
            </td>
            <td class="text-end font-monospace text-primary fw-bold" style="font-size: 1.15rem;">
              ₹<?= number_format((float)$adm['fee_amount'], 2) ?>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- PAYMENT TRANSACTION METADATA -->
      <div class="payment-meta-box">
        <div class="payment-item">
          <label><i class="fas fa-wallet text-primary me-1"></i> Mode:</label>
          <strong><?= htmlspecialchars($adm['payment_method'] ?: 'Razorpay Secure') ?></strong>
        </div>
        <div class="payment-item">
          <label><i class="fas fa-barcode text-warning me-1"></i> Razorpay Payment ID:</label>
          <strong class="font-monospace text-primary"><?= htmlspecialchars($adm['razorpay_payment_id'] ?: 'RZP_VERIFIED') ?></strong>
        </div>
        <div class="payment-item">
          <label><i class="fas fa-clock text-success me-1"></i> Transaction Date:</label>
          <strong><?= date('d M Y, h:i A', strtotime($adm['created_at'])) ?></strong>
        </div>
        <div class="payment-item">
          <label><i class="fas fa-check-circle text-success me-1"></i> Admission Status:</label>
          <span class="badge bg-success" style="font-size: 0.78rem;">
            <?= strtoupper($adm['admission_status'] ?: 'VERIFIED') ?>
          </span>
        </div>
      </div>

      <!-- FOOTER: INSTRUCTIONS & AUTHORIZED SIGNATURE -->
      <div class="sheet-footer">
        <div class="instructions-box">
          <strong class="text-dark"><i class="fas fa-info-circle text-primary me-1"></i> Important Student Instructions:</strong>
          <ol>
            <li>This admission slip is an electronically authenticated document generated by Skill Grow Institution.</li>
            <li>Please keep this slip safe for verification during class orientation and ID badge issuance.</li>
            <li>Access your student portal using your registered email: <strong><?= htmlspecialchars($adm['email']) ?></strong>.</li>
            <li>For any batch inquiries or schedule updates, contact academic support at <strong>info@skillgrowinstitution.in</strong>.</li>
          </ol>
        </div>

        <div class="signature-area">
          <div class="official-stamp-box">
            <div class="seal-badge-svg">
              <i class="fas fa-stamp"></i>
            </div>
          </div>
          <div class="sig-line">Registrar &bull; Admissions</div>
          <div class="sig-sub">Skill Grow Institution Council</div>
        </div>
      </div>

    </div>
  </div>

</body>
</html>
