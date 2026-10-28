<?php
// Official Course Enrollment & Tuition Fee Receipt - Skill Grow Institution
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';

$enrollmentId = trim($_GET['id'] ?? '');

if (empty($enrollmentId)) {
    die("Error: No enrollment ID specified.");
}

$stmt = $pdo->prepare("
    SELECT e.*, c.duration, c.mode, c.image, c.tagline 
    FROM course_enrollments e 
    LEFT JOIN courses c ON e.course_id = c.id 
    WHERE e.enrollment_id = :eid 
    LIMIT 1
");
$stmt->execute(['eid' => $enrollmentId]);
$enr = $stmt->fetch();

if (!$enr) {
    die("Error: Enrollment record not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fee Receipt - <?= htmlspecialchars($enr['enrollment_id']) ?> | Skill Grow Institution</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Roboto+Slab:wght@600;700;800&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #f1f5f9;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: #1e293b;
      padding: 40px 15px;
    }
    .receipt-container {
      max-width: 800px;
      margin: 0 auto;
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 12px;
      padding: 40px;
      box-shadow: none !important;
    }
    .receipt-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 3px solid #002365;
      padding-bottom: 25px;
      margin-bottom: 30px;
    }
    .inst-brand {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .inst-brand img {
      height: 55px;
      object-fit: contain;
    }
    .inst-name {
      font-family: 'Roboto Slab', serif;
      font-size: 1.5rem;
      font-weight: 800;
      color: #002365;
      margin: 0;
    }
    .inst-sub {
      font-size: 0.8rem;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .receipt-title-box {
      text-align: right;
    }
    .receipt-title {
      font-family: 'Roboto Slab', serif;
      font-size: 1.3rem;
      font-weight: 700;
      color: #e6a910;
      margin: 0;
    }
    .receipt-no {
      font-family: monospace;
      font-size: 0.9rem;
      font-weight: 700;
      color: #0f172a;
    }
    .meta-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 30px;
      padding: 20px;
      background: #f8fafc;
      border-radius: 8px;
      border: 1px solid #e2e8f0;
    }
    .meta-item label {
      font-size: 0.75rem;
      text-transform: uppercase;
      font-weight: 700;
      color: #64748b;
      margin-bottom: 4px;
      display: block;
    }
    .meta-item div {
      font-size: 0.95rem;
      font-weight: 700;
      color: #0f172a;
    }
    .receipt-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    .receipt-table th {
      background: #002365;
      color: #ffffff;
      padding: 12px 16px;
      font-size: 0.85rem;
      text-transform: uppercase;
    }
    .receipt-table td {
      padding: 14px 16px;
      border-bottom: 1px solid #e2e8f0;
      font-size: 0.92rem;
    }
    .total-row td {
      font-size: 1.1rem;
      font-weight: 800;
      background: #f8fafc;
      border-top: 2px solid #002365;
    }
    .receipt-footer {
      border-top: 1px solid #cbd5e1;
      padding-top: 20px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      font-size: 0.8rem;
      color: #64748b;
    }
    .signature-box {
      text-align: center;
      width: 200px;
    }
    .signature-line {
      border-top: 1px dashed #64748b;
      margin-top: 50px;
      padding-top: 6px;
      font-weight: 700;
      color: #0f172a;
    }
    .no-print {
      margin-bottom: 25px;
    }
    @media print {
      body { background: #fff; padding: 0; }
      .receipt-container { border: none; padding: 0; }
      .no-print { display: none !important; }
    }
  </style>
</head>
<body>

  <div class="receipt-container">
    
    <div class="no-print d-flex justify-content-between align-items-center">
      <a href="my-enrollments.php" class="btn btn-outline-dark btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Back to My Enrollments
      </a>
      <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
        <i class="fas fa-print me-1"></i> Print / Save as PDF
      </button>
    </div>

    <!-- Header -->
    <div class="receipt-header">
      <div class="inst-brand">
        <img src="assets/images/logo/logopng.png" alt="SG Skill Logo" onerror="this.style.display='none'">
        <div>
          <div class="inst-name">Skill Grow Institution</div>
          <div class="inst-sub">Center for Advanced Professional Learning & Technology</div>
        </div>
      </div>
      <div class="receipt-title-box">
        <div class="receipt-title">FEE RECEIPT</div>
        <div class="receipt-no">ID: <?= htmlspecialchars($enr['enrollment_id']) ?></div>
        <small class="text-muted">Date: <?= date('d M, Y', strtotime($enr['enrolled_at'])) ?></small>
      </div>
    </div>

    <!-- Student & Enrollment Metadata -->
    <div class="meta-grid">
      <div class="meta-item">
        <label>Student Name</label>
        <div><?= htmlspecialchars($enr['student_name']) ?></div>
      </div>
      <div class="meta-item">
        <label>Student ID</label>
        <div class="font-monospace text-primary"><?= htmlspecialchars($enr['student_id'] ?: 'N/A') ?></div>
      </div>
      <div class="meta-item">
        <label>Email Address</label>
        <div><?= htmlspecialchars($enr['student_email']) ?></div>
      </div>
      <div class="meta-item">
        <label>Mobile Number</label>
        <div><?= htmlspecialchars($enr['student_phone']) ?></div>
      </div>
    </div>

    <!-- Course & Fee Details -->
    <table class="receipt-table">
      <thead>
        <tr>
          <th>Description</th>
          <th>Course Duration / Mode</th>
          <th class="text-end">Amount (INR)</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <strong><?= htmlspecialchars($enr['course_name']) ?></strong><br>
            <small class="text-muted">Full Academic Tuition, Hands-on Lab Sandbox, Mentorship & Certification</small>
          </td>
          <td>
            <?= htmlspecialchars($enr['duration'] ?: '6 Months') ?><br>
            <small class="text-muted"><?= htmlspecialchars($enr['mode'] ?: 'Live & Campus') ?></small>
          </td>
          <td class="text-end font-monospace fw-bold">
            ₹<?= number_format((float)$enr['fee_amount'], 2) ?>
          </td>
        </tr>
        <tr>
          <td>
            <strong>E-Learning Portal & Project Repositories</strong>
          </td>
          <td>Included</td>
          <td class="text-end font-monospace text-success">
            ₹0.00
          </td>
        </tr>
        <tr class="total-row">
          <td colspan="2" class="text-end">Total Amount Paid:</td>
          <td class="text-end font-monospace text-primary">
            ₹<?= number_format((float)$enr['fee_amount'], 2) ?>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Transaction info -->
    <div class="p-3 mb-4 rounded-3 border bg-light d-flex justify-content-between align-items-center">
      <div>
        <span class="badge bg-success me-2">PAID IN FULL</span>
        <span class="text-muted" style="font-size: 0.85rem;">Payment Gateway: <strong><?= htmlspecialchars($enr['payment_method']) ?></strong></span>
      </div>
      <div class="font-monospace" style="font-size: 0.85rem;">
        Tx ID: <strong><?= htmlspecialchars($enr['razorpay_payment_id'] ?: 'RZP_VERIFIED') ?></strong>
      </div>
    </div>

    <!-- Footer & Signatures -->
    <div class="receipt-footer">
      <div>
        <p class="mb-1"><strong>Terms & Verification:</strong></p>
        <p class="mb-0">This document constitutes an electronic computer-generated receipt valid without physical seal.</p>
        <p class="mb-0">Skill Grow Institution &bull; Web: fairrepairguide.com &bull; Support: info@skillgrowinstitution.in</p>
      </div>
      <div class="signature-box">
        <div class="signature-line">Authorized Signatory<br><small class="text-muted">Academic Accounts Office</small></div>
      </div>
    </div>

  </div>

</body>
</html>
