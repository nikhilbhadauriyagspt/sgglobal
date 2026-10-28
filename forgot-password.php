<?php
// Forgot Password Recovery Page - Skill Grow Institution
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password | Skill Grow Institution Portal</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="assets/images/logo/logopng.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <!-- Premium Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --brand-primary: #e6a910;
      --brand-primary-hover: #cf9507;
      --brand-navy: #002365;
      --brand-dark: #070e24;
      --brand-dark-card: #0b1736;
      --brand-border: #1e2c52;
      --brand-border-active: #33497d;
      --text-muted-dark: #94a3b8;
      --font-heading: 'Roboto Slab', serif;
      --font-body: 'Plus Jakarta Sans', sans-serif;
    }

    * {
      box-sizing: border-box;
      box-shadow: none !important;
    }

    body {
      font-family: var(--font-body);
      background-color: var(--brand-dark);
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
    .auth-glow-1 {
      position: absolute;
      width: 550px;
      height: 550px;
      background: radial-gradient(circle, rgba(230, 169, 16, 0.12) 0%, rgba(0, 35, 101, 0) 70%);
      top: -120px;
      left: -120px;
      pointer-events: none;
      filter: blur(90px);
      z-index: 0;
    }

    .auth-glow-2 {
      position: absolute;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, rgba(0, 35, 101, 0) 70%);
      bottom: -120px;
      right: -120px;
      pointer-events: none;
      filter: blur(90px);
      z-index: 0;
    }

    /* Main Container */
    .auth-wrapper {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 1050px;
      background: var(--brand-dark-card);
      border: 1px solid var(--brand-border);
      border-radius: 24px;
      overflow: hidden;
    }

    .auth-wrapper > .row {
      align-items: stretch;
    }

    /* Left Visual Panel */
    .auth-visual-panel {
      position: relative;
      background: linear-gradient(180deg, rgba(7, 14, 36, 0.72) 0%, rgba(7, 14, 36, 0.96) 100%),
                  url('assets/images/about/students.jpg') no-repeat center center / cover;
      padding: 55px 45px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;
      min-height: 100%;
      flex: 1 1 auto;
    }

    .auth-brand-logo {
      height: 64px;
      width: auto;
      object-fit: contain;
    }

    .visual-quote-box h3 {
      font-family: var(--font-heading);
      font-size: 1.85rem;
      font-weight: 700;
      color: #ffffff;
      line-height: 1.35;
      margin-bottom: 14px;
    }

    .visual-quote-box p {
      color: #cbd5e1;
      font-size: 0.95rem;
      line-height: 1.6;
      margin-bottom: 24px;
    }

    .security-badge-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .security-item {
      display: flex;
      align-items: center;
      gap: 12px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 10px 16px;
      border-radius: 12px;
      font-size: 0.88rem;
      color: #e2e8f0;
    }

    .security-item i {
      color: var(--brand-primary);
      font-size: 1.1rem;
      width: 22px;
      text-align: center;
    }

    /* Right Form Panel */
    .auth-form-panel {
      padding: 50px 45px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: var(--brand-dark-card);
      min-height: 620px;
    }

    /* Step indicator pills */
    .reset-progress-pills {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 30px;
      position: relative;
    }

    .reset-progress-pills::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 2px;
      background: var(--brand-border);
      z-index: 1;
      transform: translateY(-50%);
    }

    .step-pill-btn {
      position: relative;
      z-index: 2;
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: #070e24;
      border: 2px solid var(--brand-border);
      color: #64748b;
      font-weight: 700;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.25s ease;
    }

    .step-pill-btn.active {
      border-color: var(--brand-primary);
      background: var(--brand-primary);
      color: #070e24;
    }

    .step-pill-btn.completed {
      border-color: #10b981;
      background: #10b981;
      color: #ffffff;
    }

    .auth-header {
      margin-bottom: 24px;
    }

    .auth-badge {
      display: inline-block;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: var(--brand-primary);
      background: rgba(230, 169, 16, 0.12);
      border: 1px solid rgba(230, 169, 16, 0.3);
      padding: 4px 12px;
      border-radius: 50px;
      margin-bottom: 12px;
    }

    .auth-header h2 {
      font-family: var(--font-heading);
      font-size: 1.9rem;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 6px;
    }

    .auth-header p {
      color: var(--text-muted-dark);
      font-size: 0.92rem;
      margin: 0;
    }

    /* Inputs */
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
      transition: color 0.2s;
    }

    .input-box .form-control {
      background: rgba(7, 14, 36, 0.7);
      border: 1px solid var(--brand-border);
      border-radius: 12px;
      padding: 13px 44px;
      color: #ffffff;
      font-size: 0.95rem;
      width: 100%;
      outline: none;
      transition: all 0.25s ease;
    }

    .input-box .form-control:focus {
      background: rgba(7, 14, 36, 0.95);
      border-color: var(--brand-primary);
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

    /* OTP Inputs Group */
    .otp-inputs-wrapper {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin: 20px 0;
    }

    .otp-box {
      width: 52px;
      height: 56px;
      text-align: center;
      font-size: 1.5rem;
      font-weight: 700;
      color: #ffffff;
      background: rgba(7, 14, 36, 0.8);
      border: 1px solid var(--brand-border);
      border-radius: 10px;
      outline: none;
      transition: border-color 0.2s;
    }

    .otp-box:focus {
      border-color: var(--brand-primary);
      background: rgba(7, 14, 36, 1);
    }

    /* Password strength indicator */
    .pass-strength-track {
      height: 6px;
      width: 100%;
      background: #1e293b;
      border-radius: 4px;
      overflow: hidden;
      margin-top: 8px;
    }

    .pass-strength-bar {
      height: 100%;
      width: 0%;
      transition: width 0.3s ease, background-color 0.3s ease;
    }

    .strength-reqs {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      font-size: 0.78rem;
      color: #64748b;
      margin-top: 10px;
    }

    .strength-reqs span.valid {
      color: #10b981;
    }

    .strength-reqs span.valid i {
      color: #10b981;
    }

    /* Buttons */
    .btn-auth-submit {
      width: 100%;
      background: linear-gradient(135deg, #f59e0b, #e6a910);
      color: #070e24;
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
      margin-top: 15px;
    }

    .btn-auth-submit:hover {
      background: #fbbf24;
      transform: translateY(-2px);
    }

    .btn-auth-secondary {
      width: 100%;
      background: transparent;
      color: #cbd5e1;
      border: 1px solid var(--brand-border);
      padding: 11px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 0.92rem;
      cursor: pointer;
      transition: all 0.2s;
      margin-top: 10px;
      text-align: center;
      text-decoration: none;
      display: block;
    }

    .btn-auth-secondary:hover {
      background: rgba(255, 255, 255, 0.05);
      color: #ffffff;
      border-color: var(--brand-border-active);
    }

    /* Switcher Footer */
    .auth-footer {
      margin-top: 25px;
      padding-top: 18px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      text-align: center;
      font-size: 0.9rem;
      color: var(--text-muted-dark);
    }

    .auth-footer a {
      color: var(--brand-primary);
      text-decoration: none;
      font-weight: 700;
    }

    .auth-footer a:hover {
      color: #fbbf24;
      text-decoration: underline;
    }

    /* Success Card */
    .success-celebration-card {
      text-align: center;
      padding: 20px 0;
    }

    .success-icon-badge {
      width: 80px;
      height: 80px;
      background: rgba(16, 185, 129, 0.15);
      border: 2px solid #10b981;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      color: #10b981;
      font-size: 2.2rem;
    }

    /* Step display management */
    .recovery-step {
      display: none;
    }

    .recovery-step.active {
      display: block;
      animation: fadeInStep 0.3s ease;
    }

    @keyframes fadeInStep {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 991px) {
      .auth-visual-panel {
        display: none;
      }
      .auth-form-panel {
        padding: 35px 20px;
      }
    }
  </style>
</head>

<body>

  <div class="auth-glow-1"></div>
  <div class="auth-glow-2"></div>

  <div class="auth-wrapper">
    <div class="row g-0">
      
      <!-- Left Visual Panel -->
      <div class="col-lg-6 d-none d-lg-flex flex-column">
        <div class="auth-visual-panel">
          <div>
            <a href="index.php">
              <img src="assets/images/logo/logopng.png" alt="Skill Grow Institution Logo" class="auth-brand-logo">
            </a>
          </div>

          <div class="visual-quote-box">
            <span class="badge bg-warning text-dark px-3 py-1 mb-3 font-weight-bold" style="font-size: 0.8rem;">
              <i class="fas fa-shield-alt me-1"></i> Account Security & Recovery
            </span>
            <h3>Secure Identity & Credential Recovery</h3>
            <p>
              Forgot your portal password? No worries. Verify your registered email address or student ID to securely reset your credentials within minutes.
            </p>

            <div class="security-badge-list">
              <div class="security-item">
                <i class="fas fa-lock"></i>
                <span>256-Bit Encrypted One-Time Security Passcodes</span>
              </div>
              <div class="security-item">
                <i class="fas fa-bolt"></i>
                <span>Instant OTP Delivery to Registered Email/SMS</span>
              </div>
              <div class="security-item">
                <i class="fas fa-headset"></i>
                <span>Support Desk: info@skillgrowinstitution.in</span>
              </div>
            </div>
          </div>

          <div>
            <small style="color: rgba(255, 255, 255, 0.6); font-size: 0.82rem;">
              &copy; <?= date('Y') ?> Skill Grow Institution. Verified Student Portal.
            </small>
          </div>
        </div>
      </div>

      <!-- Right Form Panel -->
      <div class="col-lg-6">
        <div class="auth-form-panel">
          
          <!-- Step Progress Visualizer -->
          <div class="reset-progress-pills">
            <div class="step-pill-btn active" id="pillStep1">1</div>
            <div class="step-pill-btn" id="pillStep2">2</div>
            <div class="step-pill-btn" id="pillStep3">3</div>
            <div class="step-pill-btn" id="pillStep4"><i class="fas fa-check" style="font-size: 0.75rem;"></i></div>
          </div>

          <!-- STEP 1: Enter Registered Email or Student ID -->
          <div class="recovery-step active" id="step1">
            <div class="auth-header">
              <span class="auth-badge"><i class="fas fa-key me-1"></i> Step 1 of 3</span>
              <h2>Reset Password</h2>
              <p>Enter your registered email address or Student ID to receive a verification OTP code.</p>
            </div>

            <form id="step1Form" onsubmit="event.preventDefault(); goToStep(2);">
              <div class="input-group-modern">
                <label for="recoveryIdentity">Registered Email or Student ID</label>
                <div class="input-box">
                  <i class="fas fa-envelope field-icon"></i>
                  <input type="text" class="form-control" id="recoveryIdentity" placeholder="student@example.com or SGI-2026-001" required>
                </div>
              </div>

              <button type="submit" class="btn-auth-submit">
                <span>Send Verification Code</span> <i class="fas fa-arrow-right"></i>
              </button>

              <a href="login.php" class="btn-auth-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Login
              </a>
            </form>
          </div>

          <!-- STEP 2: Enter 6-Digit OTP -->
          <div class="recovery-step" id="step2">
            <div class="auth-header">
              <span class="auth-badge"><i class="fas fa-mobile-alt me-1"></i> Step 2 of 3</span>
              <h2>Enter Security OTP</h2>
              <p>We've sent a 6-digit verification code to <strong id="displayTargetEmail" class="text-warning">student@example.com</strong></p>
            </div>

            <form id="step2Form" onsubmit="event.preventDefault(); goToStep(3);">
              <label class="d-block text-center text-muted mb-2" style="font-size: 0.85rem;">Enter 6-digit code</label>
              
              <div class="otp-inputs-wrapper">
                <input type="text" maxlength="1" class="otp-box" value="4" autofocus>
                <input type="text" maxlength="1" class="otp-box" value="8">
                <input type="text" maxlength="1" class="otp-box" value="2">
                <input type="text" maxlength="1" class="otp-box" value="9">
                <input type="text" maxlength="1" class="otp-box" value="1">
                <input type="text" maxlength="1" class="otp-box" value="0">
              </div>

              <div class="text-center my-3">
                <span class="text-muted" style="font-size: 0.85rem;">Didn't receive the code? </span>
                <a href="#" class="text-warning fw-semibold text-decoration-none" style="font-size: 0.85rem;" onclick="resendOtpToast(event)">Resend OTP (<span id="resendCountdown">45</span>s)</a>
              </div>

              <button type="submit" class="btn-auth-submit">
                <span>Verify & Proceed</span> <i class="fas fa-shield-alt"></i>
              </button>

              <button type="button" class="btn-auth-secondary" onclick="goToStep(1)">
                <i class="fas fa-arrow-left me-1"></i> Change Email
              </button>
            </form>
          </div>

          <!-- STEP 3: Create New Password -->
          <div class="recovery-step" id="step3">
            <div class="auth-header">
              <span class="auth-badge"><i class="fas fa-lock me-1"></i> Step 3 of 3</span>
              <h2>Create New Password</h2>
              <p>Choose a strong password to protect your student portal account.</p>
            </div>

            <form id="step3Form" onsubmit="event.preventDefault(); goToStep(4);">
              
              <!-- New Password -->
              <div class="input-group-modern">
                <label for="newPassInput">New Password</label>
                <div class="input-box">
                  <i class="fas fa-key field-icon"></i>
                  <input type="password" class="form-control" id="newPassInput" placeholder="Min. 8 characters" required oninput="checkPassStrength(this.value)">
                  <button type="button" class="toggle-pass-btn" onclick="togglePassVisibility('newPassInput', 'newPassEye')">
                    <i class="fas fa-eye" id="newPassEye"></i>
                  </button>
                </div>
                
                <!-- Password Strength Bar -->
                <div class="pass-strength-track">
                  <div class="pass-strength-bar" id="strengthBar"></div>
                </div>
                
                <div class="strength-reqs">
                  <span id="reqLength"><i class="fas fa-circle-notch"></i> 8+ chars</span>
                  <span id="reqNum"><i class="fas fa-circle-notch"></i> 1 Number</span>
                  <span id="reqSpecial"><i class="fas fa-circle-notch"></i> 1 Special char</span>
                </div>
              </div>

              <!-- Confirm Password -->
              <div class="input-group-modern">
                <label for="confirmPassInput">Confirm New Password</label>
                <div class="input-box">
                  <i class="fas fa-check-circle field-icon"></i>
                  <input type="password" class="form-control" id="confirmPassInput" placeholder="Re-enter password" required>
                  <button type="button" class="toggle-pass-btn" onclick="togglePassVisibility('confirmPassInput', 'confirmPassEye')">
                    <i class="fas fa-eye" id="confirmPassEye"></i>
                  </button>
                </div>
                <div id="passMatchFeedback" style="font-size: 0.8rem; margin-top: 5px;"></div>
              </div>

              <button type="submit" class="btn-auth-submit">
                <span>Update Password</span> <i class="fas fa-check-circle"></i>
              </button>
            </form>
          </div>

          <!-- STEP 4: Success Confirmation -->
          <div class="recovery-step" id="step4">
            <div class="success-celebration-card">
              <div class="success-icon-badge">
                <i class="fas fa-check-double"></i>
              </div>
              <h3 class="text-white fw-bold mb-2" style="font-family: var(--font-heading);">Password Reset Complete!</h3>
              <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                Your password has been securely updated. You can now log into your student dashboard with your new credentials.
              </p>

              <div class="p-3 mb-4 rounded-3 text-start" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25);">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <i class="fas fa-shield-alt text-success"></i>
                  <span class="text-white fw-semibold" style="font-size: 0.88rem;">Security Notice:</span>
                </div>
                <p class="mb-0 text-muted" style="font-size: 0.82rem;">
                  A confirmation email has been dispatched to your inbox. All previous active sessions have been safely logged out.
                </p>
              </div>

              <a href="login.php" class="btn-auth-submit text-decoration-none">
                <span>Go to Login Now</span> <i class="fas fa-sign-in-alt"></i>
              </a>

              <div class="mt-3">
                <a href="index.php" class="text-muted text-decoration-none" style="font-size: 0.85rem;">
                  <i class="fas fa-home me-1"></i> Return to Homepage
                </a>
              </div>
            </div>
          </div>

          <!-- Switcher Footer -->
          <div class="auth-footer" id="mainFooter">
            Need additional assistance? <a href="contact.php">Contact Student Support</a>
          </div>

        </div>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Step switcher logic
    function goToStep(stepNum) {
      // Update form steps
      document.querySelectorAll('.recovery-step').forEach(el => el.classList.remove('active'));
      const activeEl = document.getElementById('step' + stepNum);
      if (activeEl) {
        activeEl.classList.add('active');
      }

      // Update pills
      for (let i = 1; i <= 4; i++) {
        const pill = document.getElementById('pillStep' + i);
        if (pill) {
          pill.classList.remove('active', 'completed');
          if (i < stepNum) {
            pill.classList.add('completed');
          } else if (i === stepNum) {
            pill.classList.add('active');
          }
        }
      }

      // If step 2, update display email
      if (stepNum === 2) {
        const val = document.getElementById('recoveryIdentity').value.trim();
        if (val) {
          document.getElementById('displayTargetEmail').textContent = val;
        }
      }
    }

    // OTP Auto-focus advancement
    const otpBoxes = document.querySelectorAll('.otp-box');
    otpBoxes.forEach((box, index) => {
      box.addEventListener('input', (e) => {
        if (e.target.value.length === 1 && index < otpBoxes.length - 1) {
          otpBoxes[index + 1].focus();
        }
      });

      box.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
          otpBoxes[index - 1].focus();
        }
      });
    });

    // Password strength evaluator
    function checkPassStrength(val) {
      const bar = document.getElementById('strengthBar');
      const reqLen = document.getElementById('reqLength');
      const reqNum = document.getElementById('reqNum');
      const reqSpecial = document.getElementById('reqSpecial');

      let score = 0;
      if (val.length >= 8) {
        score += 1;
        reqLen.classList.add('valid');
        reqLen.innerHTML = '<i class="fas fa-check-circle"></i> 8+ chars';
      } else {
        reqLen.classList.remove('valid');
        reqLen.innerHTML = '<i class="fas fa-circle-notch"></i> 8+ chars';
      }

      if (/\d/.test(val)) {
        score += 1;
        reqNum.classList.add('valid');
        reqNum.innerHTML = '<i class="fas fa-check-circle"></i> 1 Number';
      } else {
        reqNum.classList.remove('valid');
        reqNum.innerHTML = '<i class="fas fa-circle-notch"></i> 1 Number';
      }

      if (/[!@#$%^&*(),.?":{}|<>]/.test(val)) {
        score += 1;
        reqSpecial.classList.add('valid');
        reqSpecial.innerHTML = '<i class="fas fa-check-circle"></i> 1 Special char';
      } else {
        reqSpecial.classList.remove('valid');
        reqSpecial.innerHTML = '<i class="fas fa-circle-notch"></i> 1 Special char';
      }

      if (score === 1) {
        bar.style.width = '33%';
        bar.style.backgroundColor = '#ef4444';
      } else if (score === 2) {
        bar.style.width = '66%';
        bar.style.backgroundColor = '#f59e0b';
      } else if (score === 3) {
        bar.style.width = '100%';
        bar.style.backgroundColor = '#10b981';
      } else {
        bar.style.width = '0%';
      }
    }

    // Toggle password eye
    function togglePassVisibility(inputId, iconId) {
      const field = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }

    // Resend OTP feedback
    function resendOtpToast(e) {
      e.preventDefault();
      alert('A fresh 6-digit OTP passcode has been resent to your email address!');
    }
  </script>
</body>
</html>
