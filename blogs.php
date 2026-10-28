<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Blogs - Skill Grow Institution</title>
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
  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
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
      height: 60vh;
      background: url('assets/images/blog-bg.jpg') no-repeat center/cover fixed;
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

    .search-bar {
      max-width: 500px;
      margin: 20px auto;
    }

    .search-bar .input-group {
      position: relative;
    }

    .search-bar input {
      border-radius: 50px 0 0 50px;
      border: none;
      padding: 10px 20px;
      font-size: 1rem;
    }

    .search-bar button.search-btn {
      border-radius: 0 50px 50px 0;
      background: var(--primary-color);
      color: #fff;
      border: none;
      padding: 10px 20px;
      transition: background 0.3s;
    }

    .search-bar button.search-btn:hover {
      background: var(--secondary-color);
    }

    .search-bar button.reset-btn {
      border-radius: 50px;
      background: #dc3545;
      color: #fff;
      border: none;
      padding: 10px 15px;
      margin-left: 10px;
      transition: background 0.3s;
    }

    .search-bar button.reset-btn:hover {
      background: #b02a37;
    }

    #search-response {
      color: #dc3545;
      text-align: center;
      margin-top: 10px;
      display: none;
    }

    /* Blog Section */
    .blog-section {
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

    /* Category Filters */
    .category-filters {
    justify-content: center;
    text-align: center;
    border: none;
    gap:20px;
    margin-bottom: 40px;
}

    .category-btn {
      background: #fff !important;
      border: 1px solid var(--primary-color)!important;
      color: var(--secondary-color) !important;
      padding: 8px 20px;
      margin: 5px;
      border-radius: 20px !important;
      text-decoration: none;
      transition: background 0.3s, color 0.3s;
    }

    .category-btn:hover,
    .category-btn.active {
      background: var(--primary-color);
      color: #fff;
    }

    /* Blog Cards */
    .blog-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .blog-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .blog-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 10px 10px 0 0;
    }

    .blog-card-body {
      padding: 20px;
    }

    .blog-card-title {
      font-family: 'Roboto Slab', serif;
      font-size: 1.5rem;
      color: var(--secondary-color);
      margin-bottom: 10px;
    }

    .blog-card-meta {
      font-size: 0.9rem;
      color: #666;
      margin-bottom: 10px;
    }

    .blog-card-meta i {
      color: var(--primary-color);
      margin-right: 5px;
    }

    .blog-card-excerpt {
      font-size: 1rem;
      line-height: 1.6;
      margin-bottom: 15px;
    }

    .read-more {
      background: var(--primary-color);
      color: #fff;
      padding: 8px 20px;
      border-radius: 20px;
      text-decoration: none;
      transition: background 0.3s;
    }

    .read-more:hover {
      background: var(--secondary-color);
    }

    /* Sidebar */
    .sidebar {
      padding: 20px;
    }

    .sidebar-widget {
      margin-bottom: 30px;
    }

    .sidebar-widget h4 {
      font-family: 'Roboto Slab', serif;
      color: var(--secondary-color);
      font-size: 1.5rem;
      margin-bottom: 20px;
    }

    .sidebar-widget ul {
      list-style: none;
      padding: 0;
    }

    .sidebar-widget ul li {
      margin-bottom: 10px;
    }

    .sidebar-widget ul li a {
      color: #333;
      text-decoration: none;
      transition: color 0.3s;
    }

    .sidebar-widget ul li a:hover {
      color: var(--primary-color);
    }

    .recent-post {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }

    .recent-post img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 5px;
      margin-right: 15px;
    }

    .recent-post-title {
      font-size: 0.95rem;
      color: var(--secondary-color);
      line-height: 1.4;
    }

    .newsletter-form {
      display: flex;
      flex-direction: column;
    }

    .newsletter-form input {
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 10px;
      margin-bottom: 10px;
      font-size: 1rem;
    }

    .newsletter-form button {
      background: var(--primary-color);
      color: #fff;
      border: none;
      padding: 10px;
      border-radius: 5px;
      transition: background 0.3s;
    }

    .newsletter-form button:hover {
      background: var(--secondary-color);
    }

    /* Pagination */
    .pagination {
      justify-content: center;
      margin-top: 40px;
    }

    .page-link {
      color: var(--secondary-color);
      border: 1px solid var(--primary-color);
      margin: 0 5px;
      border-radius: 5px;
      transition: background 0.3s, color 0.3s;
    }

    .page-link:hover,
    .page-item.active .page-link {
      background: var(--primary-color);
      color: #fff;
      border-color: var(--primary-color);
    }

    /* Newsletter Section */
    .newsletter-section {
      background: var(--primary-color);
      color: #fff;
      padding: 60px 0;
      text-align: center;
    }

    .newsletter-section h2 {
      font-family: 'Roboto Slab', serif;
      font-size: 2.5rem;
      margin-bottom: 20px;
    }

    .newsletter-section p {
      font-size: 1.1rem;
      margin-bottom: 30px;
    }

    .newsletter-section .newsletter-form {
      max-width: 500px;
      margin: 0 auto;
      flex-direction: row;
    }

    .newsletter-section input {
      border-radius: 50px 0 0 50px;
      border: none;
      padding: 12px 20px;
      flex-grow: 1;
    }

    .newsletter-section button {
      border-radius: 0 50px 50px 0;
      padding: 12px 30px;
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
        height: 50vh;
      }

      .blog-card img {
        height: 150px;
      }

      .sidebar {
        margin-top: 40px;
      }

      .newsletter-section .newsletter-form {
        flex-direction: column;
      }

      .newsletter-section input,
      .newsletter-section button {
        border-radius: 5px;
      }

      .search-bar button.reset-btn {
        margin-left: 0;
        margin-top: 10px;
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
      <h1>📝 Explore Our Blogs</h1>
      <p>Stay updated with the latest insights, tips, and trends in education and career growth.</p>
     
    </div>
  </section>

  <!-- Blog Section -->
  <section class="blog-section">
    <div class="container">
      <h2 class="section-title">Latest Blog Posts</h2>
      <!-- Category Filters -->
      <div class="category-filters">
        

        <ul class="nav nav-tabs category-filters" id="categoryTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link category-btn  active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">All</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link category-btn " id="tech-tab" data-bs-toggle="tab" data-bs-target="#technology" type="button" role="tab" aria-controls="technology" aria-selected="false">Technology</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link category-btn " id="marketing-tab" data-bs-toggle="tab" data-bs-target="#marketing" type="button" role="tab" aria-controls="marketing" aria-selected="false">Marketing</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link category-btn " id="business-tab" data-bs-toggle="tab" data-bs-target="#business" type="button" role="tab" aria-controls="business" aria-selected="false">Business</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link category-btn " id="career-tab" data-bs-toggle="tab" data-bs-target="#career" type="button" role="tab" aria-controls="career" aria-selected="false">Career</button>
  </li>
</ul>
      </div>
      <div class="row">
        <!-- Blog Grid -->
        <div class="col-lg-8">
         <!-- Tab Content -->
<div class="tab-content pt-3" id="categoryTabContent">

  <!-- All Category -->
  <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
    <div class="row" id="blog-grid">

      <!-- Technology -->
      <div class="col-md-6 fade-in" data-category="technology">
        <div class="blog-card">
          <img src="assets/images/blogs/blog1.jpeg" alt="Top 5 IT Skills to Learn in 2025">
          <div class="blog-card-body">
            <h3 class="blog-card-title">Top 5 IT Skills to Learn in 2025</h3>
            <div class="blog-card-meta">
              <span><i class="fas fa-folder"></i> Technology</span>
            </div>
            <p class="blog-card-excerpt">Discover the most in-demand IT skills to boost your career, from cloud computing to cybersecurity.</p>
          </div>
        </div>
      </div>

      <div class="col-md-6 fade-in" data-category="technology">
        <div class="blog-card">
          <img src="assets/images/blogs/blog2.jpeg" alt="Introduction to Artificial Intelligence">
          <div class="blog-card-body">
            <h3 class="blog-card-title">Introduction to Artificial Intelligence</h3>
            <div class="blog-card-meta">
              <span><i class="fas fa-folder"></i> Technology</span>
            </div>
            <p class="blog-card-excerpt">Learn the basics of AI and how it’s transforming industries worldwide.</p>
          </div>
        </div>
      </div>

      <!-- Marketing -->
      <div class="col-md-6 fade-in" data-category="marketing">
        <div class="blog-card">
          <img src="assets/images/blogs/blog5.webp" alt="SEO Trends to Watch in 2025">
          <div class="blog-card-body">
            <h3 class="blog-card-title">SEO Trends to Watch in 2025</h3>
            <div class="blog-card-meta">
              <span><i class="fas fa-folder"></i> Marketing</span>
            </div>
            <p class="blog-card-excerpt">Stay ahead with the latest SEO strategies to rank higher on search engines.</p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Technology Tab -->
  <div class="tab-pane fade" id="technology" role="tabpanel" aria-labelledby="tech-tab">
    <div class="row" id="blog-grid">

      <div class="col-md-6 fade-in" data-category="technology">
        <div class="blog-card">
          <img src="assets/images/blogs/blog3.png" alt="Mastering Web Development in 2025">
          <div class="blog-card-body">
            <h3 class="blog-card-title">Mastering Web Development in 2025</h3>
            <div class="blog-card-meta">
              <span><i class="fas fa-folder"></i> Technology</span>
            </div>
            <p class="blog-card-excerpt">Explore the latest tools and frameworks to become a top web developer.</p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Marketing Tab -->
  <div class="tab-pane fade" id="marketing" role="tabpanel" aria-labelledby="marketing-tab">
    <div class="row" id="blog-grid">

      <div class="col-md-6 fade-in" data-category="marketing">
        <div class="blog-card">
          <img src="assets/images/blogs/blog4.jpg" alt="How to Create a Winning Digital Marketing Strategy">
          <div class="blog-card-body">
            <h3 class="blog-card-title">How to Create a Winning Digital Marketing Strategy</h3>
            <div class="blog-card-meta">
              <span><i class="fas fa-folder"></i> Marketing</span>
            </div>
            <p class="blog-card-excerpt">Expert tips to craft a digital marketing plan that drives traffic and conversions.</p>
          </div>
        </div>
      </div>

      <div class="col-md-6 fade-in" data-category="marketing">
        <div class="blog-card">
          <img src="assets/images/blogs/blog6.png" alt="Social Media Marketing for Beginners">
          <div class="blog-card-body">
            <h3 class="blog-card-title">Social Media Marketing for Beginners</h3>
            <div class="blog-card-meta">
              <span><i class="fas fa-folder"></i> Marketing</span>
            </div>
            <p class="blog-card-excerpt">A beginner’s guide to leveraging social media for brand growth.</p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Business Tab -->
  <div class="tab-pane fade" id="business" role="tabpanel" aria-labelledby="business-tab">
    <div class="row" id="blog-grid">

      <div class="col-md-6 fade-in" data-category="business">
        <div class="blog-card">
          <img src="assets/images/blogs/blog78.jpeg" alt="Starting Your Own E-commerce Business">
          <div class="blog-card-body">
            <h3 class="blog-card-title">Starting Your Own E-commerce Business</h3>
            <div class="blog-card-meta">
              <span><i class="fas fa-folder"></i> Business</span>
            </div>
            <p class="blog-card-excerpt">A step-by-step guide to launching a successful online store.</p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Career Tab -->
  <div class="tab-pane fade" id="career" role="tabpanel" aria-labelledby="career-tab">
    <div class="row" id="blog-grid">

      <div class="col-md-6 fade-in" data-category="career">
        <div class="blog-card">
          <img src="assets/images/blogs/blog9.webp" alt="Tips for Cracking Your First Job Interview">
          <div class="blog-card-body">
            <h3 class="blog-card-title">Tips for Cracking Your First Job Interview</h3>
            <div class="blog-card-meta">
              <span><i class="fas fa-folder"></i> Career</span>
            </div>
            <p class="blog-card-excerpt">Practical interview advice to help freshers make a strong first impression.</p>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>


          
          
        </div>
        <!-- Sidebar -->
        <div class="col-lg-4">
          <div class="sidebar fade-in">
            <!-- Categories -->
            <div class="sidebar-widget">
              <h4>Categories</h4>
              <ul>
                <li><a href="#technology">Technology (3)</a></li>
                <li><a href="#marketing">Marketing (3)</a></li>
                <li><a href="#business">Business (3)</a></li>
                <li><a href="#career">Career (3)</a></li>
              </ul>
            </div>
            <!-- Recent Posts -->
            <div class="sidebar-widget">
              <h4>Recent Posts</h4>
              <div class="recent-post">
                <img src="assets/images/blogs/blog1.jpeg" alt="Starting Your Own E-commerce Business">
                <div>
                  <a href="#blog7" class="recent-post-title">Starting Your Own E-commerce Business</a>
                </div>
              </div>
              <div class="recent-post">
                <img src="assets/images/blogs/blog2.jpeg" alt="Career Tips for Fresh Graduates">
                <div>
                  <a href="#blog10" class="recent-post-title">Career Tips for Fresh Graduates</a>
                </div>
              </div>
              <div class="recent-post">
                <img src="assets/images/blogs/blog3.png" alt="How to Create a Winning Digital Marketing Strategy">
                <div>
                  <a href="#blog4" class="recent-post-title">How to Create a Winning Digital Marketing Strategy</a>
                </div>
              </div>
            </div>
            <!-- Newsletter Signup -->
            <div class="sidebar-widget">
              <h4>Subscribe to Our Newsletter</h4>
              <form class="newsletter-form" id="sidebar-newsletter">
                <input type="email" placeholder="Enter your email" required>
                <button type="submit">Subscribe</button>
              </form>
              <div id="sidebar-newsletter-response" class="form-response"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Newsletter Section -->
  <section class="newsletter-section">
    <div class="container">
      <h2>Stay Updated with Our Latest Blogs</h2>
      <p>Join our newsletter to receive the latest insights and tips directly in your inbox.</p>
      <form class="newsletter-form" id="main-newsletter">
        <input type="email" placeholder="Enter your email" required>
        <button type="submit">Subscribe Now</button>
      </form>
      <div id="main-newsletter-response" class="form-response"></div>
    </div>
  </section>

  
<?php include 'footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.0.4/build/js/intlTelInput.min.js"></script>
  <!-- Newsletter Form Submission -->
  <script>
    ['sidebar-newsletter', 'main-newsletter', 'footer-newsletter'].forEach(formId => {
      document.getElementById(formId).addEventListener('submit', function(event) {
        event.preventDefault();
        const emailInput = this.querySelector('input[type="email"]');
        const responseDiv = document.getElementById(formId + '-response');

        if (emailInput.value) {
          responseDiv.textContent = 'Thank you for subscribing!';
          responseDiv.style.color = '#28a745';
          responseDiv.style.display = 'block';
          emailInput.value = '';
        } else {
          responseDiv.textContent = 'Please enter a valid email address.';
          responseDiv.style.color = '#dc3545';
          responseDiv.style.display = 'block';
        }
      });
    });
  </script>
  <!-- Navbar Active Link -->
  <script>
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', function () {
        document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
        this.classList.add('active');
      });
    });
  </script>
  <!-- Search Functionality -->
  <script>
    document.getElementById('search-form').addEventListener('submit', function(event) {
      event.preventDefault();
      const searchTerm = document.getElementById('search-input').value.trim().toLowerCase();
      const blogCards = document.querySelectorAll('.blog-card');
      const responseDiv = document.getElementById('search-response');
      let hasResults = false;

      blogCards.forEach(card => {
        const title = card.querySelector('.blog-card-title').textContent.toLowerCase();
        const excerpt = card.querySelector('.blog-card-excerpt').textContent.toLowerCase();
        const category = card.querySelector('.blog-card-meta span:last-child').textContent.toLowerCase();

        if (searchTerm === '' || title.includes(searchTerm) || excerpt.includes(searchTerm) || category.includes(searchTerm)) {
          card.style.display = 'block';
          hasResults = true;
        } else {
          card.style.display = 'none';
        }
      });

      responseDiv.style.display = hasResults || searchTerm === '' ? 'none' : 'block';
      responseDiv.textContent = hasResults || searchTerm === '' ? '' : 'No results found for "' + searchTerm + '"';
    });

    document.getElementById('reset-search').addEventListener('click', function() {
      const searchInput = document.getElementById('search-input');
      const blogCards = document.querySelectorAll('.blog-card');
      const responseDiv = document.getElementById('search-response');

      searchInput.value = '';
      blogCards.forEach(card => {
        card.style.display = 'block';
      });
      responseDiv.style.display = 'none';
      responseDiv.textContent = '';

      // Reset category filters
      document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
      document.querySelector('.category-btn[data-category="all"]').classList.add('active');
    });
  </script>
  <!-- Category Filter -->
  <script>
    document.querySelectorAll('.category-btn').forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');

        const category = this.dataset.category;
        const blogCards = document.querySelectorAll('.blog-card');
        const responseDiv = document.getElementById('search-response');

        blogCards.forEach(card => {
          if (category === 'all' || card.dataset.category === category) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        });

        // Clear search input and response
        document.getElementById('search-input').value = '';
        responseDiv.style.display = 'none';
        responseDiv.textContent = '';
      });
    });
  </script>
   <script>
        const navbar = document.querySelector('.navbar');

        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // Add/remove 'scrolled' class based on scroll position
            if (scrollTop > 50) { // Trigger after scrolling 50px
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
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