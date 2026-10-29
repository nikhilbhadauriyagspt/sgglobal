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
  <title>Government Jobs - Skill Grow Institution</title>
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
      height: 70vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #fff;
      overflow: hidden;
    }

    .hero-video {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: 0;
    }

    .hero-fallback {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('assets/images/govt-jobs-bg.jpg') no-repeat center/cover fixed;
      z-index: 0;
    }

    .hero-overlay {
      background: linear-gradient(135deg, rgba(0, 35, 101, 0.8), rgba(230, 169, 16, 0.4));
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
    }

    .hero-content {
      z-index: 2;
      padding: 40px;
    }

    .hero-section h1 {
      font-family: 'Roboto Slab', serif;
      font-size: 3.5rem;
      font-weight: 900;
      text-shadow: 4px 4px 8px rgba(0, 0, 0, 0.5);
    }

    .hero-section p {
      font-size: 1.4rem;
      margin: 20px 0;
    }

    .hero-section .btn {
      background: linear-gradient(90deg, var(--primary-color), #d99b0d);
      border: none;
      font-size: 1.3rem;
      padding: 15px 40px;
      border-radius: 50px;
      animation: pulse 2s infinite;
    }

    .hero-section .btn:hover {
      background: linear-gradient(90deg, #d99b0d, var(--primary-color));
      box-shadow: 0 0 15px rgba(230, 169, 16, 0.5);
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }

    /* Benefits Section */
    .benefits-section {
      padding: 100px 0;
      background: url('assets/images/benefits-bg.jpg') no-repeat center/cover fixed;
      position: relative;
    }

  
    .benefits-section h2 {
      font-family: 'Roboto Slab', serif;
      color:var(--secondary-color);
      text-align: center;
      margin-bottom: 60px;
      font-size: 3rem;
      font-weight: 600;
      z-index: 1;
    }

    .benefits-content {
      z-index: 1;
    }

    .benefits-content h3 {
      font-family: 'Roboto Slab', serif;
      color: #000;
      font-size: 2rem;
      margin-bottom: 20px;
    }

    .benefits-content p {
      font-size: 1.2rem;
      color:rgb(57, 57, 57);
      line-height: 1.8;
      margin-bottom: 20px;
    }

    .benefits-content ul {
      padding-left: 0;
      list-style: none;
    }

    .benefits-content ul li {
      font-size: 1.2rem;
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

  

    /* Roles Section */
    .roles-section {
      padding: 100px 0;
      background: linear-gradient(180deg, #f8f9fa, #e2e6ea);
    }

    .roles-section h2 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      text-align: center;
      margin-bottom: 60px;
      font-size: 3rem;
    }

    .role-card {
      background: #fff;
      border-radius: 15px;
      padding: 30px;
      transition: transform 0.3s ease;
      margin-bottom: 30px;
    }

    .role-card:hover {
      transform: translateY(-10px);
    }

    .role-card i {
      font-size: 3rem;
      color: var(--primary-color);
      margin-bottom: 20px;
    }

    .role-card h3 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      font-size: 1.8rem;
      margin-bottom: 15px;
    }

    .role-card p, .role-card ul {
      font-size: 1.1rem;
      line-height: 1.8;
    }

    .role-card ul {
      padding-left: 25px;
      margin-top: 15px;
    }

    /* Testimonials Section */
    .testimonials-section {
      padding: 100px 0;
      background: url('assets/images/testimonial-bg.jpg') no-repeat center/cover fixed;
      position: relative;
    }

    .testimonials-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 35, 101, 0.7);
      z-index: 0;
    }

    .testimonials-section h2 {
      font-family: 'Roboto Slab', serif;
      color: #fff;
      text-align: center;
      margin-bottom: 60px;
      font-size: 3rem;
      z-index: 1;
    }

    .testimonial-card {
      background: #fff;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
      text-align: center;
      transition: transform 0.3s ease;
    }

    .testimonial-card:hover {
      transform: scale(1.05);
    }

    .testimonial-card img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      margin-bottom: 20px;
    }

    .testimonial-card p {
      font-style: italic;
      font-size: 1.2rem;
      line-height: 1.8;
      margin-bottom: 20px;
    }

    .testimonial-card h4 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      font-size: 1.4rem;
    }

    /* FAQ Section */
    .faq-section {
      padding: 100px 0;
      background: linear-gradient(180deg, #e2e6ea, #f8f9fa);
    }

    .faq-section h2 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      text-align: center;
      margin-bottom: 60px;
      font-size: 3rem;
    }

    .accordion {
      z-index: 1;
    }

    .accordion-button {
      font-family: 'Roboto Slab', serif;
      font-size: 1.2rem;
      color: var(--secondary-color);
      background: #fff;
    }

    .accordion-button:not(.collapsed) {
      background: var(--primary-color);
      color: #fff;
    }

    .accordion-body {
      font-size: 1.1rem;
      line-height: 1.8;
    }

    /* CTA Section */
    .cta-section {
      background: linear-gradient(135deg, var(--secondary-color), #003087);
      color: #fff;
      padding: 80px 0;
      text-align: center;
    }

    .cta-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 3rem;
      margin-bottom: 20px;
    }

    .cta-section p {
      font-size: 1.3rem;
      margin-bottom: 30px;
    }

    .cta-section .btn {
      background: linear-gradient(90deg, var(--primary-color), #d99b0d);
      border: none;
      font-size: 1.5rem;
      padding: 15px 50px;
      border-radius: 50px;
      transition: all 0.3s ease;
      animation: pulse 2s infinite;
    }

    .cta-section .btn:hover {
      background: linear-gradient(90deg, #d99b0d, var(--primary-color));
      box-shadow: 0 0 20px rgba(230, 169, 16, 0.6);
      transform: scale(1.05);
    }

    /* Fade-in Animation */
    .fade-in {
      opacity: 0;
      transform: translateY(40px);
      transition: opacity 1s ease-out, transform 1s ease-out;
    }

    .fade-in.visible {
      opacity: 1;
      transform: translateY(0);
    }

    @media (max-width: 767px) {
      .benefits-content, .benefits-image {
        margin-bottom: 30px;
      }
      .benefits-image img {
        max-height: 300px;
      }
      .hero-section h1 {
        font-size: 2.5rem;
      }
      .hero-section p {
        font-size: 1.2rem;
      }
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <!-- Hero Section -->
  <section class="hero-section">
    <video class="hero-video" autoplay muted loop>
      <source src="assets/videos/govt-jobs-video.mp4" type="video/mp4">
    </video>
    <div class="hero-fallback"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1>Secure Your Future with Government Jobs</h1>
      <p>Join the prestigious world of government careers in <strong>Aviation</strong> and <strong>Tourism & Hospitality</strong> with Skill Grow Institution.</p>
      <a href="#benefits" class="btn btn-primary">Discover Benefits</a>
    </div>
  </section>

  <!-- Benefits Section -->
  <section class="benefits-section fade-in" id="benefits">
    <div class="container">
      <h2>Why Choose Government Jobs?</h2>
      <div class="row align-items-center mb-5">
        <div class="col-md-6 benefits-content">
          <h3>Unmatched Job Security</h3>
          <p>Government jobs offer unparalleled stability, ensuring a secure career path even in economic downturns. With roles in aviation and tourism, you’ll contribute to national infrastructure while enjoying a reliable income.</p>
          <ul>
            <li><i class="fas fa-shield-alt"></i> Lifetime job security with minimal risk of layoffs.</li>
            <li><i class="fas fa-home"></i> Housing and medical benefits for you and your family.</li>
            <li><i class="fas fa-clock"></i> Fixed working hours for better work-life balance.</li>
          </ul>
        </div>
        <div class="col-md-6 benefits-image">
          <img src="assets/images/certi/jscr.jpg" alt="Job Security" class="img-fluid">
        </div>
      </div>
      <div class="row align-items-center mb-5">
        <div class="col-md-6 benefits-content">
          <h3>Attractive Compensation & Benefits</h3>
          <p>Government positions come with competitive salaries, regular increments, and comprehensive benefits packages, making them highly rewarding. Our training prepares you for roles with excellent financial prospects.</p>
          <ul>
            <li><i class="fas fa-wallet"></i> Fixed salary with annual increments and bonuses.</li>
            <li><i class="fas fa-money-bill-wave"></i> Pension plans for post-retirement security.</li>
            <li><i class="fas fa-briefcase-medical"></i> Comprehensive health insurance coverage.</li>
          </ul>
        </div>
        <div class="col-md-6 benefits-image">
          <img src="assets/images/certi/compensation.jpg" alt="Compensation" class="img-fluid">
        </div>
      </div>
      <div class="row align-items-center mb-5">
        <div class="col-md-6 benefits-content">
          <h3>Career Growth & Prestige</h3>
          <p>Government jobs offer structured career progression and the prestige of serving the nation. Whether in airport management or tourism boards, you’ll gain respect and opportunities for advancement.</p>
          <ul>
            <li><i class="fas fa-arrow-up"></i> Clear promotion paths based on seniority and exams.</li>
            <li><i class="fas fa-star"></i> Social recognition and professional respect.</li>
            <li><i class="fas fa-graduation-cap"></i> Access to training and development programs.</li>
          </ul>
        </div>
        <div class="col-md-6 benefits-image">
          <img src="assets/images/certi/career.jpg" alt="Career Growth" class="img-fluid">
        </div>
      </div>
      <div class="row align-items-center">
        <div class="col-md-6 benefits-content">
          <h3>Impactful Contributions</h3>
          <p>Working in government roles allows you to make a meaningful impact on society. From ensuring safe air travel to promoting tourism, your work will shape the nation’s future.</p>
          <ul>
            <li><i class="fas fa-plane"></i> Contribute to aviation safety and efficiency.</li>
            <li><i class="fas fa-globe"></i> Promote cultural and economic growth through tourism.</li>
            <li><i class="fas fa-users"></i> Serve communities and enhance public services.</li>
          </ul>
        </div>
        <div class="col-md-6 benefits-image">
          <img src="assets/images/certi/impactful.jpg" alt="Impactful Work" class="img-fluid">
        </div>
      </div>
    </div>
  </section>

  <!-- Roles Section -->
<section class="roles-section fade-in">
  <div class="container">
    <h2>Explore Government Job Roles</h2>
    <div class="row">
      <div class="col-md-6">
        <div class="role-card">
          <i class="fas fa-shield-alt"></i>
          <h3>UP Police Constable</h3>
          <p>Join the Uttar Pradesh Police force as a constable to serve and protect the public.</p>
          <ul>
            <li><strong>Eligibility:</strong> 12th pass, age 18-25.</li>
            <li><strong>Salary:</strong> ₹25,000-₹35,000/month.</li>
            <li><strong>Skills:</strong> Physical fitness, discipline.</li>
          </ul>
        </div>
      </div>
      <div class="col-md-6">
        <div class="role-card">
          <i class="fas fa-shield-alt"></i>
          <h3>MP Police Sub Inspector</h3>
          <p>Serve as a Sub Inspector in Madhya Pradesh Police with responsibilities in law enforcement.</p>
          <ul>
            <li><strong>Eligibility:</strong> Graduate, age 21-28.</li>
            <li><strong>Salary:</strong> ₹35,000-₹45,000/month.</li>
            <li><strong>Skills:</strong> Leadership, investigation.</li>
          </ul>
        </div>
      </div>
      <div class="col-md-6">
        <div class="role-card">
          <i class="fas fa-train"></i>
          <h3>Indian Railway Ticket Collector</h3>
          <p>Work with Indian Railways to ensure smooth operations and customer service.</p>
          <ul>
            <li><strong>Eligibility:</strong> 12th pass, age 18-27.</li>
            <li><strong>Salary:</strong> ₹28,000-₹38,000/month.</li>
            <li><strong>Skills:</strong> Communication, punctuality.</li>
          </ul>
        </div>
      </div>
      <div class="col-md-6">
        <div class="role-card">
          <i class="fas fa-university"></i>
          <h3>SSC CGL Examiner</h3>
          <p>Conduct examinations and assist in administrative tasks at Staff Selection Commission.</p>
          <ul>
            <li><strong>Eligibility:</strong> Graduate, age 20-30.</li>
            <li><strong>Salary:</strong> ₹30,000-₹50,000/month.</li>
            <li><strong>Skills:</strong> Attention to detail, organization.</li>
          </ul>
        </div>
      </div>
      <div class="col-md-6">
        <div class="role-card">
          <i class="fas fa-university"></i>
          <h3>Bank Probationary Officer (PO)</h3>
          <p>Manage banking operations and customer relations in government banks.</p>
          <ul>
            <li><strong>Eligibility:</strong> Graduate, age 21-30.</li>
            <li><strong>Salary:</strong> ₹40,000-₹60,000/month.</li>
            <li><strong>Skills:</strong> Analytical skills, customer service.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>


  <!-- CTA Section -->
  <section class="cta-section fade-in">
    <div class="container">
      <h2>Ready to Start Your Government Career?</h2>
      <p>Take the first step towards a rewarding and secure future with Skill Grow Institution. Apply for government jobs today!</p>
      <a href="https://www.sarkariresult.com" target="_blank" class="btn btn-primary">Apply Now</a>
    </div>
  </section>

 

  <!-- FAQ Section -->
  <section class="faq-section fade-in">
    <div class="container">
      <h2>Frequently Asked Questions</h2>
      <div class="accordion" id="faqAccordion">
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
              What qualifications are needed for government jobs?
            </button>
          </h2>
          <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Most roles require a bachelor’s degree or diploma in relevant fields like aviation or tourism. Specific exams and age limits apply, which our courses prepare you for.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
              How does Skill Grow help with placements?
            </button>
          </h2>
          <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              We offer resume-building workshops, mock interviews, and direct connections with government recruiters to ensure you’re job-ready.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
              Where can I find government job openings?
            </button>
          </h2>
          <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Click the "Apply Now" button below to visit Sarkari Result, where you’ll find the latest government job listings and application details.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
              Are government jobs suitable for freshers?
            </button>
          </h2>
          <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Yes, many roles are open to freshers with the right qualifications. Our training equips you with the skills needed to succeed.
            </div>
          </div>
        </div>
      </div>
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