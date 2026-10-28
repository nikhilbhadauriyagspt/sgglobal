<?php
// Admin Sidebar Component - Clean Executive Navigation
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar" id="adminSidebar">
  <!-- Brand Header -->
  <div class="sidebar-brand-box">
    <a href="index.php" class="brand-link">
      <img src="../assets/images/logo/logopng.png" alt="Skill Grow Logo" class="brand-logo-img">
      <div class="brand-text">
        <span class="brand-name">Skill Grow</span>
        <span class="brand-sub">Admin Console</span>
      </div>
    </a>
    <!-- Close button for mobile drawer -->
    <button type="button" class="btn-close-sidebar d-lg-none" id="sidebarCloseBtn" aria-label="Close sidebar">
      <i class="fas fa-times"></i>
    </button>
  </div>

  <!-- Navigation Menu -->
  <div class="sidebar-nav-container">
    <div class="nav-section-title">ADMIN NAVIGATION</div>
    <ul class="sidebar-menu">
      <li class="menu-item <?= ($currentPage === 'index.php') ? 'active' : '' ?>">
        <a href="index.php" class="menu-link">
          <i class="fas fa-th-large menu-icon"></i>
          <span class="menu-text">Dashboard</span>
        </a>
      </li>

      <li class="menu-item <?= ($currentPage === 'admissions.php') ? 'active' : '' ?>">
        <a href="admissions.php" class="menu-link">
          <i class="fas fa-id-card menu-icon"></i>
          <span class="menu-text">Registered Students</span>
        </a>
      </li>

      <li class="menu-item <?= in_array($currentPage, ['courses.php', 'course-add.php', 'course-edit.php']) ? 'active' : '' ?>">
        <a href="courses.php" class="menu-link">
          <i class="fas fa-graduation-cap menu-icon"></i>
          <span class="menu-text">Manage Courses</span>
        </a>
      </li>

      <li class="menu-item <?= ($currentPage === 'enrollments.php') ? 'active' : '' ?>">
        <a href="enrollments.php" class="menu-link">
          <i class="fas fa-user-check menu-icon"></i>
          <span class="menu-text">Course Enrollments</span>
        </a>
      </li>

      <li class="menu-item <?= ($currentPage === 'categories.php') ? 'active' : '' ?>">
        <a href="categories.php" class="menu-link">
          <i class="fas fa-folder-tree menu-icon"></i>
          <span class="menu-text">Course Categories</span>
        </a>
      </li>

      <li class="menu-item <?= ($currentPage === 'settings.php') ? 'active' : '' ?>">
        <a href="settings.php" class="menu-link">
          <i class="fas fa-sliders-h menu-icon"></i>
          <span class="menu-text">SMTP & Razorpay</span>
        </a>
      </li>
    </ul>
  </div>

  <!-- Bottom Admin Quick Card -->
  <div class="sidebar-user-footer">
    <div class="user-info-box">
      <div class="user-avatar">
        <i class="fas fa-user-shield"></i>
      </div>
      <div class="user-details">
        <div class="user-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Super Admin') ?></div>
        <div class="user-role"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $_SESSION['admin_role'] ?? 'super_admin'))) ?></div>
      </div>
      <a href="logout.php" class="btn-sidebar-logout" title="Logout">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </div>
  </div>
</aside>
