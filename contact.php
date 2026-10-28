<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us - Skill Grow Institution</title>
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
  <!-- GSAP for animations -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Contact Banner */
    .contact-banner {
      background: url('assets/images/banner-bg.jpg') no-repeat center center/cover;
      position: relative;
      padding: 200px 0 150px;
      text-align: center;
      color: #fff;
    }
    .contact-banner::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 35, 101, 0.7); /* --secondary-color #002365 */
    }
    .contact-banner .container {
      position: relative;
    }
    .contact-banner h1 {
      font-family: 'Roboto Slab', serif;
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }
    .contact-banner .breadcrumb {
      justify-content: center;
      background: transparent;
      padding: 0;
    }
    .contact-banner .breadcrumb-item a {
      color: #e6a910; /* --primary-color */
      text-decoration: none;
    }
    .contact-banner .breadcrumb-item a:hover {
      text-decoration: underline;
    }
    .contact-banner .breadcrumb-item {
      color: #fff;
      font-family: 'Roboto', sans-serif;
      font-size: 1rem;
    }
    .contact-banner .breadcrumb-item + .breadcrumb-item::before {
      color: #fff;
    }

    /* Contact Information Section */
    .contact-info-section {
      padding: 80px 0;
      background-color: #fff;
      position: relative;
      overflow: hidden;
    }
    .contact-info-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(0, 35, 101, 0.1), rgba(230, 169, 16, 0.1));
      z-index: 0;
    }
    .contact-info-section .container {
      position: relative;
      z-index: 1;
    }
    .contact-info-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      color: #002365;
      font-weight: 700;
      margin-bottom: 20px;
      text-align: left;
    }
    .contact-info-card {
      padding: 30px;
      background: linear-gradient(135deg, #002365, #1a3a8c);
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      color: #fff;
      transition: transform 0.3s ease;
    }
    .contact-info-card:hover {
      transform: translateY(-10px);
    }
    .contact-info-card ul {
      list-style: none;
      padding: 0;
    }
    .contact-info-card li {
      font-family: 'Roboto', sans-serif;
      font-size: 1.1rem;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }
    .contact-info-card i {
      color: #e6a910; /* --primary-color */
      font-size: 2rem;
      margin-right: 15px;
      transition: transform 0.3s ease;
    }
    .contact-info-card li:hover i {
      transform: scale(1.2);
    }
    .contact-info-card a {
      color: #fff;
      text-decoration: none;
    }
    .contact-info-card a:hover {
      color: #e6a910;
    }
    .contact-info-images {
      position: relative;
      height: 100%;
      min-height: 400px;
    }
    .contact-info-image {
    position: absolute;
    width: 380px;
    height: 290px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    border: 2px solid #e6a910;
    transition: transform 0.3s ease;
}
    .contact-info-image:hover {
      transform: scale(1.05);
    }
    .contact-info-image:nth-child(1) {
      top: 0;
      left: 0;
      transform: rotate(-5deg);
      z-index: 3;
    }
    .contact-info-image:nth-child(2) {
      top: 100px;
      left: 150px;
      transform: rotate(5deg);
      z-index: 2;
    }

    /* Contact Form Section */
    .contact-form-section {
      padding: 80px 0;
      background-color: #f8f9fa;
    }
    .contact-form-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      color: #002365;
      font-weight: 700;
      text-align: center;
      margin-bottom: 40px;
    }
    .contact-form-card {
      padding: 30px;
      background-color: #fff;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s ease;
    }
    .contact-form-card:hover {
      transform: translateY(-10px);
    }
    .contact-form .form-control {
      font-family: 'Roboto', sans-serif;
      font-size: 1rem;
      padding: 12px;
      border-radius: 5px;
      border: 1px solid #ced4da;
      margin-bottom: 20px;
      transition: border-color 0.3s ease;
    }
    .contact-form .form-control:focus {
      border-color: #e6a910;
      box-shadow: 0 0 5px rgba(230, 169, 16, 0.5);
    }
    .contact-form textarea {
      resize: vertical;
      min-height: 150px;
    }
    .contact-form .enroll-btn {
      width: 100%;
      padding: 12px;
      font-family: 'Roboto', sans-serif;
      font-size: 1.1rem;
      font-weight: 500;
      background: #e6a910;
      border: none;
      transition: transform 0.3s ease, background 0.3s ease;
    }
    .contact-form .enroll-btn:hover {
      transform: scale(1.05);
      background: #d89b00;
    }

    /* Map Section */
    .map-section {
      padding: 80px 0;
      background-color: #fff;
    }
    .map-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      color: #002365;
      font-weight: 700;
      text-align: center;
      margin-bottom: 40px;
    }
    .map-container {
      position: relative;
      padding-bottom: 56.25%; /* 16:9 aspect ratio */
      height: 0;
      overflow: hidden;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      border: 3px solid #e6a910;
    }
    .map-container iframe {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: 0;
    }

    /* CTA Section */
    .cta-section {
      padding: 60px 0;
      background: linear-gradient(135deg, #e6a910, #d89b00);
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .cta-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('https://via.placeholder.com/1920x1080?text=Pattern') no-repeat center center/cover;
      opacity: 0.1;
      z-index: 0;
    }
    .cta-section .container {
      position: relative;
      z-index: 1;
    }
    .cta-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      color: #fff;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }
    .cta-section p {
      font-family: 'Roboto', sans-serif;
      font-size: 1.2rem;
      color: #fff;
      margin-bottom: 30px;
    }
    .cta-section .enroll-btn {
      padding: 12px 30px;
      font-size: 1.1rem;
      background: #002365;
      color: #fff;
      border: none;
      transition: transform 0.3s ease, background 0.3s ease;
    }
    .cta-section .enroll-btn:hover {
      transform: scale(1.05);
      background: #1a3a8c;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
      .contact-banner {
        padding: 60px 0;
      }
      .contact-banner h1 {
        font-size: 2rem;
      }
      .contact-banner .breadcrumb-item {
        font-size: 0.9rem;
      }
      .contact-info-section,
      .contact-form-section,
      .map-section,
      .cta-section {
        padding: 60px 0;
      }
      .contact-info-section h2,
      .contact-form-section h2,
      .map-section h2,
      .cta-section h2 {
        font-size: 2rem;
      }
      .contact-info-card {
        margin-bottom: 30px;
      }
      .contact-info-card li {
        font-size: 1rem;
      }
      .contact-info-card i {
        font-size: 1.5rem;
      }
      .contact-info-images {
        min-height: 300px;
        margin-top: 30px;
      }
      .contact-info-image {
        width: 200px;
        height: 140px;
      }
      .contact-info-image:nth-child(1) {
        top: 0;
        left: 20px;
      }
      .contact-info-image:nth-child(2) {
        top: 80px;
        left: 120px;
      }
      .contact-form-card {
        padding: 20px;
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

  <!-- Contact Banner -->
  <section class="contact-banner">
    <div class="container">
      <h1>Contact Us</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
        </ol>
      </nav>
    </div>
  </section>

  <!-- Contact Information Section -->
  <section class="contact-info-section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h2>Contact Information</h2>
          <div class="contact-info-card">
            <ul>
              <li>
                <i class="fas fa-map-marker-alt"></i>
                <span>Metro Plaza Meerut,250002</span>
              </li>
              <li>
                <i class="fas fa-phone-alt"></i>
                <a href="tel:+919217431562">+91 9217431562</a>
              </li>
              <li>
                <i class="fas fa-envelope"></i>
                <a href="mailto:info@skillgrowinstitution.in">info@skillgrowinstitution.in</a>
                <a href="mailto:join@skillgrowinstitution.in">join@skillgrowinstitution.in</a>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-md-6">
          <div class="contact-info-images">
            <img src="assets/images/contact/learning.jpeg" alt="Learning" class="contact-info-image">
            <img src="assets/images/contact/digital.jpg" alt="Campus" class="contact-info-image">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Form Section -->
  <section class="contact-form-section">
    <div class="container">
      <h2>Send Us a Message</h2>
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="contact-form-card">
     <form class="contact-form" id="contact-form">
  <div id="alert-box" style="display: none;"></div>

  <div class="mb-3">
    <label for="name" class="form-label">Full Name</label>
    <input type="text" class="form-control" id="name" name="name" required>
  </div>
  <div class="mb-3">
    <label for="phone" class="form-label">Phone No</label>
    <input type="tel" class="form-control" id="phone" name="phone" required>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">Email Address</label>
    <input type="email" class="form-control" id="email" name="email" required>
  </div>
  <div class="mb-3">
    <label for="subject" class="form-label">Subject</label>
    <input type="text" class="form-control" id="subject" name="subject" required>
  </div>
  <div class="mb-3">
    <label for="message" class="form-label">Message</label>
    <textarea class="form-control" id="message" name="message" required></textarea>
  </div>
  <button type="submit" class="enroll-btn btn btn-primary">Send Message</button>
</form>

<script>
document.getElementById("contact-form").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent form from submitting normally

    const formData = new FormData(this);

    fetch("submit_contact.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const alertBox = document.getElementById("alert-box");
        if (data.status === "success") {
            alertBox.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            document.getElementById("contact-form").reset();
        } else {
            alertBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
        alertBox.style.display = "block";

        setTimeout(() => {
            alertBox.style.display = "none";
        }, 5000); // Hide after 5 sec
    })
    .catch(err => {
        console.error("Error:", err);
    });
});
</script>

            <div id="form-response" class="mt-3"></div>
          </div>
        </div>
      </div>
    </div>
  </section>



  <!-- Call to Action Section -->
  <section class="cta-section">
    <div class="container">
      <h2>Ready to Start Learning?</h2>
      <p>Join thousands of learners at Skill Grow Institution and take the first step toward your goals.</p>
      <a href="explore-course.php" class="enroll-btn">Explore Courses</a>
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

  
    // GSAP animations
    gsap.from('.contact-info-card', {
      opacity: 0,
      y: 50,
      duration: 1,
      scrollTrigger: {
        trigger: '.contact-info-section',
        start: 'top 80%',
      }
    });
    gsap.from('.contact-info-image', {
      opacity: 0,
      scale: 0.8,
      duration: 1,
      stagger: 0.2,
      scrollTrigger: {
        trigger: '.contact-info-section',
        start: 'top 80%',
      }
    });
    gsap.from('.contact-form-card', {
      opacity: 0,
      y: 50,
      duration: 1,
      scrollTrigger: {
        trigger: '.contact-form-section',
        start: 'top 80%',
      }
    });
    gsap.from('.map-container', {
      opacity: 0,
      scale: 0.9,
      duration: 1,
      scrollTrigger: {
        trigger: '.map-section',
        start: 'top 80%',
      }
    });
    gsap.from('.cta-section h2, .cta-section p, .cta-section .enroll-btn', {
      opacity: 0,
      y: 30,
      duration: 1,
      stagger: 0.2,
      scrollTrigger: {
        trigger: '.cta-section',
        start: 'top 80%',
      }
    });
  </script>
</body>

</html>