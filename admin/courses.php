<?php
// Course Catalog Management - Skill Grow Admin
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';

$pageTitle = 'Courses Management';
$msg = '';
$msgType = '';

// Handle Delete Course
if (isset($_GET['delete_id'])) {
    $delId = (int)$_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = :id");
        $stmt->execute(['id' => $delId]);
        $msg = 'Course successfully deleted from database!';
        $msgType = 'success';
    } catch (PDOException $e) {
        $msg = 'Error deleting course: ' . $e->getMessage();
        $msgType = 'danger';
    }
}

// Fetch all courses
$stmt = $pdo->query("
    SELECT c.*, cat.name AS category_name
    FROM courses c
    LEFT JOIN course_categories cat ON c.category_id = cat.id
    ORDER BY c.id DESC
");
$courses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Course Catalog Management | Skill Grow Admin</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">

  <!-- Central Unified Admin Stylesheet -->
  <link rel="stylesheet" href="assets/css/admin.css">
</head>

<body>

  <div class="admin-layout-wrapper">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div class="admin-main">
      <header class="admin-topbar">
        <div>
          <h1 class="page-title-text" style="font-size: 1.25rem; font-family: var(--font-heading); margin: 0; font-weight: 700;">Courses Management</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
              <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Admin</a></li>
              <li class="breadcrumb-item active text-warning">Courses</li>
            </ol>
          </nav>
        </div>

        <div class="d-flex gap-2">
          <a href="categories.php" class="btn btn-outline-light btn-sm px-3 py-2 rounded-3">
            <i class="fas fa-folder-tree me-1"></i> Manage Categories
          </a>
          <a href="course-add.php" class="btn-gold btn-sm">
            <i class="fas fa-plus-circle"></i> Add New Course
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

        <div class="bento-card">
          <div class="bento-header">
            <h2 class="bento-title">
              <i class="fas fa-graduation-cap text-warning me-2"></i> All Academic Programs (<?= count($courses) ?>)
            </h2>
            <div>
              <a href="../explore-course.php" target="_blank" class="text-warning text-decoration-none" style="font-size: 0.85rem;">
                <i class="fas fa-external-link-alt me-1"></i> View Live Student Catalog
              </a>
            </div>
          </div>

          <div class="table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Course</th>
                  <th>Category</th>
                  <th>Fee</th>
                  <th>Duration / Mode</th>
                  <th>Rating</th>
                  <th>Salary Range</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($courses)): ?>
                  <?php foreach ($courses as $c): ?>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center gap-3">
                          <img src="../<?= htmlspecialchars($c['image'] ?: 'assets/images/course/web-technology.webp') ?>" alt="Course" class="course-thumb-sm" onerror="this.src='../assets/images/course/web-technology.webp'">
                          <div>
                            <div class="fw-bold text-white"><?= htmlspecialchars($c['title']) ?></div>
                            <small class="text-muted font-monospace"><?= htmlspecialchars($c['slug']) ?></small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-dark border border-secondary text-warning">
                          <?= htmlspecialchars($c['category_name'] ?: 'General Studies') ?>
                        </span>
                      </td>
                      <td>
                        <span class="text-warning fw-bold font-monospace">₹<?= number_format((float)($c['fee'] ?? 14999), 0) ?></span>
                      </td>
                      <td>
                        <div><?= htmlspecialchars($c['duration']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($c['mode']) ?></small>
                      </td>
                      <td>
                        <span class="text-warning fw-bold"><i class="fas fa-star me-1"></i><?= htmlspecialchars($c['rating']) ?></span>
                        <small class="text-muted">(<?= htmlspecialchars($c['reviews']) ?>)</small>
                      </td>
                      <td>
                        <span class="text-success fw-bold" style="font-size: 0.82rem;"><?= htmlspecialchars($c['avg_salary']) ?></span>
                      </td>
                      <td>
                        <span class="badge <?= ($c['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?>">
                          <?= ucfirst($c['status']) ?>
                        </span>
                      </td>
                      <td>
                        <div class="d-flex gap-2">
                          <a href="../course-detail.php?course=<?= urlencode($c['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-info py-1 px-2" title="Preview Live Page">
                            <i class="fas fa-eye"></i>
                          </a>
                          <a href="course-edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-warning py-1 px-2" title="Edit Course Details">
                            <i class="fas fa-edit"></i>
                          </a>
                          <a href="courses.php?delete_id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete Course" onclick="return confirm('Are you sure you want to permanently delete this course?');">
                            <i class="fas fa-trash-alt"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                      No courses found. Click "+ Add New Course" to create your first course.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

        </div>

      </main>

      <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/admin.js"></script>
</body>
</html>
