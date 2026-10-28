<?php
// Admin Topbar Header Component
?>
<header class="admin-topbar">
  <div class="topbar-left">
    <!-- Sidebar Toggle Button (With Animated Icon) -->
    <button type="button" class="btn-toggle-sidebar" id="sidebarToggle" aria-label="Toggle Sidebar Navigation">
      <i class="fas fa-bars-staggered"></i>
    </button>

    <!-- Breadcrumb & Title -->
    <div class="header-page-title d-none d-sm-block">
      <h1 class="page-title-text"><?= $pageTitle ?? 'Dashboard' ?></h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= $pageTitle ?? 'Dashboard' ?></li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="topbar-right">
    
    <!-- Live Time Badge -->
    <div class="live-clock-badge d-none d-md-flex align-items-center gap-2">
      <i class="far fa-clock text-warning"></i>
      <span id="adminLiveClock"><?= date('d M Y, h:i A') ?></span>
    </div>

    <!-- Quick Website Link -->
    <a href="../index.php" target="_blank" class="btn-topbar-action d-none d-sm-flex" title="Open Main Website in New Tab">
      <i class="fas fa-external-link-alt"></i>
      <span class="d-none d-lg-inline">Live Site</span>
    </a>

    <!-- Notifications Icon with Ping -->
    <div class="dropdown">
      <button class="btn-topbar-action position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
        <i class="far fa-bell"></i>
        <span class="notification-ping"></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end admin-dropdown-menu notification-menu">
        <li class="dropdown-header">
          <h6 class="mb-0 text-white font-weight-bold">Recent System Alerts</h6>
          <small class="text-muted">Database & Applications</small>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item notification-item" href="#admissionsSection">
            <div class="notification-icon bg-warning text-dark">
              <i class="fas fa-user-plus"></i>
            </div>
            <div>
              <div class="notification-title">New Admission Verified</div>
              <small class="text-muted">SGI-2026-88412 • 2 hrs ago</small>
            </div>
          </a>
        </li>
        <li>
          <a class="dropdown-item notification-item" href="#feeSection">
            <div class="notification-icon bg-success text-white">
              <i class="fas fa-check-double"></i>
            </div>
            <div>
              <div class="notification-title">Payment Confirmed: ₹999</div>
              <small class="text-muted">UPI Transaction Verified</small>
            </div>
          </a>
        </li>
      </ul>
    </div>

    <!-- Admin Profile Dropdown -->
    <div class="dropdown">
      <button class="btn-profile-pill" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="profile-avatar-circle">
          <i class="fas fa-user-shield"></i>
        </div>
        <div class="profile-meta d-none d-md-block text-start">
          <div class="profile-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Super Admin') ?></div>
          <small class="profile-role">Administrator</small>
        </div>
        <i class="fas fa-chevron-down ms-1 text-muted" style="font-size: 0.75rem;"></i>
      </button>

      <ul class="dropdown-menu dropdown-menu-end admin-dropdown-menu">
        <li class="dropdown-header">
          <div class="fw-bold text-white"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Super Admin') ?></div>
          <small class="text-muted"><?= htmlspecialchars($_SESSION['admin_email'] ?? 'admin@skillgrowinstitution.in') ?></small>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item" href="settings.php">
            <i class="fas fa-sliders-h me-2 text-warning"></i> SMTP & Razorpay Settings
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="#adminProfileSection">
            <i class="fas fa-id-badge me-2 text-warning"></i> My Admin Profile
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="../explore-course.php" target="_blank">
            <i class="fas fa-graduation-cap me-2 text-warning"></i> View Courses
          </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item text-danger" href="logout.php">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
          </a>
        </li>
      </ul>
    </div>

  </div>
</header>
