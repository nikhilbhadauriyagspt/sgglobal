<?php
// System Settings (SMTP Email & Razorpay Gateway) - Skill Grow Admin
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';

$pageTitle = 'Settings (SMTP & Razorpay)';
$msg = '';
$msgType = '';

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $smtp_host        = trim($_POST['smtp_host'] ?? 'smtp.gmail.com');
    $smtp_port        = (int)($_POST['smtp_port'] ?? 587);
    $smtp_user        = trim($_POST['smtp_user'] ?? '');
    $smtp_pass        = trim($_POST['smtp_pass'] ?? '');
    $smtp_encryption  = trim($_POST['smtp_encryption'] ?? 'tls');
    $smtp_from_email  = trim($_POST['smtp_from_email'] ?? 'admissions@skillgrowinstitution.in');
    $smtp_from_name   = trim($_POST['smtp_from_name'] ?? 'Skill Grow Institution');
    $smtp_mode        = trim($_POST['smtp_mode'] ?? 'test');

    $razorpay_key_id     = trim($_POST['razorpay_key_id'] ?? '');
    $razorpay_key_secret = trim($_POST['razorpay_key_secret'] ?? '');
    $razorpay_mode       = trim($_POST['razorpay_mode'] ?? 'test');
    $razorpay_status     = trim($_POST['razorpay_status'] ?? 'active');
    $admission_fee       = (float)($_POST['admission_fee'] ?? 999.00);

    try {
        $stmt = $pdo->prepare("
            UPDATE system_settings SET
                smtp_host = :sh,
                smtp_port = :sp,
                smtp_user = :su,
                smtp_pass = :spw,
                smtp_encryption = :se,
                smtp_from_email = :sfe,
                smtp_from_name = :sfn,
                smtp_mode = :sm,
                razorpay_key_id = :rki,
                razorpay_key_secret = :rks,
                razorpay_mode = :rm,
                razorpay_status = :rst,
                admission_fee = :af
            WHERE id = 1
        ");

        $stmt->execute([
            'sh'  => $smtp_host,
            'sp'  => $smtp_port,
            'su'  => $smtp_user,
            'spw' => $smtp_pass,
            'se'  => $smtp_encryption,
            'sfe' => $smtp_from_email,
            'sfn' => $smtp_from_name,
            'sm'  => $smtp_mode,
            'rki' => $razorpay_key_id,
            'rks' => $razorpay_key_secret,
            'rm'  => $razorpay_mode,
            'rst' => $razorpay_status,
            'af'  => $admission_fee,
        ]);

        $msg = 'Settings successfully updated!';
        $msgType = 'success';
    } catch (PDOException $e) {
        $msg = 'Failed to update settings: ' . $e->getMessage();
        $msgType = 'danger';
    }
}

// Fetch Current Settings
$stmt = $pdo->query("SELECT * FROM system_settings WHERE id = 1 LIMIT 1");
$settings = $stmt->fetch() ?: [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_user' => '',
    'smtp_pass' => '',
    'smtp_encryption' => 'tls',
    'smtp_from_email' => 'admissions@skillgrowinstitution.in',
    'smtp_from_name' => 'Skill Grow Institution',
    'smtp_mode' => 'test',
    'razorpay_key_id' => '',
    'razorpay_key_secret' => '',
    'razorpay_mode' => 'test',
    'razorpay_status' => 'active',
    'admission_fee' => 999.00,
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings (SMTP & Razorpay) | Skill Grow Admin</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Central Unified Admin Stylesheet -->
  <link rel="stylesheet" href="assets/css/admin.css">

  <style>
    .settings-bento {
      background: var(--admin-card-bg);
      border: 1px solid var(--admin-border);
      border-radius: 18px;
      padding: 28px;
      margin-bottom: 24px;
    }

    .settings-bento-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
      padding-bottom: 14px;
      border-bottom: 1px solid var(--admin-border-subtle);
      flex-wrap: wrap;
      gap: 10px;
    }

    .settings-bento-title {
      font-family: var(--font-heading);
      font-size: 1.2rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .mode-switch-badge {
      font-size: 0.76rem;
      font-weight: 700;
      padding: 5px 12px;
      border-radius: 50px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .btn-save-settings {
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #050a18;
      font-weight: 700;
      font-size: 0.95rem;
      padding: 12px 28px;
      border-radius: 12px;
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.2s ease;
    }

    .btn-save-settings:hover {
      background: #fbbf24;
      transform: translateY(-2px);
    }
  </style>
</head>

<body>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="admin-layout-wrapper">
    
    <!-- Sidebar Navigation -->
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div class="admin-main">
      
      <!-- Topbar Header -->
      <?php require_once __DIR__ . '/includes/header.php'; ?>

      <main class="admin-content">
        
        <?php if (!empty($msg)): ?>
          <div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert" style="border-radius: 12px;">
            <i class="fas <?= ($msgType === 'success') ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-2"></i>
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form method="POST" action="settings.php">
          
          <!-- SECTION 1: SMTP EMAIL SETTINGS -->
          <div class="settings-bento">
            <div class="settings-bento-header">
              <h2 class="settings-bento-title">
                <i class="fas fa-envelope text-warning"></i> PHPMailer / SMTP Configuration
              </h2>
              <div>
                <?php if ($settings['smtp_mode'] === 'live'): ?>
                  <span class="badge bg-success mode-switch-badge"><i class="fas fa-bolt me-1"></i> Live SMTP Mode</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark mode-switch-badge"><i class="fas fa-vial me-1"></i> Test Mode (Demo OTP: 123456)</span>
                <?php endif; ?>
              </div>
            </div>

            <div class="alert alert-dark border border-secondary mb-4" style="font-size: 0.88rem; background: rgba(255,255,255,0.03);">
              <i class="fas fa-info-circle text-warning me-2"></i>
              <strong>Test Mode me:</strong> Bina actual email setup ke bhi OTP screen par auto-display ho jayega taaki form testing bina rukawat ho sake.<br>
              <strong>Live Mode me:</strong> Jab aap apna Gmail/Webmail SMTP enter karenge, PHPMailer direct user ki email par OTP aur admission confirmation PDF send karega.
            </div>

            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Email System Mode</label>
                <select class="form-select" name="smtp_mode">
                  <option value="test" <?= ($settings['smtp_mode'] === 'test') ? 'selected' : '' ?>>Test Mode (Demo OTP 123456 on Screen)</option>
                  <option value="live" <?= ($settings['smtp_mode'] === 'live') ? 'selected' : '' ?>>Live Mode (Send Real Email via PHPMailer)</option>
                </select>
              </div>

              <div class="col-md-5">
                <label class="form-label">SMTP Host</label>
                <input type="text" class="form-control" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host']) ?>" placeholder="smtp.gmail.com">
              </div>

              <div class="col-md-3">
                <label class="form-label">SMTP Port</label>
                <input type="number" class="form-control" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port']) ?>" placeholder="587">
              </div>

              <div class="col-md-6">
                <label class="form-label">SMTP Username / Email</label>
                <input type="email" class="form-control" name="smtp_user" value="<?= htmlspecialchars($settings['smtp_user']) ?>" placeholder="your-email@gmail.com">
              </div>

              <div class="col-md-6">
                <label class="form-label">SMTP Password / App Password</label>
                <input type="password" class="form-control" name="smtp_pass" value="<?= htmlspecialchars($settings['smtp_pass']) ?>" placeholder="••••••••••••••••">
                <small class="text-muted" style="font-size: 0.75rem;">Gmail ke liye Google Account me jaakar 16-digit "App Password" generate karein.</small>
              </div>

              <div class="col-md-4">
                <label class="form-label">Encryption Protocol</label>
                <select class="form-select" name="smtp_encryption">
                  <option value="tls" <?= ($settings['smtp_encryption'] === 'tls') ? 'selected' : '' ?>>TLS (Recommended - Port 587)</option>
                  <option value="ssl" <?= ($settings['smtp_encryption'] === 'ssl') ? 'selected' : '' ?>>SSL (Port 465)</option>
                  <option value="none" <?= ($settings['smtp_encryption'] === 'none') ? 'selected' : '' ?>>None</option>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label">Sender Email (From)</label>
                <input type="email" class="form-control" name="smtp_from_email" value="<?= htmlspecialchars($settings['smtp_from_email']) ?>" placeholder="admissions@skillgrowinstitution.in">
              </div>

              <div class="col-md-4">
                <label class="form-label">Sender Display Name</label>
                <input type="text" class="form-control" name="smtp_from_name" value="<?= htmlspecialchars($settings['smtp_from_name']) ?>" placeholder="Skill Grow Institution">
              </div>
            </div>
          </div>

          <!-- SECTION 2: RAZORPAY PAYMENT GATEWAY SETTINGS -->
          <div class="settings-bento">
            <div class="settings-bento-header">
              <h2 class="settings-bento-title">
                <i class="fas fa-credit-card text-warning"></i> Razorpay Payment Gateway Setup
              </h2>
              <div>
                <?php if ($settings['razorpay_mode'] === 'live'): ?>
                  <span class="badge bg-success mode-switch-badge"><i class="fas fa-check-circle me-1"></i> Razorpay Live Key</span>
                <?php else: ?>
                  <span class="badge bg-info text-dark mode-switch-badge"><i class="fas fa-flask me-1"></i> Razorpay Test Mode</span>
                <?php endif; ?>
              </div>
            </div>

            <div class="alert alert-dark border border-secondary mb-4" style="font-size: 0.88rem; background: rgba(255,255,255,0.03);">
              <i class="fas fa-info-circle text-warning me-2"></i>
              <strong>Razorpay Setup:</strong> Apne Razorpay Dashboard (<a href="https://dashboard.razorpay.com/" target="_blank" class="text-warning">dashboard.razorpay.com</a>) se <strong>Key ID</strong> aur <strong>Key Secret</strong> yaha enter karein.
              Agar test mode select hai aur keys abhi enter nahi ki hain, to checkout fallback mode me test payment simulate karega taaki testing ruk na sake.
            </div>

            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Gateway Mode</label>
                <select class="form-select" name="razorpay_mode">
                  <option value="test" <?= ($settings['razorpay_mode'] === 'test') ? 'selected' : '' ?>>Test Mode (Sandbox / Testing)</option>
                  <option value="live" <?= ($settings['razorpay_mode'] === 'live') ? 'selected' : '' ?>>Live Mode (Real Customer Payments)</option>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label">Gateway Status</label>
                <select class="form-select" name="razorpay_status">
                  <option value="active" <?= ($settings['razorpay_status'] === 'active') ? 'selected' : '' ?>>Active (Enabled)</option>
                  <option value="inactive" <?= ($settings['razorpay_status'] === 'inactive') ? 'selected' : '' ?>>Inactive (Disabled)</option>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label">Registration Fee Amount (₹ INR)</label>
                <input type="number" step="0.01" class="form-control" name="admission_fee" value="<?= htmlspecialchars($settings['admission_fee']) ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Razorpay Key ID</label>
                <input type="text" class="form-control font-monospace" name="razorpay_key_id" value="<?= htmlspecialchars($settings['razorpay_key_id']) ?>" placeholder="rzp_test_... or rzp_live_...">
              </div>

              <div class="col-md-6">
                <label class="form-label">Razorpay Key Secret</label>
                <input type="password" class="form-control font-monospace" name="razorpay_key_secret" value="<?= htmlspecialchars($settings['razorpay_key_secret']) ?>" placeholder="••••••••••••••••••••">
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="text-end mb-4">
            <button type="submit" name="save_settings" class="btn-save-settings">
              <i class="fas fa-save"></i> Save System & Gateway Settings
            </button>
          </div>

        </form>

      </main>

      <?php require_once __DIR__ . '/includes/footer.php'; ?>
