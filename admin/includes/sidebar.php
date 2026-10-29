<?php
// Admin Sidebar Component - Modern Executive SaaS Navigation
$currentPage = basename($_SERVER['PHP_SELF']);

// Fetch live sidebar badge counts if $pdo is available
$sbAdmCount = 0;
$sbCourseCount = 0;
$sbEnrCount = 0;
$sbCatCount = 0;

if (isset($pdo)) {
    try {
        $sbAdmCount    = (int)($pdo->query("SELECT COUNT(*) FROM admissions")->fetchColumn() ?: 0);
        $sbCourseCount = (int)($pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active'")->fetchColumn() ?: 0);
        $sbEnrCount    = (int)($pdo->query("SELECT COUNT(*) FROM course_enrollments")->fetchColumn() ?: 0);
        $sbCatCount    = (int)($pdo->query("SELECT COUNT(*) FROM course_categories")->fetchColumn() ?: 0);
    } catch (Exception $e) {
        // Fallback silently
    }
}
?>
<aside class="admin-sidebar" id="adminSidebar">
  <!-- Brand Header -->
  <div class="sidebar-brand-box">
    <a href="index.php" class="brand-link">
      <div class="brand-logo-wrap">
        <img src="../assets/images/logo/logopng.png" alt="Skill Grow Logo" class="brand-logo-img">
      </div>
      <div class="brand-text">
        <span class="brand-name">Skill Grow</span>
        <span class="brand-sub">Executive Console</span>
      </div>
    </a>
    <!-- Close button for mobile drawer -->
    <button type="button" class="btn-close-sidebar d-lg-none" id="sidebarCloseBtn" aria-label="Close sidebar">
      <i class="fas fa-times"></i>
    </button>
  </div>

  <!-- Navigation Menu -->
  <div class="sidebar-nav-container">
    <div class="nav-section-title">CORE OVERVIEW</div>
    <ul class="sidebar-menu">
      <li class="menu-item <?= ($currentPage === 'index.php') ? 'active' : '' ?>">
        <a href="index.php" class="menu-link" title="Dashboard & Analytics">
          <span class="menu-icon-box"><i class="fas fa-chart-pie menu-icon"></i></span>
          <span class="menu-text">Analytics Dashboard</span>
        </a>
      </li>

      <li class="menu-item <?= ($currentPage === 'admissions.php') ? 'active' : '' ?>">
        <a href="admissions.php" class="menu-link" title="Registered Students">
          <span class="menu-icon-box"><i class="fas fa-user-graduate menu-icon"></i></span>
          <span class="menu-text">Student Admissions</span>
          <?php if ($sbAdmCount > 0): ?>
            <span class="menu-badge"><?= $sbAdmCount ?></span>
          <?php endif; ?>
        </a>
      </li>

      <li class="menu-item <?= ($currentPage === 'enrollments.php') ? 'active' : '' ?>">
        <a href="enrollments.php" class="menu-link" title="Course Enrollments">
          <span class="menu-icon-box"><i class="fas fa-file-invoice-dollar menu-icon"></i></span>
          <span class="menu-text">Course Enrollments</span>
          <?php if ($sbEnrCount > 0): ?>
            <span class="menu-badge"><?= $sbEnrCount ?></span>
          <?php endif; ?>
        </a>
      </li>
    </ul>

    <div class="nav-section-title">ACADEMIC CATALOG</div>
    <ul class="sidebar-menu">
      <li class="menu-item <?= in_array($currentPage, ['courses.php', 'course-add.php', 'course-edit.php']) ? 'active' : '' ?>">
        <a href="courses.php" class="menu-link" title="Manage Courses">
          <span class="menu-icon-box"><i class="fas fa-layer-group menu-icon"></i></span>
          <span class="menu-text">Course Programs</span>
          <?php if ($sbCourseCount > 0): ?>
            <span class="menu-badge"><?= $sbCourseCount ?></span>
          <?php endif; ?>
        </a>
      </li>

      <li class="menu-item <?= ($currentPage === 'course-add.php') ? 'active' : '' ?>">
        <a href="course-add.php" class="menu-link" title="Create New Course">
          <span class="menu-icon-box"><i class="fas fa-plus-circle menu-icon"></i></span>
          <span class="menu-text">Add New Course</span>
        </a>
      </li>

      <li class="menu-item <?= ($currentPage === 'categories.php') ? 'active' : '' ?>">
        <a href="categories.php" class="menu-link" title="Course Categories">
          <span class="menu-icon-box"><i class="fas fa-folder-tree menu-icon"></i></span>
          <span class="menu-text">Course Categories</span>
          <?php if ($sbCatCount > 0): ?>
            <span class="menu-badge"><?= $sbCatCount ?></span>
          <?php endif; ?>
        </a>
      </li>
    </ul>

    <div class="nav-section-title">SYSTEM & GATEWAYS</div>
    <ul class="sidebar-menu">
      <li class="menu-item <?= ($currentPage === 'settings.php') ? 'active' : '' ?>">
        <a href="settings.php" class="menu-link" title="SMTP & Razorpay Setup">
          <span class="menu-icon-box"><i class="fas fa-sliders-h menu-icon"></i></span>
          <span class="menu-text">SMTP & Razorpay</span>
        </a>
      </li>

      <li class="menu-item">
        <a href="../index.php" target="_blank" class="menu-link" title="Visit Main Website">
          <span class="menu-icon-box"><i class="fas fa-external-link-alt menu-icon"></i></span>
          <span class="menu-text">Visit Live Portal</span>
        </a>
      </li>
    </ul>
  </div>

  <!-- Live Gateway Status Mini Box -->
  <div class="sidebar-status-card">
    <div class="sidebar-status-header">
      <span><span class="status-dot-live me-1"></span> Razorpay & SMTP</span>
      <a href="settings.php" class="text-warning text-decoration-none" style="font-size: 0.7rem;">Configure</a>
    </div>
    <div class="text-muted" style="font-size: 0.71rem; line-height: 1.35;">
      Portal database & automated receipt engine active.
    </div>
  </div>

  <!-- Bottom Admin Quick Card -->
  <div class="sidebar-user-footer">
    <div class="user-info-box">
      <div class="user-avatar">
        <?= strtoupper(substr($_SESSION['admin_name'] ?? 'S', 0, 1)) ?>
      </div>
      <div class="user-details">
        <div class="user-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Super Admin') ?></div>
        <div class="user-role"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $_SESSION['admin_role'] ?? 'super_admin'))) ?></div>
      </div>
      <a href="logout.php" class="btn-sidebar-logout" title="Sign Out">
        <i class="fas fa-power-off"></i>
      </a>
    </div>
  </div>
</aside>
