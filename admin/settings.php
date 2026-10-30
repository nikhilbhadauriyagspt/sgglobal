<?php
// System Settings (Branding, Contact, Admission Fee, SMTP & Razorpay) - Skill Grow Admin
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/../includes/settings_helper.php';

$pageTitle = 'Website & System Settings';
$msg = '';
$msgType = '';

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    // 1. General & Branding
    $site_title     = trim($_POST['site_title'] ?? 'Skill Grow Institution');
    $site_phone     = trim($_POST['site_phone'] ?? '+91 9217431562');
    $site_whatsapp  = trim($_POST['site_whatsapp'] ?? '919217431562');
    $site_email     = trim($_POST['site_email'] ?? 'info@skillgrowinstitution.in');
    $site_address   = trim($_POST['site_address'] ?? 'Metro Plaza Meerut, 250002, India');
    $admission_fee  = (float)($_POST['admission_fee'] ?? 999.00);

    // Social Links
    $social_facebook  = trim($_POST['social_facebook'] ?? '');
    $social_twitter   = trim($_POST['social_twitter'] ?? '');
    $social_linkedin  = trim($_POST['social_linkedin'] ?? '');
    $social_instagram = trim($_POST['social_instagram'] ?? '');
    $social_youtube   = trim($_POST['social_youtube'] ?? '');

    // Logo Upload Handling
    $site_logo = trim($_POST['existing_site_logo'] ?? 'assets/images/logo/logopng.png');
    if (!empty($_FILES['site_logo_file']['name']) && $_FILES['site_logo_file']['error'] === UPLOAD_ERR_OK) {
        $allowedExts = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
        $ext = strtolower(pathinfo($_FILES['site_logo_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExts, true)) {
            $uploadDir = __DIR__ . '/../assets/images/logo/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $newFileName = 'logo_' . time() . '.' . $ext;
            $destPath = $uploadDir . $newFileName;
            if (move_uploaded_file($_FILES['site_logo_file']['tmp_name'], $destPath)) {
                $site_logo = 'assets/images/logo/' . $newFileName;
            }
        }
    }

    // 2. SMTP Mail
    $smtp_host        = trim($_POST['smtp_host'] ?? 'smtp.gmail.com');
    $smtp_port        = (int)($_POST['smtp_port'] ?? 587);
    $smtp_user        = trim($_POST['smtp_user'] ?? '');
    $smtp_pass        = trim($_POST['smtp_pass'] ?? '');
    $smtp_encryption  = trim($_POST['smtp_encryption'] ?? 'tls');
    $smtp_from_email  = trim($_POST['smtp_from_email'] ?? 'admissions@skillgrowinstitution.in');
    $smtp_from_name   = trim($_POST['smtp_from_name'] ?? 'Skill Grow Institution');
    $smtp_mode        = trim($_POST['smtp_mode'] ?? 'test');

    // 3. Razorpay
    $razorpay_key_id     = trim($_POST['razorpay_key_id'] ?? '');
    $razorpay_key_secret = trim($_POST['razorpay_key_secret'] ?? '');
    $razorpay_mode       = trim($_POST['razorpay_mode'] ?? 'test');
    $razorpay_status     = trim($_POST['razorpay_status'] ?? 'active');

    try {
        $stmt = $pdo->prepare("
            UPDATE system_settings SET
                site_title = :st,
                site_logo = :sl,
                site_phone = :sph,
                site_whatsapp = :swa,
                site_email = :sem,
                site_address = :saddr,
                social_facebook = :sfb,
                social_twitter = :stw,
                social_linkedin = :sli,
                social_instagram = :sig,
                social_youtube = :syt,
                admission_fee = :af,
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
                razorpay_status = :rst
            WHERE id = 1
        ");

        $stmt->execute([
            'st'    => $site_title,
            'sl'    => $site_logo,
            'sph'   => $site_phone,
            'swa'   => $site_whatsapp,
            'sem'   => $site_email,
            'saddr' => $site_address,
            'sfb'   => $social_facebook,
            'stw'   => $social_twitter,
            'sli'   => $social_linkedin,
            'sig'   => $social_instagram,
            'syt'   => $social_youtube,
            'af'    => $admission_fee,
            'sh'    => $smtp_host,
            'sp'    => $smtp_port,
            'su'    => $smtp_user,
            'spw'   => $smtp_pass,
            'se'    => $smtp_encryption,
            'sfe'   => $smtp_from_email,
            'sfn'   => $smtp_from_name,
            'sm'    => $smtp_mode,
            'rki'   => $razorpay_key_id,
            'rks'   => $razorpay_key_secret,
            'rm'    => $razorpay_mode,
            'rst'   => $razorpay_status,
        ]);

        $msg = 'Website information, admission fees & system settings saved successfully!';
        $msgType = 'success';
    } catch (PDOException $e) {
        $msg = 'Failed to update settings: ' . $e->getMessage();
        $msgType = 'danger';
    }
}

// Fetch Current Settings
$settings = getGlobalSettings();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Website & System Settings | Skill Grow Admin</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../<?= htmlspecialchars($settings['site_logo']) ?>" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Central Unified Admin Stylesheet -->
  <link rel="stylesheet" href="assets/css/admin.css">

  <style>
    .settings-nav-tabs {
      border-bottom: 2px solid var(--admin-border);
      gap: 10px;
      margin-bottom: 24px;
    }
    .settings-nav-tabs .nav-link {
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid var(--admin-border);
      border-bottom: none;
      color: #94a3b8;
      font-weight: 700;
      font-size: 0.88rem;
      padding: 12px 20px;
      border-radius: 10px 10px 0 0;
      transition: all 0.2s;
    }
    .settings-nav-tabs .nav-link:hover {
      color: #fbbf24;
      background: rgba(255, 255, 255, 0.08);
    }
    .settings-nav-tabs .nav-link.active {
      color: #050a18 !important;
      background: #fbbf24 !important;
      border-color: #fbbf24 !important;
    }

    .settings-bento {
      background: var(--admin-card-bg);
      border: 1px solid var(--admin-border);
      border-radius: 14px;
      padding: 24px;
      margin-bottom: 24px;
    }

    .settings-bento-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--admin-border-subtle);
      flex-wrap: wrap;
      gap: 10px;
    }

    .settings-bento-title {
      font-family: var(--font-heading);
      font-size: 1.15rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo-preview-box {
      width: 140px;
      height: 70px;
      border-radius: 8px;
      background: #040817;
      border: 1.5px dashed var(--admin-border);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 6px;
      overflow: hidden;
    }
    .logo-preview-box img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }

    .btn-save-settings {
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #050a18;
      font-weight: 800;
      font-size: 0.95rem;
      padding: 12px 28px;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
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
      <header class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
          <button type="button" class="btn btn-outline-light d-lg-none" id="sidebarToggleBtn" aria-label="Toggle Navigation">
            <i class="fas fa-bars"></i>
          </button>
          <div>
            <h1 class="page-title-text" style="font-size: 1.25rem; font-family: var(--font-heading); margin: 0; font-weight: 700;">
              Website & System Configuration
            </h1>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
                <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Admin</a></li>
                <li class="breadcrumb-item active text-warning">Settings</li>
              </ol>
            </nav>
          </div>
        </div>

        <div class="d-flex gap-2">
          <a href="../index.php" target="_blank" class="btn btn-outline-light btn-sm px-3 py-2 rounded-3">
            <i class="fas fa-external-link-alt me-1"></i> View Live Website
          </a>
        </div>
      </header>

      <main class="admin-content">
        
        <?php if (!empty($msg)): ?>
          <div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert" style="border-radius: 10px;">
            <i class="fas <?= ($msgType === 'success') ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-2"></i>
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form method="POST" action="settings.php" enctype="multipart/form-data">
          <input type="hidden" name="existing_site_logo" value="<?= htmlspecialchars($settings['site_logo']) ?>">

          <!-- NAVIGATION TABS -->
          <ul class="nav nav-tabs settings-nav-tabs" id="settingsTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#tab-general" type="button" role="tab" aria-selected="true">
                <i class="fas fa-sliders-h me-2"></i> Website Logo, Contact & Admission Fee
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="smtp-tab" data-bs-toggle="tab" data-bs-target="#tab-smtp" type="button" role="tab" aria-selected="false">
                <i class="fas fa-envelope me-2"></i> Email & SMTP Setup
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="gateway-tab" data-bs-toggle="tab" data-bs-target="#tab-gateway" type="button" role="tab" aria-selected="false">
                <i class="fas fa-credit-card me-2"></i> Razorpay Payment Gateway
              </button>
            </li>
          </ul>

          <div class="tab-content" id="settingsTabContent">
            
            <!-- TAB 1: WEBSITE LOGO, CONTACT, SOCIAL & ADMISSION FEE -->
            <div class="tab-pane fade show active" id="tab-general" role="tabpanel" aria-labelledby="general-tab">
              
              <!-- 1.1 Branding & Admission Fee -->
              <div class="settings-bento">
                <div class="settings-bento-header">
                  <h2 class="settings-bento-title">
                    <i class="fas fa-university text-warning"></i> Website Branding & Universal Admission Fee
                  </h2>
                </div>

                <div class="row g-3">
                  <div class="col-md-7">
                    <label class="form-label text-light">Website / Institution Title</label>
                    <input type="text" class="form-control" name="site_title" value="<?= htmlspecialchars($settings['site_title']) ?>" required>
                  </div>

                  <div class="col-md-5">
                    <label class="form-label text-warning fw-bold">
                      <i class="fas fa-indian-rupee-sign me-1"></i> Universal Admission / Registration Fee (₹)
                    </label>
                    <input type="number" step="0.01" class="form-control text-success fw-bold font-monospace fs-6" name="admission_fee" value="<?= htmlspecialchars($settings['admission_fee']) ?>" required>
                    <small class="text-muted" style="font-size: 0.74rem;">Yeh fee sabhi courses ke initial registration me dynamic apply hogi aur final enrollment me deduct hogi.</small>
                  </div>

                  <!-- Logo Upload -->
                  <div class="col-md-8">
                    <label class="form-label text-light">Website Logo (Header, Footer, Emails & Receipts)</label>
                    <input type="file" class="form-control" name="site_logo_file" accept=".png,.jpg,.jpeg,.webp,.svg">
                    <small class="text-muted" style="font-size: 0.75rem;">Current: <code><?= htmlspecialchars($settings['site_logo']) ?></code></small>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label text-light">Current Logo Preview</label>
                    <div class="logo-preview-box">
                      <img src="../<?= htmlspecialchars($settings['site_logo']) ?>" alt="Logo Preview" onerror="this.src='../assets/images/logo/logopng.png'">
                    </div>
                  </div>
                </div>
              </div>

              <!-- 1.2 Contact Details & Helpline -->
              <div class="settings-bento">
                <div class="settings-bento-header">
                  <h2 class="settings-bento-title">
                    <i class="fas fa-address-book text-warning"></i> Official Contact Information
                  </h2>
                </div>

                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label text-light"><i class="fas fa-phone-alt text-success me-1"></i> Calling Helpline Number</label>
                    <input type="text" class="form-control" name="site_phone" value="<?= htmlspecialchars($settings['site_phone']) ?>" placeholder="+91 9217431562">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label text-light"><i class="fab fa-whatsapp text-success me-1"></i> WhatsApp Number (Without +)</label>
                    <input type="text" class="form-control" name="site_whatsapp" value="<?= htmlspecialchars($settings['site_whatsapp']) ?>" placeholder="919217431562">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label text-light"><i class="fas fa-envelope text-info me-1"></i> Support / Info Email</label>
                    <input type="email" class="form-control" name="site_email" value="<?= htmlspecialchars($settings['site_email']) ?>" placeholder="info@skillgrowinstitution.in">
                  </div>

                  <div class="col-12">
                    <label class="form-label text-light"><i class="fas fa-map-marker-alt text-danger me-1"></i> Campus / Office Physical Address</label>
                    <input type="text" class="form-control" name="site_address" value="<?= htmlspecialchars($settings['site_address']) ?>" placeholder="Metro Plaza Meerut, 250002, India">
                  </div>
                </div>
              </div>

              <!-- 1.3 Social Media Links -->
              <div class="settings-bento">
                <div class="settings-bento-header">
                  <h2 class="settings-bento-title">
                    <i class="fas fa-share-alt text-warning"></i> Social Media Profiles (Footer & Contact)
                  </h2>
                </div>

                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label text-light"><i class="fab fa-facebook-f text-primary me-1"></i> Facebook URL</label>
                    <input type="url" class="form-control" name="social_facebook" value="<?= htmlspecialchars($settings['social_facebook']) ?>" placeholder="https://facebook.com/...">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label text-light"><i class="fab fa-instagram text-danger me-1"></i> Instagram URL</label>
                    <input type="url" class="form-control" name="social_instagram" value="<?= htmlspecialchars($settings['social_instagram']) ?>" placeholder="https://instagram.com/...">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label text-light"><i class="fab fa-linkedin-in text-info me-1"></i> LinkedIn URL</label>
                    <input type="url" class="form-control" name="social_linkedin" value="<?= htmlspecialchars($settings['social_linkedin']) ?>" placeholder="https://linkedin.com/...">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-light"><i class="fab fa-twitter text-info me-1"></i> Twitter / X URL</label>
                    <input type="url" class="form-control" name="social_twitter" value="<?= htmlspecialchars($settings['social_twitter']) ?>" placeholder="https://twitter.com/...">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-light"><i class="fab fa-youtube text-danger me-1"></i> YouTube Channel URL</label>
                    <input type="url" class="form-control" name="social_youtube" value="<?= htmlspecialchars($settings['social_youtube']) ?>" placeholder="https://youtube.com/...">
                  </div>
                </div>
              </div>

            </div>

            <!-- TAB 2: SMTP EMAIL CONFIGURATION -->
            <div class="tab-pane fade" id="tab-smtp" role="tabpanel" aria-labelledby="smtp-tab">
              <div class="settings-bento">
                <div class="settings-bento-header">
                  <h2 class="settings-bento-title">
                    <i class="fas fa-envelope text-warning"></i> PHPMailer / SMTP Configuration
                  </h2>
                  <div>
                    <?php if ($settings['smtp_mode'] === 'live'): ?>
                      <span class="badge bg-success"><i class="fas fa-bolt me-1"></i> Live SMTP Mode</span>
                    <?php else: ?>
                      <span class="badge bg-warning text-dark"><i class="fas fa-vial me-1"></i> Test Mode</span>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label text-light">Email System Mode</label>
                    <select class="form-select" name="smtp_mode">
                      <option value="test" <?= ($settings['smtp_mode'] === 'test') ? 'selected' : '' ?>>Test Mode (OTP on screen & simulated receipt)</option>
                      <option value="live" <?= ($settings['smtp_mode'] === 'live') ? 'selected' : '' ?>>Live Mode (Send real email & PDF via SMTP)</option>
                    </select>
                  </div>

                  <div class="col-md-5">
                    <label class="form-label text-light">SMTP Host</label>
                    <input type="text" class="form-control" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host']) ?>" placeholder="smtp.gmail.com">
                  </div>

                  <div class="col-md-3">
                    <label class="form-label text-light">SMTP Port</label>
                    <input type="number" class="form-control" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port']) ?>" placeholder="587">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-light">SMTP Username / Email</label>
                    <input type="email" class="form-control" name="smtp_user" value="<?= htmlspecialchars($settings['smtp_user']) ?>" placeholder="your-email@gmail.com">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-light">SMTP Password / App Password</label>
                    <input type="password" class="form-control" name="smtp_pass" value="<?= htmlspecialchars($settings['smtp_pass']) ?>" placeholder="••••••••••••••••">
                    <small class="text-muted" style="font-size: 0.74rem;">Gmail ke liye Google Security me jakar 16-digit "App Password" generate karein.</small>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label text-light">Encryption Protocol</label>
                    <select class="form-select" name="smtp_encryption">
                      <option value="tls" <?= ($settings['smtp_encryption'] === 'tls') ? 'selected' : '' ?>>TLS (Port 587)</option>
                      <option value="ssl" <?= ($settings['smtp_encryption'] === 'ssl') ? 'selected' : '' ?>>SSL (Port 465)</option>
                      <option value="none" <?= ($settings['smtp_encryption'] === 'none') ? 'selected' : '' ?>>None</option>
                    </select>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label text-light">Sender Email (From)</label>
                    <input type="email" class="form-control" name="smtp_from_email" value="<?= htmlspecialchars($settings['smtp_from_email']) ?>" placeholder="admissions@skillgrowinstitution.in">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label text-light">Sender Display Name</label>
                    <input type="text" class="form-control" name="smtp_from_name" value="<?= htmlspecialchars($settings['smtp_from_name']) ?>" placeholder="Skill Grow Institution">
                  </div>
                </div>
              </div>
            </div>

            <!-- TAB 3: RAZORPAY PAYMENT GATEWAY -->
            <div class="tab-pane fade" id="tab-gateway" role="tabpanel" aria-labelledby="gateway-tab">
              <div class="settings-bento">
                <div class="settings-bento-header">
                  <h2 class="settings-bento-title">
                    <i class="fas fa-credit-card text-warning"></i> Razorpay Payment Gateway Setup
                  </h2>
                  <div>
                    <?php if ($settings['razorpay_mode'] === 'live'): ?>
                      <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Live Razorpay Mode</span>
                    <?php else: ?>
                      <span class="badge bg-info text-dark"><i class="fas fa-flask me-1"></i> Test / Sandbox Mode</span>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label text-light">Gateway Mode</label>
                    <select class="form-select" name="razorpay_mode">
                      <option value="test" <?= ($settings['razorpay_mode'] === 'test') ? 'selected' : '' ?>>Test Mode (Sandbox / Testing)</option>
                      <option value="live" <?= ($settings['razorpay_mode'] === 'live') ? 'selected' : '' ?>>Live Mode (Real Customer Payments)</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-light">Gateway Status</label>
                    <select class="form-select" name="razorpay_status">
                      <option value="active" <?= ($settings['razorpay_status'] === 'active') ? 'selected' : '' ?>>Active (Enabled)</option>
                      <option value="inactive" <?= ($settings['razorpay_status'] === 'inactive') ? 'selected' : '' ?>>Inactive (Disabled)</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-light">Razorpay Key ID</label>
                    <input type="text" class="form-control font-monospace" name="razorpay_key_id" value="<?= htmlspecialchars($settings['razorpay_key_id']) ?>" placeholder="rzp_test_... or rzp_live_...">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-light">Razorpay Key Secret</label>
                    <input type="password" class="form-control font-monospace" name="razorpay_key_secret" value="<?= htmlspecialchars($settings['razorpay_key_secret']) ?>" placeholder="••••••••••••••••••••">
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Bottom Save Button -->
          <div class="text-end mb-4">
            <button type="submit" name="save_settings" class="btn-save-settings">
              <i class="fas fa-save"></i> Save All Settings
            </button>
          </div>

        </form>

      </main>

      <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/admin.js"></script>
</body>
</html>
