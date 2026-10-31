<?php
require_once __DIR__ . '/includes/settings_helper.php';
$ftrSettings = getGlobalSettings();

// Fetch dynamic active courses for footer
$ftrCourses = [];
if (!isset($pdo)) {
    $dbPath = __DIR__ . '/admin/config/db.php';
    if (file_exists($dbPath)) {
        require_once $dbPath;
    }
}
if (isset($pdo)) {
    try {
        $stmtFtr = $pdo->query("SELECT id, title, slug FROM courses WHERE status = 'active' ORDER BY id ASC LIMIT 7");
        $ftrCourses = $stmtFtr->fetchAll();
    } catch (Exception $e) {
        $ftrCourses = [];
    }
}
?><footer class="footer-section">
  <div class="container">
    <div class="footer-grid">
      <!-- Brand Section -->
      <div class="footer-brand">
        <a href="#" class="footer-logo">
          <img src="<?= htmlspecialchars($ftrSettings['site_logo']) ?>" alt="<?= htmlspecialchars($ftrSettings['site_title']) ?>" class="footer-logo-img" onerror="this.src='assets/images/logo/logopng.png'">
        </a>
        <p class="footer-brand-text">Empowering your future with cutting-edge skills and knowledge at Skill Grow Institution.</p>
        <div class="footer-social">
          <?php if (!empty($ftrSettings['social_facebook'])): ?>
            <a href="<?= htmlspecialchars($ftrSettings['social_facebook']) ?>" target="_blank" rel="noopener" aria-label="Facebook" class="social-icon"><i class="fab fa-facebook-f"></i></a>
          <?php endif; ?>
          <?php if (!empty($ftrSettings['social_twitter'])): ?>
            <a href="<?= htmlspecialchars($ftrSettings['social_twitter']) ?>" target="_blank" rel="noopener" aria-label="Twitter" class="social-icon"><i class="fab fa-twitter"></i></a>
          <?php endif; ?>
          <?php if (!empty($ftrSettings['social_linkedin'])): ?>
            <a href="<?= htmlspecialchars($ftrSettings['social_linkedin']) ?>" target="_blank" rel="noopener" aria-label="LinkedIn" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
          <?php endif; ?>
          <?php if (!empty($ftrSettings['social_instagram'])): ?>
            <a href="<?= htmlspecialchars($ftrSettings['social_instagram']) ?>" target="_blank" rel="noopener" aria-label="Instagram" class="social-icon"><i class="fab fa-instagram"></i></a>
          <?php endif; ?>
          <?php if (!empty($ftrSettings['social_youtube'])): ?>
            <a href="<?= htmlspecialchars($ftrSettings['social_youtube']) ?>" target="_blank" rel="noopener" aria-label="YouTube" class="social-icon"><i class="fab fa-youtube"></i></a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="footer-links">
        <h4 class="footer-heading">Quick Links</h4>
        <ul class="footer-nav">
          <li><a href="index.php">Home</a></li>
          <li><a href="aboutus.php">About Us</a></li>
          <li><a href="explore-course.php">Courses</a></li>
          <li><a href="blogs.php">Blogs</a></li>
          <li><a href="certificate.php">Certificate</a></li>
          <li><a href="private-jobs.php">Apply for Private Jobs</a></li>
          <li><a href="government-jobs.php">Apply for Government Jobs</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </div>

      <!-- Our Courses (Dynamic Normal Links) -->
      <div class="footer-courses">
        <h4 class="footer-heading">Our Courses</h4>
        <ul class="footer-nav">
          <?php if (!empty($ftrCourses)): ?>
            <?php foreach ($ftrCourses as $fc): ?>
              <?php $cLink = !empty($fc['slug']) ? "course-detail.php?course=" . urlencode($fc['slug']) : "explore-course.php"; ?>
              <li><a href="<?= $cLink ?>"><?= htmlspecialchars($fc['title']) ?></a></li>
            <?php endforeach; ?>
            <li><a href="explore-course.php" style="color: var(--primary-color); font-weight: 600;">View All Courses &rarr;</a></li>
          <?php else: ?>
            <li><a href="explore-course.php">Business Studies</a></li>
            <li><a href="explore-course.php">Advanced Management</a></li>
            <li><a href="explore-course.php">Banking &amp; Financial Markets</a></li>
            <li><a href="explore-course.php">Full Stack Web Engineering</a></li>
            <li><a href="explore-course.php">Python &amp; Data Science</a></li>
            <li><a href="explore-course.php">Digital Marketing &amp; Growth</a></li>
            <li><a href="explore-course.php">Aviation &amp; Airport Ops</a></li>
          <?php endif; ?>
        </ul>
      </div>


      <!-- Contact Info -->
      <div class="footer-contact">
        <h4 class="footer-heading">Get in Touch</h4>
        <ul class="footer-contact-list">
          <li>
            <i class="fas fa-map-marker-alt"></i>
            <span><?= htmlspecialchars($ftrSettings['site_address']) ?></span>
          </li>
          <li>
           <i class="fa-solid fa-phone-volume"></i>
            <span><a href="tel:<?= htmlspecialchars($ftrSettings['site_phone']) ?>"><?= htmlspecialchars($ftrSettings['site_phone']) ?></a></span>
          </li>
          <li>
            <i class="fas fa-envelope"></i>
            <span><a href="mailto:<?= htmlspecialchars($ftrSettings['site_email']) ?>"><?= htmlspecialchars($ftrSettings['site_email']) ?></a></span>
          </li>
           <li>
            <i class="fas fa-envelope"></i>
            <span><a href="mailto:join@skillgrowinstitution.in">join@skillgrowinstitution.in</a></span>
          </li>
        </ul>
      </div>

      <!-- Newsletter Subscription -->
      <div class="footer-newsletter">
        <h4 class="footer-heading">Stay Updated</h4>
        <p>Subscribe to our newsletter for the latest courses and updates.</p>
        <form class="newsletter-form" id="newsletter-form">
          <input type="email" class="form-control" placeholder="Enter your email" required>
          <button type="submit" class="footer-btn">Subscribe</button>
        </form>
        <div id="newsletter-response" class="form-response"></div>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
      <p>© 2024 Skill Grow Institution. All Rights Reserved.</p>
      <div class="footer-bottom-links">
        <a href="privacy-policy.php">Privacy Policy</a>
        <a href="terms-of-service.php">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

    navLinks.forEach(link => {
      link.classList.remove('active'); // Remove existing active class
      const href = link.getAttribute('href');
      if (href === currentPage) {
        link.classList.add('active');
      }
    });
  });
</script>
<script>
  (function() {
    const navbarEl = document.querySelector('.navbar');
    if (!navbarEl) return;

    window.addEventListener('scroll', function() {
      let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      if (scrollTop > 50) {
        navbarEl.classList.add('scrolled');
      } else {
        navbarEl.classList.remove('scrolled');
      }
    });
  })();
</script>