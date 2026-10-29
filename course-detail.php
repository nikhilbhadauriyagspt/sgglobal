<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// Dynamic Standout Modern Course Experience for SG Skill (100% Admin & Database Driven)
require_once __DIR__ . '/admin/config/db.php';

$slug = isset($_GET['course']) ? trim($_GET['course']) : 'web-technology';

// Fetch Course from Database
$stmt = $pdo->prepare("
    SELECT c.*, cat.name AS category_name, cat.slug AS category_slug
    FROM courses c
    LEFT JOIN course_categories cat ON c.category_id = cat.id
    WHERE c.slug = :slug AND c.status = 'active'
    LIMIT 1
");
$stmt->execute(['slug' => $slug]);
$c = $stmt->fetch();

// Fallback to first active course if slug not matched
if (!$c) {
    $stmtFallback = $pdo->query("
        SELECT c.*, cat.name AS category_name, cat.slug AS category_slug
        FROM courses c
        LEFT JOIN course_categories cat ON c.category_id = cat.id
        WHERE c.status = 'active'
        ORDER BY c.id ASC LIMIT 1
    ");
    $c = $stmtFallback->fetch();
}

if (!$c) {
    die("No active course program available at this moment.");
}

// Decode JSON fields from database
$c['stats']      = json_decode($c['stats'] ?? '[]', true) ?: [];
$c['pillars']    = json_decode($c['pillars'] ?? '[]', true) ?: [];
$c['curriculum'] = json_decode($c['curriculum'] ?? '[]', true) ?: [];
$c['careers']    = json_decode($c['careers'] ?? '[]', true) ?: [];
$c['tools']      = json_decode($c['tools'] ?? '[]', true) ?: [];
$c['category']   = $c['category_name'] ?: 'Executive Studies';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($c['title']) ?> | Skill Grow Institution</title>
  
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
      --brand-primary-light: #fef3c7;
      --brand-navy: #002365;
      --brand-dark: #060e22;
      --brand-dark-card: #0b1736;
      --brand-border: #1e2c52;
      --text-muted-dark: #94a3b8;
      --font-heading: 'Roboto Slab', serif;
      --font-body: 'Plus Jakarta Sans', sans-serif;
    }

    body {
      font-family: var(--font-body);
      background-color: #070e24;
      color: #e2e8f0;
      line-height: 1.7;
      /* overflow visible for sticky */
    }

    /* Ambient Glow Backgrounds */
    .glow-aura {
      position: absolute;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(230, 169, 16, 0.12) 0%, rgba(0, 35, 101, 0) 70%);
      top: 50px;
      left: 10%;
      pointer-events: none;
      filter: blur(80px);
      z-index: 0;
    }

    .glow-aura-2 {
      position: absolute;
      width: 450px;
      height: 450px;
      background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, rgba(0, 35, 101, 0) 70%);
      top: 250px;
      right: 5%;
      pointer-events: none;
      filter: blur(90px);
      z-index: 0;
    }

    /* Standout Modern Hero */
    .neo-hero {
      position: relative;
      padding: 170px 0 80px;
      background: radial-gradient(ellipse at top, #0d1e47 0%, #060e22 70%);
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      z-index: 1;
    }

    .neo-breadcrumb {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 6px 16px;
      border-radius: 50px;
      font-size: 0.82rem;
      margin-bottom: 22px;
      backdrop-filter: blur(8px);
    }

    .neo-breadcrumb a {
      color: #94a3b8;
      text-decoration: none;
      transition: color 0.2s;
    }

    .neo-breadcrumb a:hover {
      color: var(--brand-primary);
    }

    .neo-breadcrumb span {
      color: #e2e8f0;
      font-weight: 600;
    }

    .neo-hero h1 {
      font-family: var(--font-heading);
      font-size: 3.2rem;
      font-weight: 800;
      line-height: 1.18;
      color: #ffffff;
      margin-bottom: 20px;
      background: linear-gradient(135deg, #ffffff 40%, #fef08a 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .neo-tagline {
      font-size: 1.2rem;
      color: #cbd5e1;
      max-width: 680px;
      margin-bottom: 32px;
      font-weight: 400;
      line-height: 1.6;
    }

    .neo-badge-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(230, 169, 16, 0.18);
      border: 1px solid rgba(230, 169, 16, 0.4);
      color: #fbbf24;
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      padding: 5px 14px;
      border-radius: 50px;
      margin-bottom: 18px;
    }

    /* Key Specs Metric Chips inside Hero */
    .neo-specs-strip {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      margin-bottom: 36px;
    }

    .neo-chip {
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      padding: 10px 18px;
      display: flex;
      align-items: center;
      gap: 12px;
      backdrop-filter: blur(10px);
    }

    .neo-chip i {
      color: var(--brand-primary);
      font-size: 1.2rem;
    }

    .neo-chip .chip-label {
      font-size: 0.75rem;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 0;
      line-height: 1;
    }

    .neo-chip .chip-val {
      font-size: 0.95rem;
      font-weight: 700;
      color: #ffffff;
      margin: 3px 0 0;
      line-height: 1.2;
    }

    /* Action Buttons (Standout Neo Style) */
    .neo-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      align-items: center;
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
    }

    .btn-neo-gold:hover {
      transform: translateY(-2px);
      background: #fbbf24;
      color: #070e24;
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
    }

    .btn-neo-glass:hover {
      background: rgba(255, 255, 255, 0.15);
      color: #ffffff;
      border-color: rgba(255, 255, 255, 0.3);
    }

    /* Hero Right Visual Showcase (ZERO SHADOWS AS REQUESTED) */
    .neo-visual-card {
      position: relative;
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.07), rgba(255, 255, 255, 0.02));
      border: 1.5px solid rgba(255, 255, 255, 0.15);
      border-radius: 20px;
      padding: 12px;
      box-shadow: none !important; /* No Shadow */
      backdrop-filter: blur(16px);
    }

    .neo-visual-card img {
      border-radius: 14px;
      width: 100%;
      height: 280px;
      object-fit: cover;
      box-shadow: none !important;
      display: block;
    }

    .neo-floating-stat {
      position: absolute;
      bottom: -16px;
      right: 20px;
      background: #0b1a3d;
      border: 1.5px solid rgba(230, 169, 16, 0.6);
      padding: 12px 20px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: none !important; /* No Shadow */
    }

    .neo-floating-stat i {
      color: var(--brand-primary);
      font-size: 1.5rem;
    }

    /* Modern Bento Grid Section */
    .neo-section {
      padding: 85px 0;
      position: relative;
      z-index: 2;
    }

    .section-eyebrow {
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 1.5px;
      font-weight: 700;
      color: var(--brand-primary);
      margin-bottom: 8px;
      display: block;
    }

    .section-headline {
      font-family: var(--font-heading);
      font-size: 2.3rem;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 35px;
    }

    .bento-card {
      background: var(--brand-dark-card);
      border: 1px solid var(--brand-border);
      border-radius: 18px;
      padding: 30px;
      height: 100%;
      transition: all 0.3s ease;
      position: relative;
      box-shadow: none !important; /* No Shadow */
    }

    .bento-card:hover {
      border-color: rgba(230, 169, 16, 0.5);
      transform: translateY(-3px);
    }

    .bento-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      background: rgba(230, 169, 16, 0.12);
      border: 1px solid rgba(230, 169, 16, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--brand-primary);
      font-size: 1.25rem;
      margin-bottom: 20px;
    }

    .bento-card h4 {
      font-size: 1.25rem;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 10px;
    }

    .bento-card p {
      color: #94a3b8;
      font-size: 0.95rem;
      margin: 0;
      line-height: 1.6;
    }

        /* Sticky Roadmap Sidebar */
    .sticky-roadmap-sidebar {
      margin-bottom: 30px;
    }

    @media (min-width: 992px) {
      .sticky-roadmap-sidebar {
        position: -webkit-sticky !important;
        position: sticky !important;
        top: 100px !important;
        z-index: 20;
        display: block;
      }
    }

    /* Distinctive Modern Roadmap / Timeline Syllabus */
    .timeline-item {
      display: flex;
      gap: 22px;
      margin-bottom: 22px;
      position: relative;
    }

    .timeline-item:not(:last-child)::after {
      content: "";
      position: absolute;
      left: 26px;
      top: 55px;
      bottom: -22px;
      width: 2px;
      background: linear-gradient(180deg, rgba(230, 169, 16, 0.4) 0%, rgba(30, 44, 82, 0.4) 100%);
    }

    .timeline-num {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      background: #0f2048;
      border: 1.5px solid rgba(230, 169, 16, 0.4);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 1.05rem;
      color: var(--brand-primary);
      flex-shrink: 0;
      z-index: 1;
    }

    .timeline-card {
      background: var(--brand-dark-card);
      border: 1px solid var(--brand-border);
      border-radius: 16px;
      padding: 22px 26px;
      width: 100%;
      box-shadow: none !important;
      transition: all 0.25s ease;
    }

    .timeline-card:hover {
      border-color: rgba(255, 255, 255, 0.2);
      background: #0e1c42;
    }

    .timeline-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .timeline-card h4 {
      font-size: 1.15rem;
      font-weight: 700;
      color: #ffffff;
      margin: 0;
    }

    .timeline-duration {
      font-size: 0.8rem;
      font-weight: 600;
      background: rgba(255, 255, 255, 0.08);
      color: #94a3b8;
      padding: 4px 10px;
      border-radius: 50px;
    }

    .topic-pills {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 12px;
    }

    .topic-pill {
      font-size: 0.82rem;
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
      color: #cbd5e1;
      padding: 4px 12px;
      border-radius: 6px;
    }

    /* Career Matrix Cards */
    .career-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 18px;
    }

    .career-box {
      background: var(--brand-dark-card);
      border: 1px solid var(--brand-border);
      border-radius: 16px;
      padding: 22px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: none !important;
      transition: all 0.3s;
    }

    .career-box:hover {
      border-color: var(--brand-primary);
      transform: translateY(-3px);
    }

    .career-box h5 {
      color: #ffffff;
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 6px;
    }

    .career-pkg {
      font-size: 1.15rem;
      font-weight: 800;
      color: var(--brand-primary);
      margin: 12px 0 6px;
    }

    /* Target Audience Matrix (New Section 1) */
    .audience-card {
      background: var(--brand-dark-card);
      border: 1px solid var(--brand-border);
      border-radius: 16px;
      padding: 26px;
      height: 100%;
      box-shadow: none !important;
      transition: all 0.25s ease;
    }

    .audience-card:hover {
      border-color: rgba(230, 169, 16, 0.4);
    }

    .audience-badge {
      display: inline-block;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: var(--brand-primary);
      background: rgba(230, 169, 16, 0.1);
      border: 1px solid rgba(230, 169, 16, 0.25);
      padding: 3px 10px;
      border-radius: 4px;
      margin-bottom: 14px;
    }

    .audience-card h5 {
      color: #ffffff;
      font-size: 1.15rem;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .audience-card p {
      color: #94a3b8;
      font-size: 0.92rem;
      margin: 0;
      line-height: 1.6;
    }

    /* Verified Certificate Showcase (New Section 2 - ZERO SHADOW) */
    .cert-showcase-box {
      background: linear-gradient(135deg, #0d1e47 0%, #081432 100%);
      border: 1.5px solid rgba(230, 169, 16, 0.4);
      border-radius: 22px;
      padding: 40px;
      box-shadow: none !important; /* No Shadow */
    }

    .cert-preview-frame {
      border: 2px dashed rgba(230, 169, 16, 0.4);
      border-radius: 16px;
      padding: 20px;
      background: rgba(0, 0, 0, 0.25);
      text-align: center;
      box-shadow: none !important; /* No Shadow */
    }

    .cert-preview-frame img {
      max-width: 100%;
      height: auto;
      border-radius: 10px;
      border: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: none !important; /* No Shadow */
    }

    .cert-benefit-list {
      list-style: none;
      padding: 0;
      margin: 20px 0 0;
    }

    .cert-benefit-list li {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin-bottom: 16px;
      color: #e2e8f0;
      font-size: 0.95rem;
    }

    .cert-benefit-list li i {
      color: var(--brand-primary);
      font-size: 1.1rem;
      margin-top: 3px;
    }

    /* FAQ Accordion (New Section 3 - Clean Minimal) */
    .neo-accordion .accordion-item {
      background: var(--brand-dark-card);
      border: 1px solid var(--brand-border);
      border-radius: 14px !important;
      margin-bottom: 14px;
      overflow: hidden;
      box-shadow: none !important;
    }

    .neo-accordion .accordion-button {
      background: var(--brand-dark-card);
      color: #ffffff;
      font-family: var(--font-heading);
      font-weight: 600;
      font-size: 1.05rem;
      padding: 20px 24px;
      box-shadow: none;
      border: none;
    }

    .neo-accordion .accordion-button:not(.collapsed) {
      background: #0f1f48;
      color: var(--brand-primary);
      border-bottom: 1px solid var(--brand-border);
    }

    .neo-accordion .accordion-button::after {
      filter: invert(1);
    }

    .neo-accordion .accordion-body {
      padding: 20px 24px;
      color: #94a3b8;
      font-size: 0.95rem;
      line-height: 1.7;
      background: #08122c;
    }

    /* Sticky Bottom Floating Action Bar */
    .floating-action-bar {
      position: fixed;
      bottom: 24px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(11, 23, 54, 0.9);
      border: 1px solid rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(14px);
      padding: 12px 28px;
      border-radius: 50px;
      display: flex;
      align-items: center;
      gap: 28px;
      z-index: 1000;
      max-width: 90%;
      box-shadow: none !important; /* No Shadow */
    }

    .floating-info {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .floating-title {
      font-weight: 700;
      color: #ffffff;
      font-size: 0.95rem;
      margin: 0;
      white-space: nowrap;
    }

    .floating-sub {
      font-size: 0.8rem;
      color: #94a3b8;
      margin: 0;
    }

    .btn-floating-enroll {
      background: var(--brand-primary);
      color: #070e24;
      font-weight: 700;
      font-size: 0.9rem;
      padding: 9px 24px;
      border-radius: 50px;
      text-decoration: none;
      white-space: nowrap;
      transition: all 0.2s;
    }

    .btn-floating-enroll:hover {
      background: #fbbf24;
      color: #070e24;
    }

    @media (max-width: 768px) {
      .neo-hero h1 {
        font-size: 2.2rem;
      }
      .floating-info {
        display: none;
      }
      .floating-action-bar {
        bottom: 12px;
        padding: 8px 16px;
      }
      .cert-showcase-box {
        padding: 24px;
      }
    }
  </style>
</head>

<body>

  <!-- Ambient Glow Effects -->
  <div class="glow-aura"></div>
  <div class="glow-aura-2"></div>

  <!-- Common Header -->
  <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } include 'header.php'; ?>

  <!-- Distinctive Standout Hero Section -->
  <section class="neo-hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-7">
          
          <div class="neo-breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right text-muted" style="font-size: 0.7rem;"></i>
            <a href="explore-course.php">Courses</a>
            <i class="fas fa-chevron-right text-muted" style="font-size: 0.7rem;"></i>
            <span><?= htmlspecialchars($c['category']) ?></span>
          </div>

          <div>
            <span class="neo-badge-pill">
              <i class="fas fa-sparkles me-1"></i> <?= htmlspecialchars($c['badge']) ?>
            </span>
          </div>

          <h1><?= htmlspecialchars($c['title']) ?></h1>
          <p class="neo-tagline"><?= htmlspecialchars($c['tagline']) ?></p>

          <!-- Key Metrics Chips -->
          <div class="neo-specs-strip">
            <div class="neo-chip" style="background: rgba(230,169,16,0.12); border-color: rgba(230,169,16,0.35);">
              <i class="fas fa-indian-rupee-sign text-warning"></i>
              <div>
                <p class="chip-label text-warning">Course Fee</p>
                <p class="chip-val text-white fw-bold">₹<?= number_format((float)($c['fee'] ?? 14999), 0) ?></p>
              </div>
            </div>
            <div class="neo-chip">
              <i class="fas fa-clock"></i>
              <div>
                <p class="chip-label">Duration</p>
                <p class="chip-val"><?= htmlspecialchars($c['duration']) ?></p>
              </div>
            </div>
            <div class="neo-chip">
              <i class="fas fa-graduation-cap"></i>
              <div>
                <p class="chip-label">Eligibility</p>
                <p class="chip-val"><?= htmlspecialchars($c['eligibility']) ?></p>
              </div>
            </div>
            <div class="neo-chip">
              <i class="fas fa-star text-warning"></i>
              <div>
                <p class="chip-label">Rating</p>
                <p class="chip-val"><?= htmlspecialchars($c['rating']) ?> ★ (<?= htmlspecialchars($c['reviews']) ?>)</p>
              </div>
            </div>
          </div>

          <!-- Direct Actions (No Forms) -->
          <div class="neo-actions">
            <a href="enrollnow.php?course=<?= urlencode($c['slug']) ?>" class="btn-neo-gold">
              <i class="fas fa-user-graduate"></i> Apply for Admission
            </a>
            <a href="https://wa.me/919217431562?text=Hello%20SG%20Skill,%20I%20want%20details%20about%20<?= urlencode($c['title']) ?>" target="_blank" class="btn-neo-glass">
              <i class="fab fa-whatsapp text-success"></i> WhatsApp Counselor
            </a>
          </div>

        </div>

        <!-- Right Visual Showcase (No Shadows) -->
        <div class="col-lg-5">
          <div class="neo-visual-card">
            <img src="<?= htmlspecialchars($c['image']) ?>" alt="<?= htmlspecialchars($c['title']) ?>">
            
            <div class="neo-floating-stat">
              <i class="fas fa-shield-halved"></i>
              <div>
                <small class="text-white-50 d-block" style="font-size: 0.75rem;">Guaranteed Track</small>
                <strong style="color: #ffffff; font-size: 0.95rem;"><?= htmlspecialchars($c['placement_rate']) ?></strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Bento Grid: Key Capabilities & Architecture -->
  <section class="neo-section">
    <div class="container">
      <span class="section-eyebrow">Program Architecture</span>
      <h2 class="section-headline">What Sets This Cohort Apart</h2>

      <div class="row g-4">
        <!-- Main Stats Card (Wide) -->
        <div class="col-lg-8">
          <div class="bento-card" style="background: linear-gradient(135deg, #0e1f47 0%, #081432 100%);">
            <div class="row g-4 align-items-center">
              <div class="col-md-7">
                <span class="badge bg-warning text-dark mb-2 px-3 py-1 font-weight-bold">Core Impact</span>
                <h3 class="text-white font-weight-bold" style="font-size: 1.6rem;"><?= htmlspecialchars($c['title']) ?></h3>
                <p class="text-white-50 mt-2" style="font-size: 0.95rem;">
                  <?= htmlspecialchars($c['overview']) ?>
                </p>
              </div>
              <div class="col-md-5">
                <div class="row g-3">
                  <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } foreach ($c['stats'] as $st): ?>
                    <div class="col-6">
                      <div style="background: rgba(255,255,255,0.05); padding: 14px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.08);">
                        <h4 style="color: var(--brand-primary); font-size: 1.5rem; margin: 0; font-weight: 800;"><?= htmlspecialchars($st['val']) ?></h4>
                        <small style="color: #94a3b8; font-size: 0.8rem;"><?= htmlspecialchars($st['label']) ?></small>
                      </div>
                    </div>
                  <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Compensation & Market Metric Card -->
        <div class="col-lg-4">
          <div class="bento-card" style="border-color: rgba(230, 169, 16, 0.3);">
            <div class="bento-icon">
              <i class="fas fa-arrow-trend-up"></i>
            </div>
            <h4>Earning Potential</h4>
            <p>Average salary band for certified graduates from this program:</p>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--brand-primary); margin: 15px 0 5px;">
              <?= htmlspecialchars($c['avg_salary']) ?>
            </div>
            <small style="color: #64748b;">Based on alumni placement reports</small>
          </div>
        </div>

        <!-- 4 Pillar Cards -->
        <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } foreach ($c['pillars'] as $pillar): ?>
          <div class="col-lg-3 col-md-6">
            <div class="bento-card">
              <div class="bento-icon">
                <i class="fas <?= htmlspecialchars($pillar['icon']) ?>"></i>
              </div>
              <h4><?= htmlspecialchars($pillar['title']) ?></h4>
              <p><?= htmlspecialchars($pillar['desc']) ?></p>
            </div>
          </div>
        <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } endforeach; ?>
      </div>
    </div>
  </section>

  <!-- NEW SECTION 1: Who Is This Program For? -->
  <section class="neo-section" style="background: #050b1c; border-top: 1px solid var(--brand-border); border-bottom: 1px solid var(--brand-border);">
    <div class="container">
      <div class="text-center max-w-700 mx-auto mb-5">
        <span class="section-eyebrow">Learner Profile</span>
        <h2 class="section-headline">Who Is This Program Designed For?</h2>
        <p style="color: #94a3b8; font-size: 1.05rem;">
          Whether starting your journey or advancing toward executive levels, this curriculum matches your goals.
        </p>
      </div>

      <div class="row g-4">
        <div class="col-lg-3 col-md-6">
          <div class="audience-card">
            <span class="audience-badge">Entry Level</span>
            <h5>Freshers & Graduates</h5>
            <p>Recent college passouts seeking their first corporate breakthrough with job-ready practical training.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="audience-card">
            <span class="audience-badge">Career Shift</span>
            <h5>Career Switchers</h5>
            <p>Professionals transitioning from non-core or saturated fields into high-growth industry domains.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="audience-card">
            <span class="audience-badge">Promotion Track</span>
            <h5>Working Executives</h5>
            <p>Associates aiming for promotions, leadership roles, and significant annual salary increments.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="audience-card">
            <span class="audience-badge">Independence</span>
            <h5>Founders & Freelancers</h5>
            <p>Entrepreneurs and consultants building their own agency or independent client service business.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Interactive Roadmap Curriculum -->
  <section class="neo-section">
    <div class="container">
      <div class="row position-relative">
        <div class="col-lg-4">
          <div class="sticky-roadmap-sidebar">
            <span class="section-eyebrow">Step-By-Step Journey</span>
            <h2 class="section-headline mb-3">Curriculum Roadmap</h2>
            <p style="color: #94a3b8; font-size: 1rem; line-height: 1.6;">
              A progressive roadmap designed to take you from foundational understanding to full-scale enterprise competency.
            </p>

            <div class="mt-4 p-4" style="background: var(--brand-dark-card); border-radius: 16px; border: 1px solid var(--brand-border); box-shadow: none;">
              <h5 class="text-white mb-3" style="font-size: 1rem; font-weight: 700;">
                <i class="fas fa-toolbox text-warning me-2"></i> Technologies & Tooling
              </h5>
              <div class="d-flex flex-wrap gap-2">
                <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } foreach ($c['tools'] as $tool): ?>
                  <span style="background: rgba(230, 169, 16, 0.12); color: var(--brand-primary); font-size: 0.85rem; padding: 5px 12px; border-radius: 6px; border: 1px solid rgba(230, 169, 16, 0.25);">
                    <?= htmlspecialchars($tool) ?>
                  </span>
                <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } endforeach; ?>
              </div>
            </div>

            <div class="mt-4">
              <a href="enrollnow.php?course=<?= urlencode($c['slug']) ?>" class="btn-neo-gold w-100 justify-content-center">
                <i class="fas fa-arrow-right me-2"></i> Enroll in This Batch
              </a>
            </div>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="timeline-wrap">
            <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } foreach ($c['curriculum'] as $module): ?>
              <div class="timeline-item">
                <div class="timeline-num"><?= htmlspecialchars($module['num']) ?></div>
                <div class="timeline-card">
                  <div class="timeline-header">
                    <h4><?= htmlspecialchars($module['title']) ?></h4>
                    <span class="timeline-duration"><?= htmlspecialchars($module['duration']) ?></span>
                  </div>
                  <div class="topic-pills">
                    <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } foreach ($module['topics'] as $topic): ?>
                      <span class="topic-pill">
                        <i class="fas fa-circle-check text-warning me-1"></i> <?= htmlspecialchars($topic) ?>
                      </span>
                    <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } endforeach; ?>
                  </div>
                </div>
              </div>
            <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- NEW SECTION 2: Verified Certification Showcase (Zero Shadows) -->
  <section class="neo-section" style="background: #050b1c; border-top: 1px solid var(--brand-border); border-bottom: 1px solid var(--brand-border);">
    <div class="container">
      <div class="cert-showcase-box">
        <div class="row align-items-center g-5">
          <div class="col-lg-6">
            <span class="section-eyebrow">Credential Recognition</span>
            <h2 style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 700; color: #ffffff; margin-bottom: 18px;">
              Industry-Recognized Professional Certification
            </h2>
            <p style="color: #cbd5e1; font-size: 1rem; line-height: 1.7;">
              Upon successful completion of the <?= htmlspecialchars($c['title']) ?> course and capstone projects, you earn a verified credential accredited by Skill Grow Institution and recognized by 250+ top recruitment partners.
            </p>

            <ul class="cert-benefit-list">
              <li>
                <i class="fas fa-qrcode"></i>
                <span><strong>Instant QR Verification:</strong> Employers can verify your credential authenticity online in seconds.</span>
              </li>
              <li>
                <i class="fas fa-share-nodes"></i>
                <span><strong>LinkedIn Shareable:</strong> One-click credential badge directly integration into your LinkedIn profile.</span>
              </li>
              <li>
                <i class="fas fa-file-shield"></i>
                <span><strong>Physical & Digital Formats:</strong> Receive a high-resolution tamper-proof digital copy and an embossed physical certificate.</span>
              </li>
            </ul>

            <div class="mt-4">
              <a href="enrollnow.php?course=<?= urlencode($c['slug']) ?>" class="btn-neo-gold">
                <i class="fas fa-award me-1"></i> Get Certified Today
              </a>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="cert-preview-frame">
              <img src="assets/images/certi/Certificate.png" alt="Sample Certificate" class="img-fluid">
              <div class="mt-3 text-center">
                <span class="badge bg-dark text-warning border border-warning px-3 py-2 font-weight-bold">
                  <i class="fas fa-check-double me-1"></i> Official Government Aligned Certification
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Career Outcomes & Job Matrix -->
  <section class="neo-section">
    <div class="container">
      <div class="text-center max-w-700 mx-auto mb-5">
        <span class="section-eyebrow">Industry Ready</span>
        <h2 class="section-headline">Target Roles & Market Demand</h2>
        <p style="color: #94a3b8; font-size: 1.05rem;">
          Our placement cell directly connects you with over 250+ corporate hiring partners looking for these exact profiles.
        </p>
      </div>

      <div class="career-grid">
        <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } foreach ($c['careers'] as $job): ?>
          <div class="career-box">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 0.75rem;">
                  Demand: <?= htmlspecialchars($job['demand']) ?>
                </span>
                <i class="fas fa-briefcase text-muted"></i>
              </div>
              <h5><?= htmlspecialchars($job['role']) ?></h5>
            </div>
            <div>
              <div class="career-pkg"><?= htmlspecialchars($job['package']) ?></div>
              <small style="color: #94a3b8;">Expected Annual Package</small>
            </div>
          </div>
        <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } endforeach; ?>
      </div>
    </div>
  </section>

  <!-- NEW SECTION 3: Frequently Asked Questions (FAQ) -->
  <section class="neo-section" style="background: #050b1c; border-top: 1px solid var(--brand-border);">
    <div class="container">
      <div class="text-center max-w-700 mx-auto mb-5">
        <span class="section-eyebrow">Got Doubts?</span>
        <h2 class="section-headline">Frequently Asked Questions</h2>
        <p style="color: #94a3b8; font-size: 1.05rem;">
          Here are quick answers to the most common queries from prospective students.
        </p>
      </div>

      <div class="row justify-content-center">
        <div class="col-lg-9">
          <div class="accordion neo-accordion" id="faqAccordion">
            <div class="accordion-item">
              <h2 class="accordion-header" id="faq1">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq1" aria-expanded="true">
                  How does the 100% placement assistance process work?
                </button>
              </h2>
              <div id="collapseFaq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Our dedicated corporate placement cell assists you right after the midway mark. We conduct mock technical interviews, resume refinement sessions, LinkedIn profiling, and arrange direct interview drives with our 250+ hiring network partners.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="faq2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq2">
                  What if I miss a scheduled class or live session?
                </button>
              </h2>
              <div id="collapseFaq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Every session is recorded in HD and uploaded to your student repository within 24 hours. Additionally, weekend doubt-clearing sessions and 1-on-1 mentor guidance are available so you never fall behind.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="faq3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq3">
                  Can I pay the course fee in flexible installments (EMI)?
                </button>
              </h2>
              <div id="collapseFaq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Yes, Skill Grow Institution provides 0% interest monthly installment (EMI) options and merit-based fee concessions for deserving candidates. Speak to our counselors for customized fee structures.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="faq4">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq4">
                  Will I receive both digital and physical certificates?
                </button>
              </h2>
              <div id="collapseFaq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Yes, upon completing your modules and capstone projects, you receive a tamper-proof digital certificate with an online verification QR code, followed by an embossed hard-copy certificate delivered directly to your address.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modern Clean Bottom Section (Zero Form, Pure Action) -->
  <section class="neo-section" style="background: radial-gradient(circle at center, #0e1e47 0%, #060e22 80%); border-top: 1px solid var(--brand-border);">
    <div class="container text-center py-4">
      <span class="section-eyebrow">Seats Filling Fast</span>
      <h2 style="font-family: var(--font-heading); font-size: 2.8rem; font-weight: 800; color: #ffffff; margin-bottom: 16px;">
        Ready to Transform Your Professional Future?
      </h2>
      <p style="color: #cbd5e1; font-size: 1.15rem; max-width: 650px; margin: 0 auto 36px;">
        Admissions are currently open for the upcoming 2026 cohort. Complete your application in less than 2 minutes.
      </p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="enrollnow.php?course=<?= urlencode($c['slug']) ?>" class="btn-neo-gold">
          <i class="fas fa-user-graduate"></i> Proceed to Direct Admission
        </a>
        <a href="explore-course.php" class="btn-neo-glass">
          <i class="fas fa-th-large"></i> Explore All Other Courses
        </a>
      </div>
    </div>
  </section>

  <!-- Floating Sticky Action Bar (Zero Shadow, Clean Glass) -->
  <div class="floating-action-bar">
    <div class="floating-info">
      <div>
        <p class="floating-title"><?= htmlspecialchars($c['title']) ?></p>
        <p class="floating-sub"><?= htmlspecialchars($c['duration']) ?> • <?= htmlspecialchars($c['mode']) ?></p>
      </div>
    </div>
    <a href="enrollnow.php?course=<?= urlencode($c['slug']) ?>" class="btn-floating-enroll">
      Apply Now <i class="fas fa-arrow-right ms-1"></i>
    </a>
  </div>

  <!-- Common Footer -->
  <?php
if (session_status() === PHP_SESSION_NONE) { session_start(); } include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>