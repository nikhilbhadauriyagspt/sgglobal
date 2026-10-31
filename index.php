<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings_helper.php';
require_once __DIR__ . '/includes/testimonials_helper.php';
$homeSettings = getGlobalSettings();
$homeAdmissionFee = (float)($homeSettings['admission_fee'] ?? 999.00);

ensureTestimonialsTable($pdo);
$testiStmt = $pdo->query("SELECT * FROM testimonials WHERE status = 'active' ORDER BY id ASC");
$homeTestimonials = $testiStmt->fetchAll();

// Fetch active categories
$catStmt = $pdo->query("SELECT * FROM course_categories WHERE status = 'active' ORDER BY id ASC");
$categories = $catStmt->fetchAll();

// Fetch active courses
$courseStmt = $pdo->query("
    SELECT c.*, cat.name AS category_name, cat.slug AS cat_slug 
    FROM courses c 
    LEFT JOIN course_categories cat ON c.category_id = cat.id 
    WHERE c.status = 'active' 
    ORDER BY c.id ASC
");
$allCourses = $courseStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SG Skill</title>
  <!-- Bootstrap CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- If the favicon is in the root folder -->
<link rel="icon" href="assets/images/logo/logopng.png" type="image/x-icon">

<!-- If the favicon is in an 'images' folder -->
<link rel="icon" href="assets/images/logo/logopng.png" type="image/x-icon">

<!-- If using PNG -->
<link rel="icon" href="assets/images/logo/logopng.png" type="image/png">

<!-- Optional for Apple devices -->
<link rel="apple-touch-icon" href="assets/images/logo/logopng.png">
  <!-- If the favicon is in the root folder -->
<link rel="icon" href="assets/images/logo/logopng.png" type="image/x-icon">

<!-- If the favicon is in an 'images' folder -->
<link rel="icon" href="assets/images/logo/logopng.png" type="image/x-icon">

<!-- If using PNG -->
<link rel="icon" href="assets/images/logo/logopng.png" type="image/png">

<!-- Optional for Apple devices -->
<link rel="apple-touch-icon" href="assets/images/logo/logopng.png">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="i" href="">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    integrity="sha512-/U16V2+3m4t8..." crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />


</head>

<body>

<?php include 'header.php'; ?>

    <!-- =====================================================================
         HERO SECTION
         ===================================================================== -->
    <?php $isHeroLoggedIn = !empty($_SESSION['student_logged_in']); ?>
    <style>
      .sgi-hero-v2 {
        height: 100vh;
        background: url("assets/images/banner/banner-2.jpg") no-repeat center center / cover;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #ffffff;
        position: relative;
        padding: 0 20px;
      }

      .sgi-hero-v2::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgb(0 0 0 / 76%);
      }

      .sgi-hero-v2-inner {
        position: relative;
        z-index: 2;
        max-width: 860px;
        margin: 0 auto;
      }

      .sgi-hero-v2-small {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 14px;
        text-transform: uppercase;
        letter-spacing: 2.5px;
        color: var(--primary-color);
        font-family: "Roboto Slab", serif;
      }

      .sgi-hero-v2-heading {
        font-family: "Roboto Slab", serif;
        font-size: clamp(2.4rem, 5vw, 3.85rem);
        font-weight: 700;
        color: #ffffff;
        line-height: 1.2;
        margin: 10px 0 18px;
        min-height: 1.3em;
      }

      .sgi-hero-v2-heading .type-cursor {
        display: inline-block;
        width: 3px;
        height: 0.88em;
        background: var(--primary-color);
        margin-left: 6px;
        vertical-align: middle;
        animation: sgiCursorBlink 0.9s infinite;
      }

      @keyframes sgiCursorBlink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
      }

      .sgi-hero-v2-desc {
        font-family: "Roboto", sans-serif;
        font-size: 1.08rem;
        color: rgba(255, 255, 255, 0.82);
        max-width: 650px;
        margin: 0 auto 34px;
        line-height: 1.65;
      }

      .sgi-hero-v2-btns {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 16px;
      }

      .sgi-hero-btn {
        color: #ffffff !important;
        text-decoration: none;
        background-color: #000c2784;
        border: 1.5px solid rgba(255, 255, 255, 0.22);
        padding: 15px 36px;
        border-radius: 8px;
        font-size: 1.12rem;
        font-weight: 600;
        font-family: "Roboto Slab", serif;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
      }

      .sgi-hero-btn:hover {
        background-color: #b38f24;
        border-color: #b38f24;
        color: #ffffff !important;
        transform: translateY(-2px);
      }

      .sgi-hero-btn-gold {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #050b1d !important;
        font-weight: 700;
      }

      .sgi-hero-btn-gold:hover {
        background-color: #b38f24;
        border-color: #b38f24;
        color: #ffffff !important;
      }
    </style>

    <section class="sgi-hero-v2">
      <div class="sgi-hero-v2-inner">
        <h3 class="sgi-hero-v2-small">Welcome to Skill Grow Institution</h3>

        <h1 class="sgi-hero-v2-heading">
          <span id="heroV2CourseText">Business Studies</span><span class="type-cursor"></span>
        </h1>

        <p class="sgi-hero-v2-desc">
          Empowering your future with industry-recognized courses, hands-on practical training, and career growth opportunities.
        </p>

        <div class="sgi-hero-v2-btns">
          <a href="explore-course.php" class="sgi-hero-btn">
            <i class="fas fa-book-open"></i> Browse Courses
          </a>
          <a href="enrollnow.php" class="sgi-hero-btn <?= $isHeroLoggedIn ? 'sgi-hero-btn-gold' : '' ?>">
            <i class="fas fa-graduation-cap"></i> Enroll Now
          </a>
          <?php if (!$isHeroLoggedIn): ?>
            <a href="register.php" class="sgi-hero-btn sgi-hero-btn-gold">
              <i class="fas fa-user-plus"></i> Register Yourself
            </a>
          <?php endif; ?>
        </div>
      </div>
    </section>

  <!-- Intro Section (Previously AI Course Section) -->
    <section class="ai-course-section">
        <div class="container">
            <div class="row">
                <div class="content-left">
                    <h1>Unlock Your Potential with Skill Grow Institution</h1>
                    <p>Explore a diverse range of courses at Skill Grow Institution, from Business Studies to Advanced IT and Design. Empower yourself with the skills to succeed in today’s dynamic world!</p>
                    <a href="explore-course.php"  class="enroll-btn">Discover Courses</a>
                </div>
            </div>
        </div>
    </section>

    <style>
      .course-card .card-img {
        border-radius: 8px;
        overflow: hidden;
        background: #f8fafc;
        margin-bottom: 14px;
      }
      .course-card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.78rem;
        color: #64748b;
        font-family: "Roboto", sans-serif;
        font-weight: 500;
        margin-bottom: 8px;
      }
      .course-card-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
      }
      .course-card-meta i {
        color: var(--primary-color);
      }
      .course-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        margin-top: auto;
        border-top: 1px solid #eef2f6;
      }
      .course-card-price {
        font-family: "Roboto Slab", serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--secondary-color);
        line-height: 1.1;
      }
      .course-card-price small {
        display: block;
        font-family: "Roboto", sans-serif;
        font-size: 0.68rem;
        font-weight: 500;
        color: #64748b;
        text-transform: uppercase;
      }
    </style>

<section id="courses" class="courses-section py-5">
  <div class="container">
    <h2 class="text-center mb-4">Explore Our Diverse Range of Courses</h2>
    <p class="text-center mb-5">Discover courses tailored to your career goals, from Business & Management to cutting-edge Technology & IT, designed to empower you with in-demand skills.</p>

    <!-- Dynamic Tabs Navigation with 'All' Tab First -->
    <ul class="nav nav-tabs justify-content-center mb-4" id="coursesTab" role="tablist">
      <!-- 1. All Courses Tab (Default Active) -->
      <li class="nav-item" role="presentation">
        <button class="nav-link active" 
                id="all-courses-tab" 
                data-bs-toggle="tab" 
                data-bs-target="#all-courses" 
                type="button" 
                role="tab" 
                aria-controls="all-courses" 
                aria-selected="true">
          All
        </button>
      </li>

      <!-- 2. Dynamic Categories from Database -->
      <?php foreach ($categories as $cat): 
        $catTabId = preg_replace('/[^a-zA-Z0-9_-]/', '', $cat['slug'] ?: 'cat-' . $cat['id']);
      ?>
        <li class="nav-item" role="presentation">
          <button class="nav-link" 
                  id="<?= $catTabId ?>-tab" 
                  data-bs-toggle="tab" 
                  data-bs-target="#<?= $catTabId ?>" 
                  type="button" 
                  role="tab" 
                  aria-controls="<?= $catTabId ?>" 
                  aria-selected="false">
            <?= htmlspecialchars($cat['name']) ?>
          </button>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- Dynamic Tabs Content -->
    <div class="tab-content" id="coursesTabContent">
      <!-- 1. All Courses Pane (Default Active) -->
      <div class="tab-pane fade show active" 
           id="all-courses" 
           role="tabpanel" 
           aria-labelledby="all-courses-tab">
        <div class="row g-4">
          <?php if (!empty($allCourses)): ?>
            <?php foreach ($allCourses as $course): 
              $courseImg = !empty($course['image']) ? $course['image'] : 'assets/images/course/online.webp';
              $courseExcerpt = !empty($course['tagline']) ? $course['tagline'] : substr(strip_tags($course['overview'] ?? ''), 0, 95) . '...';
              $courseDuration = !empty($course['duration']) ? $course['duration'] : '6 Months';
              $courseMode = !empty($course['mode']) ? $course['mode'] : 'Hybrid';
              $courseFee = (float)($course['fee'] ?? 14999);
            ?>
              <div class="col-md-6 col-lg-3">
                <div class="card h-100 course-card">
                  <div class="card-body d-flex flex-column">
                    <div class="card-img">
                      <img src="<?= htmlspecialchars($courseImg) ?>" width="100%" alt="<?= htmlspecialchars($course['title']) ?>" style="height: 159px; object-fit: contain;">
                    </div>
                    <div class="course-card-meta">
                      <span><i class="far fa-clock"></i> <?= htmlspecialchars($courseDuration) ?></span>
                      <span><i class="fas fa-laptop-code"></i> <?= htmlspecialchars($courseMode) ?></span>
                    </div>
                    <h5 class="card-title mb-2"><?= htmlspecialchars($course['title']) ?></h5>
                    <p class="card-text flex-grow-1 mb-3"><?= htmlspecialchars($courseExcerpt) ?></p>
                    <div class="course-card-footer">
                      <div class="course-card-price">
                        <small>Program Fee</small>
                        ₹<?= number_format($courseFee, 0) ?>
                      </div>
                      <a href="course-detail.php?course=<?= urlencode($course['slug']) ?>" class="btn course-btn">Learn More</a>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12 text-center py-5 text-muted">
              No courses available at the moment.
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- 2. Individual Category Panes -->
      <?php foreach ($categories as $cat): 
        $catTabId = preg_replace('/[^a-zA-Z0-9_-]/', '', $cat['slug'] ?: 'cat-' . $cat['id']);
        // Filter courses for this category
        $catCourses = array_filter($allCourses, function($c) use ($cat) {
          return (int)$c['category_id'] === (int)$cat['id'];
        });
      ?>
        <div class="tab-pane fade" 
             id="<?= $catTabId ?>" 
             role="tabpanel" 
             aria-labelledby="<?= $catTabId ?>-tab">
          <div class="row g-4">
            <?php if (!empty($catCourses)): ?>
              <?php foreach ($catCourses as $course): 
                $courseImg = !empty($course['image']) ? $course['image'] : 'assets/images/course/online.webp';
                $courseExcerpt = !empty($course['tagline']) ? $course['tagline'] : substr(strip_tags($course['overview'] ?? ''), 0, 95) . '...';
                $courseDuration = !empty($course['duration']) ? $course['duration'] : '6 Months';
                $courseMode = !empty($course['mode']) ? $course['mode'] : 'Hybrid';
                $courseFee = (float)($course['fee'] ?? 14999);
              ?>
                <div class="col-md-6 col-lg-3">
                  <div class="card h-100 course-card">
                    <div class="card-body d-flex flex-column">
                      <div class="card-img">
                        <img src="<?= htmlspecialchars($courseImg) ?>" width="100%" alt="<?= htmlspecialchars($course['title']) ?>" style="height: 159px; object-fit: contain;">
                      </div>
                      <div class="course-card-meta">
                        <span><i class="far fa-clock"></i> <?= htmlspecialchars($courseDuration) ?></span>
                        <span><i class="fas fa-laptop-code"></i> <?= htmlspecialchars($courseMode) ?></span>
                      </div>
                      <h5 class="card-title mb-2"><?= htmlspecialchars($course['title']) ?></h5>
                      <p class="card-text flex-grow-1 mb-3"><?= htmlspecialchars($courseExcerpt) ?></p>
                      <div class="course-card-footer">
                        <div class="course-card-price">
                          <small>Program Fee</small>
                          ₹<?= number_format($courseFee, 0) ?>
                        </div>
                        <a href="course-detail.php?course=<?= urlencode($course['slug']) ?>" class="btn course-btn">Learn More</a>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-12 text-center py-5">
                <div class="p-4" style="background: #fff; border-radius: 10px; max-width: 480px; margin: 0 auto; box-shadow: none;">
                  <h5 class="card-title mb-2"><?= htmlspecialchars($cat['name']) ?></h5>
                  <p class="card-text mb-3">Courses in this specialization are currently being updated for the 2026 academic batch.</p>
                  <a href="explore-course.php" class="btn course-btn btn-sm">Browse All Courses</a>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Call to Action -->
    <div class="text-center mt-5">
      <a href="explore-course.php" class="enroll-btn">Browse All Courses</a>
    </div>
  </div>
</section>

    <!-- =====================================================================
         WHY CHOOSE SKILL GROW INSTITUTION? SECTION (Placed below Courses)
         ===================================================================== -->
    <style>
      .sgi-why-v2 {
        padding: 80px 0;
        background-color: #000c27;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        position: relative;
      }

      .sgi-why-eyebrow {
        display: inline-block;
        font-family: "Roboto Slab", serif;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--primary-color);
        background: rgba(230, 169, 16, 0.1);
        border: 1px solid rgba(230, 169, 16, 0.3);
        padding: 6px 18px;
        border-radius: 50px;
        margin-bottom: 14px;
      }

      .sgi-why-v2 h2 {
        font-size: clamp(2rem, 3.5vw, 2.75rem);
        color: #ffffff;
        font-family: "Roboto Slab", serif;
        font-weight: 700;
        margin-bottom: 14px;
      }

      .sgi-why-sub {
        color: rgba(255, 255, 255, 0.72);
        font-family: "Roboto", sans-serif;
        font-size: 1.02rem;
        max-width: 640px;
        margin: 0 auto 50px;
      }

      .sgi-why-card {
        background: #0f1537;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-top: 3px solid var(--primary-color);
        border-radius: 14px;
        padding: 34px 28px 28px;
        height: 100%;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        position: relative;
        transition: transform 0.3s ease, border-color 0.3s ease;
      }

      .sgi-why-card:hover {
        transform: translateY(-6px);
        border-color: var(--primary-color);
      }

      .sgi-why-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 22px;
      }

      .sgi-why-icon-box {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        background: rgba(230, 169, 16, 0.14);
        border: 1px solid rgba(230, 169, 16, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 1.35rem;
      }

      .sgi-why-num {
        font-family: "Roboto Slab", serif;
        font-size: 2rem;
        font-weight: 800;
        color: rgba(255, 255, 255, 0.08);
        line-height: 1;
      }

      .sgi-why-card h4 {
        font-size: 1.35rem;
        font-family: "Roboto Slab", serif;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 18px;
        line-height: 1.35;
      }

      .sgi-why-list {
        list-style: none;
        padding: 0;
        margin: 0 0 20px;
        flex-grow: 1;
      }

      .sgi-why-list li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
        font-size: 0.96rem;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.85);
        font-family: "Roboto", sans-serif;
      }

      .sgi-why-list li:last-child {
        margin-bottom: 0;
      }

      .sgi-why-list li i {
        color: var(--primary-color);
        font-size: 1rem;
        margin-top: 5px;
        flex-shrink: 0;
      }

      .sgi-why-footer-tag {
        padding-top: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: "Roboto Slab", serif;
      }
    </style>

    <section class="sgi-why-v2">
      <div class="container">
        <div class="text-center">
          <span class="sgi-why-eyebrow">Our Core Advantage</span>
          <h2>Why Choose Skill Grow Institution?</h2>
          <p class="sgi-why-sub">
            Designed to bridge the gap between academic knowledge and real-world industry demands with hands-on mentorship.
          </p>
        </div>

        <div class="row g-4">
          <!-- Card 1: Versatile Skills Across Fields -->
          <div class="col-md-4">
            <div class="sgi-why-card">
              <div class="sgi-why-card-top">
                <div class="sgi-why-icon-box">
                  <i class="fas fa-layer-group"></i>
                </div>
                <span class="sgi-why-num">01</span>
              </div>
              <h4>Versatile Skills Across Fields</h4>
              <ul class="sgi-why-list">
                <li>
                  <i class="fas fa-check-circle"></i>
                  <span>Gain expertise in diverse areas like Business, Technology, Design, and more through our comprehensive courses.</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i>
                  <span>Build a strong foundation with practical knowledge that prepares you for real-world challenges.</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i>
                  <span>Structured curriculum curated by industry veterans and certified domain mentors.</span>
                </li>
              </ul>
              <div class="sgi-why-footer-tag">
                <i class="fas fa-award"></i> Multi-Disciplinary Excellence
              </div>
            </div>
          </div>

          <!-- Card 2: Practical Applications & Career Growth -->
          <div class="col-md-4">
            <div class="sgi-why-card">
              <div class="sgi-why-card-top">
                <div class="sgi-why-icon-box">
                  <i class="fas fa-laptop-code"></i>
                </div>
                <span class="sgi-why-num">02</span>
              </div>
              <h4>Practical Applications & Career Growth</h4>
              <ul class="sgi-why-list">
                <li>
                  <i class="fas fa-check-circle"></i>
                  <span>Apply your skills through hands-on projects, such as designing websites, analyzing data, or creating marketing strategies.</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i>
                  <span>Identify opportunities in your field and learn how to leverage your skills for career advancement.</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i>
                  <span>Acquire industry-recognized skills to enhance your resume and stand out to top employers.</span>
                </li>
              </ul>
              <div class="sgi-why-footer-tag">
                <i class="fas fa-briefcase"></i> Live Projects & Career Readiness
              </div>
            </div>
          </div>

          <!-- Card 3: Flexibility, Innovation & Opportunities -->
          <div class="col-md-4">
            <div class="sgi-why-card">
              <div class="sgi-why-card-top">
                <div class="sgi-why-icon-box">
                  <i class="fas fa-rocket"></i>
                </div>
                <span class="sgi-why-num">03</span>
              </div>
              <h4>Flexibility, Innovation & Opportunities</h4>
              <ul class="sgi-why-list">
                <li>
                  <i class="fas fa-check-circle"></i>
                  <span>Learn at your own pace with flexible course schedules tailored to your academic and professional needs.</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i>
                  <span>Explore innovative fields like AI, Digital Transformation, and UX Design to stay ahead of industry trends.</span>
                </li>
                <li>
                  <i class="fas fa-check-circle"></i>
                  <span>Open doors to exciting career paths in high-demand fields like IT, marketing, finance, and aviation.</span>
                </li>
              </ul>
              <div class="sgi-why-footer-tag">
                <i class="fas fa-clock"></i> Flexible Hybrid Learning
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
 <!-- Curriculum Section -->
    <section id="curriculum" class="curriculum-section">
        <div class="container">
            <h2 class="text-center">Explore Our Comprehensive Learning Journey</h2>
            <!-- Course Details -->
            <div class="course-details">
                <div class="detail-card">
                    <p>Duration</p>
                    <h5>Custom</h5>
                </div>
                <div class="detail-card">
                    <p>Time</p>
                    <h5>Flexible Schedule</h5>
                </div>
                <div class="detail-card">
                    <p>Course Format</p>
                    <h5>Videos, Projects, Quizzes</h5>
                </div>
                <div class="detail-card">
                    <p>Study Method</p>
                    <h5>Online & Interactive</h5>
                </div>
            </div>
            <!-- Weekly Curriculum -->
            <div class="week-item">
                <div class="d-flex align-items-start justify-content-between">
                    <span class="week-number">01</span>
                    <div class="week-box">
                        <h3>Foundations of Business & Management</h3>
                        <div class="sub-heading">Custom | Flexible Schedule</div>
                    </div>
                    <p>Dive into the essentials of business strategy and management. Learn how to analyze markets, develop business plans, and lead teams effectively with real-world case studies.</p>
                </div>
            </div>
            <div class="week-item">
                <div class="d-flex align-items-start justify-content-between">
                    <span class="week-number">02</span>
                    <div class="week-box">
                        <h3>Introduction to Web Technology</h3>
                        <div class="sub-heading">Custom | Flexible Schedule</div>
                    </div>
                    <p>Get hands-on with HTML, CSS, and JavaScript. Build your first webpage and understand the basics of web development to kickstart your tech journey.</p>
                </div>
            </div>
            <div class="week-item">
                <div class="d-flex align-items-start justify-content-between">
                    <span class="week-number">03</span>
                    <div class="week-box">
                        <h3>Digital Marketing Essentials</h3>
                        <div class="sub-heading">Custom | Flexible Schedule</div>
                    </div>
                    <p>Master the art of digital marketing with strategies for SEO, social media, and content creation. Create your own campaign and see measurable results.</p>
                </div>
            </div>
            <div class="week-item">
                <div class="d-flex align-items-start justify-content-between">
                    <span class="week-number">04</span>
                    <div class="week-box">
                        <h3>Creative Design & UX Principles</h3>
                        <div class="sub-heading">Custom | Flexible Schedule</div>
                    </div>
                    <p>Explore the fundamentals of design and user experience. Learn to create visually stunning designs and user-friendly interfaces with tools like Figma.</p>
                </div>
            </div>
            <div class="week-item">
                <div class="d-flex align-items-start justify-content-between">
                    <span class="week-number">05</span>
                    <div class="week-box">
                        <h3>Python for Data Analysis</h3>
                        <div class="sub-heading">Custom | Flexible Schedule</div>
                    </div>
                    <p>Unlock the power of Python to analyze data. Work on real datasets to create visualizations and gain insights that drive decision-making.</p>
                </div>
            </div>
            <div class="week-item">
                <div class="d-flex align-items-start justify-content-between">
                    <span class="week-number">06</span>
                    <div class="week-box">
                        <h3>Tourism & Hospitality Management</h3>
                        <div class="sub-heading">Custom | Flexible Schedule</div>
                    </div>
                    <p>Learn the ins and outs of the tourism industry. Develop skills in customer service, event planning, and sustainable tourism practices to excel in hospitality.</p>
                </div>
            </div>
        </div>
    </section>


   <!-- Quote Section -->
    <section class="quote-section">
       <blockquote>
  Education is the manifestation of the perfection already in man.
  <cite>— Swami Vivekananda —</cite>
</blockquote>

        <a href="enrollnow.php" class="enroll-btn">Start Learning Today</a>
    </section>

    <!-- Who Can Benefit Section -->
    <style>
      .sgi-benefit-section {
        padding: 80px 0;
        background-color: #000c27;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
      }

      .sgi-benefit-eyebrow {
        display: inline-block;
        padding: 6px 18px;
        border-radius: 50px;
        background: rgba(230, 169, 16, 0.14);
        border: 1px solid rgba(230, 169, 16, 0.4);
        color: var(--primary-color);
        font-family: "Roboto Slab", serif;
        font-size: 0.82rem;
        font-weight: 600;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        margin-bottom: 14px;
      }

      .sgi-benefit-section h2 {
        font-size: clamp(1.9rem, 3.3vw, 2.65rem);
        color: #ffffff;
        font-family: "Roboto Slab", serif;
        font-weight: 700;
        margin-bottom: 46px;
      }

      .sgi-benefit-card {
        background: #0f1537;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-top: 3px solid var(--primary-color);
        border-radius: 12px;
        padding: 28px 24px;
        height: 100%;
        box-shadow: none !important;
        transition: border-color 0.25s ease, transform 0.25s ease;
        display: flex;
        flex-direction: column;
      }

      .sgi-benefit-card:hover {
        border-color: var(--primary-color);
        transform: translateY(-4px);
        box-shadow: none !important;
      }

      .sgi-benefit-head {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
      }

      .sgi-benefit-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: rgba(230, 169, 16, 0.14);
        border: 1px solid rgba(230, 169, 16, 0.35);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
      }

      .sgi-benefit-head h3 {
        font-size: 1.25rem;
        font-family: "Roboto Slab", serif;
        font-weight: 700;
        color: #ffffff;
        margin: 0;
      }

      .sgi-benefit-card p {
        font-size: 0.94rem;
        line-height: 1.65;
        color: rgba(255, 255, 255, 0.78);
        font-family: "Roboto", sans-serif;
        margin: 0;
      }

      /* 2-Row x 2-Column Testimonial Slider */
      .sgi-testi-slider-wrap {
        position: relative;
        overflow: hidden;
      }

      .sgi-testi-track {
        display: flex;
        transition: transform 0.55s cubic-bezier(0.25, 0.8, 0.25, 1);
        will-change: transform;
      }

      .sgi-testi-col-slide {
        flex: 0 0 50%;
        max-width: 50%;
        padding: 0 15px;
        box-sizing: border-box;
      }

      @media (max-width: 991px) {
        .sgi-testi-col-slide {
          flex: 0 0 100%;
          max-width: 100%;
        }
      }

      .sgi-testi-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        margin-top: 10px;
      }

      .sgi-testi-arrow {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.25);
        background: transparent;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.25s ease;
      }

      .sgi-testi-arrow:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: #000c27;
      }

      .sgi-testi-dots {
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .sgi-testi-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        border: none;
        padding: 0;
        cursor: pointer;
        transition: all 0.25s ease;
      }

      .sgi-testi-dot.active {
        width: 26px;
        border-radius: 10px;
        background: var(--primary-color);
      }
    </style>

    <section class="sgi-benefit-section">
        <div class="container">
            <div class="text-center">
                <span class="sgi-benefit-eyebrow">Built For Every Learner</span>
                <h2>Who Can Benefit from Skill Grow Institution?</h2>
            </div>
            <div class="row g-4">
                <!-- Beginners -->
                <div class="col-lg-3 col-md-6">
                    <div class="sgi-benefit-card">
                        <div class="sgi-benefit-head">
                            <div class="sgi-benefit-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h3>Beginners</h3>
                        </div>
                        <p>No prior experience needed. Perfect for individuals eager to explore new fields like business, design, or technology and build a strong foundation for future growth.</p>
                    </div>
                </div>
                <!-- Tech Enthusiasts -->
                <div class="col-lg-3 col-md-6">
                    <div class="sgi-benefit-card">
                        <div class="sgi-benefit-head">
                            <div class="sgi-benefit-icon">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <h3>Tech Enthusiasts</h3>
                        </div>
                        <p>Dive into cutting-edge fields like Web Technology, Python, and AI. Gain practical skills to innovate and experiment with the latest tools in technology.</p>
                    </div>
                </div>
                <!-- Professionals -->
                <div class="col-lg-3 col-md-6">
                    <div class="sgi-benefit-card">
                        <div class="sgi-benefit-head">
                            <div class="sgi-benefit-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <h3>Professionals</h3>
                        </div>
                        <p>Enhance your career with courses in Business Studies, Management, and Digital Marketing. Learn to optimize processes, lead teams, and stay competitive in your industry.</p>
                    </div>
                </div>
                <!-- Creatives -->
                <div class="col-lg-3 col-md-6">
                    <div class="sgi-benefit-card">
                        <div class="sgi-benefit-head">
                            <div class="sgi-benefit-icon">
                                <i class="fas fa-palette"></i>
                            </div>
                            <h3>Creatives</h3>
                        </div>
                        <p>Unleash your creativity with courses in Design, UX, and Cosmetology. Develop skills to create stunning visuals and user experiences that make an impact.</p>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-12 text-center">
                    <a href="enrollnow.php" class="enroll-btn"><i class="fas fa-graduation-cap me-2"></i>Enroll Now</a>
                </div>
            </div>
        </div>
    </section>


<section id="testimonial" class="testimonial-section">
    <div class="container">
        <h2>Real Success Stories from Our Indian Students</h2>
        <?php
        // Pair testimonials into columns of 2 rows each so 2 columns x 2 rows = 4 cards slide together
        $testiColumns = array_chunk($homeTestimonials, 2);
        ?>
        <div class="sgi-testi-slider-wrap" id="sgiTestiSlider">
            <div class="sgi-testi-track" id="sgiTestiTrack">
                <?php foreach ($testiColumns as $colPair): ?>
                    <div class="sgi-testi-col-slide">
                        <?php foreach ($colPair as $tItem): ?>
                            <?php
                            $tHeadline = formatTestimonialHeadline($tItem);
                            $tStars = max(1, min(5, (int)($tItem['rating'] ?? 5)));
                            $tImg = !empty($tItem['image_path']) ? $tItem['image_path'] : 'assets/images/testi/rahul-sharma.jpg';
                            ?>
                            <div class="testimonial-item">
                                <img src="<?= htmlspecialchars($tImg) ?>" alt="<?= htmlspecialchars($tItem['student_name']) ?>" class="testimonial-img">
                                <div class="testimonial-content">
                                    <div class="testimonial-name"><?= htmlspecialchars($tHeadline) ?></div>
                                    <div class="testimonial-rating">
                                        <?php for ($s = 1; $s <= $tStars; $s++): ?>
                                            <i class="fas fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="testimonial-description">
                                        <?= nl2br(htmlspecialchars($tItem['description'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($testiColumns) > 1): ?>
                <div class="sgi-testi-controls">
                    <button type="button" class="sgi-testi-arrow" id="sgiTestiPrev" aria-label="Previous Testimonials">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="sgi-testi-dots" id="sgiTestiDots"></div>
                    <button type="button" class="sgi-testi-arrow" id="sgiTestiNext" aria-label="Next Testimonials">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<script>
(function() {
    const track = document.getElementById('sgiTestiTrack');
    const prevBtn = document.getElementById('sgiTestiPrev');
    const nextBtn = document.getElementById('sgiTestiNext');
    const dotsWrap = document.getElementById('sgiTestiDots');
    const sliderWrap = document.getElementById('sgiTestiSlider');
    if (!track) return;

    const slides = track.querySelectorAll('.sgi-testi-col-slide');
    const totalCols = slides.length;
    if (totalCols <= 1) return;

    let currentIndex = 0;
    let autoTimer = null;

    function getVisibleCols() {
        return window.innerWidth >= 992 ? 2 : 1;
    }

    function getMaxIndex() {
        return Math.max(0, totalCols - getVisibleCols());
    }

    function renderDots() {
        if (!dotsWrap) return;
        const maxIdx = getMaxIndex();
        dotsWrap.innerHTML = '';
        for (let i = 0; i <= maxIdx; i++) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'sgi-testi-dot' + (i === currentIndex ? ' active' : '');
            dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
            dot.addEventListener('click', function() {
                goToSlide(i);
                resetAuto();
            });
            dotsWrap.appendChild(dot);
        }
    }

    function goToSlide(idx) {
        const maxIdx = getMaxIndex();
        if (idx > maxIdx) idx = 0;
        if (idx < 0) idx = maxIdx;
        currentIndex = idx;
        const stepPercent = 100 / getVisibleCols();
        track.style.transform = 'translateX(-' + (currentIndex * stepPercent) + '%)';
        if (dotsWrap) {
            const dots = dotsWrap.querySelectorAll('.sgi-testi-dot');
            dots.forEach((d, i) => d.classList.toggle('active', i === currentIndex));
        }
    }

    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    function prevSlide() {
        goToSlide(currentIndex - 1);
    }

    function resetAuto() {
        if (autoTimer) clearInterval(autoTimer);
        autoTimer = setInterval(nextSlide, 4500);
    }

    if (prevBtn) prevBtn.addEventListener('click', function() { prevSlide(); resetAuto(); });
    if (nextBtn) nextBtn.addEventListener('click', function() { nextSlide(); resetAuto(); });

    if (sliderWrap) {
        sliderWrap.addEventListener('mouseenter', function() { if (autoTimer) clearInterval(autoTimer); });
        sliderWrap.addEventListener('mouseleave', function() { resetAuto(); });
    }

    window.addEventListener('resize', function() {
        if (currentIndex > getMaxIndex()) currentIndex = getMaxIndex();
        renderDots();
        goToSlide(currentIndex);
    });

    renderDots();
    goToSlide(0);
    resetAuto();
})();
</script>


   <style>
      .instructor-section {
        padding: 80px 0;
        background-color: #000c27;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
      }

      .instructor-section .sgi-about-header {
        text-align: center;
        max-width: 760px;
        margin: 0 auto 50px;
      }

      .instructor-section .sgi-about-eyebrow {
        display: inline-block;
        padding: 6px 18px;
        border-radius: 50px;
        background: rgba(230, 169, 16, 0.14);
        border: 1px solid rgba(230, 169, 16, 0.4);
        color: var(--primary-color);
        font-family: "Roboto Slab", serif;
        font-size: 0.82rem;
        font-weight: 600;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        margin-bottom: 14px;
      }

      .instructor-section h2 {
        font-size: clamp(1.9rem, 3.3vw, 2.65rem);
        font-family: "Roboto Slab", serif;
        color: #ffffff;
        margin-bottom: 0;
        font-weight: 700;
        line-height: 1.3;
      }

      .instructor-content {
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 44px;
        align-items: start;
      }

      .instructor-img-container {
        position: sticky;
        top: 100px;
        border-radius: 16px;
        overflow: hidden;
        background: #0f1537;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-top: 3px solid var(--primary-color);
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35);
      }

      .instructor-img {
        width: 100% !important;
        max-width: 100%;
        height: 440px;
        object-fit: cover;
        object-position: top center;
        display: block;
      }

      .instructor-img-caption {
        padding: 18px 20px;
        background: #0f1537;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        gap: 14px;
      }

      .instructor-img-caption-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: rgba(230, 169, 16, 0.15);
        border: 1px solid rgba(230, 169, 16, 0.35);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
      }

      .instructor-img-caption h5 {
        margin: 0 0 2px;
        font-size: 1rem;
        font-weight: 700;
        color: #ffffff;
        font-family: "Roboto Slab", serif;
      }

      .instructor-img-caption span {
        font-size: 0.82rem;
        color: rgba(255, 255, 255, 0.68);
        display: block;
      }

      .instructor-details {
        background: #0f1537;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 36px 34px;
      }

      .instructor-details h3 {
        font-size: 1.45rem;
        color: #ffffff;
        font-family: "Roboto Slab", serif;
        margin-bottom: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .instructor-details h3::before {
        content: "";
        display: inline-block;
        width: 5px;
        height: 22px;
        background: var(--primary-color);
        border-radius: 3px;
      }

      .instructor-details p {
        font-size: 0.98rem;
        color: rgba(255, 255, 255, 0.78);
        line-height: 1.75;
        margin-bottom: 14px;
        font-family: "Roboto", sans-serif;
      }

      .experience-list {
        list-style: none;
        padding: 0;
        margin: 18px 0 32px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
      }

      .experience-list li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: rgba(0, 12, 39, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 0;
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.92rem;
        line-height: 1.5;
      }

      .experience-list li i {
        color: var(--primary-color);
        font-size: 0.9rem;
        margin-top: 4px;
        margin-right: 0;
        flex-shrink: 0;
      }

      .experience-list li .exp-item-body {
        display: flex;
        flex-direction: column;
        gap: 3px;
      }

      .experience-list li .focused-text {
        font-weight: 700;
        width: auto;
        color: #ffffff;
        margin-right: 0;
        font-family: "Roboto Slab", serif;
        font-size: 0.96rem;
      }

      .experience-list li .exp-item-desc {
        color: rgba(255, 255, 255, 0.72);
        font-size: 0.88rem;
        line-height: 1.45;
      }

      .brands-list {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        justify-content: flex-start;
        margin-top: 16px;
      }

      .brand-pill {
        background: #ffffff;
        border-radius: 10px;
        padding: 10px 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 54px;
        min-width: 110px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transition: transform 0.25s ease;
      }

      .brand-pill:hover {
        transform: translateY(-3px);
      }

      .brand-pill .brand-img {
        width: auto;
        max-width: 85px;
        max-height: 34px;
        height: auto;
        object-fit: contain;
        filter: none;
      }

      @media (max-width: 991px) {
        .instructor-content {
          grid-template-columns: 1fr;
          gap: 28px;
        }
        .instructor-img-container {
          position: static;
          max-width: 480px;
          margin: 0 auto;
          width: 100%;
        }
        .instructor-details {
          padding: 26px 20px;
          text-align: left;
        }
        .experience-list {
          grid-template-columns: 1fr;
        }
      }
   </style>

   <section class="instructor-section">
    <div class="container">
        <div class="sgi-about-header">
            <span class="sgi-about-eyebrow">Who We Are</span>
            <h2>Unlock Your Potential with Skill Grow Institution</h2>
        </div>
        <div class="instructor-content">
            <div class="instructor-img-container">
                <img src="assets/images/banner/about-skillgrow.jpg" alt="Skill Grow Institution" class="instructor-img">
                <div class="instructor-img-caption">
                    <div class="instructor-img-caption-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h5>Skill Grow Institution</h5>
                        <span>Industry-Focused Training &amp; Career Mentorship</span>
                    </div>
                </div>
            </div>
            <div class="instructor-details">
                <h3>About Us</h3>
                <p>Skill Grow Institution is a hub of opportunity and innovation. Our diverse range of courses—from Business Studies to Advanced IT, Aviation, and Creative Design—empowers learners to thrive in a fast-paced, tech-driven world. We focus on skill-based education, real-world experience, and practical learning methods that align with industry demands.</p>
                <p class="mb-4">Whether you're looking to advance your career, switch industries, or ignite your passion, Skill Grow Institution offers the perfect launchpad. Our faculty members are industry leaders and experts committed to helping students become confident, skilled professionals.</p>

                <h3>Explore Our Courses</h3>
                <ul class="experience-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div class="exp-item-body">
                            <span class="focused-text">Business &amp; Management</span>
                            <span class="exp-item-desc">Business Studies, Management Studies, Financial Markets, Business Intelligence</span>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div class="exp-item-body">
                            <span class="focused-text">Aviation &amp; Hospitality</span>
                            <span class="exp-item-desc">Aviation, Tourism &amp; Hospitality, Mass Communication</span>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div class="exp-item-body">
                            <span class="focused-text">Technology &amp; IT</span>
                            <span class="exp-item-desc">Web Technology, Python, Robotic Programming, AI in HRM, Digital Transformation, Data Analytics</span>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div class="exp-item-body">
                            <span class="focused-text">Design &amp; Creativity</span>
                            <span class="exp-item-desc">Design Study, Cosmetology, 3D Design, UX Design</span>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div class="exp-item-body">
                            <span class="focused-text">Marketing</span>
                            <span class="exp-item-desc">Digital Marketing Strategy, E-commerce</span>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div class="exp-item-body">
                            <span class="focused-text">Life &amp; General Skills</span>
                            <span class="exp-item-desc">Life Skills, General Studies</span>
                        </div>
                    </li>
                </ul>

                <h3>Brands We Work With</h3>
                <p>We believe in partnerships that empower progress. Skill Grow Institution has proudly collaborated with reputed brands and industry experts to deliver quality training and placement support.</p>
                <div class="brands-list">
                    <div class="brand-pill"><img src="assets/images/client/tcs.png" alt="TCS" class="brand-img"></div>
                    <div class="brand-pill"><img src="assets/images/client/hcl.png" alt="HCL" class="brand-img"></div>
                    <div class="brand-pill"><img src="assets/images/client/Logo_ITC.png" alt="ITC" class="brand-img"></div>
                    <div class="brand-pill"><img src="assets/images/client/emirates.png" alt="Emirates" class="brand-img"></div>
                    <div class="brand-pill"><img src="assets/images/client/Qatar-Airways.png" alt="Qatar Airways" class="brand-img"></div>
                </div>
            </div>
        </div>
    </div>
</section>


     <style>
        #form-enroll .header-item p i {
          color: #ffb606;
          margin-right: 6px;
        }
        #form-enroll .enrollment-step .sub-heading {
          display: flex;
          align-items: center;
          gap: 8px;
        }
        #form-enroll .confirmation-text ul {
          list-style: none;
          padding-left: 0;
          margin: 18px 0;
        }
        #form-enroll .confirmation-text ul li {
          display: flex;
          align-items: flex-start;
          gap: 10px;
          margin-bottom: 12px;
          line-height: 1.5;
        }
        #form-enroll .confirmation-text ul li i {
          color: #ffb606;
          margin-top: 4px;
          font-size: 0.92rem;
          width: 18px;
          text-align: center;
          flex-shrink: 0;
        }
     </style>
     <section id="form-enroll" class="enrollment-section">
        <div class="container">
            <!-- Enrollment Header -->
           <div class="enrollment-header">
    <div class="header-item">
        <h2>Admissions Open</h2>
    </div>
    <div class="header-item">
        <p><i class="fas fa-chair"></i> Limited Seats</p>
        <h5>Enroll Early to Reserve Your Spot</h5>
    </div>
    <div class="header-item">
        <p><i class="fas fa-chalkboard-teacher"></i> Skill-Based Programs</p>
        <h5>Learn from Industry Experts</h5>
    </div>
    <div class="header-item">
        <p><i class="fas fa-laptop-code"></i> Flexible Learning</p>
        <h5>Online &amp; Offline Batches Available</h5>
    </div>
</div>

            <div class="row">
              <div class="col-md-4">
                 <div class="enrollment-step">
                    <p class="sub-heading"><i class="fas fa-user-plus"></i> Step 1</p>
                    <h4>Online Registration &amp; Seat Booking</h4>
                    <div class="confirmation-text">
                      <p>Start your admission process by registering online and reserving your seat for your chosen program.</p>
                      <ul>
                        <li><i class="fas fa-envelope-open-text"></i> <span>Instant Email OTP Verification</span></li>
                        <li><i class="fas fa-file-upload"></i> <span>Upload Basic KYC &amp; Academic Documents</span></li>
                        <li><i class="fas fa-rupee-sign"></i> <span>Confirm Seat Booking Online (₹999)</span></li>
                        <li><i class="fas fa-id-badge"></i> <span>Get Official Student ID &amp; Password on Email</span></li>
                      </ul>
                      <p>Already logged-in students can directly book a seat for another course without re-filling details.</p>
                      <a href="register.php" class="enroll-btn d-inline-block mt-3"><i class="fas <?= $isStudentLoggedIn ? 'fa-bookmark' : 'fa-user-plus' ?> me-2"></i><?= $isStudentLoggedIn ? 'Book Seat' : 'Register Yourself' ?></a>
                    </div>
                 </div>
              </div>
              <div class="col-md-4">
                 <div class="enrollment-step">
                    <p class="sub-heading"><i class="fas fa-file-invoice-dollar"></i> Step 2</p>
                    <h4>Course Enrollment &amp; Fee Adjustment</h4>
                    <div class="confirmation-text">
                      <p>Complete your full course admission on the enrollment portal using your registered Email or Student ID.</p>
                      <ul>
                        <li><i class="fas fa-book-open"></i> <span>Choose Your Preferred Course Program</span></li>
                        <li><i class="fas fa-tags"></i> <span>Automatic ₹999 Seat Booking Fee Deduction</span></li>
                        <li><i class="fas fa-percent"></i> <span>Apply Valid Scholarship or Promo Code</span></li>
                        <li><i class="fas fa-shield-alt"></i> <span>100% Secure Online Fee Payment</span></li>
                      </ul>
                      <p>Your paid registration/seat booking amount is automatically deducted from the total course fee.</p>
                      <a href="enrollnow.php" class="enroll-btn d-inline-block mt-3"><i class="fas fa-graduation-cap me-2"></i>Enroll Now</a>
                    </div>
                 </div>
              </div>
              <div class="col-md-4">
                 <div class="enrollment-step">
                    <p class="sub-heading"><i class="fas fa-user-graduate"></i> Step 3</p>
                    <h4>Confirmation &amp; Student Portal Access</h4>
                    <div class="confirmation-text">
                      <p>Receive instant enrollment confirmation on your email along with complete access to your Student Dashboard.</p>
                      <ul>
                        <li><i class="fas fa-check-circle"></i> <span>Official Enrollment Number (ENR) Issued</span></li>
                        <li><i class="fas fa-file-download"></i> <span>Download Verified Admission &amp; Fee Receipts</span></li>
                        <li><i class="fas fa-layer-group"></i> <span>Manage Multiple Enrolled Courses in One Place</span></li>
                        <li><i class="fas fa-award"></i> <span>Online Certificate Request &amp; Verification</span></li>
                      </ul>
                      <p>Log in anytime with your Student ID or Email to track your academic journey with Skill Grow Institution.</p>
                      <a href="<?= $isStudentLoggedIn ? 'my-enrollments.php' : 'login.php' ?>" class="enroll-btn d-inline-block mt-3"><i class="fas <?= $isStudentLoggedIn ? 'fa-th-large' : 'fa-sign-in-alt' ?> me-2"></i><?= $isStudentLoggedIn ? 'My Enrollments' : 'Student Login' ?></a>
                    </div>
                 </div>
              </div>
            </div>
           
        </div>
    </section>
     <!-- Waitlist Section -->
    <section class="waitlist-section">
        <div class="container">
            <h2>Excited for New Courses at Skill Grow Institution? Join the Waitlist!</h2>
            <div class="waitlist-form-container">
                <form id="waitlist-form" class="waitlist-form">
                    <input type="email" class="form-control" placeholder="Enter your email to stay updated" required>
                    <button type="submit" class="btn">Join Now</button>
                </form>
                <div id="form-response" class="form-response"></div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="faq-header-wrap">
                <span class="faq-eyebrow">Clear Answers</span>
                <h2>Frequently Asked Questions About Skill Grow Institution</h2>
                <p class="faq-sub">Everything you need to know about admissions, learning format, courses, and certifications.</p>
            </div>
            <div class="row faq-container">
                <!-- Left Column -->
                <div class="col-lg-6 faq-column">
                    <div class="accordion" id="faqAccordionLeft">
                        <!-- FAQ 1 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingLeftOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeftOne" aria-expanded="false" aria-controls="collapseLeftOne">
                                    What types of courses does Skill Grow Institution offer?
                                </button>
                            </h2>
                            <div id="collapseLeftOne" class="accordion-collapse collapse" aria-labelledby="headingLeftOne" data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    <p>We offer a wide range of courses across multiple fields, including Business & Management, Technology & IT, Design & Creativity, Marketing, and Specialized Fields like Aviation and Tourism. Whether you’re looking to start a new career or enhance your skills, we have something for everyone!</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingLeftTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeftTwo" aria-expanded="false" aria-controls="collapseLeftTwo">
                                    How do I enroll in a course at Skill Grow Institution?
                                </button>
                            </h2>
                            <div id="collapseLeftTwo" class="accordion-collapse collapse" aria-labelledby="headingLeftTwo" data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    <p>Enrolling is easy! Browse our courses from the "Courses" menu, select the one that interests you, and click "<a href="enrollnow.php">Enroll Now</a> to start your registration. You’ll be guided through a simple process to set up your account and begin learning.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingLeftThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeftThree" aria-expanded="false" aria-controls="collapseLeftThree">
                                    Are the courses self-paced or scheduled?
                                </button>
                            </h2>
                            <div id="collapseLeftThree" class="accordion-collapse collapse" aria-labelledby="headingLeftThree" data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    <p>Our courses are designed to be flexible! Most programs offer a self-paced learning experience, allowing you to study at your convenience. Some courses may include live sessions or scheduled projects, but we ensure you have the flexibility to balance learning with your schedule.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 4 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingLeftFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeftFour" aria-expanded="false" aria-controls="collapseLeftFour">
                                    What kind of support do I get while learning?
                                </button>
                            </h2>
                            <div id="collapseLeftFour" class="accordion-collapse collapse" aria-labelledby="headingLeftFour" data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    <p>We provide comprehensive support to ensure your success! You’ll have access to instructor guidance, a community of learners, and 24/7 technical support. Plus, our interactive platforms include forums and Q&A sessions to help you stay on track.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="col-lg-6 faq-column">
                    <div class="accordion" id="faqAccordionRight">
                        <!-- FAQ 1 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRightOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRightOne" aria-expanded="true" aria-controls="collapseRightOne">
                                    Do I need prior experience to join a course?
                                </button>
                            </h2>
                            <div id="collapseRightOne" class="accordion-collapse collapse show" aria-labelledby="headingRightOne" data-bs-parent="#faqAccordionRight">
                                <div class="accordion-body">
                                    <p>No prior experience is needed for most courses! We offer programs for all levels, from beginners to advanced learners. Each course is designed to guide you step-by-step, ensuring you gain the skills you need to succeed.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRightTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRightTwo" aria-expanded="false" aria-controls="collapseRightTwo">
                                    Will I receive a certificate upon completion?
                                </button>
                            </h2>
                            <div id="collapseRightTwo" class="accordion-collapse collapse" aria-labelledby="headingRightTwo" data-bs-parent="#faqAccordionRight">
                                <div class="accordion-body">
                                    <p>Yes! Upon successfully completing a course, you’ll receive a certificate of completion from Skill Grow Institution. This certificate can be added to your resume or LinkedIn profile to showcase your new skills.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRightThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRightThree" aria-expanded="false" aria-controls="collapseRightThree">
                                    Can I switch between courses if I change my mind?
                                </button>
                            </h2>
                            <div id="collapseRightThree" class="accordion-collapse collapse" aria-labelledby="headingRightThree" data-bs-parent="#faqAccordionRight">
                                <div class="accordion-body">
                                    <p>We understand that interests can change! You can switch courses within 24 hours of enrollment. Contact our support team, and we’ll help you transition to a course that better fits your goals.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 4 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRightFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRightFour" aria-expanded="false" aria-controls="collapseRightFour">
                                    How can I stay updated on new courses?
                                </button>
                            </h2>
                            <div id="collapseRightFour" class="accordion-collapse collapse" aria-labelledby="headingRightFour" data-bs-parent="#faqAccordionRight">
                                <div class="accordion-body">
                                    <p>Join our waitlist above to receive notifications about upcoming courses and new program launches. You can also follow us on social media or check our website regularly for updates!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.0.4/build/js/intlTelInput.min.js"></script>
   <script>
        document.getElementById('waitlist-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent actual form submission
            const emailInput = this.querySelector('input[type="email"]');
            const responseDiv = document.getElementById('form-response');

            if (emailInput.value) {
                responseDiv.textContent = 'Thank you for joining the waitlist! We’ll notify you soon.';
                responseDiv.style.color = '#28a745';
                responseDiv.style.display = 'block';
                emailInput.value = ''; // Clear the input
            } else {
                responseDiv.textContent = 'Please enter a valid email address.';
                responseDiv.style.color = '#dc3545';
                responseDiv.style.display = 'block';
            }
        });
    </script>
     <script>
        const navbar = document.querySelector('.navbar');

        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // Add/remove 'scrolled' class based on scroll position
            if (scrollTop > 50) { // Trigger after scrolling 50px
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
  <script>
    // Add active class to nav links on click
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', function () {
        document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
        this.classList.add('active');
      });
    });
  </script>
  <script>
    const courses = [
  "Business Studies",
  "Management Studies",
  "Financial Markets and Product",
  "Business Intelligence",
  "Aviation",
  "Tourism and Hospitality",
  "Journalism and Mass Communication",
  "Life Skills",
  "General Studies",
  "Web Technology",
  "Python Data Structures",
  "Introduction to Robotic Programming",
  "AI in Human Resource Management",
  "Advance Digital Transformation",
  "Advance Data Analytics",
  "IT Automation",
  "IT Support",
  "Design Study",
  "Cosmetology Studies",
  "3D Painting and Design for Educators",
  "UX Design",
  "Digital Marketing Strategy",
  "Digital Marketing & E-commerce"
];

const courseHeading = document.getElementById('course-heading');
const heroV2CourseText = document.getElementById('heroV2CourseText');
let courseIndex = 0;
let charIndex = 0;
let isDeleting = false;

function typeCourse() {
  const currentCourse = courses[courseIndex];
  const speed = isDeleting ? 50 : 100; // Faster deletion, slower typing

  if (!isDeleting && charIndex <= currentCourse.length) {
    // Typing
    const sub = currentCourse.substring(0, charIndex);
    if (courseHeading) courseHeading.textContent = sub;
    if (heroV2CourseText) heroV2CourseText.textContent = sub;
    charIndex++;
  } else if (isDeleting && charIndex >= 0) {
    // Deleting
    const sub = currentCourse.substring(0, charIndex);
    if (courseHeading) courseHeading.textContent = sub;
    if (heroV2CourseText) heroV2CourseText.textContent = sub;
    charIndex--;
  }

  if (!isDeleting && charIndex > currentCourse.length) {
    // Pause after typing
    setTimeout(() => {
      isDeleting = true;
    }, 1000); // 1-second pause before deleting
  } else if (isDeleting && charIndex < 0) {
    // Move to next course
    isDeleting = false;
    courseIndex = (courseIndex + 1) % courses.length; // Loop back to first course
  }

  setTimeout(typeCourse, speed);
}

// Start the animation
setTimeout(typeCourse, 500); // Initial delay
  </script>

</body>

</html>