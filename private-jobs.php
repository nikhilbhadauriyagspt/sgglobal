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
  <title>Private Sector Jobs - Skill Grow Institution</title>
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
      background: #f8f9fa;
    }

    /* Hero Section */
    .hero-section {
      position: relative;
      height: 60vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #fff;
      background: url('assets/images/private-jobs-bg.jpg') no-repeat center/cover fixed;
    }

    .hero-overlay {
      background: rgba(0, 35, 101, 0.7);
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
      font-size: 3rem;
      font-weight: 700;
    }

    .hero-section p {
      font-size: 1.3rem;
      margin: 15px 0;
    }

    .hero-section .btn {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      font-size: 1.2rem;
      padding: 10px 30px;
      border-radius: 5px;
      transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .hero-section .btn:hover {
      background-color: #d99b0d;
      transform: scale(1.05);
    }

    /* Benefits Section */
    .benefits-section {
      padding: 80px 0;
      background: #fff;
    }

    .benefits-section h2 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      text-align: center;
      margin-bottom: 50px;
      font-size: 2.5rem;
    }

    .benefits-content {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 10px;
    }

    .benefits-content h3 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      font-size: 1.8rem;
      margin-bottom: 15px;
    }

    .benefits-content p {
      font-size: 1.1rem;
      color: #555;
      line-height: 1.7;
      margin-bottom: 15px;
    }

    .benefits-content ul {
      padding-left: 0;
      list-style: none;
    }

    .benefits-content ul li {
      font-size: 1.1rem;
      color: #333;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
    }

    .benefits-content ul li i {
      color: var(--primary-color);
      font-size: 1.3rem;
      margin-right: 10px;
    }

    .benefits-image img {
      border-radius: 10px;
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
      max-height: 350px;
      object-fit: cover;
      width: 100%;
      transition: transform 0.3s ease;
    }

    .benefits-image img:hover {
      transform: scale(1.03);
    }

    /* Roles Section */
    .roles-section {
      padding: 80px 0;
      background: #f0f4f8;
    }

    .roles-section h2 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      text-align: center;
      margin-bottom: 50px;
      font-size: 2.5rem;
    }

    .role-card {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
      margin-bottom: 20px;
    }

    .role-card:hover {
      transform: translateY(-5px);
    }

    .role-card i {
      font-size: 2.5rem;
      color: var(--primary-color);
      margin-bottom: 15px;
    }

    .role-card h3 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      font-size: 1.6rem;
      margin-bottom: 10px;
    }

    .role-card p, .role-card ul {
      font-size: 1rem;
      line-height: 1.6;
    }

    .role-card ul {
      padding-left: 20px;
      margin-top: 10px;
    }

    /* Testimonials Section */
    .testimonials-section {
      padding: 80px 0;
      background: #fff;
    }

    .testimonials-section h2 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      text-align: center;
      margin-bottom: 50px;
      font-size: 2.5rem;
    }

    .testimonial-card {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: transform 0.3s ease;
    }

    .testimonial-card:hover {
      transform: scale(1.03);
    }

    .testimonial-card img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-bottom: 15px;
    }

    .testimonial-card p {
      font-style: italic;
      font-size: 1rem;
      line-height: 1.6;
      margin-bottom: 15px;
    }

    .testimonial-card h4 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      font-size: 1.2rem;
    }

    /* FAQ Section */
    .faq-section {
      padding: 80px 0;
      background: #f0f4f8;
    }

    .faq-section h2 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      text-align: center;
      margin-bottom: 50px;
      font-size: 2.5rem;
    }

    .accordion-item {
      background: #fff;
      border-radius: 10px;
      margin-bottom: 10px;
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }

    .accordion-button {
      font-family: 'Roboto Slab', serif;
      font-size: 1.1rem;
      color: var(--secondary-color);
      background: #fff;
    }

    .accordion-button:not(.collapsed) {
      background: var(--primary-color);
      color: #fff;
    }

    .accordion-body {
      font-size: 1rem;
      line-height: 1.6;
    }

    /* CTA Section */
    .cta-section {
      background: var(--secondary-color);
      color: #fff;
      padding: 60px 0;
      text-align: center;
    }

    .cta-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      margin-bottom: 15px;
    }

    .cta-section p {
      font-size: 1.2rem;
      margin-bottom: 25px;
    }

    .cta-section .btn {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      font-size: 1.3rem;
      padding: 12px 40px;
      border-radius: 5px;
      transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .cta-section .btn:hover {
      background-color: #d99b0d;
      transform: scale(1.05);
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

    @media (max-width: 767px) {
      .benefits-content, .benefits-image {
        margin-bottom: 20px;
      }
      .benefits-image img {
        max-height: 250px;
      }
      .hero-section h1 {
        font-size: 2rem;
      }
      .hero-section p {
        font-size: 1rem;
      }
      .benefits-section h2, .roles-section h2, .testimonials-section h2, .faq-section h2 {
        font-size: 2rem;
      }
      .cta-section .btn {
        font-size: 1.1rem;
        padding: 10px 30px;
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
      <h1>Launch Your Career in the Private Sector</h1>
      <p>Explore exciting opportunities in <strong>Aviation</strong> and <strong>Tourism & Hospitality</strong> with Skill Grow Institution.</p>
      <a href="#benefits" class="btn btn-primary">Discover Opportunities</a>
    </div>
  </section>

  <!-- Benefits Section -->
  <section class="benefits-section fade-in" id="benefits">
    <div class="container">
      <h2>Why Choose Private Sector Jobs?</h2>
      <div class="row align-items-center mb-4">
        <div class="col-md-6 benefits-content">
          <h3>Competitive Salaries</h3>
          <p>Private sector jobs offer attractive pay packages with performance-based incentives, allowing you to earn based on your skills and dedication. Our training prepares you for high-paying roles in top companies.</p>
          <ul>
            <li><i class="fas fa-money-bill-wave"></i> High starting salaries with bonuses.</li>
            <li><i class="fas fa-chart-line"></i> Performance-based increments.</li>
            <li><i class="fas fa-wallet"></i> Additional perks like travel allowances.</li>
          </ul>
        </div>
        <div class="col-md-6 benefits-image">
          <img src="assets/images/certi/business.jpg" alt="Competitive Salaries" class="img-fluid">
        </div>
      </div>
      <div class="row align-items-center mb-4">
        <div class="col-md-6 benefits-content">
          <h3>Fast Career Growth</h3>
          <p>The private sector rewards talent and hard work with rapid promotions and leadership opportunities. Whether in airlines or hotels, you can climb the career ladder quickly with our training.</p>
          <ul>
            <li><i class="fas fa-arrow-up"></i> Quick promotions based on performance.</li>
            <li><i class="fas fa-briefcase"></i> Leadership roles in top organizations.</li>
            <li><i class="fas fa-star"></i> Recognition for outstanding contributions.</li>
          </ul>
        </div>
        <div class="col-md-6 benefits-image">
          <img src="assets/images/certi/Career-Growth.jpg" alt="Career Growth" class="img-fluid">
        </div>
      </div>
      <div class="row align-items-center mb-4">
        <div class="col-md-6 benefits-content">
          <h3>Dynamic Work Environment</h3>
          <p>Private sector jobs offer fast-paced, innovative workplaces where you can thrive. From airlines to luxury hotels, you’ll work in exciting, customer-focused settings.</p>
          <ul>
            <li><i class="fas fa-building"></i> Modern, tech-driven workplaces.</li>
            <li><i class="fas fa-users"></i> Collaborative and diverse teams.</li>
            <li><i class="fas fa-lightbulb"></i> Opportunities to innovate and excel.</li>
          </ul>
        </div>
        <div class="col-md-6 benefits-image">
          <img src="assets/images/certi/worken.jpg" alt="Work Environment" class="img-fluid">
        </div>
      </div>
      <div class="row align-items-center">
        <div class="col-md-6 benefits-content">
          <h3>Global Opportunities</h3>
          <p>Private sector roles in aviation and hospitality open doors to international careers. Work with global brands and travel the world while advancing your career.</p>
          <ul>
            <li><i class="fas fa-globe"></i> Jobs with international airlines and hotel chains.</li>
            <li><i class="fas fa-plane"></i> Opportunities to work abroad.</li>
            <li><i class="fas fa-passport"></i> Exposure to diverse cultures and markets.</li>
          </ul>
        </div>
        <div class="col-md-6 benefits-image">
          <img src="assets/images/certi/Global-Connection.jpg" alt="Global Opportunities" class="img-fluid">
        </div>
      </div>
    </div>
  </section>

  <!-- Roles Section -->
  <section class="roles-section fade-in">
    <div class="container">
      <h2>Explore Private Sector Job Roles</h2>
      <div class="row">
        <div class="col-md-6">
          <div class="role-card">
            <i class="fas fa-plane"></i>
            <h3>Cabin Crew</h3>
            <p>Deliver exceptional in-flight service with top airlines, ensuring passenger safety and comfort.</p>
            <ul>
              <li><strong>Eligibility:</strong> 12th pass, age 18-27.</li>
              <li><strong>Salary:</strong> ₹30,000-₹60,000/month.</li>
              <li><strong>Skills:</strong> Communication, customer service.</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6">
          <div class="role-card">
            <i class="fas fa-hotel"></i>
            <h3>Hotel Manager</h3>
            <p>Lead operations in luxury hotels, overseeing staff and ensuring guest satisfaction.</p>
            <ul>
              <li><strong>Eligibility:</strong> Degree in hospitality, age 21-35.</li>
              <li><strong>Salary:</strong> ₹50,000-₹1,00,000/month.</li>
              <li><strong>Skills:</strong> Leadership, organization.</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6">
          <div class="role-card">
            <i class="fas fa-suitcase"></i>
            <h3>Travel Consultant</h3>
            <p>Plan and book travel itineraries for clients, working with leading travel agencies.</p>
            <ul>
              <li><strong>Eligibility:</strong> 12th pass, age 18-30.</li>
              <li><strong>Salary:</strong> ₹25,000-₹50,000/month.</li>
              <li><strong>Skills:</strong> Sales, customer service.</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6">
          <div class="role-card">
            <i class="fas fa-calendar-alt"></i>
            <h3>Event Coordinator</h3>
            <p>Organize corporate and social events for hotels or event management firms.</p>
            <ul>
              <li><strong>Eligibility:</strong> Degree in event management, age 21-30.</li>
              <li><strong>Salary:</strong> ₹30,000-₹60,000/month.</li>
              <li><strong>Skills:</strong> Planning, creativity.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- CTA Section -->
  <section class="cta-section fade-in">
    <div class="container">
      <h2>Ready to Join the Private Sector?</h2>
      <p>Start your exciting career in aviation or hospitality with Skill Grow Institution. Apply for private sector jobs today!</p>
      <a href="https://www.naukri.com" target="_blank" class="btn btn-primary">Apply Now</a>
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
              What qualifications are needed for private sector jobs?
            </button>
          </h2>
          <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Most roles require a 12th pass or a degree in relevant fields like aviation or hospitality. Our courses provide the necessary skills and certifications.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
              How does Skill Grow assist with private sector placements?
            </button>
          </h2>
          <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              We offer resume workshops, interview coaching, and partnerships with top private companies to help you secure your dream job.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
              Where can I find private job openings?
            </button>
          </h2>
          <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Click the "Apply Now" button below to visit Naukri.com, where you’ll find the latest private sector job listings.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
              Are private sector jobs stable?
            </button>
          </h2>
          <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              While less stable than government jobs, private sector roles offer competitive pay and growth, especially in reputed firms. Our training ensures you’re job-ready.
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