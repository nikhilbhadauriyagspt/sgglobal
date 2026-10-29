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
  <title>About Us - Skill Grow Institution</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-/U16V2+3m4t8..." crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Google Fonts (Roboto & Roboto Slab) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Banner Section */
    .about-banner {
      background: url('assets/images/banner/skill-aboutbg.jpg') no-repeat right/cover;
      position: relative;
      padding: 240px 0 200px;
      text-align: center;
      color: #fff;
    }
    .about-banner::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #0023659f;
    }
    .about-banner .container {
      position: relative;
    }
    .about-banner h1 {
      font-family: 'Roboto Slab', serif;
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .about-banner .breadcrumb {
      justify-content: center;
      background: transparent;
      padding: 0;
    }
    .about-banner .breadcrumb-item a {
      color: #e6a910; /* --primary-color */
      text-decoration: none;
    }
    .about-banner .breadcrumb-item a:hover {
      text-decoration: underline;
    }
    .about-banner .breadcrumb-item {
      color: #fff;
      font-family: 'Roboto', sans-serif;
      font-size: 1rem;
    }
    .about-banner .breadcrumb-item + .breadcrumb-item::before {
      color: #fff;
    }
.about-content-section {
      padding: 80px 0;
      background-color: #fff;
    }
    .about-content-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      color: #002365;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .about-content-section p {
      font-family: 'Roboto', sans-serif;
      font-size: 1.1rem;
      color: #6c757d;
      line-height: 1.6;
      margin-bottom: 20px;
    }
    .about-images {
      position: relative;
      height: 100%;
      min-height: 400px;
    }
    .about-image {
      position: absolute;
      width: 250px;
      height: 180px;
      object-fit: cover;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      border: 2px solid #002365;
      transition: transform 0.3s ease;
    }
    .about-image:hover {
      transform: scale(1.05);
    }
    .about-image:nth-child(1) {
      top: 0;
      left: 0;
      transform: rotate(-5deg);
      z-index: 3;
    }
    .about-image:nth-child(2) {
      top: 100px;
      left: 150px;
      transform: rotate(5deg);
      z-index: 2;
    }
    .about-image:nth-child(3) {
      top: 220px;
      left: 50px;
      transform: rotate(0deg);
      z-index: 1;
    }
    /* Mission & Vision Section */
    .mission-vision-section {
      padding: 80px 0;
      background-color: #f8f9fa;
    }
    .mission-vision-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      color: #002365;
      font-weight: 700;
      text-align: center;
      margin-bottom: 40px;
    }
    .mission-vision-card {
      padding: 30px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      height: 100%;
      transition: transform 0.3s ease;
    }
    .mission-vision-card:hover {
      transform: translateY(-10px);
    }
    .mission-vision-card h3 {
      font-family: 'Roboto Slab', serif;
      font-size: 1.8rem;
      color: #002365;
      font-weight: 600;
      margin-bottom: 15px;
    }
    .mission-vision-card p {
      font-family: 'Roboto', sans-serif;
      font-size: 1rem;
      color: #6c757d;
      line-height: 1.6;
    }

    /* History Section */
    .history-section {
      padding: 80px 0;
      background-color: #fff;
    }
    .history-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      color: #002365;
      font-weight: 700;
      text-align: center;
      margin-bottom: 40px;
    }
    .history-content p {
      font-family: 'Roboto', sans-serif;
      font-size: 1.1rem;
      color: #6c757d;
      line-height: 1.6;
      margin-bottom: 20px;
      text-align: center;
    }

    /* Values Section */
    .values-section {
      padding: 80px 0;
      background-color: #f8f9fa;
    }
    .values-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      color: #002365;
      font-weight: 700;
      text-align: center;
      margin-bottom: 40px;
    }
    .value-card {
      padding: 20px;
      text-align: center;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }
    .value-card:hover {
      transform: translateY(-10px);
    }
    .value-card i {
      font-size: 2.5rem;
      color: #e6a910; /* --primary-color */
      margin-bottom: 15px;
    }
    .value-card h4 {
      font-family: 'Roboto Slab', serif;
      font-size: 1.5rem;
      color: #002365;
      font-weight: 600;
      margin-bottom: 10px;
    }
    .value-card p {
      font-family: 'Roboto', sans-serif;
      font-size: 1rem;
      color: #6c757d;
    }

    /* Team Section */
    .team-section {
      padding: 80px 0;
      background-color: #fff;
    }
    .team-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      color: #002365;
      font-weight: 700;
      text-align: center;
      margin-bottom: 40px;
    }
    .team-card {
      text-align: center;
      padding: 20px;
      background-color: #f8f9fa;
      border-radius: 10px;
      transition: transform 0.3s ease;
    }
    .team-card:hover {
      transform: translateY(-10px);
    }
    .team-card img {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      margin-bottom: 15px;
      object-fit: cover;
    }
    .team-card h4 {
      font-family: 'Roboto Slab', serif;
      font-size: 1.3rem;
      color: #002365;
      font-weight: 600;
      margin-bottom: 5px;
    }
    .team-card p {
      font-family: 'Roboto', sans-serif;
      font-size: 1rem;
      color: #6c757d;
    }

    /* Testimonials Section */
    .testimonials-section {
      padding: 80px 0;
      background-color: #002365; /* --secondary-color */
      color: #fff;
    }
    .testimonials-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      font-weight: 700;
      text-align: center;
      margin-bottom: 40px;
    }
    .testimonial-card {
      padding: 20px;
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      text-align: center;
      transition: transform 0.3s ease;
    }
    .testimonial-card:hover {
      transform: translateY(-10px);
    }
    .testimonial-card img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-bottom: 15px;
      object-fit: cover;
    }
    .testimonial-card p {
      font-family: 'Roboto', sans-serif;
      font-size: 1rem;
      font-style: italic;
      margin-bottom: 15px;
    }
    .testimonial-card h5 {
      font-family: 'Roboto Slab', serif;
      font-size: 1.2rem;
      font-weight: 500;
      color: #e6a910; /* --primary-color */
    }

    /* Achievements Section */
    .achievements-section {
      padding: 80px 0;
      background-color: #fff;
      text-align: center;
    }
    .achievements-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      color: #002365;
      font-weight: 700;
      margin-bottom: 40px;
    }
    .achievement-card i {
      font-size: 2.5rem;
      color: #e6a910; /* --primary-color */
      margin-bottom: 15px;
    }
    .achievement-card h3 {
      font-family: 'Roboto Slab', serif;
      font-size: 2rem;
      color: #002365;
      font-weight: 700;
    }
    .achievement-card p {
      font-family: 'Roboto', sans-serif;
      font-size: 1rem;
      color: #6c757d;
    }

    /* Partnerships Section */
    .partnerships-section {
      padding: 80px 0;
      background-color: #f8f9fa;
    }
    .partnerships-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      color: #002365;
      font-weight: 700;
      text-align: center;
      margin-bottom: 40px;
    }
    .partner-logo img {
      max-width: 150px;
      filter: grayscale(100%);
      transition: filter 0.3s ease;
    }
    .partner-logo img:hover {
      filter: grayscale(0%);
    }

    /* CTA Section */
    .cta-section {
      padding: 60px 0;
      background-color: #e6a910; /* --primary-color */
      text-align: center;
    }
    .cta-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      color: #fff;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .cta-section p {
      font-family: 'Roboto', sans-serif;
      font-size: 1.2rem;
      color: #fff;
      margin-bottom: 30px;
    }
.cta-section .enroll-btn{
    background: #002365;
}
    /* Responsive Adjustments */
    @media (max-width: 768px) {
      .about-banner {
        padding: 160px 0 30px;
    }
      .about-banner h1 {
        font-size: 2rem;
      }
      .about-banner .breadcrumb-item {
        font-size: 0.9rem;
      }
      .about-content-section {
        padding: 60px 0;
      }
      .about-content-section h2 {
        font-size: 2rem;
      }
      .about-content-section p {
        font-size: 1rem;
      }
      .about-images {
        min-height: 300px;
        margin-top: 30px;
      }
      .about-image {
        width: 200px;
        height: 140px;
      }
      .about-image:nth-child(1) {
        top: 0;
        left: 20px;
      }
      .about-image:nth-child(2) {
        top: 80px;
        left: 120px;
      }
      .about-image:nth-child(3) {
        top: 160px;
        left: 50px;
      }
      .mission-vision-section,
      .history-section,
      .values-section,
      .team-section,
      .testimonials-section,
      .achievements-section,
      .partnerships-section,
      .cta-section {
        padding: 60px 0;
      }
      .mission-vision-section h2,
      .history-section h2,
      .values-section h2,
      .team-section h2,
      .testimonials-section h2,
      .achievements-section h2,
      .partnerships-section h2,
      .cta-section h2 {
        font-size: 2rem;
      }
      .mission-vision-card h3 {
        font-size: 1.5rem;
      }
      .mission-vision-card p,
      .history-content p,
      .value-card p,
      .team-card p,
      .testimonial-card p,
      .achievement-card p {
        font-size: 0.9rem;
      }
      .value-card h4 {
        font-size: 1.3rem;
      }
      .team-card img {
        width: 120px;
        height: 120px;
      }
      .team-card h4 {
        font-size: 1.2rem;
      }
      .testimonial-card img {
        width: 60px;
        height: 60px;
      }
      .testimonial-card h5 {
        font-size: 1rem;
      }
      .achievement-card h3 {
        font-size: 1.5rem;
      }
      .partner-logo img {
        max-width: 120px;
      }
      .cta-section p {
        font-size: 1rem;
      }
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <!-- About Us Banner -->
  <section class="about-banner">
    <div class="container">
      <h1>About Skill Grow Institution</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">About Us</li>
        </ol>
      </nav>
    </div>
  </section>
<!-- New About Content Section -->
  <section class="about-content-section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h2>About Skill Grow Institution</h2>
          <p>Skill Grow Institution is dedicated to empowering individuals with practical, industry-relevant skills in Business, Technology, Design, and more. Since our founding in 2018, we’ve grown into a global platform, serving over 10,000 learners across 100+ countries. Our commitment to accessible, high-quality education drives us to create innovative learning experiences that bridge the gap between ambition and achievement.</p>
          <a href="#courses" class="enroll-btn">Explore Our Courses</a>
        </div>
        <div class="col-md-6">
          <div class="about-images">
            <img src="assets/images/about/classroom.jpg" alt="Classroom" class="about-image">
            <img src="assets/images/about/students.jpg" alt="Students" class="about-image">
            <img src="assets/images/about/elearning.jpg" alt="Learning" class="about-image">
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Mission & Vision Section -->
  <section class="mission-vision-section">
    <div class="container">
      <h2>Our Mission & Vision</h2>
      <div class="row">
        <div class="col-md-6 mb-4">
          <div class="mission-vision-card">
            <h3>Our Mission</h3>
            <p>At Skill Grow Institution, our mission is to provide accessible, high-quality education to learners worldwide. We aim to empower individuals with practical skills in Business, Technology, Design, and more, helping them achieve their personal and professional goals.</p>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="mission-vision-card">
            <h3>Our Vision</h3>
            <p>We envision a world where everyone has the opportunity to learn, grow, and succeed. By offering diverse courses and fostering a supportive learning community, we strive to bridge the gap between ambition and achievement.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- History Section -->
  <section class="history-section">
    <div class="container">
      <h2>Our Journey</h2>
      <div class="history-content">
        <p>Founded in 2018, Skill Grow Institution started as a small initiative to provide accessible education in Business and Technology. Over the years, we’ve expanded our offerings to include Design, Marketing, and Specialized Fields like Aviation and Tourism, reaching learners in over 100 countries.</p>
        <p>Our commitment to quality education and practical learning has helped us grow into a trusted platform, empowering over 10,000 students to achieve their dreams. We continue to innovate and expand, ensuring every learner has the tools to succeed.</p>
      </div>
    </div>
  </section>

  <!-- Values Section -->
  <section class="values-section">
    <div class="container">
      <h2>Our Core Values</h2>
      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="value-card">
            <i class="fas fa-graduation-cap"></i>
            <h4>Excellence in Education</h4>
            <p>We are committed to delivering high-quality, practical education that prepares learners for real-world challenges.</p>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="value-card">
            <i class="fas fa-globe"></i>
            <h4>Accessibility for All</h4>
            <p>We believe education should be accessible to everyone, regardless of location or background.</p>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="value-card">
            <i class="fas fa-users"></i>
            <h4>Community & Support</h4>
            <p>We foster a supportive learning community where students can connect, collaborate, and grow together.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Team Section -->
  <section class="team-section">
  <div class="container">
    <h2>Meet Our Team</h2>
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="team-card">
          <img src="assets/images/testi/priya.jpeg" alt="Priya Sharma">
         <h4>Priya Sharma</h4>
<p>Head Instructor</p>
<p>With over 15 years of experience in the education industry, Priya is dedicated to empowering learners through innovative programs.</p>
 </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="team-card">
          <img src="assets/images/testi/amin.jpg" alt="Amit Verma">
          <h4>Amit Verma</h4>
          <p>Head of Technology</p>
          <p>Amit leads our tech initiatives, helping students stay ahead in the ever-evolving digital world through hands-on training.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="team-card">
          <img src="assets/images/testi/neha-kapoor.webp" alt="Neha Kapoor">
          <h4>Neha Kapoor</h4>
<p>Course Designer & Trainer</p>
<p>Neha brings a fresh perspective to our design courses, ensuring creativity, clarity, and practical skills in every module.</p>
  </div>
      </div>
    </div>
  </div>
</section>


  <!-- Testimonials Section -->
  <section class="testimonials-section">
    <div class="container">
      <h2>What Our Students Say</h2>
      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="testimonial-card">
            <img src="assets/images/testi/rahul-sharma.jpg" alt="Alex Carter">
            <p>"Skill Grow Institution transformed my career! The Digital Marketing course gave me the skills to launch my own business."</p>
            <h5>Rahul Sharma</h5>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="testimonial-card">
            <img src="assets/images/testi/priya.jpg" alt="Sarah Lee">
            <p>"I loved the flexibility of the Web Technology course. The hands-on projects were incredibly helpful!"</p>
            <h5>Priya Sharma</h5>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="testimonial-card">
            <img src="assets/images/testi/aman.jpg" alt="Michael Brown">
            <p>"The instructors are amazing, and the community support made learning so much easier."</p>
            <h5>Aman</h5>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Achievements Section -->
  <section class="achievements-section">
    <div class="container">
      <h2>Our Achievements</h2>
      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="achievement-card">
            <i class="fas fa-users"></i>
            <h3>10,000+</h3>
            <p>Students Enrolled</p>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="achievement-card">
            <i class="fas fa-book-open"></i>
            <h3>50+</h3>
            <p>Courses Offered</p>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="achievement-card">
            <i class="fas fa-globe"></i>
            <h3>100+</h3>
            <p>Countries Reached</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Partnerships Section -->
  <section class="partnerships-section">
    <div class="container">
      <h2>Our Partners</h2>
     <div class="row">
      <div class="owl-carousel owl-theme client partner-carousel">
  <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/emirates.png" alt="Partner 1">
    </div>
  </div>
  <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/Air-France.png" alt="Partner 2">
    </div>
  </div>
  <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/all-turkish.png" alt="Partner 3">
    </div>
  </div>
   <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/british.png" alt="Partner 3">
    </div>
  </div>
   <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/etihad.png" alt="Partner 3">
    </div>
  </div>
   <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/Lufthansa-Simbolo.png" alt="Partner 3">
    </div>
  </div>
   <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/Qatar-Airways.png" alt="Partner 3">
    </div>
  </div>
   <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/singapore-airlines.png" alt="Partner 3">
    </div>
  </div>
   <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/hcl.png" alt="Partner 3">
    </div>
  </div>
   <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/tcs.png" alt="Partner 3">
    </div>
  </div>
   <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/rambagh.png" alt="Partner 3">
    </div>
  </div>
   <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/vindhyavilas-badhav.png" alt="Partner 3">
    </div>
  </div>
   <div class="item">
    <div class="partner-logo">
      <img src="assets/images/client/Logo_ITC.png" alt="Partner 3">
    </div>
  </div>
 
  <div class="item">
    <div class="partner-logo">
      <img src="assets/images/logo/logopng.png" alt="Partner 4">
    </div>
  </div>
  
</div>
     </div>
    </div>
  </section>

  <!-- Call to Action Section -->
  <section class="cta-section">
    <div class="container">
      <h2>Ready to Start Your Learning Journey?</h2>
      <p>Join thousands of learners at Skill Grow Institution and take the first step toward achieving your goals.</p>
      <a href="#courses" class="enroll-btn">Explore Courses</a>
    </div>
  </section>

<?php include 'footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    // Navbar scroll effect
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
      let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      if (scrollTop > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });

    // Add active class to nav links on click
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', function() {
        document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
        this.classList.add('active');
      });
    });
  </script>
  <script>
  $(document).ready(function(){
    $(".partner-carousel").owlCarousel({
      loop: true,
      margin: 20,
      autoplay: true,
      autoplayTimeout: 2000,
      responsive: {
        0: {
          items: 2
        },
        600: {
          items: 3
        },
        1000: {
          items: 7
        }
      }
    });
  });
</script>
</body>

</html>