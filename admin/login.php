<?php
// Admin Portal Login - Skill Grow Institution
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect straight to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/config/db.php';

$error = '';
$success = '';

if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out') {
    $success = 'You have been securely logged out of the admin console.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($usernameOrEmail) || empty($password)) {
        $error = 'Please enter both your admin username/email and password.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE (username = :u OR email = :e) LIMIT 1");
            $stmt->execute(['u' => $usernameOrEmail, 'e' => $usernameOrEmail]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                if ($admin['status'] !== 'active') {
                    $error = 'This administrative account has been deactivated. Please contact the system owner.';
                } else {
                    // Update last login
                    $updateStmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = :id");
                    $updateStmt->execute(['id' => $admin['id']]);

                    // Set session
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id']        = $admin['id'];
                    $_SESSION['admin_name']      = $admin['name'];
                    $_SESSION['admin_username']  = $admin['username'];
                    $_SESSION['admin_email']     = $admin['email'];
                    $_SESSION['admin_role']      = $admin['role'];

                    header("Location: index.php");
                    exit;
                }
            } else {
                $error = 'Invalid admin credentials. Please verify your username and password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Control Center | Skill Grow Institution</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <!-- Premium Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --admin-primary: #e6a910;
      --admin-primary-hover: #cf9507;
      --admin-navy: #002365;
      --admin-dark: #060b1b;
      --admin-card: #0c1634;
      --admin-border: #1a274e;
      --text-muted: #94a3b8;
      --font-heading: 'Roboto Slab', serif;
      --font-body: 'Plus Jakarta Sans', sans-serif;
    }

    * {
      box-sizing: border-box;
      box-shadow: none !important;
    }

    body {
      font-family: var(--font-body);
      background-color: var(--admin-dark);
      color: #e2e8f0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px 15px;
      position: relative;
      overflow-x: hidden;
    }

    /* Ambient Background Glows */
    .admin-glow-1 {
      position: absolute;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(230, 169, 16, 0.1) 0%, rgba(0, 35, 101, 0) 70%);
      top: -150px;
      left: -150px;
      pointer-events: none;
      filter: blur(100px);
      z-index: 0;
    }

    .admin-glow-2 {
      position: absolute;
      width: 550px;
      height: 550px;
      background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, rgba(0, 35, 101, 0) 70%);
      bottom: -150px;
      right: -150px;
      pointer-events: none;
      filter: blur(100px);
      z-index: 0;
    }

    .admin-login-box {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 460px;
      background: var(--admin-card);
      border: 1px solid var(--admin-border);
      border-radius: 20px;
      padding: 44px 38px;
    }

    .admin-logo-wrapper {
      text-align: center;
      margin-bottom: 28px;
    }

    .admin-logo-wrapper img {
      height: 62px;
      width: auto;
      object-fit: contain;
    }

    .admin-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--admin-primary);
      background: rgba(230, 169, 16, 0.12);
      border: 1px solid rgba(230, 169, 16, 0.3);
      padding: 4px 14px;
      border-radius: 50px;
      margin-top: 14px;
    }

    .admin-login-header {
      text-align: center;
      margin-bottom: 28px;
    }

    .admin-login-header h2 {
      font-family: var(--font-heading);
      font-size: 1.85rem;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 6px;
    }

    .admin-login-header p {
      color: var(--text-muted);
      font-size: 0.92rem;
      margin: 0;
    }

    .input-group-modern {
      position: relative;
      margin-bottom: 20px;
    }

    .input-group-modern label {
      display: block;
      font-size: 0.85rem;
      font-weight: 600;
      color: #cbd5e1;
      margin-bottom: 8px;
    }

    .input-box {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-box .field-icon {
      position: absolute;
      left: 16px;
      color: #64748b;
      font-size: 1rem;
      pointer-events: none;
    }

    .input-box .form-control {
      background: rgba(6, 11, 27, 0.7);
      border: 1px solid var(--admin-border);
      border-radius: 12px;
      padding: 13px 44px;
      color: #ffffff;
      font-size: 0.95rem;
      width: 100%;
      outline: none;
      transition: all 0.25s ease;
    }

    .input-box .form-control:focus {
      background: rgba(6, 11, 27, 0.95);
      border-color: var(--admin-primary);
    }

    .toggle-pass-btn {
      position: absolute;
      right: 14px;
      background: transparent;
      border: none;
      color: #64748b;
      cursor: pointer;
      font-size: 1rem;
      padding: 4px;
    }

    .toggle-pass-btn:hover {
      color: #cbd5e1;
    }

    .btn-admin-submit {
      width: 100%;
      background: linear-gradient(135deg, #f59e0b, #e6a910);
      color: #060b1b;
      font-weight: 700;
      font-size: 1rem;
      padding: 13px;
      border-radius: 12px;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: all 0.2s ease;
      margin-top: 10px;
    }

    .btn-admin-submit:hover {
      background: #fbbf24;
      transform: translateY(-2px);
    }

    .admin-login-footer {
      margin-top: 28px;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      text-align: center;
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .admin-login-footer a {
      color: var(--admin-primary);
      text-decoration: none;
    }

    .admin-login-footer a:hover {
      text-decoration: underline;
    }

    .security-notice {
      background: rgba(230, 169, 16, 0.06);
      border: 1px dashed rgba(230, 169, 16, 0.25);
      border-radius: 10px;
      padding: 10px 14px;
      font-size: 0.82rem;
      color: #94a3b8;
      margin-top: 22px;
      text-align: center;
    }
  </style>
</head>

<body>

  <div class="admin-glow-1"></div>
  <div class="admin-glow-2"></div>

  <div class="admin-login-box">
    
    <div class="admin-logo-wrapper">
      <a href="../index.php">
        <img src="../assets/images/logo/logopng.png" alt="Skill Grow Institution">
      </a>
      <br>
      <span class="admin-badge">
        <i class="fas fa-user-shield"></i> Admin Control Center
      </span>
    </div>

    <div class="admin-login-header">
      <h2>Staff Sign In</h2>
      <p>Authorized administration credentials required.</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px; font-size: 0.88rem;">
        <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px; font-size: 0.88rem;">
        <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      
      <!-- Username or Email -->
      <div class="input-group-modern">
        <label for="adminUsername">Username or Official Email</label>
        <div class="input-box">
          <i class="fas fa-user-tie field-icon"></i>
          <input type="text" class="form-control" id="adminUsername" name="username" placeholder="admin or admin@skillgrowinstitution.in" required autofocus>
        </div>
      </div>

      <!-- Password -->
      <div class="input-group-modern">
        <label for="adminPassword">Password</label>
        <div class="input-box">
          <i class="fas fa-lock field-icon"></i>
          <input type="password" class="form-control" id="adminPassword" name="password" placeholder="••••••••" required>
          <button type="button" class="toggle-pass-btn" id="togglePassword" aria-label="Toggle password">
            <i class="fas fa-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn-admin-submit">
        <span>Authenticate & Enter</span> <i class="fas fa-arrow-right"></i>
      </button>

    </form>

    <div class="security-notice">
      <i class="fas fa-info-circle text-warning me-1"></i> Default Login: <strong>admin</strong> / <strong>admin123</strong>
    </div>

    <div class="admin-login-footer">
      <a href="../index.php"><i class="fas fa-arrow-left me-1"></i> Back to Main Website</a>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const toggleBtn = document.getElementById('togglePassword');
    const passInput = document.getElementById('adminPassword');
    const eyeIcon = document.getElementById('eyeIcon');

    if (toggleBtn && passInput) {
      toggleBtn.addEventListener('click', function() {
        if (passInput.type === 'password') {
          passInput.type = 'text';
          eyeIcon.classList.remove('fa-eye');
          eyeIcon.classList.add('fa-eye-slash');
        } else {
          passInput.type = 'password';
          eyeIcon.classList.remove('fa-eye-slash');
          eyeIcon.classList.add('fa-eye');
        }
      });
    }
  </script>
</body>
</html>
