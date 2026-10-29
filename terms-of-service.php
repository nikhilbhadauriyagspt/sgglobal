<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Terms of Service - Skill Grow Institution</title>
  <!-- Bootstrap CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- If the favicon is in the root folder -->
<link rel="icon" href="assets/images/logo/logopng.png" type="image/x-icon">

<!-- If the favicon is in an 'images' folder -->
<link rel="icon" href="assets/images/logo/logopng.png" type="image/x-icon">

<!-- If using PNG -->
<link rel="icon" href="assets/images/logo/logopng.png" type="image/png">

<!-- Optional for Apple devices -->
<link rel="apple-touch-icon" href="assets/images/logo/logopng.png">
  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <style>
    :root {
      --primary-color: #e6a910;
      --secondary-color: #002365;
    }

    body {
      font-family: 'Roboto', sans-serif;
      color: #333;
      overflow-x: hidden;
    }

    /* Hero Section */
    .hero-section {
      position: relative;
      height: 50vh;
      background: url('assets/images/terms-bg.jpg') no-repeat center/cover fixed;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #fff;
    }

    .hero-overlay {
      background: linear-gradient(rgba(0, 35, 101, 0.8), rgba(0, 35, 101, 0.6));
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
    }

    .hero-content {
      z-index: 2;
      padding: 20px;
    }

    .hero-section h1 {
      font-family: 'Roboto Slab', serif;
      font-size: 3rem;
      font-weight: 700;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .hero-section p {
      font-size: 1.3rem;
      margin: 15px 0;
    }

    /* Terms Section */
    .terms-section {
      padding: 80px 0;
      background: #f5f7fa;
    }

    .section-title {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      font-size: 2.8rem;
      margin-bottom: 40px;
      text-align: center;
      position: relative;
    }

    .section-title::after {
      content: '';
      width: 80px;
      height: 4px;
      background: var(--primary-color);
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
    }

    .terms-content {
      max-width: 800px;
      margin: 0 auto;
    }

    .terms-content h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 1.8rem;
      color: var(--secondary-color);
      margin: 30px 0 15px;
    }

    .terms-content p,
    .terms-content ul {
      font-size: 1rem;
      line-height: 1.8;
      color: #555;
    }

    .terms-content ul {
      padding-left: 20px;
      margin-bottom: 20px;
    }

    .terms-content ul li {
      margin-bottom: 10px;
    }

    .terms-content a {
      color: var(--primary-color);
      text-decoration: none;
      transition: color 0.3s;
    }

    .terms-content a:hover {
      color: var(--secondary-color);
    }

    /* Fade-in Animation */
    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .fade-in.visible {
      opacity: 1;
      transform: translateY(0);
    }

    @media (max-width: 768px) {
      .hero-section h1 {
        font-size: 2rem;
      }

      .hero-section p {
        font-size: 1rem;
      }

      .section-title {
        font-size: 2rem;
      }

      .hero-section {
        height: 40vh;
      }

      .terms-content h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
    <?php include 'header.php'; ?>
  <!-- Hero Section -->
  <section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1>Terms of Service</h1>
      <p>Understand the terms governing your use of our services.</p>
    </div>
  </section>

  <!-- Terms Section -->
  <section class="terms-section">
    <div class="container">
      <h2 class="section-title">Terms of Service</h2>
      <div class="terms-content">
        <p><strong>Last Updated:</strong> May 14, 2025</p>

        <h2>1. Introduction</h2>
        <p>Welcome to Skill Grow Institution. These Terms of Service (“Terms”) govern your use of our website, courses, and related services (collectively, “Services”). By accessing or using our Services, you agree to be bound by these Terms. If you do not agree, please do not use our Services.</p>

        <h2>2. Acceptance of Terms</h2>
        <p>By creating an account, enrolling in a course, or otherwise using our Services, you confirm that you are at least 18 years old (or have parental consent if under 18) and agree to comply with these Terms.</p>

        <h2>3. Services Provided</h2>
        <p>Skill Grow Institution offers online courses, certifications, and educational resources in various fields, including Technology, Marketing, Business, and Career Development. We reserve the right to modify, suspend, or discontinue any Service at any time without notice.</p>

        <h2>4. User Responsibilities</h2>
        <p>As a user, you agree to:</p>
        <ul>
          <li>Provide accurate and complete information during registration.</li>
          <li>Maintain the confidentiality of your account credentials.</li>
          <li>Use our Services for personal, non-commercial purposes only.</li>
          <li>Not share, distribute, or reproduce course materials without permission.</li>
          <li>Refrain from engaging in unlawful, abusive, or disruptive behavior on our platform.</li>
        </ul>

        <h2>5. Intellectual Property</h2>
        <p>All content on our website, including course materials, videos, text, and logos, is owned by Skill Grow Institution or its licensors and protected by copyright and other intellectual property laws. You may not copy, modify, or distribute our content without written permission.</p>

        <h2>6. Payment Terms</h2>
        <p>Some Services require payment. By enrolling in a paid course, you agree to:</p>
        <ul>
          <li>Pay the fees displayed at the time of purchase.</li>
          <li>Provide valid payment information.</li>
          <li>Understand that all sales are final unless otherwise stated in our refund policy.</li>
        </ul>
        <p>Refunds, if applicable, are processed according to our refund policy, available upon request.</p>

        <h2>7. Termination</h2>
        <p>We may suspend or terminate your access to our Services if you:</p>
        <ul>
          <li>Violate these Terms.</li>
          <li>Engage in fraudulent or harmful activities.</li>
          <li>Fail to pay applicable fees.</li>
        </ul>
        <p>Upon termination, your access to courses and materials may be revoked without refund.</p>

        <h2>8. Limitation of Liability</h2>
        <p>To the fullest extent permitted by law, Skill Grow Institution is not liable for any indirect, incidental, or consequential damages arising from your use of our Services. Our total liability shall not exceed the amount you paid for the Service in question.</p>

        <h2>9. Governing Law</h2>
        <p>These Terms are governed by the laws of Singapore. Any disputes arising from these Terms shall be resolved in the courts of Singapore.</p>

        <h2>10. Changes to These Terms</h2>
        <p>We may update these Terms periodically. Changes will be posted on this page with an updated “Last Updated” date. Continued use of our Services constitutes acceptance of the revised Terms.</p>

        <h2>11. Contact Us</h2>
        <p>If you have questions about these Terms, please contact us:</p>
        <ul>
          <li><strong>Email:</strong> <a href="mailto:info@skillgrowinstitution.in">info@skillgrowinstitution.in</a></li>
          <li><strong>Phone:</strong> +91 9217431562</li>
          <li><strong>Address:</strong> Metro Plaza Meerut,250002</li>
        </ul>
      </div>
    </div>
  </section>
  <?php include 'footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Fade-in Animation on Scroll -->
  <script>
     document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', function () {
        document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
        this.classList.add('active');
      });
    });
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
      let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      if (scrollTop > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });
    const fadeInElements = document.querySelectorAll('.fade-in');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.2 });

    fadeInElements.forEach(element => observer.observe(element));
  </script>
</body>
</html>