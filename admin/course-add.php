<?php
// Add New Course - Skill Grow Admin
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';

$pageTitle = 'Add New Course';
$msg = '';
$msgType = '';

// Handle Course Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title          = trim($_POST['title'] ?? '');
    $slug           = trim($_POST['slug'] ?? '');
    $categoryId     = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $tagline        = trim($_POST['tagline'] ?? '');
    $badge          = trim($_POST['badge'] ?? 'Certificate Program');
    $duration       = trim($_POST['duration'] ?? '6 Months');
    $effort         = trim($_POST['effort'] ?? '10 hrs / week');
    $mode           = trim($_POST['mode'] ?? 'Hybrid');
    $fee            = !empty($_POST['fee']) ? floatval($_POST['fee']) : 14999.00;
    $eligibility    = trim($_POST['eligibility'] ?? '10+2 / Any Graduate');
    $language       = trim($_POST['language'] ?? 'English & Hindi');
    $avgSalary      = trim($_POST['avg_salary'] ?? '₹4.5L - ₹9.5L / yr');
    $placementRate  = trim($_POST['placement_rate'] ?? '95% Placed');
    $rating         = trim($_POST['rating'] ?? '4.9');
    $reviews        = trim($_POST['reviews'] ?? '350+');
    $overview       = trim($_POST['overview'] ?? '');
    $status         = trim($_POST['status'] ?? 'active');

    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    }

    // Handle Image Upload
    $imagePath = 'assets/images/course/web-technology.webp';
    if (!empty($_FILES['course_image']['name']) && $_FILES['course_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['course_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
        if (in_array($ext, $allowed)) {
            $newFileName = 'course_' . time() . '_' . mt_rand(100, 999) . '.' . $ext;
            $dest = __DIR__ . '/../uploads/courses/' . $newFileName;
            if (move_uploaded_file($_FILES['course_image']['tmp_name'], $dest)) {
                $imagePath = 'uploads/courses/' . $newFileName;
            }
        }
    } elseif (!empty($_POST['existing_image_path'])) {
        $imagePath = trim($_POST['existing_image_path']);
    }

    // Process Stats (4 boxes)
    $stats = [];
    if (!empty($_POST['stat_val'])) {
        foreach ($_POST['stat_val'] as $idx => $val) {
            $label = $_POST['stat_label'][$idx] ?? '';
            if (!empty($val) || !empty($label)) {
                $stats[] = ['val' => trim($val), 'label' => trim($label)];
            }
        }
    }

    // Process Pillars (4 focus areas)
    $pillars = [];
    if (!empty($_POST['pillar_title'])) {
        foreach ($_POST['pillar_title'] as $idx => $pTitle) {
            if (!empty(trim($pTitle))) {
                $pillars[] = [
                    'icon'  => trim($_POST['pillar_icon'][$idx] ?? 'fa-check-circle'),
                    'title' => trim($pTitle),
                    'desc'  => trim($_POST['pillar_desc'][$idx] ?? '')
                ];
            }
        }
    }

    // Process Curriculum Modules (Dynamic Multi-Modules)
    $curriculum = [];
    if (!empty($_POST['module_title']) && is_array($_POST['module_title'])) {
        $modNum = 1;
        foreach ($_POST['module_title'] as $idx => $mTitle) {
            $trimmedTitle = trim($mTitle);
            if (!empty($trimmedTitle)) {
                $topicsRaw = $_POST['module_topics'][$idx] ?? '';
                $topics = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $topicsRaw))));
                $curriculum[] = [
                    'num'      => sprintf("%02d", $modNum),
                    'title'    => $trimmedTitle,
                    'duration' => trim($_POST['module_duration'][$idx] ?? '4 Weeks'),
                    'topics'   => array_values($topics)
                ];
                $modNum++;
            }
        }
    }

    // Process Careers
    $careers = [];
    if (!empty($_POST['career_role'])) {
        foreach ($_POST['career_role'] as $idx => $role) {
            if (!empty(trim($role))) {
                $careers[] = [
                    'role'    => trim($role),
                    'demand'  => trim($_POST['career_demand'][$idx] ?? 'High'),
                    'package' => trim($_POST['career_package'][$idx] ?? '₹5.0 - 9.0 LPA')
                ];
            }
        }
    }

    // Process Tools (Comma separated)
    $toolsRaw = trim($_POST['tools'] ?? '');
    $tools = array_filter(array_map('trim', explode(',', $toolsRaw)));

    try {
        $stmt = $pdo->prepare("
            INSERT INTO courses (
                category_id, title, slug, tagline, badge, image, rating, reviews,
                duration, effort, mode, fee, eligibility, language, avg_salary, placement_rate,
                overview, stats, pillars, curriculum, careers, tools, status
            ) VALUES (
                :cid, :title, :slug, :tagline, :badge, :image, :rating, :reviews,
                :duration, :effort, :mode, :fee, :eligibility, :language, :avg_salary, :placement_rate,
                :overview, :stats, :pillars, :curriculum, :careers, :tools, :status
            )
        ");

        $stmt->execute([
            'cid'            => $categoryId,
            'title'          => $title,
            'slug'           => $slug,
            'tagline'        => $tagline,
            'badge'          => $badge,
            'image'          => $imagePath,
            'rating'         => $rating,
            'reviews'        => $reviews,
            'duration'       => $duration,
            'effort'         => $effort,
            'mode'           => $mode,
            'fee'            => $fee,
            'eligibility'    => $eligibility,
            'language'       => $language,
            'avg_salary'     => $avgSalary,
            'placement_rate' => $placementRate,
            'overview'       => $overview,
            'stats'          => json_encode($stats),
            'pillars'        => json_encode($pillars),
            'curriculum'     => json_encode($curriculum),
            'careers'        => json_encode($careers),
            'tools'          => json_encode(array_values($tools)),
            'status'         => $status
        ]);

        header("Location: courses.php?msg=" . urlencode("Course '{$title}' added successfully!"));
        exit;

    } catch (PDOException $e) {
        $msg = 'Database Error: ' . $e->getMessage();
        $msgType = 'danger';
    }
}

// Fetch categories for dropdown
$categories = $pdo->query("SELECT id, name FROM course_categories WHERE status = 'active' ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create New Course | Skill Grow Admin</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Central Unified Admin Stylesheet -->
  <link rel="stylesheet" href="assets/css/admin.css">

  <style>
    .dynamic-subcard { background: rgba(6, 11, 27, 0.5); border: 1px solid var(--admin-border); border-radius: 12px; padding: 16px; margin-bottom: 14px; }
  </style>
</head>

<body>

  <div class="admin-layout-wrapper">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div class="admin-main">
      <header class="admin-topbar">
        <div>
          <h1 class="page-title-text" style="font-size: 1.25rem; font-family: var(--font-heading); margin: 0; font-weight: 700;">Create Course Program</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
              <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Admin</a></li>
              <li class="breadcrumb-item"><a href="courses.php" class="text-muted text-decoration-none">Courses</a></li>
              <li class="breadcrumb-item active text-warning">Add Course</li>
            </ol>
          </nav>
        </div>

        <div>
          <a href="courses.php" class="btn btn-outline-light btn-sm px-3 py-2 rounded-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Courses
          </a>
        </div>
      </header>

      <main class="admin-content">
        
        <?php if (!empty($msg)): ?>
          <div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert" style="border-radius: 12px;">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form method="POST" action="course-add.php" enctype="multipart/form-data">
          
          <!-- SECTION 1: BASIC INFORMATION -->
          <div class="bento-card">
            <div class="bento-header">
              <h2 class="bento-title"><i class="fas fa-info-circle text-warning me-2"></i> 1. Basic Course Details</h2>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Course Title *</label>
                <input type="text" class="form-control" name="title" placeholder="e.g. Full Stack Cloud & AI Engineering" required id="courseTitle" oninput="autoSlug(this.value)">
              </div>

              <div class="col-md-6">
                <label class="form-label">URL Slug * (Unique)</label>
                <input type="text" class="form-control font-monospace" name="slug" placeholder="e.g. full-stack-cloud-ai" required id="courseSlug">
              </div>

              <div class="col-md-4">
                <label class="form-label">Category *</label>
                <select class="form-select" name="category_id" required>
                  <option value="" disabled selected>Select Category</option>
                  <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label">Program Badge / Tag</label>
                <input type="text" class="form-control" name="badge" value="Flagship Program" placeholder="e.g. Flagship Program, Executive Cohort">
              </div>

              <div class="col-md-4">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                  <option value="active" selected>Active (Published)</option>
                  <option value="inactive">Inactive (Draft)</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Short Tagline / Subtitle *</label>
                <input type="text" class="form-control" name="tagline" placeholder="e.g. Architect enterprise digital systems with modern technologies and hands-on drills." required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Course Thumbnail / Banner Upload</label>
                <input type="file" class="form-control" name="course_image" accept="image/*">
                <small class="text-muted" style="font-size: 0.75rem;">Recommended size: 800x500px (JPG, PNG, WebP)</small>
              </div>

              <div class="col-md-6">
                <label class="form-label">Or Existing Image Path</label>
                <input type="text" class="form-control font-monospace" name="existing_image_path" value="assets/images/course/web-technology.webp">
              </div>

              <div class="col-12">
                <label class="form-label">Comprehensive Program Overview (Narrative) *</label>
                <textarea class="form-control" name="overview" rows="4" placeholder="Detailed paragraph explaining what the course offers, methodology, and learning outcomes..." required></textarea>
              </div>
            </div>
          </div>

          <!-- SECTION 2: KEY SPECIFICATIONS & SALARY -->
          <div class="bento-card">
            <div class="bento-header">
              <h2 class="bento-title"><i class="fas fa-sliders text-warning me-2"></i> 2. Specs & Career Metrics</h2>
            </div>

            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Duration</label>
                <input type="text" class="form-control" name="duration" value="6 Months" placeholder="e.g. 6 Months, 1 Year">
              </div>

              <div class="col-md-3">
                <label class="form-label">Weekly Effort</label>
                <input type="text" class="form-control" name="effort" value="10-12 hrs / week">
              </div>

              <div class="col-md-3">
                <label class="form-label">Learning Mode</label>
                <input type="text" class="form-control" name="mode" value="Hybrid (Live + Campus)">
              </div>

              <div class="col-md-3">
                <label class="form-label text-warning fw-bold"><i class="fas fa-indian-rupee-sign me-1"></i> Course Fee (₹) *</label>
                <input type="number" step="100" class="form-control text-warning fw-bold" name="fee" value="14999" placeholder="14999" required>
              </div>

              <div class="col-md-3">
                <label class="form-label">Eligibility</label>
                <input type="text" class="form-control" name="eligibility" value="10+2 / Any Graduate">
              </div>

              <div class="col-md-3">
                <label class="form-label">Instruction Language</label>
                <input type="text" class="form-control" name="language" value="English & Hindi">
              </div>

              <div class="col-md-3">
                <label class="form-label">Average Expected Salary</label>
                <input type="text" class="form-control text-success fw-bold" name="avg_salary" value="₹4.5L - ₹9.5L / yr">
              </div>

              <div class="col-md-3">
                <label class="form-label">Placement Success Rate</label>
                <input type="text" class="form-control text-warning fw-bold" name="placement_rate" value="95% Placed">
              </div>

              <div class="col-md-3">
                <label class="form-label">Student Rating & Reviews</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="rating" value="4.9" placeholder="4.9">
                  <input type="text" class="form-control" name="reviews" value="450+" placeholder="450+">
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION 3: 4 KEY STAT HIGHLIGHTS -->
          <div class="bento-card">
            <div class="bento-header">
              <h2 class="bento-title"><i class="fas fa-chart-simple text-warning me-2"></i> 3. Key Stat Badges (4 Display Badges)</h2>
            </div>

            <div class="row g-3">
              <?php 
              $defaultStats = [
                ['val' => '10+', 'label' => 'Live Enterprise Projects'],
                ['val' => '95%', 'label' => 'Placement Success Rate'],
                ['val' => '1:1', 'label' => 'Industry Mentoring Sessions'],
                ['val' => '100%', 'label' => 'Practical Hands-on Labs']
              ];
              foreach ($defaultStats as $i => $st): 
              ?>
                <div class="col-md-3">
                  <div class="dynamic-subcard">
                    <label class="form-label">Badge <?= $i + 1 ?> Value</label>
                    <input type="text" class="form-control mb-2 fw-bold text-warning" name="stat_val[]" value="<?= htmlspecialchars($st['val']) ?>">
                    <label class="form-label">Badge <?= $i + 1 ?> Label</label>
                    <input type="text" class="form-control" name="stat_label[]" value="<?= htmlspecialchars($st['label']) ?>">
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- SECTION 4: 4 CORE PILLARS -->
          <div class="bento-card">
            <div class="bento-header">
              <h2 class="bento-title"><i class="fas fa-layer-group text-warning me-2"></i> 4. Four Core Pillars / Focus Areas</h2>
            </div>

            <div class="row g-3">
              <?php 
              $defaultPillars = [
                ['icon' => 'fa-laptop-code', 'title' => 'Frontend Engineering', 'desc' => 'High-performance interactive responsive interfaces with HTML5, CSS3 and JavaScript.'],
                ['icon' => 'fa-server', 'title' => 'Server & Backend APIs', 'desc' => 'Secure enterprise RESTful application architecture, session management, and microservices.'],
                ['icon' => 'fa-database', 'title' => 'Database Optimization', 'desc' => 'Relational database schema modeling, queries, indexing, and data security.'],
                ['icon' => 'fa-cloud-arrow-up', 'title' => 'Cloud & Deployment', 'desc' => 'CI/CD pipeline workflows, production server hardening, and live deployment.']
              ];
              foreach ($defaultPillars as $i => $pil):
              ?>
                <div class="col-md-6">
                  <div class="dynamic-subcard">
                    <div class="row g-2">
                      <div class="col-4">
                        <label class="form-label">FA Icon</label>
                        <input type="text" class="form-control font-monospace" name="pillar_icon[]" value="<?= htmlspecialchars($pil['icon']) ?>">
                      </div>
                      <div class="col-8">
                        <label class="form-label">Pillar Title</label>
                        <input type="text" class="form-control fw-bold" name="pillar_title[]" value="<?= htmlspecialchars($pil['title']) ?>">
                      </div>
                      <div class="col-12 mt-2">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="pillar_desc[]" value="<?= htmlspecialchars($pil['desc']) ?>">
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- SECTION 5: CURRICULUM ROADMAP MODULES -->
          <div class="bento-card">
            <div class="bento-header d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div>
                <h2 class="bento-title mb-0"><i class="fas fa-route text-warning me-2"></i> 5. Curriculum Roadmap Modules</h2>
                <div class="text-muted" style="font-size: 0.78rem;">Add, customize, or delete as many learning roadmap modules as needed.</div>
              </div>
              <button type="button" class="btn btn-warning btn-sm fw-bold px-3 py-2" onclick="addCurriculumModule()">
                <i class="fas fa-plus-circle me-1"></i> Add Module
              </button>
            </div>

            <div id="curriculumContainer">
              <?php 
              $defaultModules = [
                ['title' => 'Foundations & Architecture', 'dur' => '4 Weeks', 'topics' => "Semantic HTML5 & Modern Layouts\nResponsive Flexbox & CSS Grid\nGit & GitHub Version Control"],
                ['title' => 'Interactive Applications & State', 'dur' => '6 Weeks', 'topics' => "Modern ES6+ JavaScript\nDOM Manipulation & Event Handlers\nAsync/Await & Fetch APIs"],
                ['title' => 'Backend Engineering & Relational DBs', 'dur' => '6 Weeks', 'topics' => "PHP 8 & Object-Oriented Principles\nMySQL Schema Design & PDO Security\nRESTful API Architecture"],
                ['title' => 'Capstone Project & Production Launch', 'dur' => '4 Weeks', 'topics' => "End-to-End Enterprise Project\nAuthentication & Role-Based Access\nServer Hardening & Live Deployment"]
              ];
              foreach ($defaultModules as $i => $mod):
              ?>
                <div class="dynamic-subcard curriculum-module-card mb-3 p-3" style="background: #070e24; border: 1px solid #1e2c52; border-radius: 10px;">
                  <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary">
                    <span class="badge bg-warning text-dark font-monospace fw-bold px-2 py-1 module-badge-num">Module <?= sprintf("%02d", $i + 1) ?></span>
                    <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2" onclick="removeCurriculumModule(this)" title="Delete this module">
                      <i class="fas fa-trash-alt me-1"></i> Delete Module
                    </button>
                  </div>
                  <div class="row g-2">
                    <div class="col-md-8">
                      <label class="form-label" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #cbd5e1;">Module Title</label>
                      <input type="text" class="form-control form-control-sm text-white fw-bold" style="background: #04091a; border-color: #2a3c6d;" name="module_title[]" value="<?= htmlspecialchars($mod['title']) ?>" placeholder="e.g. System Architecture & Development" required>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #cbd5e1;">Module Duration</label>
                      <input type="text" class="form-control form-control-sm text-white" style="background: #04091a; border-color: #2a3c6d;" name="module_duration[]" value="<?= htmlspecialchars($mod['dur']) ?>" placeholder="e.g. 4 Weeks">
                    </div>
                    <div class="col-12 mt-2">
                      <label class="form-label" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #cbd5e1;">Topics Covered (One per line)</label>
                      <textarea class="form-control form-control-sm text-white" style="background: #04091a; border-color: #2a3c6d;" name="module_topics[]" rows="3" placeholder="Topic 1&#10;Topic 2&#10;Topic 3"><?= htmlspecialchars($mod['topics']) ?></textarea>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <div id="emptyCurriculumNotice" class="text-center py-4 text-muted" style="display: none;">
              <i class="fas fa-route fa-2x mb-2 text-secondary"></i>
              <div>No curriculum modules added yet. Click <strong>"+ Add Module"</strong> above to add your first module.</div>
            </div>
          </div>

          <!-- SECTION 6: CAREER MATRIX & TOOLS -->
          <div class="bento-card">
            <div class="bento-header">
              <h2 class="bento-title"><i class="fas fa-briefcase text-warning me-2"></i> 6. Career Roles & Industry Tools</h2>
            </div>

            <div class="row g-3">
              <div class="col-md-7">
                <label class="form-label mb-2">Target Job Profiles (Role, Demand, Salary Package)</label>
                <?php 
                $defaultCareers = [
                  ['role' => 'Full Stack Developer', 'demand' => 'Very High', 'pkg' => '₹5.5 - 12 LPA'],
                  ['role' => 'Frontend Software Engineer', 'demand' => 'High', 'pkg' => '₹4.8 - 9.0 LPA'],
                  ['role' => 'Backend & API Engineer', 'demand' => 'High', 'pkg' => '₹5.0 - 10 LPA'],
                  ['role' => 'Web Application Architect', 'demand' => 'Surging', 'pkg' => '₹7.0 - 14 LPA']
                ];
                foreach ($defaultCareers as $cr):
                ?>
                  <div class="row g-2 mb-2">
                    <div class="col-5">
                      <input type="text" class="form-control" name="career_role[]" value="<?= htmlspecialchars($cr['role']) ?>" placeholder="Job Title">
                    </div>
                    <div class="col-3">
                      <input type="text" class="form-control" name="career_demand[]" value="<?= htmlspecialchars($cr['demand']) ?>" placeholder="Demand">
                    </div>
                    <div class="col-4">
                      <input type="text" class="form-control text-success fw-bold" name="career_package[]" value="<?= htmlspecialchars($cr['pkg']) ?>" placeholder="Salary">
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="col-md-5">
                <label class="form-label">Technologies & Tools (Comma Separated)</label>
                <textarea class="form-control" name="tools" rows="5" placeholder="e.g. HTML5, CSS3, JavaScript, PHP 8, MySQL, Bootstrap 5, Git, Postman, Docker, Apache">HTML5, CSS3, JavaScript, PHP 8, MySQL, Git, GitHub, Postman, Bootstrap 5, REST APIs</textarea>
                <small class="text-muted" style="font-size: 0.75rem;">These will be shown in the Technologies & Tooling pills section.</small>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="text-end mb-5">
            <button type="submit" class="btn-gold px-4 py-3">
              <i class="fas fa-check-circle me-1"></i> Publish Course to Website
            </button>
          </div>

        </form>

      </main>

      <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </div>
  </div>

  <script>
    function autoSlug(text) {
      const slug = text.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
      document.getElementById('courseSlug').value = slug;
    }

    // Dynamic Curriculum Modules Add & Delete
    function updateModuleNumbers() {
      const container = document.getElementById('curriculumContainer');
      const cards = container.querySelectorAll('.curriculum-module-card');
      const emptyNotice = document.getElementById('emptyCurriculumNotice');
      
      if (emptyNotice) {
        emptyNotice.style.display = (cards.length === 0) ? 'block' : 'none';
      }

      cards.forEach((card, index) => {
        const num = String(index + 1).padStart(2, '0');
        const badge = card.querySelector('.module-badge-num');
        if (badge) badge.innerText = 'Module ' + num;
      });
    }

    function addCurriculumModule(title = '', duration = '4 Weeks', topics = '') {
      const container = document.getElementById('curriculumContainer');
      const card = document.createElement('div');
      card.className = 'dynamic-subcard curriculum-module-card mb-3 p-3';
      card.style.background = '#070e24';
      card.style.border = '1px solid #1e2c52';
      card.style.borderRadius = '10px';
      
      card.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary">
          <span class="badge bg-warning text-dark font-monospace fw-bold px-2 py-1 module-badge-num">Module</span>
          <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2" onclick="removeCurriculumModule(this)" title="Delete this module">
            <i class="fas fa-trash-alt me-1"></i> Delete Module
          </button>
        </div>
        <div class="row g-2">
          <div class="col-md-8">
            <label class="form-label" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #cbd5e1;">Module Title</label>
            <input type="text" class="form-control form-control-sm text-white fw-bold" style="background: #04091a; border-color: #2a3c6d;" name="module_title[]" value="${title}" placeholder="e.g. Advanced System Architecture" required>
          </div>
          <div class="col-md-4">
            <label class="form-label" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #cbd5e1;">Module Duration</label>
            <input type="text" class="form-control form-control-sm text-white" style="background: #04091a; border-color: #2a3c6d;" name="module_duration[]" value="${duration}" placeholder="e.g. 4 Weeks">
          </div>
          <div class="col-12 mt-2">
            <label class="form-label" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #cbd5e1;">Topics Covered (One per line)</label>
            <textarea class="form-control form-control-sm text-white" style="background: #04091a; border-color: #2a3c6d;" name="module_topics[]" rows="3" placeholder="Topic 1&#10;Topic 2&#10;Topic 3">${topics}</textarea>
          </div>
        </div>
      `;
      container.appendChild(card);
      updateModuleNumbers();

      const titleInput = card.querySelector('input[name="module_title[]"]');
      if (titleInput) {
        titleInput.focus();
      }
    }

    function removeCurriculumModule(btn) {
      if (confirm('Are you sure you want to delete this curriculum module?')) {
        const card = btn.closest('.curriculum-module-card');
        if (card) {
          card.remove();
          updateModuleNumbers();
        }
      }
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/admin.js"></script>
</body>
</html>
