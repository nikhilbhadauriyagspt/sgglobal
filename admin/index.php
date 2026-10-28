<?php
// Skill Grow Institution - Centralized Executive Admin Dashboard
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';

$pageTitle = 'Dashboard';

// Fetch Real Statistics from Database
try {
    // Total Admissions
    $stmtAdm = $pdo->query("SELECT COUNT(*) AS total FROM admissions");
    $totalAdmissions = $stmtAdm->fetch()['total'] ?? 0;

    // Total Fees Collected
    $stmtFee = $pdo->query("SELECT SUM(fee_amount) AS total_fees FROM admissions WHERE payment_status = 'paid'");
    $totalFees = $stmtFee->fetch()['total_fees'] ?? 0;

    // Total Inquiries
    $stmtInq = $pdo->query("SELECT COUNT(*) AS total FROM inquiries");
    $totalInquiries = $stmtInq->fetch()['total'] ?? 0;

    // Total Admins
    $stmtAdmin = $pdo->query("SELECT COUNT(*) AS total FROM admins");
    $totalAdmins = $stmtAdmin->fetch()['total'] ?? 0;

    // Recent Admissions List
    $stmtRecent = $pdo->query("SELECT * FROM admissions ORDER BY id DESC LIMIT 8");
    $recentAdmissions = $stmtRecent->fetchAll();

} catch (PDOException $e) {
    $totalAdmissions = 0;
    $totalFees = 0;
    $totalInquiries = 0;
    $totalAdmins = 1;
    $recentAdmissions = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard | Skill Grow Institution</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <!-- Premium Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/admin.css">

  <style>
    :root {
      --admin-primary: #e6a910;
      --admin-primary-hover: #cf9507;
      --admin-navy: #002365;
      --admin-dark-bg: #060b1b;
      --admin-sidebar-bg: #091026;
      --admin-card-bg: #0c1634;
      --admin-card-hover: #0f1c42;
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
      padding: 0;
      overflow-x: hidden;
      min-height: 100vh;
    }

    /* Layout Wrapper */
    .admin-layout-wrapper {
      display: flex;
      min-height: 100vh;
      position: relative;
    }

    /* ============================================================
       SIDEBAR STYLING
    ============================================================ */
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
      transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease;
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
      overflow: hidden;
    }

    .brand-logo-img {
      height: 42px;
      width: auto;
      object-fit: contain;
      flex-shrink: 0;
    }

    .brand-text {
      display: flex;
      flex-direction: column;
      white-space: nowrap;
      transition: opacity 0.2s ease;
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

    .btn-close-sidebar {
      background: transparent;
      border: none;
      color: var(--text-muted);
      font-size: 1.2rem;
      cursor: pointer;
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
      white-space: nowrap;
      transition: opacity 0.2s ease;
    }

    .sidebar-menu {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .menu-item {
      margin-bottom: 4px;
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
      white-space: nowrap;
      position: relative;
    }

    .menu-link:hover {
      background: rgba(255, 255, 255, 0.05);
      color: #ffffff;
    }

    .menu-item.active .menu-link {
      background: linear-gradient(90deg, rgba(230, 169, 16, 0.15) 0%, rgba(230, 169, 16, 0.04) 100%);
      border-left: 3px solid var(--admin-primary);
      color: var(--admin-primary);
      font-weight: 700;
    }

    .menu-icon {
      font-size: 1.05rem;
      width: 22px;
      text-align: center;
      flex-shrink: 0;
      color: #94a3b8;
      transition: color 0.2s ease;
    }

    .menu-item.active .menu-icon,
    .menu-link:hover .menu-icon {
      color: var(--admin-primary);
    }

    .small-icon {
      font-size: 0.75rem;
      opacity: 0.6;
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
      flex-shrink: 0;
    }

    .user-details {
      flex: 1;
      overflow: hidden;
      white-space: nowrap;
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
      transition: all 0.2s;
    }

    .btn-sidebar-logout:hover {
      background: #ef4444;
      color: #ffffff;
    }

    /* ============================================================
       TOPBAR HEADER STYLING
    ============================================================ */
    .admin-main {
      flex: 1;
      margin-left: var(--sidebar-width);
      display: flex;
      flex-direction: column;
      min-width: 0;
      transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 16px;
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
      font-size: 1.15rem;
      transition: all 0.2s ease;
    }

    .btn-toggle-sidebar:hover {
      background: var(--admin-primary);
      color: #060b1b;
      border-color: var(--admin-primary);
    }

    .page-title-text {
      font-family: var(--font-heading);
      font-size: 1.25rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0;
      line-height: 1.2;
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

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .live-clock-badge {
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid var(--admin-border);
      padding: 7px 14px;
      border-radius: 50px;
      font-size: 0.8rem;
      color: #cbd5e1;
      font-weight: 500;
    }

    .btn-topbar-action {
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid var(--admin-border);
      color: #cbd5e1;
      padding: 8px 14px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.85rem;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-topbar-action:hover {
      background: rgba(255, 255, 255, 0.12);
      color: #ffffff;
    }

    .notification-ping {
      position: absolute;
      top: 6px;
      right: 6px;
      width: 8px;
      height: 8px;
      background-color: #ef4444;
      border-radius: 50%;
    }

    .btn-profile-pill {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--admin-border);
      border-radius: 50px;
      padding: 5px 14px 5px 6px;
      display: flex;
      align-items: center;
      gap: 10px;
      color: #ffffff;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-profile-pill:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(230, 169, 16, 0.4);
    }

    .profile-avatar-circle {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: var(--admin-primary);
      color: #060b1b;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.85rem;
      font-weight: 700;
    }

    .profile-name {
      font-size: 0.82rem;
      font-weight: 700;
      line-height: 1.1;
    }

    .profile-role {
      font-size: 0.7rem;
      color: var(--text-muted);
    }

    .admin-dropdown-menu {
      background: #091026;
      border: 1px solid var(--admin-border);
      border-radius: 14px;
      padding: 10px;
      min-width: 240px;
      margin-top: 10px;
    }

    .admin-dropdown-menu .dropdown-item {
      color: #cbd5e1;
      padding: 9px 12px;
      border-radius: 8px;
      font-size: 0.86rem;
      transition: all 0.2s;
    }

    .admin-dropdown-menu .dropdown-item:hover {
      background: rgba(255, 255, 255, 0.08);
      color: #ffffff;
    }

    .notification-item {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .notification-icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.95rem;
      flex-shrink: 0;
    }

    .notification-title {
      font-size: 0.85rem;
      font-weight: 600;
      color: #ffffff;
      line-height: 1.2;
    }

    /* ============================================================
       COLLAPSED SIDEBAR STATE (DESKTOP)
    ============================================================ */
    .admin-layout-wrapper.sidebar-collapsed .admin-sidebar {
      width: var(--sidebar-collapsed-width);
    }

    .admin-layout-wrapper.sidebar-collapsed .brand-text,
    .admin-layout-wrapper.sidebar-collapsed .nav-section-title,
    .admin-layout-wrapper.sidebar-collapsed .menu-text,
    .admin-layout-wrapper.sidebar-collapsed .user-details,
    .admin-layout-wrapper.sidebar-collapsed .menu-link .badge,
    .admin-layout-wrapper.sidebar-collapsed .menu-link .small-icon {
      display: none !important;
    }

    .admin-layout-wrapper.sidebar-collapsed .menu-link {
      justify-content: center;
      padding: 12px 0;
    }

    .admin-layout-wrapper.sidebar-collapsed .menu-icon {
      font-size: 1.25rem;
      margin: 0;
    }

    .admin-layout-wrapper.sidebar-collapsed .admin-main {
      margin-left: var(--sidebar-collapsed-width);
    }

    /* ============================================================
       BODY CONTENT & BENTO CARDS
    ============================================================ */
    .admin-content {
      padding: 28px;
      flex: 1;
    }

    /* Welcome Banner */
    .welcome-banner {
      background: linear-gradient(135deg, #0c1634 0%, #11204a 100%);
      border: 1px solid var(--admin-border);
      border-radius: 18px;
      padding: 28px 32px;
      margin-bottom: 28px;
      position: relative;
      overflow: hidden;
    }

    .welcome-banner::after {
      content: '';
      position: absolute;
      right: -50px;
      top: -50px;
      width: 250px;
      height: 250px;
      background: radial-gradient(circle, rgba(230, 169, 16, 0.12) 0%, transparent 70%);
      pointer-events: none;
    }

    .welcome-banner h2 {
      font-family: var(--font-heading);
      font-size: 1.7rem;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 6px;
    }

    .welcome-banner p {
      color: var(--text-muted);
      font-size: 0.95rem;
      margin-bottom: 0;
    }

    /* Metric Cards */
    .stat-card {
      background: var(--admin-card-bg);
      border: 1px solid var(--admin-border);
      border-radius: 16px;
      padding: 22px;
      height: 100%;
      transition: all 0.25s ease;
      position: relative;
    }

    .stat-card:hover {
      background: var(--admin-card-hover);
      border-color: rgba(230, 169, 16, 0.4);
      transform: translateY(-2px);
    }

    .stat-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
    }

    .stat-icon-wrapper {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
    }

    .stat-icon-gold {
      background: rgba(230, 169, 16, 0.12);
      border: 1px solid rgba(230, 169, 16, 0.3);
      color: var(--admin-primary);
    }

    .stat-icon-green {
      background: rgba(16, 185, 129, 0.12);
      border: 1px solid rgba(16, 185, 129, 0.3);
      color: #10b981;
    }

    .stat-icon-blue {
      background: rgba(59, 130, 246, 0.12);
      border: 1px solid rgba(59, 130, 246, 0.3);
      color: #3b82f6;
    }

    .stat-icon-purple {
      background: rgba(168, 85, 247, 0.12);
      border: 1px solid rgba(168, 85, 247, 0.3);
      color: #a855f7;
    }

    .stat-trend {
      font-size: 0.78rem;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 50px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .stat-trend-up {
      background: rgba(16, 185, 129, 0.15);
      color: #10b981;
    }

    .stat-value {
      font-family: var(--font-heading);
      font-size: 2rem;
      font-weight: 700;
      color: #ffffff;
      line-height: 1.1;
      margin-bottom: 4px;
    }

    .stat-label {
      color: var(--text-muted);
      font-size: 0.85rem;
      font-weight: 500;
      margin: 0;
    }

    /* Content Bento Blocks */
    .bento-card {
      background: var(--admin-card-bg);
      border: 1px solid var(--admin-border);
      border-radius: 18px;
      padding: 24px;
      height: 100%;
    }

    .bento-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
      padding-bottom: 14px;
      border-bottom: 1px solid var(--admin-border-subtle);
    }

    .bento-title {
      font-family: var(--font-heading);
      font-size: 1.15rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Modern Table */
    .admin-table {
      width: 100%;
      color: #cbd5e1;
      border-collapse: separate;
      border-spacing: 0;
    }

    .admin-table th {
      background: rgba(0, 0, 0, 0.2);
      color: #94a3b8;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      padding: 12px 14px;
      border-bottom: 1px solid var(--admin-border);
    }

    .admin-table td {
      padding: 14px;
      border-bottom: 1px solid var(--admin-border-subtle);
      font-size: 0.88rem;
      vertical-align: middle;
    }

    .admin-table tr:hover td {
      background: rgba(255, 255, 255, 0.02);
    }

    .app-id-badge {
      font-family: monospace;
      font-size: 0.82rem;
      font-weight: 700;
      color: var(--admin-primary);
      background: rgba(230, 169, 16, 0.1);
      padding: 3px 8px;
      border-radius: 6px;
      border: 1px solid rgba(230, 169, 16, 0.25);
    }

    .status-pill {
      font-size: 0.74rem;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 50px;
      display: inline-block;
      text-transform: capitalize;
    }

    .status-verified,
    .status-approved,
    .status-paid {
      background: rgba(16, 185, 129, 0.15);
      color: #10b981;
      border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .status-under_review,
    .status-pending {
      background: rgba(245, 158, 11, 0.15);
      color: #f59e0b;
      border: 1px solid rgba(245, 158, 11, 0.3);
    }

    /* Department Progress */
    .prog-item {
      margin-bottom: 16px;
    }

    .prog-header {
      display: flex;
      justify-content: space-between;
      font-size: 0.85rem;
      margin-bottom: 6px;
    }

    .prog-track {
      height: 8px;
      background: rgba(255, 255, 255, 0.06);
      border-radius: 50px;
      overflow: hidden;
    }

    .prog-bar {
      height: 100%;
      border-radius: 50px;
    }

    /* Action Buttons */
    .quick-action-btn {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 14px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid var(--admin-border);
      color: #ffffff;
      text-decoration: none;
      font-size: 0.88rem;
      font-weight: 600;
      transition: all 0.2s ease;
      margin-bottom: 10px;
    }

    .quick-action-btn:hover {
      background: rgba(230, 169, 16, 0.1);
      border-color: var(--admin-primary);
      color: var(--admin-primary);
    }

    .quick-action-btn i {
      font-size: 1.1rem;
      width: 24px;
      text-align: center;
      color: var(--admin-primary);
    }

    /* Footer */
    .admin-footer-bar {
      padding: 20px 28px;
      border-top: 1px solid var(--admin-border);
      background: rgba(6, 11, 27, 0.9);
      margin-top: auto;
    }

    /* Mobile Backdrop */
    .sidebar-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(4px);
      z-index: 1040;
    }

    @media (max-width: 991px) {
      .admin-sidebar {
        transform: translateX(-100%);
      }

      .admin-sidebar.show-mobile {
        transform: translateX(0);
      }

      .sidebar-overlay.show {
        display: block;
      }

      .admin-main {
        margin-left: 0 !important;
      }

      .admin-content {
        padding: 18px;
      }

      .admin-topbar {
        padding: 0 16px;
      }
    }
  </style>
</head>

<body>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="admin-layout-wrapper">
    
    <!-- Sidebar Navigation Component -->
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="admin-main">
      
      <!-- Topbar Header Component -->
      <?php require_once __DIR__ . '/includes/header.php'; ?>

      <!-- Page Content Body -->
      <main class="admin-content">
        
        <!-- Welcome Executive Banner -->
        <div class="welcome-banner">
          <div class="row align-items-center">
            <div class="col-lg-8">
              <h2>Good <?= (date('H') < 12) ? 'Morning' : ((date('H') < 17) ? 'Afternoon' : 'Evening') ?>, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Super Admin') ?>! 👋</h2>
              <p>Welcome to the Skill Grow Institution Centralized Portal. Here is your operational overview for today.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
              <a href="../register.php" target="_blank" class="btn btn-warning fw-bold px-3 py-2 me-2" style="border-radius: 10px; font-size: 0.88rem;">
                <i class="fas fa-plus-circle me-1"></i> New Admission Form
              </a>
            </div>
          </div>
        </div>

        <!-- 4 Metric Cards Grid -->
        <div class="row g-3 mb-4">
          
          <!-- Metric 1: Total Admissions -->
          <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
              <div class="stat-header">
                <div class="stat-icon-wrapper stat-icon-gold">
                  <i class="fas fa-user-graduate"></i>
                </div>
                <span class="stat-trend stat-trend-up">
                  <i class="fas fa-arrow-trend-up"></i> +14.2%
                </span>
              </div>
              <div class="stat-value"><?= number_format($totalAdmissions) ?></div>
              <p class="stat-label">Total Admissions</p>
            </div>
          </div>

          <!-- Metric 2: Total Fees Collected -->
          <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
              <div class="stat-header">
                <div class="stat-icon-wrapper stat-icon-green">
                  <i class="fas fa-wallet"></i>
                </div>
                <span class="stat-trend stat-trend-up">
                  <i class="fas fa-check-circle"></i> Verified
                </span>
              </div>
              <div class="stat-value">₹<?= number_format($totalFees, 2) ?></div>
              <p class="stat-label">Registration Fees Collected</p>
            </div>
          </div>

          <!-- Metric 3: Student Inquiries -->
          <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
              <div class="stat-header">
                <div class="stat-icon-wrapper stat-icon-blue">
                  <i class="fas fa-envelope-open-text"></i>
                </div>
                <span class="stat-trend stat-trend-up">
                  <i class="fas fa-bolt"></i> Live
                </span>
              </div>
              <div class="stat-value"><?= number_format($totalInquiries) ?></div>
              <p class="stat-label">Student Inquiries & Leads</p>
            </div>
          </div>

          <!-- Metric 4: Active Programs & Admins -->
          <div class="col-sm-6 col-xl-3">
            <div class="stat-card">
              <div class="stat-header">
                <div class="stat-icon-wrapper stat-icon-purple">
                  <i class="fas fa-user-shield"></i>
                </div>
                <span class="stat-trend stat-trend-up">
                  <i class="fas fa-shield-alt"></i> Active
                </span>
              </div>
              <div class="stat-value"><?= number_format($totalAdmins) ?></div>
              <p class="stat-label">Active Administrators</p>
            </div>
          </div>

        </div><!-- End row -->

        <!-- Main Dashboard Split (Table + Widgets) -->
        <div class="row g-4">
          
          <!-- Left: Recent Admissions Table (8 cols) -->
          <div class="col-xl-8">
            <div class="bento-card" id="admissionsSection">
              <div class="bento-header">
                <h3 class="bento-title">
                  <i class="fas fa-list-check text-warning"></i> Recent Student Admissions
                </h3>
                <a href="admissions.php" class="btn btn-outline-warning btn-sm" style="border-radius: 8px;">
                  <i class="fas fa-id-card me-1"></i> View All Registered Students
                </a>
              </div>

              <div class="table-responsive">
                <table class="admin-table">
                  <thead>
                    <tr>
                      <th>App ID</th>
                      <th>Student Name</th>
                      <th>Course Program</th>
                      <th>Fee Status</th>
                      <th>Admission</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($recentAdmissions)): ?>
                      <?php foreach ($recentAdmissions as $row): ?>
                        <tr>
                          <td>
                            <span class="app-id-badge"><?= htmlspecialchars($row['application_id']) ?></span>
                          </td>
                          <td>
                            <div class="fw-bold text-white"><?= htmlspecialchars($row['full_name']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($row['email']) ?></small>
                          </td>
                          <td>
                            <span class="text-white" style="font-size: 0.84rem;"><?= htmlspecialchars($row['course_name']) ?></span>
                          </td>
                          <td>
                            <span class="status-pill status-paid">
                              <i class="fas fa-check me-1"></i> ₹<?= number_format($row['fee_amount'], 0) ?>
                            </span>
                          </td>
                          <td>
                            <span class="status-pill status-<?= htmlspecialchars($row['admission_status']) ?>">
                              <?= htmlspecialchars(str_replace('_', ' ', $row['admission_status'])) ?>
                            </span>
                          </td>
                          <td>
                            <small class="text-muted"><?= date('d M, H:i', strtotime($row['created_at'])) ?></small>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                          <i class="fas fa-inbox fa-2x mb-2 d-block"></i> No admission records found yet in database.
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>

          <!-- Right: Widgets & Actions (4 cols) -->
          <div class="col-xl-4">
            
            <!-- Quick Actions Card -->
            <div class="bento-card mb-4">
              <div class="bento-header">
                <h3 class="bento-title">
                  <i class="fas fa-bolt text-warning"></i> Quick Actions
                </h3>
              </div>
              
              <a href="courses.php" class="quick-action-btn" style="border-color: rgba(230, 169, 16, 0.4);">
                <i class="fas fa-graduation-cap text-warning"></i>
                <span class="text-white">Manage & Add Courses</span>
              </a>

              <a href="categories.php" class="quick-action-btn">
                <i class="fas fa-folder-tree"></i>
                <span>Course Categories</span>
              </a>

              <a href="settings.php" class="quick-action-btn">
                <i class="fas fa-sliders-h text-warning"></i>
                <span>SMTP & Razorpay Settings</span>
              </a>

              <a href="../register.php" target="_blank" class="quick-action-btn">
                <i class="fas fa-user-plus"></i>
                <span>Open New Student Admission</span>
              </a>

              <a href="../explore-course.php" target="_blank" class="quick-action-btn">
                <i class="fas fa-graduation-cap"></i>
                <span>Browse Live Courses</span>
              </a>

              <a href="../index.php" target="_blank" class="quick-action-btn">
                <i class="fas fa-globe"></i>
                <span>Preview Student Website</span>
              </a>

              <a href="logout.php" class="quick-action-btn text-danger" style="border-color: rgba(239, 68, 68, 0.3);">
                <i class="fas fa-sign-out-alt text-danger"></i>
                <span>Log Out of Admin Console</span>
              </a>
            </div>

            <!-- Enrollment Breakdown by Department -->
            <div class="bento-card" id="feeSection">
              <div class="bento-header">
                <h3 class="bento-title">
                  <i class="fas fa-chart-pie text-warning"></i> Program Demand
                </h3>
              </div>

              <!-- Item 1 -->
              <div class="prog-item">
                <div class="prog-header">
                  <span class="text-white">Technology & Cloud IT</span>
                  <span class="text-warning fw-bold">42%</span>
                </div>
                <div class="prog-track">
                  <div class="prog-bar bg-warning" style="width: 42%;"></div>
                </div>
              </div>

              <!-- Item 2 -->
              <div class="prog-item">
                <div class="prog-header">
                  <span class="text-white">Aviation & Airport Ops</span>
                  <span class="text-info fw-bold">28%</span>
                </div>
                <div class="prog-track">
                  <div class="prog-bar bg-info" style="width: 28%;"></div>
                </div>
              </div>

              <!-- Item 3 -->
              <div class="prog-item">
                <div class="prog-header">
                  <span class="text-white">Banking & Financial Tech</span>
                  <span class="text-success fw-bold">18%</span>
                </div>
                <div class="prog-track">
                  <div class="prog-bar bg-success" style="width: 18%;"></div>
                </div>
              </div>

              <!-- Item 4 -->
              <div class="prog-item">
                <div class="prog-header">
                  <span class="text-white">Hospitality & Tourism</span>
                  <span class="text-primary fw-bold">12%</span>
                </div>
                <div class="prog-track">
                  <div class="prog-bar bg-primary" style="width: 12%;"></div>
                </div>
              </div>

            </div>

          </div>

        </div><!-- End row -->

      </main>

      <!-- Admin Footer Component -->
      <?php require_once __DIR__ . '/includes/footer.php'; ?>
