<?php
// Dynamic Explore Courses Page for SG Skill (100% Admin & Database Driven)
require_once __DIR__ . '/admin/config/db.php';

// Fetch active categories
$stmtCat = $pdo->query("SELECT * FROM course_categories WHERE status = 'active' ORDER BY id ASC");
$dbCategories = $stmtCat->fetchAll();

// Fetch active courses
$stmtCourses = $pdo->query("
    SELECT c.*, cat.name AS category_name, cat.slug AS cat_key
    FROM courses c
    LEFT JOIN course_categories cat ON c.category_id = cat.id
    WHERE c.status = 'active'
    ORDER BY c.id DESC
");
$allCourses = $stmtCourses->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Explore All Courses | Skill Grow Institution</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <!-- Premium Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="assets/css/style.css">

  <style>
    :root {
      --brand-primary: #e6a910;
      --brand-primary-hover: #cf9507;
      --brand-navy: #002365;
      --brand-dark: #070e24;
      --brand-dark-card: #0b1736;
      --brand-border: #1e2c52;
      --text-muted-dark: #94a3b8;
      --font-heading: 'Roboto Slab', serif;
      --font-body: 'Plus Jakarta Sans', sans-serif;
    }

    body {
      font-family: var(--font-body);
      background-color: var(--brand-dark);
      color: #e2e8f0;
      line-height: 1.7;
    }

    /* Full-Width Hero with Background Image */
    .explore-hero {
      position: relative;
      background: linear-gradient(180deg, rgba(7, 14, 36, 0.85) 0%, rgba(7, 14, 36, 0.98) 100%),
                  url('assets/images/banner/banner.jpg') no-repeat center center / cover;
      padding: 190px 0 100px;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .explore-hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(230, 169, 16, 0.15);
      border: 1px solid rgba(230, 169, 16, 0.4);
      color: var(--brand-primary);
      font-size: 0.82rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      padding: 6px 18px;
      border-radius: 50px;
      margin-bottom: 20px;
    }

    .explore-hero h1 {
      font-family: var(--font-heading);
      font-size: 3.2rem;
      font-weight: 800;
      line-height: 1.2;
      color: #ffffff;
      margin-bottom: 18px;
      background: linear-gradient(135deg, #ffffff 40%, #fef08a 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .explore-hero p.lead-summary {
      font-size: 1.2rem;
      color: #cbd5e1;
      max-width: 720px;
      margin: 0 auto 35px;
      font-weight: 400;
    }

    /* Live Search & Filter Bar inside Hero */
    .search-filter-box {
      max-width: 620px;
      margin: 0 auto;
      position: relative;
    }

    .search-input-wrap {
      position: relative;
      background: rgba(11, 23, 54, 0.9);
      border: 1.5px solid rgba(255, 255, 255, 0.18);
      border-radius: 50px;
      padding: 6px 12px 6px 24px;
      display: flex;
      align-items: center;
      gap: 12px;
      backdrop-filter: blur(12px);
      box-shadow: none !important; /* No Shadow */
    }

    .search-input-wrap i {
      color: var(--brand-primary);
      font-size: 1.15rem;
    }

    .search-input-wrap input {
      background: transparent;
      border: none;
      outline: none;
      color: #ffffff;
      width: 100%;
      font-size: 1rem;
    }

    .search-input-wrap input::placeholder {
      color: #94a3b8;
    }

    /* Sticky Horizontal Category Filter Strip */
    .filter-pills-section {
      background: rgba(8, 18, 44, 0.95);
      border-bottom: 1px solid var(--brand-border);
      padding: 14px 0;
      position: -webkit-sticky !important;
      position: sticky !important;
      top: 130px;
      z-index: 950;
      backdrop-filter: blur(14px);
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.4);
    }

    .filter-pills-wrap {
      display: flex;
      align-items: center;
      gap: 10px;
      overflow-x: auto;
      padding-bottom: 4px;
      scrollbar-width: none;
    }

    .filter-pills-wrap::-webkit-scrollbar {
      display: none;
    }

    .filter-btn {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #94a3b8;
      font-size: 0.9rem;
      font-weight: 600;
      padding: 8px 20px;
      border-radius: 50px;
      cursor: pointer;
      white-space: nowrap;
      transition: all 0.25s ease;
      box-shadow: none !important;
    }

    .filter-btn:hover {
      background: rgba(255, 255, 255, 0.12);
      color: #ffffff;
      border-color: rgba(255, 255, 255, 0.25);
    }

    .filter-btn.active {
      background: var(--brand-primary);
      color: #070e24;
      border-color: var(--brand-primary);
      font-weight: 700;
    }

    /* Course Cards Grid Section */
    .courses-catalog-section {
      padding: 70px 0 100px;
    }

    .catalog-counter-strip {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 35px;
      flex-wrap: wrap;
      gap: 15px;
    }

    .catalog-counter-strip h3 {
      font-family: var(--font-heading);
      font-size: 1.8rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0;
    }

    /* Modern Standout Course Card (Zero Shadows on Images & Cards) */
    .neo-course-card {
      background: var(--brand-dark-card);
      border: 1px solid var(--brand-border);
      border-radius: 18px;
      overflow: hidden;
      height: 100%;
      display: flex;
      flex-direction: column;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      box-shadow: none !important; /* No Shadow */
    }

    .neo-course-card:hover {
      border-color: rgba(230, 169, 16, 0.5);
      transform: translateY(-4px);
    }

    .card-img-container {
      position: relative;
      height: 200px;
      overflow: hidden;
      background: #050b1c;
      box-shadow: none !important; /* No Shadow */
    }

    .card-img-container img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
      box-shadow: none !important; /* No Shadow */
    }

    .neo-course-card:hover .card-img-container img {
      transform: scale(1.04);
    }

    .card-img-container {
      position: relative;
      height: 210px;
      overflow: hidden;
      background: #060e22;
      border-bottom: 1px solid var(--brand-border);
      box-shadow: none !important;
    }

    .card-img-container img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      box-shadow: none !important;
      transition: transform 0.35s ease;
    }

    .card-cat-pill {
      display: inline-block;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: var(--brand-primary);
      background: rgba(230, 169, 16, 0.12);
      border: 1px solid rgba(230, 169, 16, 0.25);
      padding: 3px 10px;
      border-radius: 50px;
    }

    .card-body-content {
      padding: 24px;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
    }

    .card-meta-line {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
      font-size: 0.85rem;
    }

    .card-category {
      color: #94a3b8;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-size: 0.75rem;
    }

    .card-rating {
      color: var(--brand-primary);
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .card-course-title {
      font-family: var(--font-heading);
      font-size: 1.28rem;
      font-weight: 700;
      color: #ffffff;
      line-height: 1.35;
      margin-bottom: 10px;
    }

    .card-excerpt {
      color: #94a3b8;
      font-size: 0.92rem;
      line-height: 1.6;
      margin-bottom: 20px;
      flex-grow: 1;
    }

    .card-footer-strip {
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      padding-top: 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
    }

    .card-duration-chip {
      color: #cbd5e1;
      font-size: 0.85rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .card-duration-chip i {
      color: var(--brand-primary);
    }

    .btn-view-course {
      background: rgba(230, 169, 16, 0.12);
      border: 1px solid rgba(230, 169, 16, 0.35);
      color: var(--brand-primary);
      font-weight: 700;
      font-size: 0.88rem;
      padding: 8px 18px;
      border-radius: 8px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.25s ease;
      box-shadow: none !important;
    }

    .btn-view-course:hover {
      background: var(--brand-primary);
      color: #070e24;
      border-color: var(--brand-primary);
      transform: translateY(-1px);
    }

    /* Bottom Standout Call to Action */
    .explore-bottom-cta {
      background: radial-gradient(circle at center, #0e1e47 0%, #060e22 80%);
      border-top: 1px solid var(--brand-border);
      padding: 80px 0;
      text-align: center;
    }

    .explore-bottom-cta h2 {
      font-family: var(--font-heading);
      font-size: 2.6rem;
      font-weight: 800;
      color: #ffffff;
      margin-bottom: 16px;
    }

    .explore-bottom-cta p {
      color: #cbd5e1;
      font-size: 1.15rem;
      max-width: 650px;
      margin: 0 auto 35px;
    }

    .btn-neo-gold {
      background: linear-gradient(135deg, #f59e0b, #e6a910);
      color: #070e24;
      font-weight: 700;
      font-size: 1rem;
      padding: 14px 34px;
      border-radius: 50px;
      border: none;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      box-shadow: none !important;
    }

    .btn-neo-gold:hover {
      background: #fbbf24;
      color: #070e24;
      transform: translateY(-2px);
    }

    .btn-neo-glass {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.18);
      color: #ffffff;
      font-weight: 600;
      padding: 14px 28px;
      border-radius: 50px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
      box-shadow: none !important;
    }

    .btn-neo-glass:hover {
      background: rgba(255, 255, 255, 0.15);
      color: #ffffff;
    }

    @media (max-width: 768px) {
      .explore-hero {
        padding: 140px 0 60px;
      }
      .explore-hero h1 {
        font-size: 2.2rem;
      }

    }
  </style>
</head>

<body>

  <!-- Site Header -->
  <?php include 'header.php'; ?>

  <!-- Full-Width Hero with Background Image (No Shadow) -->
  <section class="explore-hero">
    <div class="container">
      
      <span class="explore-hero-badge">
        <i class="fas fa-graduation-cap"></i> Industry-Accredited Programs
      </span>

      <h1>Explore Professional Career Courses</h1>
      <p class="lead-summary">
        Discover hands-on diploma and certificate curriculums tailored to the requirements of top multinational employers.
      </p>

      <!-- Live Search Bar -->
      <div class="search-filter-box">
        <div class="search-input-wrap">
          <i class="fas fa-search"></i>
          <input type="text" id="courseSearchInput" placeholder="Search by course name, skill, or domain...">
        </div>
      </div>

    </div>
  </section>

  <!-- Sticky Category Filters Strip -->
  <div class="filter-pills-section">
    <div class="container">
      <div class="filter-pills-wrap">
        <button class="filter-btn active" data-filter="all">All Programs</button>
        <button class="filter-btn" data-filter="tech">Technology & IT</button>
        <button class="filter-btn" data-filter="business">Business & Management</button>
        <button class="filter-btn" data-filter="marketing">Marketing & E-Commerce</button>
        <button class="filter-btn" data-filter="specialized">Aviation & Hospitality</button>
        <button class="filter-btn" data-filter="design">Design & Creative Arts</button>
      </div>
    </div>
  </div>

  <!-- Full-Width Course Catalog Grid (NO FORM) -->
  <section class="courses-catalog-section">
    <div class="container">
      
      <div class="catalog-counter-strip">
        <div>
          <h3>Available Programs</h3>
          <small style="color: #94a3b8;" id="resultsCount">Showing all <?= count($allCourses) ?> verified courses</small>
        </div>
        <div class="d-none d-md-block">
          <span class="badge bg-warning text-dark px-3 py-2 font-weight-bold" style="font-size: 0.85rem;">
            <i class="fas fa-check-circle me-1"></i> Admissions Open for 2026 Cohort
          </span>
        </div>
      </div>

      <!-- 3-Column Modern Grid -->
      <div class="row g-4" id="coursesGrid">
        <?php foreach ($allCourses as $course): ?>
          <div class="col-lg-4 col-md-6 course-grid-item" data-category="<?= htmlspecialchars($course['cat_key']) ?>" data-title="<?= strtolower(htmlspecialchars($course['title'])) ?>">
            <div class="neo-course-card">
              
              <div class="card-img-container">
                <img src="<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['title']) ?>" loading="lazy">
              </div>

              <div class="card-body-content">
                <div class="card-meta-line">
                  <span class="card-cat-pill"><?= htmlspecialchars($course['category_name'] ?? 'General Studies') ?></span>
                  <span class="card-rating"><i class="fas fa-star text-warning"></i> <?= htmlspecialchars($course['rating']) ?></span>
                </div>

                <h4 class="card-course-title"><?= htmlspecialchars($course['title']) ?></h4>
                <p class="card-excerpt"><?= htmlspecialchars($course['tagline'] ?: substr(strip_tags($course['overview'] ?? ''), 0, 110) . '...') ?></p>

                <div class="card-footer-strip">
                  <div class="d-flex align-items-center gap-2">
                    <div class="card-duration-chip">
                      <i class="fas fa-clock"></i> <?= htmlspecialchars($course['duration']) ?>
                    </div>
                    <div class="card-duration-chip" style="color: var(--brand-primary); font-weight: 700; border-color: rgba(230,169,16,0.3);">
                      ₹<?= number_format((float)($course['fee'] ?? 14999), 0) ?>
                    </div>
                  </div>
                  <a href="course-detail.php?course=<?= htmlspecialchars($course['slug']) ?>" class="btn-view-course">
                    View Details <i class="fas fa-arrow-right"></i>
                  </a>
                </div>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- No Results Fallback -->
      <div id="noResultsMsg" class="text-center py-5" style="display: none;">
        <i class="fas fa-search fa-3x mb-3 text-warning"></i>
        <h4 class="text-white">No courses match your search</h4>
        <p style="color: #94a3b8;">Try searching for a different keyword or view all courses above.</p>
      </div>

    </div>
  </section>

  <!-- Clean Bottom Action Section (Zero Form) -->
  <section class="explore-bottom-cta">
    <div class="container">
      <h2>Not Sure Which Course Fits Your Career?</h2>
      <p>Our academic counselors are available to guide you based on your background, interest, and career ambitions.</p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="https://wa.me/919217431562?text=Hello%20SG%20Skill,%20I%20need%20help%20choosing%20the%20right%20course" target="_blank" class="btn-neo-gold">
          <i class="fab fa-whatsapp"></i> Speak to Career Counselor
        </a>
        <a href="enrollnow.php" class="btn-neo-glass">
          <i class="fas fa-user-graduate"></i> Apply for Direct Admission
        </a>
      </div>
    </div>
  </section>

  <!-- Site Footer -->
  <?php include 'footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Live Search & Category Filter Script
    const searchInput = document.getElementById('courseSearchInput');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const courseItems = document.querySelectorAll('.course-grid-item');
    const noResults = document.getElementById('noResultsMsg');
    const resultsCount = document.getElementById('resultsCount');

    let currentCategory = 'all';

    function filterCourses() {
      const searchTerm = searchInput.value.toLowerCase().trim();
      let visibleCount = 0;

      courseItems.forEach(item => {
        const itemCat = item.getAttribute('data-category');
        const itemTitle = item.getAttribute('data-title');
        
        const matchesCategory = (currentCategory === 'all' || itemCat === currentCategory);
        const matchesSearch = itemTitle.includes(searchTerm);

        if (matchesCategory && matchesSearch) {
          item.style.display = 'block';
          visibleCount++;
        } else {
          item.style.display = 'none';
        }
      });

      if (visibleCount === 0) {
        noResults.style.display = 'block';
      } else {
        noResults.style.display = 'none';
      }

      resultsCount.textContent = `Showing ${visibleCount} verified courses`;
    }

    // Category click handler
    filterButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        filterButtons.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentCategory = this.getAttribute('data-filter');
        filterCourses();
      });
    });

    // Search input handler
    searchInput.addEventListener('input', filterCourses);

    // Precision Sticky Top Adjustment (Locks filter bar exactly at bottom of fixed navbar)
    function updateFilterStickyTop() {
      const navbar = document.querySelector('.navbar');
      const filterSection = document.querySelector('.filter-pills-section');
      if (navbar && filterSection) {
        const navRect = navbar.getBoundingClientRect();
        const exactBottom = Math.round(navRect.bottom);
        if (exactBottom > 0) {
          filterSection.style.top = exactBottom + 'px';
        }
      }
    }

    // Navbar scroll effect
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
      if ((window.pageYOffset || document.documentElement.scrollTop) > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
      updateFilterStickyTop();
    });

    window.addEventListener('load', updateFilterStickyTop);
    window.addEventListener('resize', updateFilterStickyTop);
    document.addEventListener('DOMContentLoaded', updateFilterStickyTop);
    updateFilterStickyTop();
  </script>
</body>
</html>
