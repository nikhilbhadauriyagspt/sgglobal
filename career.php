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
  <title>Career Opportunities - Skill Grow Institution</title>
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
    padding-top: 130px;
    position: relative;
    height: 75vh;
    background: url(assets/images/career-bg.jpg) no-repeat center / cover fixed;
    display: flex
;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #fff;
}

    .hero-overlay {
      background: linear-gradient(135deg, rgba(0, 35, 101, 0.85), rgba(230, 169, 16, 0.3));
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
    }

    .hero-content {
      z-index: 2;
      padding: 30px;
    }

    .hero-section h1 {
      font-family: 'Roboto Slab', serif;
      font-size: 3.5rem;
      font-weight: 800;
      text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
    }

    .hero-section p {
      font-size: 1.4rem;
      margin: 20px 0;
    }

    .hero-section .btn {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      font-size: 1.2rem;
      padding: 10px 30px;
      transition: transform 0.3s ease;
    }

    .hero-section .btn:hover {
      transform: scale(1.05);
      background-color: #d99b0d;
    }

    /* Specialized Fields Section */
    .fields-section {
      padding: 80px 0;
      background: linear-gradient(180deg, #f8f9fa, #e9ecef);
    }

    .fields-section h2 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      text-align: center;
      margin-bottom: 50px;
      font-size: 2.8rem;
    }

    .field-card {
      background: #fff;
      border-radius: 15px;
      padding: 30px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .field-card:hover {
      transform: translateY(-12px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .field-card i {
      font-size: 3.5rem;
      color: var(--primary-color);
      margin-bottom: 20px;
    }

    .field-card h3 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      font-size: 2rem;
      margin-bottom: 15px;
    }

    .field-card p, .field-card ul {
      font-size: 1.1rem;
      line-height: 1.7;
    }

    .field-card ul {
      text-align: left;
      padding-left: 20px;
      list-style: none;
      margin-top: 15px;
    }
.field-card ul li i{
    font-size: 17px;
}
    .field-card img {
      border-radius: 10px;
      margin-top: 20px;
      max-height: 220px;
      object-fit: cover;
    }

    .badge {
      position: absolute;
      top: 15px;
      right: 15px;
      background-color: var(--primary-color);
      color: #fff;
      padding: 8px 15px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 500;
    }

    /* Jobs Section */
    .jobs-section {
      padding: 80px 0;
      background-color: #fff;
    }

    .jobs-section h2 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      text-align: center;
      margin-bottom: 50px;
      font-size: 2.8rem;
    }

    .job-card {
      background: #f8f9fa;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
      text-align: center;
      transition: transform 0.3s ease;
    }

    .job-card:hover {
      transform: scale(1.03);
    }

    .job-card i {
      font-size: 3rem;
      color: var(--primary-color);
      margin-bottom: 20px;
    }

    .job-card h3 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      font-size: 1.8rem;
      margin-bottom: 15px;
    }

    .job-card p, .job-card ul {
      font-size: 1.1rem;
      line-height: 1.7;
    }

    .job-card ul {
        list-style: none;
      text-align: left;
      padding-left: 20px;
      margin-top: 15px;
    }
    .job-card ul li i{
        font-size: 17px;
        list-style: none;
    }

    .job-card .btn {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      font-weight: 500;
      padding: 10px 25px;
    }

    .job-card .btn:hover {
      background-color: #d99b0d;
      border-color: #d99b0d;
    }

    /* Testimonials Section */
    .testimonials-section {
      padding: 80px 0;
      background: linear-gradient(180deg, #e9ecef, #f8f9fa);
    }

    .testimonials-section h2 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      text-align: center;
      margin-bottom: 50px;
      font-size: 2.8rem;
    }

    .testimonial-card {
      background: #fff;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
      text-align: center;
    }

    .testimonial-card img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-bottom: 15px;
    }

    .testimonial-card p {
      font-style: italic;
      font-size: 1.1rem;
      color:#000;
      line-height: 1.7;
      margin-bottom: 15px;
    }

    .testimonial-card h4 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      font-size: 1.3rem;
    }

    /* Stats Section */
    .stats-section {
      padding: 60px 0;
      background: var(--secondary-color);
      color: #fff;
      text-align: center;
    }

    .stats-section h2 {
      font-family: 'Roboto Slab', serif;
      margin-bottom: 40px;
      font-size: 2.8rem;
    }

    .stat-item {
      font-size: 1.2rem;
    }

    .stat-item h3 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 10px;
    }

    /* CTA Section */
    .cta-section {
      background: linear-gradient(135deg, var(--secondary-color), #003087);
      color: #fff;
      padding: 60px 0;
      text-align: center;
    }

    .cta-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      margin-bottom: 20px;
    }

    .cta-section p {
      font-size: 1.2rem;
      margin-bottom: 30px;
    }

    .cta-section .btn {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      font-size: 1.2rem;
      padding: 12px 40px;
    }

    .cta-section .btn:hover {
      background-color: #d99b0d;
    }

    /* Fade-in Animation */
    .fade-in {
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }

    .fade-in.visible {
      opacity: 1;
      transform: translateY(0);
    }
    
    /* New Section: Why Choose Skill Grow Careers */
    .benefits-section {
      padding: 100px 0;
      background: url('assets/images/benefits-bg.jpg') no-repeat center/cover fixed;
      position: relative;
    }


    .benefits-section h2 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      text-align: center;
      margin-bottom: 60px;
      font-size: 3rem;
      z-index: 1;
    }

    .benefits-content {
      z-index: 1;
    }

    .benefits-content h3 {
      font-family: 'Roboto Slab', serif;
      color:#000;
      font-size: 2rem;
      margin-bottom: 20px;
    }

    .benefits-content p {
      font-size: 1.2rem;
      color:rgb(91, 91, 91);
      line-height: 1.8;
      margin-bottom: 20px;
    }

    .benefits-content ul {
      padding-left: 0;
      list-style: none;
    }

    .benefits-content ul li {
      font-size:15px;
      color: #000;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
    }

    .benefits-content ul li i {
      color: var(--primary-color);
      font-size: 1.5rem;
      margin-right: 15px;
      transition: transform 0.3s ease;
    }

    .benefits-content ul li:hover i {
      transform: scale(1.2);
    }

    .benefits-image img {
      border-radius: 15px;
      transition: transform 0.3s ease;
      max-height: 400px;
      object-fit: cover;
      width: 100%;
    }

    .benefits-image img:hover {
      transform: scale(1.05);
    }

    @media (max-width: 767px) {
      .benefits-content, .benefits-image {
        margin-bottom: 30px;
      }
      .benefits-image img {
        max-height: 300px;
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
      <h1>Shape Your Future with Skill Grow Careers</h1>
      <p>Unlock exciting opportunities in <strong>Aviation</strong> and <strong>Tourism & Hospitality</strong> with our world-class training.</p>
      <!-- <a href="#fields" class="btn btn-primary">Explore Careers</a> -->
    </div>
  </section>

   <!-- New Section: Why Choose Skill Grow Careers -->
  <section class="benefits-section fade-in">
    <div class="container">
      <h2>Why Choose Skill Grow Careers</h2>
      <div class="row align-items-center">
        <div class="col-md-6 benefits-content">
          <h3>Your Path to Success</h3>
          <p>At Skill Grow Institution, we empower you to achieve your career goals with industry-leading training and support. Our programs in <strong>Aviation</strong> and <strong>Tourism & Hospitality</strong> are designed to set you apart.</p>
          <ul>
            <li><i class="fas fa-star"></i> Industry-aligned courses with hands-on training.</li>
            <li><i class="fas fa-briefcase"></i> 95% placement rate with top employers.</li>
            <li><i class="fas fa-users"></i> Dedicated career support and networking opportunities.</li>
            <li><i class="fas fa-certificate"></i> Globally recognized certifications.</li>
          </ul>
        </div>
        <div class="col-md-6 benefits-image">
          <img src="assets/images/certi/Career-Success.png" alt="Career Success" class="img-fluid">
        </div>
      </div>
    </div>
  </section>

  <!-- Specialized Fields Section -->
  <section class="fields-section fade-in" id="fields">
    <div class="container">
      <h2>Thrive in High-Demand Fields</h2>
      <div class="row g-4">
        <div class="col-md-6">
          <div class="field-card">
            <span class="badge">High Demand</span>
            <i class="fas fa-plane"></i>
            <h3>Aviation</h3>
            <p>Join the fast-paced world of aviation with careers that take you to new heights. Our programs prepare you for roles like:</p>
            <ul>
              <li><i class="fas fa-check-circle"></i> Commercial Pilot</li>
              <li><i class="fas fa-check-circle"></i> Air Traffic Controller</li>
              <li><i class="fas fa-check-circle"></i> Aircraft Maintenance Engineer</li>
            </ul>
            <img src="assets/images/certi/avation.webp" alt="Aviation Career" class="img-fluid">
          </div>
        </div>
        <div class="col-md-6">
          <div class="field-card">
            <span class="badge">Top Rated</span>
            <i class="fas fa-hotel"></i>
            <h3>Tourism & Hospitality</h3>
            <p>Create unforgettable experiences in the booming tourism and hospitality industry. Explore careers such as:</p>
            <ul>
              <li><i class="fas fa-check-circle"></i> Hotel Manager</li>
              <li><i class="fas fa-check-circle"></i> Travel Consultant</li>
              <li><i class="fas fa-check-circle"></i> Event Coordinator</li>
            </ul>
            <img src="assets/images/certi/hospitality.jpeg" alt="Hospitality Career" class="img-fluid">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Government & Private Jobs Section -->
  <section class="jobs-section fade-in">
    <div class="container">
      <h2>Choose Your Career Path</h2>
      <div class="row g-4">
        <div class="col-md-6">
    <div class="job-card">
    <i class="fas fa-briefcase"></i>
    <h3>Government Jobs</h3>
    <p>Secure a stable and rewarding career in the public sector.<br> <b>Popular Government Jobs:</b></p>
    <ul>
        <li><i class="fas fa-star"></i> UP Police</li>
        <li><i class="fas fa-star"></i> MP Police</li>
        <li><i class="fas fa-star"></i> Indian Railways</li>
    </ul>
    <a href="government-jobs.php" class="btn btn-primary">Apply for Government Jobs</a>
</div>
   </div>
        <div class="col-md-6">
          <div class="job-card">
            <i class="fas fa-building"></i>
            <h3>Private Sector Jobs</h3>
            <p>Work with top companies in aviation and hospitality. <br><b>Advantages include:</b></p>
            <ul>
              <li><i class="fas fa-star"></i> Competitive Salaries</li>
              <li><i class="fas fa-star"></i> Career Growth</li>
              <li><i class="fas fa-star"></i> Roles in Airlines & Luxury Hotels</li>
            </ul>
            <a href="private-jobs.php" class="btn btn-primary">Apply for Private Jobs</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="testimonials-section fade-in">
    <div class="container">
      <h2>Success Stories</h2>
      <div class="row g-4">
        <div class="col-md-6">
          <div class="testimonial-card">
            <img src="assets/images/testi/rahul-sharma.jpg" alt="Alumni 1">
            <p>"Skill Grow’s aviation program gave me the skills to become a commercial pilot. I’m now flying with a leading airline!"</p>
            <h4>Rahul Sharma, Pilot</h4>
          </div>
        </div>
        <div class="col-md-6">
          <div class="testimonial-card">
            <img src="assets/images/testi/priya.jpeg" alt="Alumni 2">
            <p>"The hospitality course transformed my career. I now manage a 5-star hotel, thanks to Skill Grow!"</p>
            <h4>Priya Singh, Hotel Manager</h4>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- CTA Section -->
  <section class="cta-section fade-in">
    <div class="container">
      <h2>Launch Your Dream Career</h2>
      <p>Join thousands of successful professionals. Enroll now and take the first step!</p>
      <a href="#fields" class="btn btn-primary">Get Started</a>
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