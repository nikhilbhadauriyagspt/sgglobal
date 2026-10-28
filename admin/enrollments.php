<?php
// Admin Course Enrollments Management - Skill Grow Institution
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../includes/db.php';

// Filter & Search
$search = trim($_GET['search'] ?? '');
$filterCourse = trim($_GET['course'] ?? '');

$sql = "
    SELECT e.*, c.title as current_course_title, c.image as course_image
    FROM course_enrollments e
    LEFT JOIN courses c ON e.course_id = c.id
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $sql .= " AND (e.student_name LIKE :s1 OR e.student_email LIKE :s2 OR e.enrollment_id LIKE :s3 OR e.student_id LIKE :s4)";
    $params['s1'] = "%$search%";
    $params['s2'] = "%$search%";
    $params['s3'] = "%$search%";
    $params['s4'] = "%$search%";
}

if (!empty($filterCourse)) {
    $sql .= " AND (e.course_slug = :cslug OR e.course_name = :cname)";
    $params['cslug'] = $filterCourse;
    $params['cname']  = $filterCourse;
}

$sql .= " ORDER BY e.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$enrollments = $stmt->fetchAll();

// Metrics
$mStmt = $pdo->query("
    SELECT 
        COUNT(*) as total_count,
        COALESCE(SUM(fee_amount), 0) as total_revenue,
        COUNT(DISTINCT student_email) as unique_students
    FROM course_enrollments
");
$metrics = $mStmt->fetch() ?: ['total_count' => 0, 'total_revenue' => 0, 'unique_students' => 0];

// Active courses list for filter
$coursesList = $pdo->query("SELECT title, slug FROM courses ORDER BY title ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Enrollments & Tuition Fees | SG Admin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
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
          <h1 class="page-title-text" style="font-size: 1.25rem; font-family: var(--font-heading); margin: 0; font-weight: 700;">
            Course Enrollments & Tuition Fees
          </h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.78rem;">
              <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none">Admin</a></li>
              <li class="breadcrumb-item active text-warning">Enrollments</li>
            </ol>
          </nav>
        </div>

        <div class="d-flex gap-2">
          <a href="../enrollnow.php" target="_blank" class="btn btn-outline-light btn-sm px-3 py-2 rounded-3">
            <i class="fas fa-external-link-alt me-1"></i> Live Enrollment Portal
          </a>
        </div>
      </header>

      <main class="admin-content">
        
        <!-- Metrics Row -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="stat-metric-card">
              <div class="stat-metric-icon">
                <i class="fas fa-graduation-cap"></i>
              </div>
              <div>
                <div class="text-muted" style="font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Total Course Enrollments</div>
                <div class="text-white fw-bold" style="font-size: 1.5rem; font-family: var(--font-heading);"><?= number_format($metrics['total_count']) ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="stat-metric-card">
              <div class="stat-metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="fas fa-indian-rupee-sign"></i>
              </div>
              <div>
                <div class="text-muted" style="font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Tuition Revenue Collected</div>
                <div class="text-success fw-bold" style="font-size: 1.5rem; font-family: var(--font-heading);">₹<?= number_format((float)$metrics['total_revenue'], 0) ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="stat-metric-card">
              <div class="stat-metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                <i class="fas fa-users"></i>
              </div>
              <div>
                <div class="text-muted" style="font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Unique Enrolled Students</div>
                <div class="text-info fw-bold" style="font-size: 1.5rem; font-family: var(--font-heading);"><?= number_format($metrics['unique_students']) ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bento-card mb-4 p-3">
          <form method="GET" action="enrollments.php" class="row g-2 align-items-center">
            <div class="col-md-5">
              <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search student name, email, ID, or enrollment ID...">
              </div>
            </div>

            <div class="col-md-4">
              <select class="form-select" name="course">
                <option value="">All Academic Programs</option>
                <?php foreach ($coursesList as $cl): ?>
                  <option value="<?= htmlspecialchars($cl['slug']) ?>" <?= ($filterCourse === $cl['slug']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cl['title']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
              <button type="submit" class="btn btn-warning fw-bold flex-grow-1">
                Filter
              </button>
              <?php if (!empty($search) || !empty($filterCourse)): ?>
                <a href="enrollments.php" class="btn btn-outline-light">Reset</a>
              <?php endif; ?>
            </div>
          </form>
        </div>

        <!-- Main Enrollments Table -->
        <div class="bento-card">
          <div class="bento-header">
            <h2 class="bento-title">
              <i class="fas fa-user-check text-warning me-2"></i> Verified Enrollments (<?= count($enrollments) ?>)
            </h2>
          </div>

          <div class="table-responsive">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Enrollment ID</th>
                  <th>Student Info</th>
                  <th>Enrolled Course</th>
                  <th>Tuition Fee</th>
                  <th>Payment Gateway</th>
                  <th>Enrolled Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($enrollments)): ?>
                  <?php foreach ($enrollments as $enr): ?>
                    <tr>
                      <td>
                        <span class="enrollment-badge"><?= htmlspecialchars($enr['enrollment_id']) ?></span>
                      </td>

                      <td>
                        <div class="fw-bold text-white"><?= htmlspecialchars($enr['student_name']) ?></div>
                        <div class="text-muted" style="font-size: 0.78rem;">
                          <?= htmlspecialchars($enr['student_email']) ?> &bull; <?= htmlspecialchars($enr['student_phone']) ?>
                        </div>
                        <?php if (!empty($enr['student_id'])): ?>
                          <small class="text-warning font-monospace" style="font-size: 0.75rem;">ID: <?= htmlspecialchars($enr['student_id']) ?></small>
                        <?php endif; ?>
                      </td>

                      <td>
                        <div class="fw-bold text-white"><?= htmlspecialchars($enr['course_name']) ?></div>
                        <small class="text-muted font-monospace"><?= htmlspecialchars($enr['course_slug']) ?></small>
                      </td>

                      <td>
                        <span class="text-success fw-bold font-monospace fs-6">
                          ₹<?= number_format((float)$enr['fee_amount'], 2) ?>
                        </span>
                      </td>

                      <td>
                        <div><span class="badge bg-dark border border-secondary text-white"><?= htmlspecialchars($enr['payment_method']) ?></span></div>
                        <small class="text-muted font-monospace" style="font-size: 0.72rem;"><?= htmlspecialchars($enr['razorpay_payment_id'] ?: 'N/A') ?></small>
                      </td>

                      <td>
                        <div style="font-size: 0.82rem; color: #cbd5e1;"><?= date('d M, Y', strtotime($enr['enrolled_at'])) ?></div>
                        <small class="text-muted"><?= date('h:i A', strtotime($enr['enrolled_at'])) ?></small>
                      </td>

                      <td>
                        <span class="badge <?= ($enr['payment_status'] === 'paid') ? 'bg-success' : 'bg-warning' ?>">
                          <?= ucfirst($enr['payment_status']) ?>
                        </span>
                      </td>

                      <td>
                        <a href="../enrollment-receipt.php?id=<?= urlencode($enr['enrollment_id']) ?>" target="_blank" class="btn btn-outline-warning btn-sm" title="View & Print Official Receipt" style="border-radius: 6px;">
                          <i class="fas fa-file-invoice"></i> Receipt
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                      <i class="fas fa-folder-open fa-2x mb-3 d-block text-secondary"></i>
                      No course enrollments match the selected filters.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/admin.js"></script>
</body>
</html>
