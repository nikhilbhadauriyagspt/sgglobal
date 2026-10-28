<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
  <style>
    .header-auth-btns {
      display: flex;
      align-items: center;
    }
    .btn-student-avatar {
      background: #0b1736;
      border: 1.5px solid #e6a910;
      color: #ffffff;
      padding: 5px 14px 5px 6px;
      border-radius: 50px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.88rem;
      font-weight: 600;
      box-shadow: none !important;
    }
    .btn-student-avatar:hover, .btn-student-avatar:focus {
      background: #11204a;
      border-color: #fbbf24;
      color: #ffffff;
    }
    .student-avatar-circle {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: #e6a910;
      color: #070e24;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.85rem;
    }
    .student-dropdown-menu {
      border-radius: 12px;
      border: 1px solid #1a274e;
      padding: 10px;
      min-width: 230px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .btn-nav-login {
      background: rgba(255, 255, 255, 0.08);
      color: #ffffff;
      border: 1.5px solid rgba(255, 255, 255, 0.35);
      font-family: 'Roboto Slab', serif;
      font-weight: 600;
      font-size: 0.88rem;
      padding: 7px 16px;
      border-radius: 50px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      transition: all 0.25s ease;
    }
    .navbar.scrolled .btn-nav-login {
      color: var(--secondary-color);
      border-color: var(--secondary-color);
      background: transparent;
    }
    .btn-nav-login:hover {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
      border-color: #ffffff;
    }
    .navbar.scrolled .btn-nav-login:hover {
      background: var(--secondary-color);
      color: #ffffff;
    }
    .btn-nav-register {
      background: var(--primary-color);
      color: #002365 !important;
      border: 1.5px solid var(--primary-color);
      font-family: 'Roboto Slab', serif;
      font-weight: 700;
      font-size: 0.88rem;
      padding: 7px 18px;
      border-radius: 50px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      transition: all 0.25s ease;
    }
    .btn-nav-register:hover {
      background: #d89b00;
      border-color: #d89b00;
      color: #002365 !important;
      transform: translateY(-1px);
    }
    @media (max-width: 991px) {
      .header-auth-btns {
        margin-top: 15px;
        padding-bottom: 10px;
      }
    }
  </style>
  <!-- Topbar -->
  <div class="topbar">
      <div class="container">
          <div class="row">
              <div class="col-md-6">
                  <div class="topbar-contact">
                      <a href="tel:+919217431562"><i class="fas fa-phone"></i> +91 9217431562</a>
                      <a href="mailto:info@skillgrowinstitution.in"><i class="fas fa-envelope"></i> info@skillgrowinstitution.in</a>
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="topbar-right">

                      <div class="topbar-chat">
                          <a href="https://wa.me/919217431562" target="_blank">
                              <i class="fas fa-comment-dots"></i> Live Chat </a>
                      </div>
                      <div class="topbar-login">
                          <a href="login.php"><i class="fas fa-user-lock"></i> Student Portal</a>
                      </div>
                  </div>
              </div>
          </div>

      </div>
  </div>

  <!-- Navbar -->
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
              <ul class="navbar-nav">
                  <li class="nav-item">
                      <a class="nav-link active" href="index.php">Home</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="aboutus.php">About</a>
                  </li>
                  <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="coursesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          Courses
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="coursesDropdown">
                          <!-- Business & Management -->
                          <div class="row">
                              <div class="col-md-4">
                                  <li class="dropdown-header">Business & Management</li>
                                  <li><a class="dropdown-item" href="explore-course.php">Business Studies</a></li>
                                  <li><a class="dropdown-item" href="course-detail.php?course=management-studies">Management Studies</a></li>
                                  <li><a class="dropdown-item" href="course-detail.php?course=banking-and-finance">Financial Markets and Product</a></li>
                                  <li><a class="dropdown-item" href="explore-course.php">Business Intelligence</a></li>
                                  <li>
                                      <hr class="dropdown-divider">
                                  </li>
                                  <!-- Specialized Fields -->
                                  <li class="dropdown-header">Specialized Fields</li>
                                  <li><a class="dropdown-item" href="course-detail.php?course=airport-management">Aviation</a></li>
                                  <li><a class="dropdown-item" href="course-detail.php?course=tourism-and-hospitality">Tourism and Hospitality</a></li>
                                  <li><a class="dropdown-item" href="course-detail.php?course=journalism-and-mass-communication">Journalism and Mass Communication</a></li>
                                  <li><a class="dropdown-item" href="explore-course.php">Life Skills</a></li>
                                  <li><a class="dropdown-item" href="explore-course.php">General Studies</a></li>
                              </div>
                              <div class="col-md-4"> <!-- Technology & IT -->
                                  <li class="dropdown-header">Technology & IT</li>
                                  <li><a class="dropdown-item" href="course-detail.php?course=web-technology">Web Technology</a></li>
                                  <li><a class="dropdown-item" href="explore-course.php">Python Data Structures</a></li>
                                  <li><a class="dropdown-item" href="explore-course.php">Introduction to Robotic Programming</a></li>
                                  <li><a class="dropdown-item" href="explore-course.php">AI in Human Resource Management</a></li>
                                  <li><a class="dropdown-item" href="explore-course.php">Advance Digital Transformation</a></li>
                                  <li><a class="dropdown-item" href="explore-course.php">Advance Data Analytics</a></li>
                                  <li><a class="dropdown-item" href="explore-course.php">IT Automation</a></li>
                                  <li><a class="dropdown-item" href="course-detail.php?course=it-support">IT Support</a></li>
                                  <li>
                                      <hr class="dropdown-divider">
                                  </li>
                              </div>
                              <div class="col-md-4">
                                  <!-- Design & Creativity -->
                                  <li class="dropdown-header">Design & Creativity</li>
                                  <li><a class="dropdown-item" href="explore-course.php">Design Study</a></li>
                                  <li><a class="dropdown-item" href="explore-course.php">Cosmetology Studies</a></li>
                                  <li><a class="dropdown-item" href="explore-course.php">3D Painting and Design for Educators</a></li>
                                  <li><a class="dropdown-item" href="explore-course.php">UX Design</a></li>
                                  <li>
                                      <hr class="dropdown-divider">
                                  </li>
                                  <li class="dropdown-header">Marketing</li>
                                  <li><a class="dropdown-item" href="course-detail.php?course=digital-marketing-strategy">Digital Marketing Strategy</a></li>
                                  <li><a class="dropdown-item" href="course-detail.php?course=digital-marketing-ecommerce">Digital Marketing & E-commerce</a></li>
                                  <li>
                                      <hr class="dropdown-divider">
                                  </li>
                              </div>
                          </div>
                      </ul>
                  </li>

                  <li class="nav-item">
                      <a class="nav-link" href="blogs.php">Blogs</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="certificate.php">Certificate</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="career.php">Career</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="contact.php">Contact</a>
                  </li>
              </ul>

                            <?php if (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true): ?>
                  <div class="dropdown ms-lg-3">
                      <button class="btn btn-student-avatar dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <span class="student-avatar-circle"><i class="fas fa-user-graduate"></i></span>
                          <span class="d-none d-md-inline student-avatar-name"><?= htmlspecialchars($_SESSION['student_name'] ?? 'Student') ?></span>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end student-dropdown-menu">
                          <li class="dropdown-header">
                              <div class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['student_name'] ?? 'Student') ?></div>
                              <small class="text-primary font-monospace"><?= htmlspecialchars($_SESSION['student_id'] ?? '') ?></small>
                          </li>
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item" href="my-enrollments.php"><i class="fas fa-graduation-cap text-warning me-2"></i> My Enrolled Courses & Receipts</a></li>
                          <li><a class="dropdown-item" href="enrollnow.php"><i class="fas fa-plus-circle text-success me-2"></i> Enroll in New Course</a></li>
                          <li><a class="dropdown-item" href="download-receipt.php"><i class="fas fa-file-invoice text-info me-2"></i> Admission Slip / Receipt</a></li>
                          <li><a class="dropdown-item" href="explore-course.php"><i class="fas fa-book-open text-primary me-2"></i> Course Catalog</a></li>
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item text-danger" href="student-logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                      </ul>
                  </div>
              <?php else: ?>
                  <div class="d-flex align-items-center ms-lg-3 header-auth-btns">
                      <a href="login.php" class="btn-nav-login me-2">
                          <i class="fas fa-arrow-right-to-bracket me-1"></i> Login
                      </a>
                      <a href="register.php" class="btn-nav-register">
                          <i class="fas fa-user-plus me-1"></i> Register
                      </a>
                  </div>
              <?php endif; ?>

          </div>
      </div>
  </nav>