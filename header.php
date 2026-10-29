<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connect to DB if not already connected so we can sync student profile & dynamic courses
if (!isset($pdo)) {
    $dbPath = __DIR__ . '/admin/config/db.php';
    if (file_exists($dbPath)) {
        require_once $dbPath;
    }
}

$isStudentLoggedIn = !empty($_SESSION['student_logged_in']);
$hdrStudentId      = $_SESSION['student_id'] ?? '';
$hdrStudentName    = $_SESSION['student_name'] ?? 'Student';
$hdrStudentEmail   = $_SESSION['student_email'] ?? '';
$hdrStudentAvatar  = $_SESSION['student_avatar'] ?? '';
$hdrStudentCourse  = $_SESSION['student_course'] ?? '';

// Sync fresh student info from DB if logged in
if ($isStudentLoggedIn && isset($pdo) && (!empty($hdrStudentId) || !empty($hdrStudentEmail))) {
    try {
        $stmtHdrStu = $pdo->prepare("SELECT * FROM students WHERE student_id = :sid OR email = :em LIMIT 1");
        $stmtHdrStu->execute(['sid' => $hdrStudentId, 'em' => $hdrStudentEmail]);
        $dbStudent = $stmtHdrStu->fetch();
        if ($dbStudent) {
            $hdrStudentId     = $dbStudent['student_id'];
            $hdrStudentName   = $dbStudent['full_name'];
            $hdrStudentEmail  = $dbStudent['email'];
            $hdrStudentAvatar = $dbStudent['avatar'] ?? '';
            $hdrStudentCourse = $dbStudent['course_name'] ?? '';
            $_SESSION['student_id']     = $hdrStudentId;
            $_SESSION['student_name']   = $hdrStudentName;
            $_SESSION['student_email']  = $hdrStudentEmail;
            $_SESSION['student_avatar'] = $hdrStudentAvatar;
            $_SESSION['student_course'] = $hdrStudentCourse;
        }
    } catch (Exception $e) {
        // Fallback to session values
    }
}

$currentScript = basename($_SERVER['PHP_SELF']);
?>
<style>
  /* ============================================================
     UNIFIED GLOBAL FRONTEND HEADER (HOME PAGE STYLE EVERYWHERE)
  ============================================================ */
  .topbar {
    background: #002365 !important;
    color: #ffffff !important;
    padding: 9px 0 !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    z-index: 1035 !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    font-family: 'Plus Jakarta Sans', 'Roboto', sans-serif;
  }

  .topbar-contact {
    display: flex;
    align-items: center;
    gap: 20px;
    font-size: 0.86rem;
  }

  .topbar-contact a,
  .topbar-right a {
    color: #f1f5f9 !important;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-weight: 500;
    transition: color 0.2s ease;
  }

  .topbar-contact a:hover,
  .topbar-right a:hover {
    color: #e6a910 !important;
  }

  .topbar-contact i,
  .topbar-right i {
    color: #e6a910 !important;
    font-size: 0.92rem;
  }

  .topbar-right {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 22px;
    font-size: 0.86rem;
  }

  /* Main Navbar - Home Page Glassmorphism Style */
  .navbar {
    background: rgba(255, 255, 255, 0.45) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    position: fixed !important;
    top: 41px !important;
    left: 0 !important;
    width: 100% !important;
    z-index: 1030 !important;
    padding: 4px 0 !important;
    transition: all 0.3s ease !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
  }

  .navbar.scrolled {
    background: #ffffff !important;
    box-shadow: 0 6px 24px rgba(0, 35, 101, 0.1) !important;
    border-bottom: 1px solid rgba(0, 35, 101, 0.08) !important;
  }

  .navbar .navbar-brand img {
    height: 72px !important;
    width: auto !important;
    object-fit: contain;
    transition: height 0.25s ease;
  }

  .navbar.scrolled .navbar-brand img {
    height: 64px !important;
  }

  .navbar-nav .nav-link {
    color: #ffffff !important;
    font-weight: 700 !important;
    line-height: 18px !important;
    padding: 26px 20px !important;
    font-size: 0.98rem !important;
    transition: color 0.25s ease !important;
    font-family: 'Roboto Slab', serif !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
  }

  .navbar.scrolled .navbar-nav .nav-link {
    color: #002365 !important;
    text-shadow: none !important;
  }

  .navbar-nav .nav-link:hover,
  .navbar-nav .nav-link.active,
  .navbar.scrolled .navbar-nav .nav-link:hover,
  .navbar.scrolled .navbar-nav .nav-link.active {
    color: #e6a910 !important;
  }

  /* ============================================================
     POLISHED COURSES MEGA DROPDOWN
  ============================================================ */
  .navbar-nav .sg-courses-dropdown {
    background: #09122c !important;
    border: 1px solid #1e2f5d !important;
    border-radius: 16px !important;
    padding: 22px !important;
    min-width: 820px !important;
    left: -220px !important;
    margin-top: 0 !important;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45) !important;
  }

  .sg-courses-dropdown .dropdown-header {
    color: #e6a910 !important;
    font-family: 'Roboto Slab', serif;
    font-size: 0.82rem !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 6px 12px 8px !important;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .sg-courses-dropdown .dropdown-item {
    color: #cbd5e1 !important;
    font-family: 'Plus Jakarta Sans', 'Roboto', sans-serif;
    font-size: 0.87rem !important;
    font-weight: 500 !important;
    padding: 8px 12px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .sg-courses-dropdown .dropdown-item:hover {
    background: rgba(230, 169, 16, 0.14) !important;
    color: #fbbf24 !important;
    transform: translateX(3px);
  }

  .sg-courses-dropdown .dropdown-divider {
    border-color: rgba(255, 255, 255, 0.08) !important;
    margin: 10px 0 !important;
  }

  .sg-mega-footer {
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
  }

  /* ============================================================
     AUTH BUTTONS & EXECUTIVE STUDENT PROFILE DROPDOWN
  ============================================================ */
  .header-auth-btns {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .btn-nav-login {
    background: rgba(7, 14, 36, 0.55);
    color: #ffffff !important;
    border: 1.5px solid rgba(255, 255, 255, 0.45);
    font-family: 'Roboto Slab', serif;
    font-weight: 600;
    font-size: 0.88rem;
    padding: 8px 18px;
    border-radius: 50px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.25s ease;
  }

  .navbar.scrolled .btn-nav-login {
    color: #002365 !important;
    border-color: #002365;
    background: transparent;
  }

  .btn-nav-login:hover {
    background: #002365;
    color: #ffffff !important;
    border-color: #002365;
  }

  .navbar.scrolled .btn-nav-login:hover {
    background: #002365;
    color: #ffffff !important;
  }

  .btn-nav-register {
    background: linear-gradient(135deg, #f59e0b 0%, #e6a910 100%);
    color: #070e24 !important;
    border: 1.5px solid #e6a910;
    font-family: 'Roboto Slab', serif;
    font-weight: 700;
    font-size: 0.88rem;
    padding: 8px 20px;
    border-radius: 50px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.25s ease;
  }

  .btn-nav-register:hover {
    background: #fbbf24;
    border-color: #fbbf24;
    color: #070e24 !important;
    transform: translateY(-1px);
  }

  /* Logged-In Student Trigger Button */
  .btn-student-avatar {
    background: #09132e !important;
    border: 1.5px solid #e6a910 !important;
    color: #ffffff !important;
    padding: 5px 14px 5px 6px !important;
    border-radius: 50px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 9px !important;
    font-size: 0.86rem !important;
    font-weight: 600 !important;
    transition: all 0.2s ease !important;
  }

  .btn-student-avatar:hover,
  .btn-student-avatar:focus {
    background: #102048 !important;
    border-color: #fbbf24 !important;
    color: #ffffff !important;
  }

  .student-avatar-circle {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f59e0b 0%, #e6a910 100%);
    color: #070e24;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.88rem;
    font-weight: 800;
    overflow: hidden;
    flex-shrink: 0;
  }

  .student-avatar-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* Modern Student Profile Dropdown Menu */
  .student-dropdown-menu {
    background: #09132e !important;
    border: 1px solid #1e3163 !important;
    border-radius: 16px !important;
    padding: 12px !important;
    min-width: 285px !important;
    margin-top: 12px !important;
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45) !important;
  }

  .stu-drop-profile-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 12px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .stu-drop-avatar-lg {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #070e24;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    font-weight: 800;
    flex-shrink: 0;
    overflow: hidden;
  }

  .stu-drop-avatar-lg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .stu-drop-info {
    overflow: hidden;
    flex: 1;
  }

  .stu-drop-name {
    color: #ffffff;
    font-weight: 700;
    font-size: 0.92rem;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .stu-drop-email {
    color: #94a3b8;
    font-size: 0.75rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .stu-drop-id-pill {
    display: inline-block;
    margin-top: 4px;
    font-family: monospace;
    font-size: 0.72rem;
    font-weight: 700;
    color: #fbbf24;
    background: rgba(245, 158, 11, 0.14);
    border: 1px solid rgba(245, 158, 11, 0.3);
    padding: 1px 8px;
    border-radius: 6px;
  }

  .student-dropdown-menu .dropdown-item {
    color: #cbd5e1 !important;
    font-family: 'Plus Jakarta Sans', 'Roboto', sans-serif;
    font-size: 0.85rem !important;
    font-weight: 600 !important;
    padding: 9px 12px !important;
    border-radius: 10px !important;
    display: flex !important;
    align-items: center !important;
    gap: 11px !important;
    transition: all 0.2s ease !important;
    margin-bottom: 2px;
  }

  .student-dropdown-menu .dropdown-item i.drop-ico {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    flex-shrink: 0;
  }

  .student-dropdown-menu .dropdown-item:hover {
    background: rgba(245, 158, 11, 0.14) !important;
    color: #ffffff !important;
  }

  .student-dropdown-menu .dropdown-item.text-danger-item {
    color: #f87171 !important;
  }

  .student-dropdown-menu .dropdown-item.text-danger-item:hover {
    background: rgba(239, 68, 68, 0.15) !important;
    color: #fca5a5 !important;
  }

  .student-dropdown-menu .dropdown-divider {
    border-color: rgba(255, 255, 255, 0.08) !important;
    margin: 8px 0 !important;
  }

  /* Responsive Mobile Adjustments */
  @media (max-width: 991.98px) {
    .navbar {
      top: 38px !important;
      background: rgba(7, 14, 36, 0.95) !important;
      padding: 8px 0 !important;
    }
    .navbar .navbar-brand img {
      height: 52px !important;
    }
    .navbar-toggler {
      border-color: rgba(255, 255, 255, 0.3) !important;
    }
    .navbar-toggler-icon {
      filter: invert(1);
    }
    .navbar.scrolled .navbar-toggler-icon {
      filter: none;
    }
    .navbar-collapse {
      background: #09132e !important;
      border: 1px solid #1e3163;
      border-radius: 14px;
      padding: 16px;
      margin-top: 10px;
      max-height: 82vh;
      overflow-y: auto;
      height: auto !important;
    }
    .navbar-nav .nav-link,
    .navbar.scrolled .navbar-nav .nav-link {
      color: #ffffff !important;
      padding: 12px 10px !important;
    }
    .navbar-nav .sg-courses-dropdown {
      min-width: 100% !important;
      left: 0 !important;
      padding: 14px !important;
    }
    .header-auth-btns {
      margin-top: 12px;
      padding-top: 12px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
  }
</style>

<!-- Topbar -->
<div class="topbar">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6 col-7">
        <div class="topbar-contact">
          <a href="tel:+919217431562"><i class="fas fa-phone"></i> <span>+91 9217431562</span></a>
          <a href="mailto:info@skillgrowinstitution.in" class="d-none d-sm-inline-flex"><i class="fas fa-envelope"></i> <span>info@skillgrowinstitution.in</span></a>
        </div>
      </div>
      <div class="col-md-6 col-5">
        <div class="topbar-right">
          <div class="topbar-chat d-none d-sm-block">
            <a href="https://wa.me/919217431562" target="_blank">
              <i class="fas fa-comment-dots"></i> Live Chat
            </a>
          </div>
          <div class="topbar-login">
            <?php if ($isStudentLoggedIn): ?>
              <a href="my-enrollments.php">
                <i class="fas fa-user-check"></i> <?= htmlspecialchars($hdrStudentName) ?> (<?= htmlspecialchars($hdrStudentId ?: 'Portal') ?>)
              </a>
            <?php else: ?>
              <a href="login.php"><i class="fas fa-user-lock"></i> Student Portal</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="assets/images/logo/logopng.png" alt="Skill Grow Institution Logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link <?= ($currentScript === 'index.php') ? 'active' : '' ?>" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentScript === 'aboutus.php') ? 'active' : '' ?>" href="aboutus.php">About</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= in_array($currentScript, ['explore-course.php', 'course-detail.php', 'enrollnow.php']) ? 'active' : '' ?>" href="#" id="coursesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Courses
          </a>
          <div class="dropdown-menu sg-courses-dropdown" aria-labelledby="coursesDropdown">
            <div class="row g-3">
              <div class="col-lg-4">
                <div class="dropdown-header"><i class="fas fa-briefcase"></i> Business & Finance</div>
                <a class="dropdown-item" href="course-detail.php?course=business-studies"><span>Business Studies</span> <i class="fas fa-angle-right small opacity-50"></i></a>
                <a class="dropdown-item" href="course-detail.php?course=management-studies"><span>Advanced Management</span> <i class="fas fa-angle-right small opacity-50"></i></a>
                <a class="dropdown-item" href="course-detail.php?course=banking-and-finance"><span>Banking & Financial Markets</span> <i class="fas fa-angle-right small opacity-50"></i></a>
                <hr class="dropdown-divider">
                <div class="dropdown-header"><i class="fas fa-plane-departure"></i> Aviation & Hospitality</div>
                <a class="dropdown-item" href="course-detail.php?course=airport-management"><span>Aviation & Airport Ops</span> <i class="fas fa-angle-right small opacity-50"></i></a>
                <a class="dropdown-item" href="explore-course.php"><span>Tourism & Hospitality</span> <i class="fas fa-angle-right small opacity-50"></i></a>
              </div>

              <div class="col-lg-4">
                <div class="dropdown-header"><i class="fas fa-laptop-code"></i> Technology & IT</div>
                <a class="dropdown-item" href="course-detail.php?course=web-technology"><span>Full Stack Web Engineering</span> <i class="fas fa-angle-right small opacity-50"></i></a>
                <a class="dropdown-item" href="explore-course.php"><span>Python & Data Science</span> <i class="fas fa-angle-right small opacity-50"></i></a>
                <a class="dropdown-item" href="explore-course.php"><span>AI & Cloud Computing</span> <i class="fas fa-angle-right small opacity-50"></i></a>
                <a class="dropdown-item" href="explore-course.php"><span>IT Automation & Support</span> <i class="fas fa-angle-right small opacity-50"></i></a>
              </div>

              <div class="col-lg-4">
                <div class="dropdown-header"><i class="fas fa-bullhorn"></i> Growth & Creative</div>
                <a class="dropdown-item" href="course-detail.php?course=digital-marketing-strategy"><span>Digital Marketing & Growth</span> <i class="fas fa-angle-right small opacity-50"></i></a>
                <a class="dropdown-item" href="explore-course.php"><span>UI/UX & Product Design</span> <i class="fas fa-angle-right small opacity-50"></i></a>
                <a class="dropdown-item" href="explore-course.php"><span>E-Commerce & Branding</span> <i class="fas fa-angle-right small opacity-50"></i></a>
              </div>
            </div>

            <div class="sg-mega-footer">
              <span class="text-light small opacity-75"><i class="fas fa-certificate text-warning me-1"></i> Industry-Accredited Certification Programs</span>
              <div class="d-flex gap-2">
                <a href="explore-course.php" class="btn btn-sm btn-outline-warning rounded-pill px-3">View All Courses</a>
                <a href="enrollnow.php" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3">Direct Enrollment</a>
              </div>
            </div>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentScript === 'blogs.php') ? 'active' : '' ?>" href="blogs.php">Blogs</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentScript === 'certificate.php') ? 'active' : '' ?>" href="certificate.php">Certificate</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= in_array($currentScript, ['career.php', 'government-jobs.php', 'private-jobs.php']) ? 'active' : '' ?>" href="career.php">Career</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentScript === 'contact.php') ? 'active' : '' ?>" href="contact.php">Contact</a>
        </li>
      </ul>

      <?php if ($isStudentLoggedIn): ?>
        <div class="dropdown ms-lg-3 header-auth-btns">
          <button class="btn btn-student-avatar dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="student-avatar-circle">
              <?php if (!empty($hdrStudentAvatar) && file_exists(__DIR__ . '/' . $hdrStudentAvatar)): ?>
                <img src="<?= htmlspecialchars($hdrStudentAvatar) ?>" alt="Avatar">
              <?php else: ?>
                <?= strtoupper(substr($hdrStudentName, 0, 1)) ?>
              <?php endif; ?>
            </span>
            <span class="student-avatar-name"><?= htmlspecialchars($hdrStudentName) ?></span>
          </button>

          <div class="dropdown-menu dropdown-menu-end student-dropdown-menu">
            <!-- Student Mini Profile Header -->
            <div class="stu-drop-profile-card">
              <div class="stu-drop-avatar-lg">
                <?php if (!empty($hdrStudentAvatar) && file_exists(__DIR__ . '/' . $hdrStudentAvatar)): ?>
                  <img src="<?= htmlspecialchars($hdrStudentAvatar) ?>" alt="Avatar">
                <?php else: ?>
                  <?= strtoupper(substr($hdrStudentName, 0, 1)) ?>
                <?php endif; ?>
              </div>
              <div class="stu-drop-info">
                <div class="stu-drop-name"><?= htmlspecialchars($hdrStudentName) ?></div>
                <div class="stu-drop-email"><?= htmlspecialchars($hdrStudentEmail) ?></div>
                <?php if (!empty($hdrStudentId)): ?>
                  <span class="stu-drop-id-pill"><i class="fas fa-id-badge me-1"></i><?= htmlspecialchars($hdrStudentId) ?></span>
                <?php endif; ?>
              </div>
            </div>

            <a class="dropdown-item" href="my-enrollments.php">
              <i class="fas fa-user-pen drop-ico text-warning"></i>
              <div>
                <div>My Profile & Edit Details</div>
                <small class="text-secondary d-block" style="font-size: 0.7rem;">Personal info, password & photo</small>
              </div>
            </a>

            <a class="dropdown-item" href="my-enrollments.php#enrolledCoursesSection">
              <i class="fas fa-graduation-cap drop-ico text-info"></i>
              <div>
                <div>My Enrolled Courses</div>
                <small class="text-secondary d-block" style="font-size: 0.7rem;">Active batches & fee receipts</small>
              </div>
            </a>

            <a class="dropdown-item" href="enrollnow.php">
              <i class="fas fa-plus-circle drop-ico text-success"></i>
              <div>
                <div>Enroll in New Course</div>
                <small class="text-secondary d-block" style="font-size: 0.7rem;">Join a new batch program</small>
              </div>
            </a>

            <a class="dropdown-item" href="download-receipt.php">
              <i class="fas fa-file-invoice drop-ico text-primary"></i>
              <div>
                <div>Admission Slip & Receipt</div>
                <small class="text-secondary d-block" style="font-size: 0.7rem;">Download A4 admission slip</small>
              </div>
            </a>

            <hr class="dropdown-divider">

            <a class="dropdown-item text-danger-item" href="student-logout.php">
              <i class="fas fa-power-off drop-ico text-danger"></i>
              <span>Sign Out of Portal</span>
            </a>
          </div>
        </div>
      <?php else: ?>
        <div class="ms-lg-3 header-auth-btns">
          <a href="login.php" class="btn-nav-login">
            <i class="fas fa-arrow-right-to-bracket"></i> Login
          </a>
          <a href="register.php" class="btn-nav-register">
            <i class="fas fa-user-plus"></i> Register
          </a>
        </div>
      <?php endif; ?>

    </div>
  </div>
</nav>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const navEl = document.querySelector('.navbar');
    function handleNavScroll() {
      if (!navEl) return;
      if ((window.pageYOffset || document.documentElement.scrollTop) > 40) {
        navEl.classList.add('scrolled');
      } else {
        navEl.classList.remove('scrolled');
      }
    }
    window.addEventListener('scroll', handleNavScroll);
    handleNavScroll();
  });
</script>
