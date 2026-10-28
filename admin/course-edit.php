<?php
// Edit Existing Course - Skill Grow Admin
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';

$pageTitle = 'Edit Course';
$msg = '';
$msgType = '';

$courseId = (int)($_GET['id'] ?? 0);
if ($courseId <= 0) {
    header("Location: courses.php");
    exit;
}

// Fetch current course
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $courseId]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: courses.php");
    exit;
}

// Handle Update
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

    // Handle Image Upload
    $imagePath = $course['image'];
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

    // Process Stats
    $stats = [];
    if (!empty($_POST['stat_val'])) {
        foreach ($_POST['stat_val'] as $idx => $val) {
            $label = $_POST['stat_label'][$idx] ?? '';
            if (!empty($val) || !empty($label)) {
                $stats[] = ['val' => trim($val), 'label' => trim($label)];
            }
        }
    }

    // Process Pillars
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

    // Process Curriculum Modules
    $curriculum = [];
    if (!empty($_POST['module_title'])) {
        foreach ($_POST['module_title'] as $idx => $mTitle) {
            if (!empty(trim($mTitle))) {
                $topicsRaw = $_POST['module_topics'][$idx] ?? '';
                $topics = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $topicsRaw))));
                $curriculum[] = [
                    'num'      => sprintf("%02d", $idx + 1),
                    'title'    => trim($mTitle),
                    'duration' => trim($_POST['module_duration'][$idx] ?? '4 Weeks'),
                    'topics'   => array_values($topics)
                ];
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

    // Process Tools
    $toolsRaw = trim($_POST['tools'] ?? '');
    $tools = array_filter(array_map('trim', explode(',', $toolsRaw)));

    try {
        $stmt = $pdo->prepare("
            UPDATE courses SET
                category_id    = :cid,
                title          = :title,
                slug           = :slug,
                tagline        = :tagline,
                badge          = :badge,
                image          = :image,
                rating         = :rating,
                reviews        = :reviews,
                duration       = :duration,
                effort         = :effort,
                mode           = :mode,
                fee            = :fee,
                eligibility    = :eligibility,
                language       = :language,
                avg_salary     = :avg_salary,
                placement_rate = :placement_rate,
                overview       = :overview,
                stats          = :stats,
                pillars        = :pillars,
                curriculum     = :curriculum,
                careers        = :careers,
                tools          = :tools,
                status         = :status
            WHERE id = :id
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
            'status'         => $status,
            'id'             => $courseId
        ]);

        $msg = 'Course updated successfully!';
        $msgType = 'success';

        // Refresh course data
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $courseId]);
        $course = $stmt->fetch();

    } catch (PDOException $e) {
        $msg = 'Database Error: ' . $e->getMessage();
        $msgType = 'danger';
    }
}

// Unpack JSONs
$savedStats      = json_decode($course['stats'] ?? '[]', true) ?: [];
$savedPillars    = json_decode($course['pillars'] ?? '[]', true) ?: [];
$savedCurriculum = json_decode($course['curriculum'] ?? '[]', true) ?: [];
$savedCareers    = json_decode($course['careers'] ?? '[]', true) ?: [];
$savedTools      = json_decode($course['tools'] ?? '[]', true) ?: [];
$toolsStr        = implode(', ', $savedTools);

$categories = $pdo->query("SELECT id, name FROM course_categories WHERE status = 'active' ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Course: <?= htmlspecialchars($course['title']) ?> | Skill Grow Admin</title>
  
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
          <h1 class="page-title-text" style="font-size: 1.25rem; font-family: var(--font-heading); margin: 0; font-weight: 700;">Edit Course: <?= htmlspecialchars($course['title']) ?></h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
              <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Admin</a></li>
              <li class="breadcrumb-item"><a href="courses.php" class="text-muted text-decoration-none">Courses</a></li>
              <li class="breadcrumb-item active text-warning">Edit</li>
            </ol>
          </nav>
        </div>

        <div class="d-flex gap-2">
          <a href="../course-detail.php?course=<?= urlencode($course['slug']) ?>" target="_blank" class="btn btn-outline-warning btn-sm px-3 py-2 rounded-3">
            <i class="fas fa-eye me-1"></i> Preview Live
          </a>
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

        <form method="POST" action="course-edit.php?id=<?= $course['id'] ?>" enctype="multipart/form-data">
          
          <!-- SECTION 1: BASIC INFORMATION -->
          <div class="bento-card">
            <div class="bento-header">
              <h2 class="bento-title"><i class="fas fa-info-circle text-warning me-2"></i> 1. Basic Course Details</h2>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Course Title *</label>
                <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($course['title']) ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">URL Slug * (Unique)</label>
                <input type="text" class="form-control font-monospace" name="slug" value="<?= htmlspecialchars($course['slug']) ?>" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">Category *</label>
                <select class="form-select" name="category_id" required>
                  <option value="" disabled>Select Category</option>
                  <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($course['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($cat['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label">Program Badge / Tag</label>
                <input type="text" class="form-control" name="badge" value="<?= htmlspecialchars($course['badge']) ?>">
              </div>

              <div class="col-md-4">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                  <option value="active" <?= ($course['status'] === 'active') ? 'selected' : '' ?>>Active (Published)</option>
                  <option value="inactive" <?= ($course['status'] === 'inactive') ? 'selected' : '' ?>>Inactive (Draft)</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Short Tagline / Subtitle *</label>
                <input type="text" class="form-control" name="tagline" value="<?= htmlspecialchars($course['tagline']) ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Update Thumbnail / Banner Upload</label>
                <input type="file" class="form-control" name="course_image" accept="image/*">
                <small class="text-muted" style="font-size: 0.75rem;">Current: <?= htmlspecialchars($course['image']) ?></small>
              </div>

              <div class="col-md-6">
                <label class="form-label">Or Image Path</label>
                <input type="text" class="form-control font-monospace" name="existing_image_path" value="<?= htmlspecialchars($course['image']) ?>">
              </div>

              <div class="col-12">
                <label class="form-label">Comprehensive Program Overview (Narrative) *</label>
                <textarea class="form-control" name="overview" rows="4" required><?= htmlspecialchars($course['overview']) ?></textarea>
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
                <input type="text" class="form-control" name="duration" value="<?= htmlspecialchars($course['duration']) ?>">
              </div>

              <div class="col-md-3">
                <label class="form-label">Weekly Effort</label>
                <input type="text" class="form-control" name="effort" value="<?= htmlspecialchars($course['effort']) ?>">
              </div>

              <div class="col-md-3">
                <label class="form-label">Learning Mode</label>
                <input type="text" class="form-control" name="mode" value="<?= htmlspecialchars($course['mode']) ?>">
              </div>

              <div class="col-md-3">
                <label class="form-label text-warning fw-bold"><i class="fas fa-indian-rupee-sign me-1"></i> Course Fee (₹) *</label>
                <input type="number" step="100" class="form-control text-warning fw-bold" name="fee" value="<?= htmlspecialchars((string)($course['fee'] ?? 14999)) ?>" required>
              </div>

              <div class="col-md-3">
                <label class="form-label">Eligibility</label>
                <input type="text" class="form-control" name="eligibility" value="<?= htmlspecialchars($course['eligibility']) ?>">
              </div>

              <div class="col-md-3">
                <label class="form-label">Instruction Language</label>
                <input type="text" class="form-control" name="language" value="<?= htmlspecialchars($course['language']) ?>">
              </div>

              <div class="col-md-3">
                <label class="form-label">Average Expected Salary</label>
                <input type="text" class="form-control text-success fw-bold" name="avg_salary" value="<?= htmlspecialchars($course['avg_salary']) ?>">
              </div>

              <div class="col-md-3">
                <label class="form-label">Placement Success Rate</label>
                <input type="text" class="form-control text-warning fw-bold" name="placement_rate" value="<?= htmlspecialchars($course['placement_rate']) ?>">
              </div>

              <div class="col-md-3">
                <label class="form-label">Student Rating & Reviews</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="rating" value="<?= htmlspecialchars($course['rating']) ?>">
                  <input type="text" class="form-control" name="reviews" value="<?= htmlspecialchars($course['reviews']) ?>">
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
              for ($i = 0; $i < 4; $i++): 
                $st = $savedStats[$i] ?? ['val' => '', 'label' => ''];
              ?>
                <div class="col-md-3">
                  <div class="dynamic-subcard">
                    <label class="form-label">Badge <?= $i + 1 ?> Value</label>
                    <input type="text" class="form-control mb-2 fw-bold text-warning" name="stat_val[]" value="<?= htmlspecialchars($st['val']) ?>">
                    <label class="form-label">Badge <?= $i + 1 ?> Label</label>
                    <input type="text" class="form-control" name="stat_label[]" value="<?= htmlspecialchars($st['label']) ?>">
                  </div>
                </div>
              <?php endfor; ?>
            </div>
          </div>

          <!-- SECTION 4: 4 CORE PILLARS -->
          <div class="bento-card">
            <div class="bento-header">
              <h2 class="bento-title"><i class="fas fa-layer-group text-warning me-2"></i> 4. Four Core Pillars / Focus Areas</h2>
            </div>

            <div class="row g-3">
              <?php 
              for ($i = 0; $i < 4; $i++):
                $pil = $savedPillars[$i] ?? ['icon' => 'fa-check-circle', 'title' => '', 'desc' => ''];
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
              <?php endfor; ?>
            </div>
          </div>

          <!-- SECTION 5: CURRICULUM ROADMAP MODULES -->
          <div class="bento-card">
            <div class="bento-header">
              <h2 class="bento-title"><i class="fas fa-route text-warning me-2"></i> 5. Curriculum Roadmap Modules</h2>
            </div>

            <div id="curriculumContainer">
              <?php 
              for ($i = 0; $i < 5; $i++):
                $mod = $savedCurriculum[$i] ?? ['title' => '', 'duration' => '', 'topics' => []];
                $topicsText = is_array($mod['topics']) ? implode("\n", $mod['topics']) : '';
              ?>
                <div class="dynamic-subcard">
                  <div class="row g-2">
                    <div class="col-md-8">
                      <label class="form-label">Module <?= $i + 1 ?> Title</label>
                      <input type="text" class="form-control fw-bold" name="module_title[]" value="<?= htmlspecialchars($mod['title']) ?>">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Module Duration</label>
                      <input type="text" class="form-control" name="module_duration[]" value="<?= htmlspecialchars($mod['duration'] ?? '4 Weeks') ?>">
                    </div>
                    <div class="col-12 mt-2">
                      <label class="form-label">Topics Covered (One per line)</label>
                      <textarea class="form-control" name="module_topics[]" rows="3"><?= htmlspecialchars($topicsText) ?></textarea>
                    </div>
                  </div>
                </div>
              <?php endfor; ?>
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
                for ($i = 0; $i < 4; $i++):
                  $cr = $savedCareers[$i] ?? ['role' => '', 'demand' => 'High', 'package' => ''];
                ?>
                  <div class="row g-2 mb-2">
                    <div class="col-5">
                      <input type="text" class="form-control" name="career_role[]" value="<?= htmlspecialchars($cr['role']) ?>" placeholder="Job Title">
                    </div>
                    <div class="col-3">
                      <input type="text" class="form-control" name="career_demand[]" value="<?= htmlspecialchars($cr['demand']) ?>" placeholder="Demand">
                    </div>
                    <div class="col-4">
                      <input type="text" class="form-control text-success fw-bold" name="career_package[]" value="<?= htmlspecialchars($cr['package']) ?>" placeholder="Salary">
                    </div>
                  </div>
                <?php endfor; ?>
              </div>

              <div class="col-md-5">
                <label class="form-label">Technologies & Tools (Comma Separated)</label>
                <textarea class="form-control" name="tools" rows="5" placeholder="e.g. Jira, Python, React, MySQL"><?= htmlspecialchars($toolsStr) ?></textarea>
                <small class="text-muted" style="font-size: 0.75rem;">These will be shown in the Technologies & Tooling pills section.</small>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="text-end mb-5">
            <button type="submit" class="btn-gold px-4 py-3">
              <i class="fas fa-save me-1"></i> Update & Publish Changes
            </button>
          </div>

        </form>

      </main>

      <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/admin.js"></script>
</body>
</html>
