<?php
// Student Testimonials Management - Skill Grow Admin
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/../includes/testimonials_helper.php';

ensureTestimonialsTable($pdo);

$pageTitle = 'Student Testimonials';
$msg = '';
$msgType = '';

function handleTestimonialImageUpload(string $inputName, string $existingPath = 'assets/images/testi/rahul-sharma.jpg'): string {
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return $existingPath;
    }
    $tmpName = $_FILES[$inputName]['tmp_name'];
    $origName = basename($_FILES[$inputName]['name']);
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
    if (!in_array($ext, $allowed, true)) {
        return $existingPath;
    }
    $targetDir = dirname(__DIR__) . '/assets/images/testi';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }
    $safeName = 'testi_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $destFull = $targetDir . '/' . $safeName;
    if (move_uploaded_file($tmpName, $destFull)) {
        return 'assets/images/testi/' . $safeName;
    }
    return $existingPath;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add') {
        $name   = trim($_POST['student_name'] ?? '');
        $age    = trim($_POST['student_age'] ?? '');
        $desig  = trim($_POST['student_designation'] ?? '');
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        $desc   = trim($_POST['description'] ?? '');
        $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
        $img    = handleTestimonialImageUpload('photo', 'assets/images/testi/rahul-sharma.jpg');

        if (!empty($name) && !empty($desc)) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO testimonials (student_name, student_age, student_designation, rating, description, image_path, status)
                    VALUES (:name, :age, :desig, :rating, :desc, :img, :status)
                ");
                $stmt->execute([
                    'name'   => $name,
                    'age'    => $age,
                    'desig'  => $desig,
                    'rating' => $rating,
                    'desc'   => $desc,
                    'img'    => $img,
                    'status' => $status,
                ]);
                $msg = 'Testimonial added successfully!';
                $msgType = 'success';
            } catch (PDOException $e) {
                $msg = 'Error adding testimonial: ' . $e->getMessage();
                $msgType = 'danger';
            }
        } else {
            $msg = 'Please enter student name and testimonial description.';
            $msgType = 'warning';
        }
    } elseif ($action === 'edit') {
        $id     = (int)($_POST['id'] ?? 0);
        $name   = trim($_POST['student_name'] ?? '');
        $age    = trim($_POST['student_age'] ?? '');
        $desig  = trim($_POST['student_designation'] ?? '');
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        $desc   = trim($_POST['description'] ?? '');
        $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
        $oldImg = trim($_POST['existing_image'] ?? 'assets/images/testi/rahul-sharma.jpg');
        $img    = handleTestimonialImageUpload('photo', $oldImg);

        if ($id > 0 && !empty($name) && !empty($desc)) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE testimonials
                    SET student_name = :name,
                        student_age = :age,
                        student_designation = :desig,
                        rating = :rating,
                        description = :desc,
                        image_path = :img,
                        status = :status
                    WHERE id = :id
                ");
                $stmt->execute([
                    'name'   => $name,
                    'age'    => $age,
                    'desig'  => $desig,
                    'rating' => $rating,
                    'desc'   => $desc,
                    'img'    => $img,
                    'status' => $status,
                    'id'     => $id,
                ]);
                $msg = 'Testimonial updated successfully!';
                $msgType = 'success';
            } catch (PDOException $e) {
                $msg = 'Error updating testimonial: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = :id");
                $stmt->execute(['id' => $id]);
                $msg = 'Testimonial deleted successfully!';
                $msgType = 'success';
            } catch (PDOException $e) {
                $msg = 'Error deleting testimonial: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    }
}

$testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Testimonials | Skill Grow Admin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/admin.css">
</head>

<body>

  <div class="admin-layout-wrapper">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

    <div class="admin-main">
      <header class="admin-topbar">
        <div>
          <h1 class="page-title-text" style="font-size: 1.25rem; font-family: var(--font-heading); margin: 0; font-weight: 700;">Student Testimonials</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
              <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Admin</a></li>
              <li class="breadcrumb-item active text-warning">Testimonials</li>
            </ol>
          </nav>
        </div>
        <div class="d-flex gap-2">
          <a href="../index.php#testimonial" target="_blank" class="btn btn-outline-light btn-sm px-3 py-2 rounded-3">
            <i class="fas fa-external-link-alt me-1"></i> View on Homepage
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
          <!-- Left: Add New Testimonial Form (4 cols) -->
          <div class="col-lg-4">
            <div class="bento-card">
              <div class="bento-header">
                <h3 class="bento-title"><i class="fas fa-comment-medical text-warning me-1"></i> Add Testimonial</h3>
              </div>
              <form method="POST" action="testimonials.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">

                <div class="mb-3">
                  <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Student Name *</label>
                  <input type="text" class="form-control" name="student_name" placeholder="e.g. Rahul Sharma" required>
                </div>

                <div class="row g-2 mb-3">
                  <div class="col-4">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Age</label>
                    <input type="text" class="form-control" name="student_age" placeholder="e.g. 22">
                  </div>
                  <div class="col-8">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Role / Course Title</label>
                    <input type="text" class="form-control" name="student_designation" placeholder="e.g. Pilot / Hospitality Student">
                  </div>
                </div>

                <div class="row g-2 mb-3">
                  <div class="col-6">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Star Rating</label>
                    <select class="form-select" name="rating">
                      <option value="5" selected>5 Stars (★★★★★)</option>
                      <option value="4">4 Stars (★★★★)</option>
                      <option value="3">3 Stars (★★★)</option>
                      <option value="2">2 Stars (★★)</option>
                      <option value="1">1 Star (★)</option>
                    </select>
                  </div>
                  <div class="col-6">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Status</label>
                    <select class="form-select" name="status">
                      <option value="active" selected>Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Student Photo</label>
                  <input type="file" class="form-control" name="photo" accept=".jpg,.jpeg,.png,.webp,.avif">
                  <small class="text-muted" style="font-size: 0.74rem;">Square image recommended (JPG, PNG, WEBP)</small>
                </div>

                <div class="mb-3">
                  <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Testimonial Story *</label>
                  <textarea class="form-control" name="description" rows="4" placeholder="Write the student's review / success story..." required></textarea>
                </div>

                <button type="submit" class="btn-gold w-100">
                  <i class="fas fa-plus me-1"></i> Add Testimonial
                </button>
              </form>
            </div>
          </div>

          <!-- Right: List All Testimonials (8 cols) -->
          <div class="col-lg-8">
            <div class="bento-card">
              <div class="bento-header">
                <h3 class="bento-title"><i class="fas fa-star text-warning me-1"></i> Published Testimonials (<?= count($testimonials) ?>)</h3>
              </div>

              <div class="table-responsive">
                <table class="admin-table">
                  <thead>
                    <tr>
                      <th>Student</th>
                      <th>Rating &amp; Story</th>
                      <th>Status</th>
                      <th class="text-end">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($testimonials as $t): ?>
                      <?php $headline = formatTestimonialHeadline($t); ?>
                      <tr>
                        <td style="min-width: 210px;">
                          <div class="d-flex align-items-center gap-3">
                            <img src="../<?= htmlspecialchars($t['image_path']) ?>" alt="<?= htmlspecialchars($t['student_name']) ?>" style="width: 52px; height: 52px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(230, 169, 16, 0.5); flex-shrink: 0;">
                            <div>
                              <div class="fw-bold text-white" style="font-size: 0.9rem;"><?= htmlspecialchars($headline) ?></div>
                              <small class="text-muted" style="font-size: 0.75rem;">ID #<?= (int)$t['id'] ?></small>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="text-warning mb-1" style="font-size: 0.78rem;">
                            <?php for ($s = 1; $s <= (int)$t['rating']; $s++): ?>
                              <i class="fas fa-star"></i>
                            <?php endfor; ?>
                          </div>
                          <div class="text-muted" style="font-size: 0.83rem; line-height: 1.45; max-width: 420px;">
                            <?= htmlspecialchars($t['description']) ?>
                          </div>
                        </td>
                        <td>
                          <span class="badge <?= ($t['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?>">
                            <?= ucfirst($t['status']) ?>
                          </span>
                        </td>
                        <td class="text-end" style="white-space: nowrap;">
                          <button
                            type="button"
                            class="btn btn-sm btn-outline-warning py-1 px-2 me-1"
                            title="Edit Testimonial"
                            onclick='openEditModal(<?= json_encode($t, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>
                            <i class="fas fa-edit"></i>
                          </button>
                          <form method="POST" action="testimonials.php" class="d-inline" onsubmit="return confirm('Delete this testimonial?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
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

  <!-- Edit Testimonial Modal -->
  <div class="modal fade" id="editTestimonialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background: #0f1537; color: #fff; border: 1px solid rgba(255,255,255,0.12); border-radius: 14px;">
        <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
          <h5 class="modal-title"><i class="fas fa-edit text-warning me-2"></i>Edit Testimonial</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="testimonials.php" enctype="multipart/form-data">
          <div class="modal-body">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="existing_image" id="edit_existing_image">

            <div class="mb-3">
              <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Student Name *</label>
              <input type="text" class="form-control" name="student_name" id="edit_student_name" required>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-4">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Age</label>
                <input type="text" class="form-control" name="student_age" id="edit_student_age">
              </div>
              <div class="col-8">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Role / Course Title</label>
                <input type="text" class="form-control" name="student_designation" id="edit_student_designation">
              </div>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Star Rating</label>
                <select class="form-select" name="rating" id="edit_rating">
                  <option value="5">5 Stars (★★★★★)</option>
                  <option value="4">4 Stars (★★★★)</option>
                  <option value="3">3 Stars (★★★)</option>
                  <option value="2">2 Stars (★★)</option>
                  <option value="1">1 Star (★)</option>
                </select>
              </div>
              <div class="col-6">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Status</label>
                <select class="form-select" name="status" id="edit_status">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Replace Photo (Optional)</label>
              <input type="file" class="form-control" name="photo" accept=".jpg,.jpeg,.png,.webp,.avif">
            </div>

            <div class="mb-2">
              <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Testimonial Story *</label>
              <textarea class="form-control" name="description" id="edit_description" rows="4" required></textarea>
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
            <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-gold btn-sm px-4">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/admin.js"></script>
  <script>
    function openEditModal(item) {
      document.getElementById('edit_id').value = item.id;
      document.getElementById('edit_student_name').value = item.student_name || '';
      document.getElementById('edit_student_age').value = item.student_age || '';
      document.getElementById('edit_student_designation').value = item.student_designation || '';
      document.getElementById('edit_rating').value = item.rating || 5;
      document.getElementById('edit_status').value = item.status || 'active';
      document.getElementById('edit_description').value = item.description || '';
      document.getElementById('edit_existing_image').value = item.image_path || 'assets/images/testi/rahul-sharma.jpg';
      const modal = new bootstrap.Modal(document.getElementById('editTestimonialModal'));
      modal.show();
    }
  </script>
</body>
</html>
