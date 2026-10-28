<?php
// Student Official Admission Slip & Fee Receipt - Skill Grow Institution
session_start();
require_once __DIR__ . '/admin/config/db.php';

$appId = trim($_GET['app_id'] ?? ($_SESSION['student_app_id'] ?? ''));

if (empty($appId)) {
    die("Error: No admission application specified.");
}

$stmt = $pdo->prepare("SELECT * FROM admissions WHERE application_id = :id LIMIT 1");
$stmt->execute(['id' => $appId]);
$adm = $stmt->fetch();

if (!$adm) {
    die("Error: Admission record not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admission Slip - <?= htmlspecialchars($adm['application_id']) ?> | Skill Grow Institution</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <style>
    body {
      background: #f1f5f9;
      font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #1e293b;
      padding: 30px 15px;
    }

    .receipt-container {
      max-width: 820px;
      margin: 0 auto;
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 12px;
      padding: 40px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.06);
      position: relative;
    }

    .receipt-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #002365;
      padding-bottom: 20px;
      margin-bottom: 25px;
    }

    .receipt-logo {
      height: 60px;
      width: auto;
    }

    .receipt-title-box {
      text-align: right;
    }

    .receipt-title {
      font-size: 22px;
      font-weight: 800;
      color: #002365;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 0;
    }

    .receipt-sub {
      font-size: 13px;
      color: #64748b;
      font-weight: 600;
    }

    .status-badge {
      display: inline-block;
      background: #ecfdf5;
      border: 1px solid #10b981;
      color: #047857;
      font-size: 13px;
      font-weight: 700;
      padding: 4px 12px;
      border-radius: 50px;
      margin-top: 5px;
    }

    .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 25px;
    }

    .info-item {
      font-size: 14px;
    }

    .info-label {
      color: #64748b;
      font-weight: 600;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 3px;
    }

    .info-value {
      color: #0f172a;
      font-weight: 700;
    }

    .fee-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 25px;
    }

    .fee-table th {
      background: #002365;
      color: #ffffff;
      padding: 12px;
      font-size: 13px;
      text-transform: uppercase;
    }

    .fee-table td {
      padding: 14px 12px;
      border-bottom: 1px solid #e2e8f0;
      font-size: 14px;
    }

    .watermark-seal {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      opacity: 0.04;
      pointer-events: none;
      width: 320px;
    }

    .receipt-footer {
      border-top: 1px dashed #cbd5e1;
      padding-top: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 12px;
      color: #64748b;
    }

    .no-print-bar {
      max-width: 820px;
      margin: 0 auto 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    @media print {
      body {
        background: #ffffff;
        padding: 0;
      }
      .no-print-bar {
        display: none !important;
      }
      .receipt-container {
        border: none;
        box-shadow: none;
        padding: 20px 0;
      }
    }
  </style>
</head>
<body>

  <!-- Screen Action Bar -->
  <div class="no-print-bar">
    <a href="index.php" class="btn btn-outline-dark btn-sm">
      <i class="fas fa-arrow-left me-1"></i> Back to Website
    </a>
    <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold px-3">
      <i class="fas fa-print me-1"></i> Print / Save as PDF
    </button>
  </div>

  <div class="receipt-container">
    <img src="assets/images/logo/logopng.png" alt="Watermark" class="watermark-seal">

    <!-- Header -->
    <div class="receipt-header">
      <div>
        <img src="assets/images/logo/logopng.png" alt="Skill Grow Institution" class="receipt-logo">
        <div style="font-size: 12px; color: #64748b; margin-top: 6px;">
          Skill Grow Institution of Professional Training<br>
          Official Admission & Registration Portal
        </div>
      </div>
      <div class="receipt-title-box">
        <h1 class="receipt-title">Admission Slip</h1>
        <div class="receipt-sub">Fee Acknowledgment Receipt</div>
        <span class="status-badge">
          <i class="fas fa-check-circle me-1"></i> Fee Paid & Verified
        </span>
      </div>
    </div>

    <!-- Candidate & Course Details -->
    <div class="info-grid">
      <div class="info-item">
        <div class="info-label">Application Number</div>
        <div class="info-value font-monospace text-primary"><?= htmlspecialchars($adm['application_id']) ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Student ID</div>
        <div class="info-value font-monospace text-success"><?= htmlspecialchars($adm['student_id'] ?: 'SGI-' . substr($adm['application_id'], -5)) ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Student Full Name</div>
        <div class="info-value"><?= htmlspecialchars($adm['full_name']) ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Enrolled Course / Program</div>
        <div class="info-value text-dark"><?= htmlspecialchars($adm['course_name']) ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Contact Email</div>
        <div class="info-value"><?= htmlspecialchars($adm['email']) ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Registered Phone</div>
        <div class="info-value"><?= htmlspecialchars($adm['phone']) ?></div>
      </div>
    </div>

    <!-- Fee Breakdown Table -->
    <table class="fee-table">
      <thead>
        <tr>
          <th>Fee Description</th>
          <th>Payment Mode</th>
          <th>Reference / Transaction ID</th>
          <th class="text-end">Amount (INR)</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <strong>Official Admission & Registration Fee</strong><br>
            <small class="text-muted">Includes digital kit, orientation & batch allotment</small>
          </td>
          <td><?= htmlspecialchars($adm['payment_method'] ?: 'Razorpay') ?></td>
          <td class="font-monospace" style="font-size: 12px;"><?= htmlspecialchars($adm['razorpay_payment_id'] ?: 'RZP_VERIFIED') ?></td>
          <td class="text-end fw-bold">₹<?= number_format($adm['fee_amount'], 2) ?></td>
        </tr>
        <tr style="background: #f8fafc; font-weight: 700;">
          <td colspan="3" class="text-end">Total Amount Paid:</td>
          <td class="text-end text-success">₹<?= number_format($adm['fee_amount'], 2) ?></td>
        </tr>
      </tbody>
    </table>

    <!-- Footer & Disclaimer -->
    <div class="receipt-footer">
      <div>
        <strong>Admission Verification Cell</strong><br>
        Issue Date: <?= date('d M Y, h:i A', strtotime($adm['created_at'])) ?>
      </div>
      <div class="text-end">
        Authorized Digital Signatory<br>
        <strong>Skill Grow Institution Admissions</strong>
      </div>
    </div>

  </div>

</body>
</html>
