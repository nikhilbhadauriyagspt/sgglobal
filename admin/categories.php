<?php
// Course Categories Management - Skill Grow Admin
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';

$pageTitle = 'Course Categories';
$msg = '';
$msgType = '';

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $name   = trim($_POST['name'] ?? '');
        $slug   = trim($_POST['slug'] ?? '');
        $icon   = trim($_POST['icon'] ?? 'fa-graduation-cap');
        $status = trim($_POST['status'] ?? 'active');

        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }

        if (!empty($name)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO course_categories (name, slug, icon, status) VALUES (:n, :s, :i, :st)");
                $stmt->execute(['n' => $name, 's' => $slug, 'i' => $icon, 'st' => $status]);
                $msg = 'Category added successfully!';
                $msgType = 'success';
            } catch (PDOException $e) {
                $msg = 'Error adding category: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    } elseif ($_POST['action'] === 'edit') {
        $id     = (int)($_POST['id'] ?? 0);
        $name   = trim($_POST['name'] ?? '');
        $slug   = trim($_POST['slug'] ?? '');
        $icon   = trim($_POST['icon'] ?? 'fa-graduation-cap');
        $status = trim($_POST['status'] ?? 'active');

        if ($id > 0 && !empty($name)) {
            try {
                $stmt = $pdo->prepare("UPDATE course_categories SET name = :n, slug = :s, icon = :i, status = :st WHERE id = :id");
                $stmt->execute(['n' => $name, 's' => $slug, 'i' => $icon, 'st' => $status, 'id' => $id]);
                $msg = 'Category updated successfully!';
                $msgType = 'success';
            } catch (PDOException $e) {
                $msg = 'Error updating category: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM course_categories WHERE id = :id");
                $stmt->execute(['id' => $id]);
                $msg = 'Category removed successfully!';
                $msgType = 'success';
            } catch (PDOException $e) {
                $msg = 'Error deleting category: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    }
}

// Fetch all categories with count of courses
$stmt = $pdo->query("
    SELECT cat.*, COUNT(c.id) AS total_courses
    FROM course_categories cat
    LEFT JOIN courses c ON cat.id = c.category_id
    GROUP BY cat.id
    ORDER BY cat.id ASC
");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Course Categories | Skill Grow Admin</title>
  
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
          <h1 class="page-title-text" style="font-size: 1.25rem; font-family: var(--font-heading); margin: 0; font-weight: 700;">Course Categories Management</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
              <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Admin</a></li>
              <li class="breadcrumb-item"><a href="courses.php" class="text-muted text-decoration-none">Courses</a></li>
              <li class="breadcrumb-item active text-warning">Categories</li>
            </ol>
          </nav>
        </div>
        <div class="d-flex gap-2">
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

        <div class="row g-4">
          <!-- Left: Add New Category Form (4 cols) -->
          <div class="col-lg-4">
            <div class="bento-card">
              <div class="bento-header">
                <h3 class="bento-title"><i class="fas fa-plus-circle text-warning me-1"></i> Add Category</h3>
              </div>
              <form method="POST" action="categories.php">
                <input type="hidden" name="action" value="add">
                
                <div class="mb-3">
                  <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Category Name *</label>
                  <input type="text" class="form-control" name="name" placeholder="e.g. Artificial Intelligence & Cloud" required id="catNameInput" oninput="autoSlug(this.value)">
                </div>

                <div class="mb-3">
                  <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">URL Slug *</label>
                  <input type="text" class="form-control font-monospace" name="slug" placeholder="e.g. ai-cloud" required id="catSlugInput">
                </div>

                <div class="mb-3">
                  <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">FontAwesome Icon</label>
                  <input type="text" class="form-control" name="icon" value="fa-graduation-cap" placeholder="fa-laptop-code, fa-plane, etc.">
                </div>

                <div class="mb-3">
                  <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Status</label>
                  <select class="form-select" name="status">
                    <option value="active" selected>Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                </div>

                <button type="submit" class="btn-gold w-100">
                  <i class="fas fa-plus me-1"></i> Save Category
                </button>
              </form>
            </div>
          </div>

          <!-- Right: List All Categories (8 cols) -->
          <div class="col-lg-8">
            <div class="bento-card">
              <div class="bento-header">
                <h3 class="bento-title"><i class="fas fa-folder-tree text-warning me-1"></i> Active Categories (<?= count($categories) ?>)</h3>
              </div>

              <div class="table-responsive">
                <table class="admin-table">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Category Name</th>
                      <th>Slug</th>
                      <th>Active Courses</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($categories as $cat): ?>
                      <tr>
                        <td>#<?= $cat['id'] ?></td>
                        <td>
                          <div class="d-flex align-items-center gap-2">
                            <i class="fas <?= htmlspecialchars($cat['icon'] ?: 'fa-folder') ?> text-warning"></i>
                            <span class="fw-bold text-white"><?= htmlspecialchars($cat['name']) ?></span>
                          </div>
                        </td>
                        <td class="font-monospace text-muted" style="font-size: 0.82rem;"><?= htmlspecialchars($cat['slug']) ?></td>
                        <td>
                          <span class="badge bg-dark border border-secondary px-2 py-1 text-warning">
                            <?= $cat['total_courses'] ?> Courses
                          </span>
                        </td>
                        <td>
                          <span class="badge <?= ($cat['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?>">
                            <?= ucfirst($cat['status']) ?>
                          </span>
                        </td>
                        <td>
                          <form method="POST" action="categories.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category? Associated courses will be set to uncategorized.');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete">
                              <i class="fas fa-trash-alt"></i>
                            </button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>

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
      document.getElementById('catSlugInput').value = slug;
    }
  </script>
  <script src="assets/js/admin.js"></script>
</body>
</html>
