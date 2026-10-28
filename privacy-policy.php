<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Privacy Policy - Skill Grow Institution</title>
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
  <!-- Google Fonts -->
   <link rel="stylesheet" href="assets/css/style.css">
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
      background: url('assets/images/privacy-bg.jpg') no-repeat center/cover fixed;
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

    /* Policy Section */
    .policy-section {
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

    .policy-content {
      max-width: 800px;
      margin: 0 auto;
    }

    .policy-content h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 1.8rem;
      color: var(--secondary-color);
      margin: 30px 0 15px;
    }

    .policy-content p,
    .policy-content ul {
      font-size: 1rem;
      line-height: 1.8;
      color: #555;
    }

    .policy-content ul {
      padding-left: 20px;
      margin-bottom: 20px;
    }

    .policy-content ul li {
      margin-bottom: 10px;
    }

    .policy-content a {
      color: var(--primary-color);
      text-decoration: none;
      transition: color 0.3s;
    }

    .policy-content a:hover {
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

      .policy-content h2 {
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
      <h1>Privacy Policy</h1>
      <p>Your privacy matters to us. Learn how we protect your information.</p>
    </div>
  </section>

  <!-- Policy Section -->
  <section class="policy-section">
    <div class="container">
      <h2 class="section-title">Privacy Policy</h2>
      <div class="policy-content">
        <p><strong>Last Updated:</strong> May 14, 2025</p>

        <h2>1. Introduction</h2>
        <p>At Skill Grow Institution, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your personal information when you visit our website, enroll in our courses, or interact with our services. By using our website, you consent to the practices described in this policy.</p>

        <h2>2. Information We Collect</h2>
        <p>We may collect the following types of information:</p>
        <ul>
          <li><strong>Personal Information:</strong> Name, email address, phone number, billing address, and other information you provide when registering for courses, subscribing to newsletters, or contacting us.</li>
          <li><strong>Usage Data:</strong> Information about your interactions with our website, such as IP address, browser type, pages visited, and time spent on the site.</li>
          <li><strong>Course Data:</strong> Information related to your course progress, quiz results, and certificates earned.</li>
          <li><strong>Cookies and Tracking Data:</strong> Data collected via cookies and similar technologies to enhance your browsing experience and analyze website performance.</li>
        </ul>

        <h2>3. How We Use Your Information</h2>
        <p>We use your information to:</p>
        <ul>
          <li>Provide and manage our educational services, including course enrollment and certification.</li>
          <li>Communicate with you about course updates, promotions, or customer support inquiries.</li>
          <li>Improve our website and services through analytics and user feedback.</li>
          <li>Ensure the security of our platform and prevent fraud.</li>
          <li>Comply with legal obligations and enforce our policies.</li>
        </ul>

        <h2>4. Data Sharing</h2>
        <p>We do not sell your personal information. We may share your data with:</p>
        <ul>
          <li><strong>Service Providers:</strong> Third-party vendors who assist with payment processing, email delivery, or analytics (e.g., Stripe, Google Analytics).</li>
          <li><strong>Legal Authorities:</strong> If required by law or to protect our rights and safety.</li>
          <li><strong>Business Partners:</strong> With your consent, for co-branded courses or promotions.</li>
        </ul>

        <h2>5. Cookies and Tracking Technologies</h2>
        <p>We use cookies to enhance your experience, such as remembering your login details or tracking site usage. You can manage cookie preferences through your browser settings. Disabling cookies may affect certain website functionalities.</p>

        <h2>6. Data Security</h2>
        <p>We implement industry-standard security measures, including encryption and secure servers, to protect your data. However, no online platform is 100% secure, and we cannot guarantee absolute security.</p>

        <h2>7. Your Rights</h2>
        <p>Depending on your location, you may have the following rights:</p>
        <ul>
          <li>Access, correct, or delete your personal information.</li>
          <li>Opt out of marketing communications.</li>
          <li>Request a copy of your data in a portable format.</li>
          <li>Object to or restrict certain data processing activities.</li>
        </ul>
        <p>To exercise these rights, contact us at <a href="mailto:info@skillgrowinstitution.in">info@skillgrowinstitution.in</a>.</p>

        <h2>8. Third-Party Links</h2>
        <p>Our website may contain links to third-party sites. We are not responsible for their privacy practices. Please review their policies before providing personal information.</p>

        <h2>9. Children’s Privacy</h2>
        <p>Our services are not intended for individuals under 13. We do not knowingly collect data from children. If we learn such data has been collected, we will delete it promptly.</p>

        <h2>10. Changes to This Policy</h2>
        <p>We may update this Privacy Policy periodically. Changes will be posted on this page with an updated “Last Updated” date. Continued use of our services constitutes acceptance of the revised policy.</p>

        <h2>11. Contact Us</h2>
        <p>If you have questions about this Privacy Policy, please contact us:</p>
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