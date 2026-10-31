<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/testimonials_helper.php';

ensureTestimonialsTable($pdo);
$testiStmt = $pdo->query("SELECT * FROM testimonials WHERE status = 'active' ORDER BY id ASC LIMIT 6");
$aboutTestimonials = $testiStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us - Skill Grow Institution</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-/U16V2+3m4t8..." crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Google Fonts (Roboto & Roboto Slab) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Global Section Stylings */
    .sgi-about-eyebrow {
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

    /* Banner Section */
    .about-banner {
      background: url('assets/images/banner/skill-aboutbg.jpg') no-repeat center/cover;
      position: relative;
      padding: 210px 0 120px;
      text-align: center;
      color: #fff;
    }
    .about-banner::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 12, 39, 0.85);
    }
    .about-banner .container {
      position: relative;
      z-index: 2;
    }
    .about-banner h1 {
      font-family: 'Roboto Slab', serif;
      font-size: clamp(2.2rem, 4vw, 3.2rem);
      font-weight: 700;
      margin-bottom: 14px;
    }
    .about-banner .breadcrumb {
      justify-content: center;
      background: transparent;
      padding: 0;
      margin: 0;
    }
    .about-banner .breadcrumb-item a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
    }
    .about-banner .breadcrumb-item {
      color: rgba(255, 255, 255, 0.75);
      font-family: 'Roboto', sans-serif;
      font-size: 0.95rem;
    }
    .about-banner .breadcrumb-item + .breadcrumb-item::before {
      color: rgba(255, 255, 255, 0.4);
    }

    /* About Content Section */
    .about-content-section {
      padding: 85px 0;
      background-color: #000c27;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .about-content-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: clamp(1.9rem, 3.2vw, 2.5rem);
      color: #ffffff;
      font-weight: 700;
      margin-bottom: 20px;
      line-height: 1.3;
    }
    .about-content-section p {
      font-family: 'Roboto', sans-serif;
      font-size: 1rem;
      color: rgba(255, 255, 255, 0.78);
      line-height: 1.75;
      margin-bottom: 25px;
    }
    .about-images-mosaic {
      position: relative;
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      grid-template-rows: repeat(12, 1fr);
      min-height: 380px;
      gap: 15px;
    }
    .mosaic-img-1 {
      grid-column: 1 / 9;
      grid-row: 1 / 10;
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 14px;
      border: 2px solid rgba(255, 255, 255, 0.1);
      border-top: 3px solid var(--primary-color);
    }
    .mosaic-img-2 {
      grid-column: 7 / 13;
      grid-row: 5 / 13;
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 14px;
      border: 3px solid #000c27;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    /* Mission & Vision Section */
    .mission-vision-section {
      padding: 80px 0;
      background-color: #0f1537;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .mission-vision-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: clamp(1.9rem, 3vw, 2.4rem);
      color: #ffffff;
      font-weight: 700;
      text-align: center;
      margin-bottom: 45px;
    }
    .mission-vision-card {
      padding: 36px 30px;
      background-color: #000c27;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-top: 3px solid var(--primary-color);
      border-radius: 14px;
      height: 100%;
      transition: transform 0.3s ease, border-color 0.3s ease;
    }
    .mission-vision-card:hover {
      transform: translateY(-6px);
      border-color: var(--primary-color);
    }
    .mv-icon-box {
      width: 52px;
      height: 52px;
      border-radius: 12px;
      background: rgba(230, 169, 16, 0.14);
      border: 1px solid rgba(230, 169, 16, 0.35);
      color: var(--primary-color);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      margin-bottom: 20px;
    }
    .mission-vision-card h3 {
      font-family: 'Roboto Slab', serif;
      font-size: 1.45rem;
      color: #ffffff;
      font-weight: 700;
      margin-bottom: 15px;
    }
    .mission-vision-card p {
      font-family: 'Roboto', sans-serif;
      font-size: 0.95rem;
      color: rgba(255, 255, 255, 0.75);
      line-height: 1.7;
      margin: 0;
    }

    /* History / Journey Section */
    .history-section {
      padding: 80px 0;
      background-color: #000c27;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .history-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: clamp(1.9rem, 3vw, 2.4rem);
      color: #ffffff;
      font-weight: 700;
      text-align: center;
      margin-bottom: 30px;
    }
    .history-card {
      background: #0f1537;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 14px;
      padding: 36px 32px;
      max-width: 900px;
      margin: 0 auto;
    }
    .history-content p {
      font-family: 'Roboto', sans-serif;
      font-size: 1rem;
      color: rgba(255, 255, 255, 0.8);
      line-height: 1.8;
      margin-bottom: 18px;
    }
    .history-content p:last-child {
      margin-bottom: 0;
    }

    /* Values Section */
    .values-section {
      padding: 80px 0;
      background-color: #0f1537;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .values-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: clamp(1.9rem, 3vw, 2.4rem);
      color: #ffffff;
      font-weight: 700;
      text-align: center;
      margin-bottom: 45px;
    }
    .value-card {
      padding: 32px 24px;
      text-align: center;
      background-color: #000c27;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-top: 3px solid var(--primary-color);
      border-radius: 14px;
      height: 100%;
      transition: transform 0.3s ease, border-color 0.3s ease;
    }
    .value-card:hover {
      transform: translateY(-6px);
      border-color: var(--primary-color);
    }
    .value-icon-box {
      width: 60px;
      height: 60px;
      border-radius: 14px;
      background: rgba(230, 169, 16, 0.14);
      border: 1px solid rgba(230, 169, 16, 0.35);
      color: var(--primary-color);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 20px;
    }
    .value-card h4 {
      font-family: 'Roboto Slab', serif;
      font-size: 1.3rem;
      color: #ffffff;
      font-weight: 700;
      margin-bottom: 12px;
    }
    .value-card p {
      font-family: 'Roboto', sans-serif;
      font-size: 0.94rem;
      color: rgba(255, 255, 255, 0.72);
      line-height: 1.65;
      margin: 0;
    }

    /* Team Section */
    .team-section {
      padding: 80px 0;
      background-color: #000c27;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .team-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: clamp(1.9rem, 3vw, 2.4rem);
      color: #ffffff;
      font-weight: 700;
      text-align: center;
      margin-bottom: 45px;
    }
    .team-card {
      text-align: center;
      padding: 30px 22px;
      background-color: #0f1537;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 14px;
      height: 100%;
      transition: transform 0.3s ease, border-color 0.3s ease;
    }
    .team-card:hover {
      transform: translateY(-6px);
      border-color: var(--primary-color);
    }
    .team-card img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      margin-bottom: 18px;
      object-fit: cover;
      border: 3px solid var(--primary-color);
      padding: 3px;
      background: #000c27;
    }
    .team-card h4 {
      font-family: 'Roboto Slab', serif;
      font-size: 1.25rem;
      color: #ffffff;
      font-weight: 700;
      margin-bottom: 4px;
    }
    .team-card .team-role {
      font-family: 'Roboto Slab', serif;
      font-size: 0.88rem;
      font-weight: 600;
      color: var(--primary-color);
      margin-bottom: 12px;
      display: block;
    }
    .team-card p {
      font-family: 'Roboto', sans-serif;
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.72);
      line-height: 1.6;
      margin: 0;
    }

    /* Testimonials Section */
    .testimonials-section {
      padding: 80px 0;
      background-color: #0f1537;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .testimonials-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: clamp(1.9rem, 3vw, 2.4rem);
      font-weight: 700;
      text-align: center;
      color: #ffffff;
      margin-bottom: 45px;
    }
    .testimonial-card {
      padding: 28px 24px;
      background-color: #000c27;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 14px;
      text-align: left;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform 0.3s ease;
    }
    .testimonial-card:hover {
      transform: translateY(-6px);
      border-color: rgba(230, 169, 16, 0.4);
    }
    .testi-header {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 16px;
    }
    .testimonial-card img {
      width: 55px;
      height: 55px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--primary-color);
      flex-shrink: 0;
    }
    .testi-header h5 {
      font-family: 'Roboto Slab', serif;
      font-size: 1.05rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0 0 2px;
    }
    .testi-header span {
      font-size: 0.8rem;
      color: var(--primary-color);
      display: block;
    }
    .testimonial-card p {
      font-family: 'Roboto', sans-serif;
      font-size: 0.92rem;
      color: rgba(255, 255, 255, 0.75);
      line-height: 1.65;
      margin: 0;
      flex-grow: 1;
    }

    /* Achievements Section */
    .achievements-section {
      padding: 80px 0;
      background-color: #000c27;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .achievements-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: clamp(1.9rem, 3vw, 2.4rem);
      color: #ffffff;
      font-weight: 700;
      text-align: center;
      margin-bottom: 45px;
    }
    .achievement-card {
      padding: 30px 20px;
      background: #0f1537;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 14px;
      text-align: center;
      height: 100%;
      transition: transform 0.3s ease;
    }
    .achievement-card:hover {
      transform: translateY(-6px);
      border-color: var(--primary-color);
    }
    .achievement-card i {
      font-size: 2.2rem;
      color: var(--primary-color);
      margin-bottom: 14px;
    }
    .achievement-card h3 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.4rem;
      color: #ffffff;
      font-weight: 800;
      margin-bottom: 6px;
    }
    .achievement-card p {
      font-family: 'Roboto', sans-serif;
      font-size: 0.95rem;
      color: rgba(255, 255, 255, 0.72);
      margin: 0;
    }

    /* Partnerships Section */
    .partnerships-section {
      padding: 70px 0;
      background-color: #0f1537;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .partnerships-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: clamp(1.9rem, 3vw, 2.4rem);
      color: #ffffff;
      font-weight: 700;
      text-align: center;
      margin-bottom: 40px;
    }
    .partner-pill {
      background: #ffffff;
      border-radius: 12px;
      padding: 12px 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 65px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    .partner-pill img {
      max-height: 40px;
      width: auto;
      max-width: 100%;
      object-fit: contain;
    }

    /* CTA Section */
    .cta-section {
      padding: 70px 0;
      background: linear-gradient(135deg, #001238 0%, #002365 100%);
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      text-align: center;
    }
    .cta-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: clamp(2rem, 3.5vw, 2.75rem);
      color: #ffffff;
      font-weight: 700;
      margin-bottom: 14px;
    }
    .cta-section p {
      font-family: 'Roboto', sans-serif;
      font-size: 1.05rem;
      color: rgba(255, 255, 255, 0.8);
      max-width: 680px;
      margin: 0 auto 30px;
      line-height: 1.7;
    }

    @media (max-width: 768px) {
      .about-images-mosaic {
        min-height: 280px;
        margin-top: 30px;
      }
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <!-- About Us Banner -->
  <section class="about-banner">
    <div class="container">
      <span class="sgi-about-eyebrow">Discover Our Institution</span>
      <h1>About Skill Grow Institution</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">About Us</li>
        </ol>
      </nav>
    </div>
  </section>

  <!-- About Content Section -->
  <section class="about-content-section">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-6">
          <span class="sgi-about-eyebrow">Excellence In Learning</span>
          <h2>Empowering Ambition with Industry-Ready Skills</h2>
          <p>Skill Grow Institution is dedicated to empowering individuals with practical, industry-relevant skills across Business, Technology, Design, Aviation, and more. We focus on bridging the gap between academic theory and real-world execution through hands-on mentors and verified curricula.</p>
          <p>Whether you are starting out or upgrading for your dream career, our flexible programs provide the guidance, real-world case studies, and official certifications needed to thrive.</p>
          <a href="explore-course.php" class="enroll-btn d-inline-block"><i class="fas fa-book-open me-2"></i>Explore Our Courses</a>
        </div>
        <div class="col-lg-6">
          <div class="about-images-mosaic">
            <img src="assets/images/about/classroom.jpg" alt="Classroom" class="mosaic-img-1">
            <img src="assets/images/about/students.jpg" alt="Students" class="mosaic-img-2">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Mission & Vision Section -->
  <section class="mission-vision-section">
    <div class="container">
      <div class="text-center mb-4">
        <span class="sgi-about-eyebrow">Our Guiding Light</span>
        <h2>Our Mission &amp; Vision</h2>
      </div>
      <div class="row g-4">
        <div class="col-md-6">
          <div class="mission-vision-card">
            <div class="mv-icon-box">
              <i class="fas fa-bullseye"></i>
            </div>
            <h3>Our Mission</h3>
            <p>At Skill Grow Institution, our mission is to provide accessible, high-quality, practical education to learners across India and beyond. We aim to equip ambitious individuals with job-ready skills in Business, Tech, and Design to help them achieve meaningful career outcomes.</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mission-vision-card">
            <div class="mv-icon-box">
              <i class="fas fa-eye"></i>
            </div>
            <h3>Our Vision</h3>
            <p>We envision an educational landscape where every motivated learner has direct access to high-impact career learning without barriers. By pairing practical industry projects with verified credentials, we create pathways for lifelong professional success.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- History / Journey Section -->
  <section class="history-section">
    <div class="container">
      <div class="text-center mb-4">
        <span class="sgi-about-eyebrow">Milestones</span>
        <h2>Our Journey</h2>
      </div>
      <div class="history-card">
        <div class="history-content">
          <p>Founded as an initiative for high-impact practical education, <strong>Skill Grow Institution</strong> has grown into a trusted institution offering comprehensive training programs in Management, Information Technology, Design, and Specialized Domains like Aviation and Hospitality.</p>
          <p>Our commitment to structured curriculum, certified domain experts, and student mentorship has helped thousands of students step into competitive careers with confidence and verified credentials.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Values Section -->
  <section class="values-section">
    <div class="container">
      <div class="text-center mb-4">
        <span class="sgi-about-eyebrow">What Defines Us</span>
        <h2>Our Core Values</h2>
      </div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="value-card">
            <div class="value-icon-box">
              <i class="fas fa-graduation-cap"></i>
            </div>
            <h4>Excellence in Education</h4>
            <p>Delivering practical, high-standard curriculum that prepares learners directly for corporate and technical challenges.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="value-card">
            <div class="value-icon-box">
              <i class="fas fa-globe"></i>
            </div>
            <h4>Accessibility for All</h4>
            <p>Providing flexible hybrid and online programs ensuring quality education is accessible regardless of background.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="value-card">
            <div class="value-icon-box">
              <i class="fas fa-hands-helping"></i>
            </div>
            <h4>Mentorship &amp; Support</h4>
            <p>Fostering an encouraging environment where students receive 1-on-1 counseling, doubt clearing, and guidance.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Team Section -->
  <section class="team-section">
    <div class="container">
      <div class="text-center mb-4">
        <span class="sgi-about-eyebrow">Leadership &amp; Faculty</span>
        <h2>Meet Our Team</h2>
      </div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="team-card">
            <img src="assets/images/testi/priya.jpeg" alt="Priya Sharma">
            <h4>Priya Sharma</h4>
            <span class="team-role">Head of Academic Programs</span>
            <p>Over 12 years of experience in higher education curricula, guiding learners to excel through innovative pedagogies.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="team-card">
            <img src="assets/images/testi/amin.jpg" alt="Amit Verma">
            <h4>Amit Verma</h4>
            <span class="team-role">Head of Technology &amp; IT</span>
            <p>Leads our tech disciplines, mentoring students in full-stack architecture, Python programming, and enterprise software.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="team-card">
            <img src="assets/images/testi/neha-kapoor.webp" alt="Neha Kapoor">
            <h4>Neha Kapoor</h4>
            <span class="team-role">Design &amp; Creative Mentor</span>
            <p>Specializes in UX design and modern digital branding, emphasizing portfolio-building and user-centric aesthetics.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="testimonials-section">
    <div class="container">
      <div class="text-center mb-4">
        <span class="sgi-about-eyebrow">Student Experiences</span>
        <h2>What Our Students Say</h2>
      </div>
      <div class="row g-4">
        <?php foreach (array_slice($aboutTestimonials, 0, 3) as $abT): ?>
          <div class="col-md-4">
            <div class="testimonial-card">
              <div>
                <div class="testi-header">
                  <img src="<?= htmlspecialchars($abT['image_path']) ?>" alt="<?= htmlspecialchars($abT['student_name']) ?>">
                  <div>
                    <h5><?= htmlspecialchars($abT['student_name']) ?></h5>
                    <span><?= htmlspecialchars($abT['student_designation']) ?></span>
                  </div>
                </div>
                <div class="text-warning mb-2" style="font-size: 0.85rem;">
                  <?php for ($i = 0; $i < (int)$abT['rating']; $i++): ?>
                    <i class="fas fa-star"></i>
                  <?php endfor; ?>
                </div>
                <p>"<?= htmlspecialchars($abT['description']) ?>"</p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Achievements Section -->
  <section class="achievements-section">
    <div class="container">
      <div class="text-center mb-4">
        <span class="sgi-about-eyebrow">Our Impact</span>
        <h2>Key Milestones</h2>
      </div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="achievement-card">
            <i class="fas fa-user-graduate"></i>
            <h3>10,000+</h3>
            <p>Learners Trained</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="achievement-card">
            <i class="fas fa-book-open"></i>
            <h3>50+</h3>
            <p>Certified Course Programs</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="achievement-card">
            <i class="fas fa-briefcase"></i>
            <h3>95%</h3>
            <p>Career Advancement Rate</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Partnerships Section -->
  <section class="partnerships-section">
    <div class="container">
      <div class="text-center mb-4">
        <span class="sgi-about-eyebrow">Industry Ties</span>
        <h2>Brands &amp; Airline Partners</h2>
      </div>
      <div class="owl-carousel owl-theme partner-carousel">
        <div class="item"><div class="partner-pill"><img src="assets/images/client/tcs.png" alt="TCS"></div></div>
        <div class="item"><div class="partner-pill"><img src="assets/images/client/hcl.png" alt="HCL"></div></div>
        <div class="item"><div class="partner-pill"><img src="assets/images/client/Logo_ITC.png" alt="ITC"></div></div>
        <div class="item"><div class="partner-pill"><img src="assets/images/client/emirates.png" alt="Emirates"></div></div>
        <div class="item"><div class="partner-pill"><img src="assets/images/client/Qatar-Airways.png" alt="Qatar Airways"></div></div>
        <div class="item"><div class="partner-pill"><img src="assets/images/client/Air-France.png" alt="Air France"></div></div>
        <div class="item"><div class="partner-pill"><img src="assets/images/client/british.png" alt="British Airways"></div></div>
        <div class="item"><div class="partner-pill"><img src="assets/images/client/singapore-airlines.png" alt="Singapore Airlines"></div></div>
      </div>
    </div>
  </section>

  <!-- Call to Action Section -->
  <section class="cta-section">
    <div class="container">
      <span class="sgi-about-eyebrow">Take The Next Step</span>
      <h2>Ready to Start Your Learning Journey?</h2>
      <p>Join thousands of learners at Skill Grow Institution and take the first step toward achieving your goals with industry mentorship and verified certification.</p>
      <a href="enrollnow.php" class="enroll-btn d-inline-block"><i class="fas fa-graduation-cap me-2"></i>Enroll Now</a>
    </div>
  </section>

<?php include 'footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    $(document).ready(function(){
      $(".partner-carousel").owlCarousel({
        loop: true,
        margin: 18,
        autoplay: true,
        autoplayTimeout: 2200,
        responsive: {
          0: { items: 2 },
          600: { items: 4 },
          1000: { items: 6 }
        }
      });
    });
  </script>
</body>

</html>