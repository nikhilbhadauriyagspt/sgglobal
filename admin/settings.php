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

  <style>
    :root {
      --admin-primary: #e6a910;
      --admin-primary-hover: #cf9507;
      --admin-navy: #002365;
      --admin-dark-bg: #060b1b;
      --admin-sidebar-bg: #091026;
      --admin-card-bg: #0c1634;
      --admin-border: #1a274e;
      --admin-border-subtle: rgba(255, 255, 255, 0.08);
      --text-main: #f1f5f9;
      --text-muted: #94a3b8;
      --font-heading: 'Roboto Slab', serif;
      --font-body: 'Plus Jakarta Sans', sans-serif;
      --sidebar-width: 260px;
      --sidebar-collapsed-width: 80px;
      --topbar-height: 72px;
    }

    * {
      box-sizing: border-box;
      box-shadow: none !important;
    }

    body {
      font-family: var(--font-body);
      background-color: var(--admin-dark-bg);
      color: var(--text-main);
      margin: 0;
      overflow-x: hidden;
      min-height: 100vh;
    }

    .admin-layout-wrapper {
      display: flex;
      min-height: 100vh;
      position: relative;
    }

    /* Sidebar styles */
    .admin-sidebar {
      width: var(--sidebar-width);
      background-color: var(--admin-sidebar-bg);
      border-right: 1px solid var(--admin-border);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 1050;
      transition: width 0.3s ease, transform 0.3s ease;
    }

    .sidebar-brand-box {
      height: var(--topbar-height);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      border-bottom: 1px solid var(--admin-border);
    }

    .brand-link {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
    }

    .brand-logo-img {
      height: 42px;
      width: auto;
      object-fit: contain;
    }

    .brand-name {
      font-family: var(--font-heading);
      font-size: 1.05rem;
      font-weight: 700;
      color: #ffffff;
      line-height: 1.2;
    }

    .brand-sub {
      font-size: 0.72rem;
      color: var(--admin-primary);
      text-transform: uppercase;
      letter-spacing: 0.8px;
      font-weight: 600;
    }

    .sidebar-nav-container {
      flex: 1;
      padding: 20px 14px;
      overflow-y: auto;
    }

    .nav-section-title {
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #64748b;
      margin: 18px 10px 8px;
    }

    .sidebar-menu {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .menu-link {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 11px 14px;
      border-radius: 10px;
      color: #cbd5e1;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    .menu-link:hover, .menu-item.active .menu-link {
      background: rgba(230, 169, 16, 0.12);
      border-left: 3px solid var(--admin-primary);
      color: var(--admin-primary);
      font-weight: 700;
    }

    .menu-icon {
      font-size: 1.05rem;
      width: 22px;
      text-align: center;
    }

    .sidebar-user-footer {
      padding: 16px;
      border-top: 1px solid var(--admin-border);
      background: rgba(0, 0, 0, 0.2);
    }

    .user-info-box {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: rgba(230, 169, 16, 0.15);
      border: 1px solid rgba(230, 169, 16, 0.3);
      color: var(--admin-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
    }

    .user-name {
      font-size: 0.88rem;
      font-weight: 700;
      color: #ffffff;
      line-height: 1.2;
    }

    .user-role {
      font-size: 0.72rem;
      color: var(--text-muted);
    }

    .btn-sidebar-logout {
      color: #ef4444;
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.2);
      padding: 8px 10px;
      border-radius: 8px;
      font-size: 0.85rem;
      text-decoration: none;
    }

    /* Main Area */
    .admin-main {
      flex: 1;
      margin-left: var(--sidebar-width);
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    .admin-topbar {
      height: var(--topbar-height);
      background: rgba(9, 16, 38, 0.85);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--admin-border);
      position: sticky;
      top: 0;
      z-index: 1020;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 28px;
    }

    .btn-toggle-sidebar {
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid var(--admin-border);
      color: #ffffff;
      width: 42px;
      height: 42px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    .page-title-text {
      font-family: var(--font-heading);
      font-size: 1.25rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0;
    }

    .breadcrumb {
      font-size: 0.78rem;
      margin: 0;
    }

    .breadcrumb a {
      color: var(--text-muted);
      text-decoration: none;
    }

    .breadcrumb .active {
      color: var(--admin-primary);
    }

    .admin-content {
      padding: 28px;
      flex: 1;
    }

    /* Settings Bento Box */
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
    }

    .settings-bento-title {
      font-family: var(--font-heading);
      font-size: 1.25rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-label {
      font-size: 0.85rem;
      font-weight: 600;
      color: #cbd5e1;
      margin-bottom: 6px;
    }

    .form-control, .form-select {
      background: rgba(6, 11, 27, 0.7);
      border: 1px solid var(--admin-border);
      color: #ffffff;
      border-radius: 10px;
      padding: 11px 14px;
      font-size: 0.92rem;
    }

    .form-control:focus, .form-select:focus {
      background: rgba(6, 11, 27, 0.95);
      border-color: var(--admin-primary);
      color: #ffffff;
    }

    .mode-switch-badge {
      font-size: 0.78rem;
      font-weight: 700;
      padding: 5px 12px;
      border-radius: 50px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .btn-save-settings {
      background: linear-gradient(135deg, #f59e0b, #e6a910);
      color: #060b1b;
      font-weight: 700;
      font-size: 1rem;
      padding: 13px 32px;
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

    .admin-footer-bar {
      padding: 20px 28px;
      border-top: 1px solid var(--admin-border);
      background: rgba(6, 11, 27, 0.9);
      margin-top: auto;
    }
  </style>
</head>

<body>

  <div class="admin-layout-wrapper">
    
    <!-- Sidebar Navigation -->
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div class="admin-main">
      
      <!-- Topbar Header -->
      <header class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
          <button type="button" class="btn-toggle-sidebar" id="sidebarToggle">
            <i class="fas fa-bars-staggered"></i>
          </button>
          <div>
            <h1 class="page-title-text">System & Gateway Settings</h1>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Settings</li>
              </ol>
            </nav>
          </div>
        </div>

        <div>
          <a href="index.php" class="btn btn-outline-light btn-sm px-3 py-2 rounded-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
          </a>
        </div>
      </header>

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
    </div>
  </div>
</body>
</html>
