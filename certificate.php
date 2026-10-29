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
  <title>Certificate Request - Skill Grow Institution</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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
      background: url('assets/images/certificate-bg.jpg') no-repeat center/cover fixed;
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

    /* Intro Content Section */
    .intro-section {
      padding: 60px 0;
      background-color: #fff;
    }

    .intro-section h2 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      margin-bottom: 30px;
      font-weight: 700;
      font-size: 40px;
      text-align: center;
    }

    .intro-section p {
      font-size: 1.1rem;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .intro-section .img-fluid {
      border-radius: 10px;
      height: 300px ;
      object-fit: contain;
      margin-bottom: 20px;
    }

    /* Form Section */
    .form-section {
      padding: 60px 0;
      background-color: #f8f9fa;
    }

    .form-section h2 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      margin-bottom: 30px;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 5px rgba(230, 169, 16, 0.5);
    }

    #form-response {
      display: none;
      text-align: center;
      margin-top: 20px;
      font-weight: 500;
    }

    .sidebar-content {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .sidebar-content h3 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      font-size: 1.8rem;
      margin-bottom: 20px;
    }

    .sidebar-content ul {
      padding-left: 20px;
    }

    .sidebar-content ul li {
      font-size: 1rem;
      line-height: 1.8;
      margin-bottom: 10px;
    }

    /* CTA Section */
    .cta-section {
      background: var(--primary-color);
      color: #fff;
      padding: 40px 0;
      text-align: center;
    }

    .cta-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.2rem;
      margin-bottom: 15px;
    }

    .cta-section p {
      font-size: 1.1rem;
      margin-bottom: 0;
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
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1>Request Your Certificate</h1>
      <p>Showcase your skills with a certificate from Skill Grow Institution.</p>
    </div>
  </section>

  <!-- Intro Content Section -->
  <section class="intro-section fade-in">
    <div class="container">
      <h2>Why Our Certificates Matter</h2>
      <div class="row align-items-center">
        <div class="col-md-6">
          <p>Earning a certificate from Skill Grow Institution is more than just a document—it's a testament to your dedication and skills. Our certificates are recognized by industry leaders and can boost your career prospects.</p>
          <p>Whether you're looking to advance in your current role or explore new opportunities, our certificates validate your expertise and commitment to lifelong learning.</p>
        </div>
        <div class="col-md-6">
          <img src="assets/images/certi/Certificate.png" alt="Sample Certificate" class="img-fluid">
        </div>
      </div>
      <div class="row align-items-center mt-5">
        <div class="col-md-6 order-md-2">
          <p>Our alumni haverate this content to see how they’re doing today.</p>
          <p>Join thousands of successful graduates who have used their Skill Grow certificates to stand out in the job market.</p>
        </div>
        <div class="col-md-6 order-md-1">
          <img src="assets/images/certi/graduation.png" alt="Graduate Success" class="img-fluid">
        </div>
      </div>
    </div>
  </section>

  <!-- Form Section -->
  <section class="form-section fade-in">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <h2>Certificate Request Form</h2>
       <form id="certificate-form" class="row g-3" method="POST" action="certificate_request.php">
  <div class="col-md-6">
    <label for="full-name" class="form-label">Full Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="full-name" name="full_name" placeholder="Enter your full name" required>
  </div>
  <div class="col-md-6">
    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
  </div>
  <div class="col-md-6">
    <label for="course" class="form-label">Course Completed <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="course" name="course" placeholder="Enter course name" required>
  </div>
  <div class="col-md-6">
    <label for="certificate-type" class="form-label">Certificate Type <span class="text-danger">*</span></label>
    <select class="form-select" id="certificate-type" name="certificate_type" required>
      <option value="" disabled selected>Select certificate type</option>
      <option value="digital">Digital Certificate</option>
      <option value="physical">Physical Certificate</option>
      <option value="both">Both Digital & Physical</option>
    </select>
  </div>
  <div class="col-md-12">
    <label for="phone" class="form-label">Phone No. <span class="text-danger">*</span></label>
    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter Phone No." required>
  </div>
  <div class="col-12">
    <label for="additional-info" class="form-label">Additional Information</label>
    <textarea class="form-control" id="additional-info" name="additional_info" rows="4" placeholder="Any additional details or special requests"></textarea>
  </div>
  <div class="col-12 text-center">
    <button type="submit" class="btn btn-primary">Submit Request</button>
  </div>
</form>
        </div>
        <div class="col-lg-4">
          <div class="sidebar-content">
            <h3>Why Get Certified?</h3>
            <ul>
              <li>Enhance your resume with credible credentials.</li>
              <li>Stand out to employers and clients.</li>
              <li>Validate your skills and knowledge.</li>
              <li>Unlock new career opportunities.</li>
              <li>Choose between digital or physical certificates.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta-section fade-in">
    <div class="container">
      <h2>Celebrate Your Achievement</h2>
      <p>Request your certificate today and share your success with the world!</p>
    </div>
  </section>

  <?php include 'footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
  <!-- Fade-in Animation on Scroll -->
  <script>
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