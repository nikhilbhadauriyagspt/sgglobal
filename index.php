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
  <title>SG Skill</title>
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
  <!-- If the favicon is in the root folder -->
<link rel="icon" href="assets/images/logo/logopng.png" type="image/x-icon">

<!-- If the favicon is in an 'images' folder -->
<link rel="icon" href="assets/images/logo/logopng.png" type="image/x-icon">

<!-- If using PNG -->
<link rel="icon" href="assets/images/logo/logopng.png" type="image/png">

<!-- Optional for Apple devices -->
<link rel="apple-touch-icon" href="assets/images/logo/logopng.png">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="i" href="">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    integrity="sha512-/U16V2+3m4t8..." crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />


</head>

<body>

<?php include 'header.php'; ?>

    <!-- Banner -->
    <section class="banner">
       <div class="banner-content text-center">
    <h3 class="small-heading">Welcome to Skill Grow Institution</h3>
    <h1 class="large-heading" id="course-heading"></h1>
    <div class="d-flex gap-3 justify-content-center">
      <a href="explore-course.php" class="banner-btn">Browse Courses</a>
      <a href="enrollnow.php" class="banner-btn">Enroll Now</a>
      <a href="aboutus.php" class="banner-btn">Learn More</a>
     
    </div>
  </div>
    </section>

  <!-- Intro Section (Previously AI Course Section) -->
    <section class="ai-course-section">
        <div class="container">
            <div class="row">
                <div class="content-left">
                    <h1>Unlock Your Potential with Skill Grow Institution</h1>
                    <p>Explore a diverse range of courses at Skill Grow Institution, from Business Studies to Advanced IT and Design. Empower yourself with the skills to succeed in today’s dynamic world!</p>
                    <a href="explore-course.php"  class="enroll-btn">Discover Courses</a>
                </div>
            </div>
        </div>
    </section>


<!-- Why Choose Section (Previously What to Expect Section) -->
    <section class="expect-section">
        <div class="container">
            <h2 class="text-center">Why Choose Skill Grow Institution?</h2>
            <div class="row">
                <!-- Versatile Skills Across Fields -->
                <div class="col-md-4 mb-4">
                    <div class="expert-box">
                        <h4>Versatile Skills Across Fields</h4>
                        <ul>
                            <li>
                                <i class="fas fa-check"></i>
                                Gain expertise in diverse areas like Business, Technology, Design, and more through our comprehensive courses.
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Build a strong foundation with practical knowledge that prepares you for real-world challenges.
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Practical Applications & Career Growth -->
                <div class="col-md-4 mb-4">
                    <div class="expert-box">
                        <h4>Practical Applications & Career Growth</h4>
                        <ul>
                            <li>
                                <i class="fas fa-check"></i>
                                Apply your skills through hands-on projects, such as designing a website, analyzing data, or creating marketing strategies.
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Identify opportunities in your field and learn how to leverage your skills for career advancement.
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Acquire industry-recognized skills to enhance your resume and stand out to employers.
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Flexibility, Innovation & Opportunities -->
                <div class="col-md-4 mb-4">
                    <div class="expert-box">
                        <h4>Flexibility, Innovation & Opportunities</h4>
                        <ul>
                            <li>
                                <i class="fas fa-check"></i>
                                Learn at your own pace with flexible course schedules tailored to your needs.
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Explore innovative fields like AI, Digital Transformation, and UX Design to stay ahead of industry trends.
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Enhance your current skill set, whether you're in business, hospitality, or creative industries.
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                Open doors to exciting career paths in high-demand fields like IT, marketing, and aviation.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section id="courses" class="courses-section py-5">
  <div class="container">
    <h2 class="text-center mb-4">Explore Our Diverse Range of Courses</h2>
    <p class="text-center mb-5">Discover courses tailored to your career goals, from Business & Management to cutting-edge Technology & IT, designed to empower you with in-demand skills.</p>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs justify-content-center mb-4" id="coursesTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="business Tab" data-bs-toggle="tab" data-bs-target="#business" type="button" role="tab" aria-controls="business" aria-selected="true">Business & Management</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="specialized-tab" data-bs-toggle="tab" data-bs-target="#specialized" type="button" role="tab" aria-controls="specialized" aria-selected="false">Specialized Fields</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tech-tab" data-bs-toggle="tab" data-bs-target="#tech" type="button" role="tab" aria-controls="tech" aria-selected="false">Technology & IT</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="design-tab" data-bs-toggle="tab" data-bs-target="#design" type="button" role="tab" aria-controls="design" aria-selected="false">Design & Creativity</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="marketing-tab" data-bs-toggle="tab" data-bs-target="#marketing" type="button" role="tab" aria-controls="marketing" aria-selected="false">Marketing</button>
      </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="coursesTabContent">
      <!-- Business & Management -->
      <div class="tab-pane fade show active" id="business" role="tabpanel" aria-labelledby="business-tab">
        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                <div class="card-img">
                    <img src="assets/images/course/online.webp" width="100%" alt="">
                </div>
                <h5 class="card-title">Business Studies</h5>
                <p class="card-text">Learn the fundamentals of business operations, strategy, and entrepreneurship to thrive in dynamic markets.</p>
                <a href="course-detail.php?course=business-studies" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/management.webp" width="100%" alt="">
                </div>
                <h5 class="card-title">Management Studies</h5>
                <p class="card-text">Develop leadership and organizational skills to manage teams and drive business success.</p>
                <a href="course-detail.php?course=management-studies" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/finacial.png" width="100%" alt="">
                </div>
                <h5 class="card-title">Financial Markets and Product</h5>
                <p class="card-text">Explore financial instruments, markets, and products to make informed investment decisions.</p>
                <a href="course-detail.php?course=banking-and-finance" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/business-intelligence-png.png" width="100%" alt="">
                </div>
                <h5 class="card-title">Business Intelligence</h5>
                <p class="card-text">Master data-driven decision-making with tools and techniques for business analytics.</p>
                <a href="course-detail.php?course=business-intelligence" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Specialized Fields -->
      <div class="tab-pane fade" id="specialized" role="tabpanel" aria-labelledby="specialized-tab">
        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/avation.webp" width="100%" alt="">
                </div>
                <h5 class="card-title">Aviation</h5>
                <p class="card-text">Gain insights into aviation operations, safety, and management for a high-flying career.</p>
                <a href="course-detail.php?course=airport-management" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/toiuris.png" width="100%" alt="">
                </div>
                <h5 class="card-title">Tourism and Hospitality</h5>
                <p class="card-text">Learn customer service and management skills for the global tourism industry.</p>
                <a href="course-detail.php?course=tourism-and-hospitality" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/jounss.webp" width="100%" alt="">
                </div>
                <h5 class="card-title">Journalism and Mass Communication</h5>
                <p class="card-text">Develop storytelling and media skills for journalism and communication careers.</p>
                <a href="course-detail.php?course=journalism-and-mass-communication" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/life-skills.png" width="100%" alt="">
                </div>
                <h5 class="card-title">Life Skills</h5>
                <p class="card-text">Enhance essential skills for personal growth and professional success.</p>
                <a href="explore-course.php" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/generalstudies.webp" width="100%" alt="">
                </div>
                <h5 class="card-title">General Studies</h5>
                <p class="card-text">Build a broad knowledge base with interdisciplinary studies for versatile career paths.</p>
                <a href="explore-course.php" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Technology & IT -->
      <div class="tab-pane fade" id="tech" role="tabpanel" aria-labelledby="tech-tab">
        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/web-technology.webp" width="100%" alt="">
                </div>
                <h5 class="card-title">Web Technology</h5>
                <p class="card-text">Master HTML, CSS, and JavaScript to build responsive and modern websites.</p>
                <a href="course-detail.php?course=web-technology" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/python.webp" width="100%" alt="">
                </div>
                <h5 class="card-title">Python Data Structures</h5>
                <p class="card-text">Learn Python programming and data structures for efficient coding and analysis.</p>
                <a href="course-detail.php?course=python-data-structures" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/robotic.png" width="100%" alt="">
                </div>
                <h5 class="card-title">Introduction to Robotic Programming</h5>
                <p class="card-text">Explore the basics of programming robots for automation and innovation.</p>
                <a href="explore-course.php" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/Revolutionising-AI.png" width="100%" alt="">
                </div>
                <h5 class="card-title">AI in Human Resource Management</h5>
                <p class="card-text">Leverage AI to streamline HR processes and enhance workforce management.</p>
                <a href="explore-course.php" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/advance-digital.png" width="100%" alt="">
                </div>
                <h5 class="card-title">Advance Digital Transformation</h5>
                <p class="card-text">Drive organizational change with advanced digital strategies and technologies.</p>
                <a href="explore-course.php" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/datasnyalyst.png" width="100%" alt="">
                </div>
                <h5 class="card-title">Advance Data Analytics</h5>
                <p class="card-text">Analyze complex datasets to uncover insights and inform decisions.</p>
                <a href="course-detail.php?course=advance-data-analytics" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/artificial-intelligence.webp" width="100%" alt="">
                </div>
                <h5 class="card-title">IT Automation</h5>
                <p class="card-text">Automate IT processes to improve efficiency and scalability.</p>
                <a href="explore-course.php" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/Support-PNG-Transparent-Picture.png" width="100%" alt="">
                </div>
                <h5 class="card-title">IT Support</h5>
                <p class="card-text">Develop skills to troubleshoot and support IT systems effectively.</p>
                <a href="course-detail.php?course=it-support" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Design & Creativity -->
      <div class="tab-pane fade" id="design" role="tabpanel" aria-labelledby="design-tab">
        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                  <div class="card-img">
                    <img src="assets/images/course/design.jpg" width="100%" alt="">
                </div>
                <h5 class="card-title">Design Study</h5>
                <p class="card-text">Learn the principles of design to create visually impactful projects.</p>
                <a href="explore-course.php" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                <div class="card-img">
                    <img src="assets/images/course/th.jpeg" width="100%" alt="">
                </div>
                <h5 class="card-title">Cosmetology Studies</h5>
                <p class="card-text">Master techniques in beauty and personal care for a creative career.</p>
                <a href="explore-course.php" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                <div class="card-img">
                    <img src="assets/images/course/3d.jpg" width="100%" alt="">
                </div>
                <h5 class="card-title">3D Painting and Design for Educators</h5>
                <p class="card-text">Create 3D designs and paintings for educational applications.</p>
                <a href="explore-course.php" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                <div class="card-img">
                    <img src="assets/images/course/What-is-UI-UX-Design.jpg" width="100%" alt="">
                </div>
                <h5 class="card-title">UX Design</h5>
                <p class="card-text">Design user-friendly interfaces with a focus on user experience.</p>
                <a href="course-detail.php?course=ux-design" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Marketing -->
      <div class="tab-pane fade" id="marketing" role="tabpanel" aria-labelledby="marketing-tab">
        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                <div class="card-img">
                    <img src="assets/images/course/digita.jpeg" width="100%" alt="">
                </div>
                <h5 class="card-title">Digital Marketing Strategy</h5>
                <p class="card-text">Craft effective digital marketing campaigns to boost brand visibility.</p>
                <a href="course-detail.php?course=digital-marketing-strategy" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 course-card">
              <div class="card-body">
                <div class="card-img">
                    <img src="assets/images/course/Digital-Marketing.png" width="100%" alt="">
                </div>
                <h5 class="card-title">Digital Marketing & E-commerce</h5>
                <p class="card-text">Learn strategies to succeed in online marketing and e-commerce platforms.</p>
                <a href="course-detail.php?course=digital-marketing-ecommerce" class="btn course-btn">Learn More</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Call to Action -->
    <div class="text-center mt-5">
      <a href="explore-course.php" class="enroll-btn">Browse All Courses</a>
    </div>
  </div>
</section>
 <!-- Curriculum Section -->
    <section id="curriculum" class="curriculum-section">
        <div class="container">
            <h2 class="text-center">Explore Our Comprehensive Learning Journey</h2>
            <!-- Course Details -->
            <div class="course-details">
                <div class="detail-card">
                    <p>Duration</p>
                    <h5>Custom</h5>
                </div>
                <div class="detail-card">
                    <p>Time</p>
                    <h5>Flexible Schedule</h5>
                </div>
                <div class="detail-card">
                    <p>Course Format</p>
                    <h5>Videos, Projects, Quizzes</h5>
                </div>
                <div class="detail-card">
                    <p>Study Method</p>
                    <h5>Online & Interactive</h5>
                </div>
            </div>
            <!-- Weekly Curriculum -->
            <div class="week-item">
                <div class="d-flex align-items-start justify-content-between">
                    <span class="week-number">01</span>
                    <div class="week-box">
                        <h3>Foundations of Business & Management</h3>
                        <div class="sub-heading">Custom | Flexible Schedule</div>
                    </div>
                    <p>Dive into the essentials of business strategy and management. Learn how to analyze markets, develop business plans, and lead teams effectively with real-world case studies.</p>
                </div>
            </div>
            <div class="week-item">
                <div class="d-flex align-items-start justify-content-between">
                    <span class="week-number">02</span>
                    <div class="week-box">
                        <h3>Introduction to Web Technology</h3>
                        <div class="sub-heading">Custom | Flexible Schedule</div>
                    </div>
                    <p>Get hands-on with HTML, CSS, and JavaScript. Build your first webpage and understand the basics of web development to kickstart your tech journey.</p>
                </div>
            </div>
            <div class="week-item">
                <div class="d-flex align-items-start justify-content-between">
                    <span class="week-number">03</span>
                    <div class="week-box">
                        <h3>Digital Marketing Essentials</h3>
                        <div class="sub-heading">Custom | Flexible Schedule</div>
                    </div>
                    <p>Master the art of digital marketing with strategies for SEO, social media, and content creation. Create your own campaign and see measurable results.</p>
                </div>
            </div>
            <div class="week-item">
                <div class="d-flex align-items-start justify-content-between">
                    <span class="week-number">04</span>
                    <div class="week-box">
                        <h3>Creative Design & UX Principles</h3>
                        <div class="sub-heading">Custom | Flexible Schedule</div>
                    </div>
                    <p>Explore the fundamentals of design and user experience. Learn to create visually stunning designs and user-friendly interfaces with tools like Figma.</p>
                </div>
            </div>
            <div class="week-item">
                <div class="d-flex align-items-start justify-content-between">
                    <span class="week-number">05</span>
                    <div class="week-box">
                        <h3>Python for Data Analysis</h3>
                        <div class="sub-heading">Custom | Flexible Schedule</div>
                    </div>
                    <p>Unlock the power of Python to analyze data. Work on real datasets to create visualizations and gain insights that drive decision-making.</p>
                </div>
            </div>
            <div class="week-item">
                <div class="d-flex align-items-start justify-content-between">
                    <span class="week-number">06</span>
                    <div class="week-box">
                        <h3>Tourism & Hospitality Management</h3>
                        <div class="sub-heading">Custom | Flexible Schedule</div>
                    </div>
                    <p>Learn the ins and outs of the tourism industry. Develop skills in customer service, event planning, and sustainable tourism practices to excel in hospitality.</p>
                </div>
            </div>
        </div>
    </section>


   <!-- Quote Section -->
    <section class="quote-section">
       <blockquote>
  Education is the manifestation of the perfection already in man.
  <cite>— Swami Vivekananda —</cite>
</blockquote>

        <a href="enrollnow.php" class="enroll-btn">Start Learning Today</a>
    </section>

    <!-- Who Can Benefit Section -->
    <section class="expect-section">
        <div class="container">
            <h2 class="text-center">Who Can Benefit from Skill Grow Institution?</h2>
            <div class="row">
                <!-- Beginners -->
                <div class="col-lg-3 col-md-6">
                    <div class="audience-item">
                        <div class="audience-heading">
                            <i class="fas fa-user audience-icon"></i>
                            <h3>Beginners</h3>
                        </div>
                        <p>No prior experience needed. Perfect for individuals eager to explore new fields like business, design, or technology and build a strong foundation for future growth.</p>
                    </div>
                </div>
                <!-- Tech Enthusiasts -->
                <div class="col-lg-3 col-md-6">
                    <div class="audience-item">
                        <div class="audience-heading">
                            <i class="fas fa-laptop-code audience-icon"></i>
                            <h3>Tech Enthusiasts</h3>
                        </div>
                        <p>Dive into cutting-edge fields like Web Technology, Python, and AI. Gain practical skills to innovate and experiment with the latest tools in technology.</p>
                    </div>
                </div>
                <!-- Professionals -->
                <div class="col-lg-3 col-md-6">
                    <div class="audience-item">
                        <div class="audience-heading">
                            <i class="fas fa-briefcase audience-icon"></i>
                            <h3>Professionals</h3>
                        </div>
                        <p>Enhance your career with courses in Business Studies, Management, and Digital Marketing. Learn to optimize processes, lead teams, and stay competitive in your industry.</p>
                    </div>
                </div>
                <!-- Creatives -->
                <div class="col-lg-3 col-md-6">
                    <div class="audience-item">
                        <div class="audience-heading">
                            <i class="fas fa-code audience-icon"></i>
                            <h3>Creatives</h3>
                        </div>
                        <p>Unleash your creativity with courses in Design, UX, and Cosmetology. Develop skills to create stunning visuals and user experiences that make an impact.</p>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-12 text-center">
                    <a href="enrollnow.php" class="enroll-btn">Enroll Now</a>
                </div>
            </div>
        </div>
    </section>


<section id="testimonial" class="testimonial-section">
    <div class="container">
        <h2>Real Success Stories from Our Indian Students</h2>
        <div class="row">
            <!-- Testimonial 1 -->
            <div class="col-lg-6">
                <div class="testimonial-item">
                    <img src="assets/images/testi/rahul-sharma.jpg" alt="Rahul" class="testimonial-img">
                    <div class="testimonial-content">
                        <div class="testimonial-name">Rahul Sharma, 22, Pilot</div>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <div class="testimonial-description">
                            I always dreamed of flying and <b>Skill Grow Institution</b> helped turn that dream into reality. The trainers are industry experts and the mock interview sessions were a big help. Today, I'm proud to be selected by a leading airline.
                        </div>
                    </div>
                </div>
            </div>
            <!-- Testimonial 2 -->
            <div class="col-lg-6">
                <div class="testimonial-item">
                    <img src="assets/images/testi/priya.jpg" alt="Priya" class="testimonial-img">
                    <div class="testimonial-content">
                        <div class="testimonial-name">Priya Mehta, 20, Hospitality Management Student</div>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <div class="testimonial-description">
                            After joining the Hospitality course, I’ve gained both knowledge and confidence. <b>Skill Grow Institution</b> gave me practical exposure through hotel visits and guest lectures. I recently got placed at a 5-star property in Mumbai. I'm truly thankful!
                        </div>
                    </div>
                </div>
            </div>
            <!-- Testimonial 3 -->
            <div class="col-lg-6">
                <div class="testimonial-item">
                    <img src="assets/images/testi/aman.jpg" alt="Aman" class="testimonial-img">
                    <div class="testimonial-content">
                        <div class="testimonial-name">Aman Verma, 24, Tourism & Travel Graduate</div>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <div class="testimonial-description">
                            <b>Skill Grow Institution</b> prepared me for the travel industry in the best possible way. I learned how to manage travel bookings, client handling, and even international tourism trends. I’m now working with a reputed travel agency in Delhi.
                        </div>
                    </div>
                </div>
            </div>
            <!-- Testimonial 4 -->
            <div class="col-lg-6">
                <div class="testimonial-item">
                    <img src="assets/images/testi/sneha.jpeg" alt="Sneha" class="testimonial-img">
                    <div class="testimonial-content">
                        <div class="testimonial-name">Sneha Rajput, 21, Ground Staff Executive</div>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <div class="testimonial-description">
                            From grooming sessions to communication skills, everything was top-notch at <b>Skill Grow Institution</b>. I always wanted to work at the airport and now I'm working at IGI Airport, New Delhi. This institute changed my life.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


   <section class="instructor-section">
    <div class="container">
        <h2>Unlock Your Potential with Skill Grow Institution</h2>
        <div class="instructor-content">
            <div class="instructor-img-container">
                <img src="assets/images/banner/midal.png.jpg" width="100%" alt="Skill Grow Institution" class="instructor-img">
            </div>
            <div class="instructor-details">
                <h3>About Us</h3>
                <p>Skill Grow Institution is a hub of opportunity and innovation. Our diverse range of courses—from Business Studies to Advanced IT, Aviation, and Creative Design—empowers learners to thrive in a fast-paced, tech-driven world. We focus on skill-based education, real-world experience, and practical learning methods that align with industry demands.</p>
                <p>Whether you're looking to advance your career, switch industries, or ignite your passion, Skill Grow Institution offers the perfect launchpad. Our faculty members are industry leaders and experts committed to helping students become confident, skilled professionals.</p>

                <h3>Explore Our Courses</h3>
                <ul class="experience-list">
                    <li><i class="fas fa-circle"></i><span class="focused-text">Business & Management:</span> Business Studies, Management Studies, Financial Markets, Business Intelligence</li>
                    <li><i class="fas fa-circle"></i><span class="focused-text">Aviation & Hospitality:</span> Aviation, Tourism & Hospitality, Mass Communication</li>
                    <li><i class="fas fa-circle"></i><span class="focused-text">Technology & IT:</span> Web Technology, Python, Robotic Programming, AI in HRM, Digital Transformation, Data Analytics</li>
                    <li><i class="fas fa-circle"></i><span class="focused-text">Design & Creativity:</span> Design Study, Cosmetology, 3D Design, UX Design</li>
                    <li><i class="fas fa-circle"></i><span class="focused-text">Marketing:</span> Digital Marketing Strategy, E-commerce</li>
                    <li><i class="fas fa-circle"></i><span class="focused-text">Life & General Skills:</span> Life Skills, General Studies</li>
                </ul>

                <h3>Brands We Work With</h3>
                <p>We believe in partnerships that empower progress. Skill Grow Institution has proudly collaborated with reputed brands and industry experts to deliver quality training and placement support.</p>
                <div class="brands-list">
                    <img src="https://eduma.thimpress.com/demo-main/wp-content/uploads/sites/95/2024/02/partner-5.png" alt="Brand Partner 5" class="brand-img">
                    <img src="https://eduma.thimpress.com/demo-main/wp-content/uploads/sites/95/2024/02/partner-4.png" alt="Brand Partner 4" class="brand-img">
                    <img src="https://eduma.thimpress.com/demo-main/wp-content/uploads/sites/95/2024/02/partner-3.png" alt="Brand Partner 3" class="brand-img">
                    <img src="https://eduma.thimpress.com/demo-main/wp-content/uploads/sites/95/2024/02/partner-2.png" alt="Brand Partner 2" class="brand-img">
                    <img src="https://eduma.thimpress.com/demo-main/wp-content/uploads/sites/95/2024/02/partner-1.png" alt="Brand Partner 1" class="brand-img">
                </div>
            </div>
        </div>
    </div>
</section>


     <section id="form-enroll" class="enrollment-section">
        <div class="container">
            <!-- Enrollment Header -->
           <div class="enrollment-header">
    <div class="header-item">
        <h2>Admissions Open</h2>
    </div>
    <div class="header-item">
        <p>Limited Seats</p>
        <h5>Enroll Early to Reserve Your Spot</h5>
    </div>
    <div class="header-item">
        <p>Skill-Based Programs</p>
        <h5>Learn from Industry Experts</h5>
    </div>
    <div class="header-item">
        <p>Flexible Learning</p>
        <h5>Online & Offline Batches Available</h5>
    </div>
</div>

            <div class="row">
              <div class="col-md-4">
                 <div class="enrollment-step">
                <p class="sub-heading">Step 1</p>
                <h4>Enrollment Form</h4>
               <form class="enrollment-form" action="index-submit.php" method="POST">
    <div class="mb-3">
        <input type="text" name="name" class="form-control" placeholder="Your Name *" required>
    </div>
    <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email *" required>
    </div>
    <div class="mb-3">
        <input type="tel" name="phone" class="form-control" placeholder="Phone *" required>
    </div>
    <div class="mb-3">
        <textarea name="note" class="form-control" rows="5" placeholder="Note"></textarea>
    </div>
    <button type="submit" class="enroll-btn">Enroll Now</button>
</form>
            </div>
              </div>
            <div class="col-md-4">
  <div class="enrollment-step">
    <p class="sub-heading">Step 2</p>
    <h4>Career Counseling Session</h4>
    <div class="confirmation-text">
      <p>Once you fill out the form, our expert counselor will contact you for a free one-on-one session to help you choose the right course based on your interests and goals.</p>
      <ul>
        <li>Personalized Course Recommendations</li>
        <li>Career Path Guidance</li>
        <li>Clarify All Your Questions</li>
      </ul>
      <p>We’re here to ensure you make the most informed decision before starting your journey with us.</p>
    </div>
  </div>
</div>
              <div class="col-md-4">
                <div class="enrollment-step">
                <p class="sub-heading">Step 3</p>
                <h4>Please check our email for enrollment confirmation!</h4>
                <div class="confirmation-text">
                    <p>We will send you an enrollment confirmation and an invitation to our Eduma Community.</p>
                    <p>Join this community and dive into the various discussions, threads, and activities happening in the community.</p>
                </div>
            </div>
              </div>
            </div>
           
        </div>
    </section>
     <!-- Waitlist Section -->
    <section class="waitlist-section">
        <div class="container">
            <h2>Excited for New Courses at Skill Grow Institution? Join the Waitlist!</h2>
            <div class="waitlist-form-container">
                <form id="waitlist-form" class="waitlist-form">
                    <input type="email" class="form-control" placeholder="Enter your email to stay updated" required>
                    <button type="submit" class="btn">Join Now</button>
                </form>
                <div id="form-response" class="form-response"></div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h2>Frequently Asked Questions About Skill Grow Institution</h2>
            <div class="row faq-container">
                <!-- Left Column -->
                <div class="col-lg-6 faq-column">
                    <div class="accordion" id="faqAccordionLeft">
                        <!-- FAQ 1 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingLeftOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeftOne" aria-expanded="false" aria-controls="collapseLeftOne">
                                    What types of courses does Skill Grow Institution offer?
                                </button>
                            </h2>
                            <div id="collapseLeftOne" class="accordion-collapse collapse" aria-labelledby="headingLeftOne" data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    <p>We offer a wide range of courses across multiple fields, including Business & Management, Technology & IT, Design & Creativity, Marketing, and Specialized Fields like Aviation and Tourism. Whether you’re looking to start a new career or enhance your skills, we have something for everyone!</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingLeftTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeftTwo" aria-expanded="false" aria-controls="collapseLeftTwo">
                                    How do I enroll in a course at Skill Grow Institution?
                                </button>
                            </h2>
                            <div id="collapseLeftTwo" class="accordion-collapse collapse" aria-labelledby="headingLeftTwo" data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    <p>Enrolling is easy! Browse our courses from the "Courses" menu, select the one that interests you, and click "<a href="enrollnow.php">Enroll Now</a> to start your registration. You’ll be guided through a simple process to set up your account and begin learning.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingLeftThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeftThree" aria-expanded="false" aria-controls="collapseLeftThree">
                                    Are the courses self-paced or scheduled?
                                </button>
                            </h2>
                            <div id="collapseLeftThree" class="accordion-collapse collapse" aria-labelledby="headingLeftThree" data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    <p>Our courses are designed to be flexible! Most programs offer a self-paced learning experience, allowing you to study at your convenience. Some courses may include live sessions or scheduled projects, but we ensure you have the flexibility to balance learning with your schedule.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 4 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingLeftFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeftFour" aria-expanded="false" aria-controls="collapseLeftFour">
                                    What kind of support do I get while learning?
                                </button>
                            </h2>
                            <div id="collapseLeftFour" class="accordion-collapse collapse" aria-labelledby="headingLeftFour" data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    <p>We provide comprehensive support to ensure your success! You’ll have access to instructor guidance, a community of learners, and 24/7 technical support. Plus, our interactive platforms include forums and Q&A sessions to help you stay on track.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="col-lg-6 faq-column">
                    <div class="accordion" id="faqAccordionRight">
                        <!-- FAQ 1 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRightOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRightOne" aria-expanded="true" aria-controls="collapseRightOne">
                                    Do I need prior experience to join a course?
                                </button>
                            </h2>
                            <div id="collapseRightOne" class="accordion-collapse collapse show" aria-labelledby="headingRightOne" data-bs-parent="#faqAccordionRight">
                                <div class="accordion-body">
                                    <p>No prior experience is needed for most courses! We offer programs for all levels, from beginners to advanced learners. Each course is designed to guide you step-by-step, ensuring you gain the skills you need to succeed.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRightTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRightTwo" aria-expanded="false" aria-controls="collapseRightTwo">
                                    Will I receive a certificate upon completion?
                                </button>
                            </h2>
                            <div id="collapseRightTwo" class="accordion-collapse collapse" aria-labelledby="headingRightTwo" data-bs-parent="#faqAccordionRight">
                                <div class="accordion-body">
                                    <p>Yes! Upon successfully completing a course, you’ll receive a certificate of completion from Skill Grow Institution. This certificate can be added to your resume or LinkedIn profile to showcase your new skills.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRightThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRightThree" aria-expanded="false" aria-controls="collapseRightThree">
                                    Can I switch between courses if I change my mind?
                                </button>
                            </h2>
                            <div id="collapseRightThree" class="accordion-collapse collapse" aria-labelledby="headingRightThree" data-bs-parent="#faqAccordionRight">
                                <div class="accordion-body">
                                    <p>We understand that interests can change! You can switch courses within 24 hours of enrollment. Contact our support team, and we’ll help you transition to a course that better fits your goals.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ 4 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRightFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRightFour" aria-expanded="false" aria-controls="collapseRightFour">
                                    How can I stay updated on new courses?
                                </button>
                            </h2>
                            <div id="collapseRightFour" class="accordion-collapse collapse" aria-labelledby="headingRightFour" data-bs-parent="#faqAccordionRight">
                                <div class="accordion-body">
                                    <p>Join our waitlist above to receive notifications about upcoming courses and new program launches. You can also follow us on social media or check our website regularly for updates!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
   
<?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.0.4/build/js/intlTelInput.min.js"></script>
   <script>
        document.getElementById('waitlist-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent actual form submission
            const emailInput = this.querySelector('input[type="email"]');
            const responseDiv = document.getElementById('form-response');

            if (emailInput.value) {
                responseDiv.textContent = 'Thank you for joining the waitlist! We’ll notify you soon.';
                responseDiv.style.color = '#28a745';
                responseDiv.style.display = 'block';
                emailInput.value = ''; // Clear the input
            } else {
                responseDiv.textContent = 'Please enter a valid email address.';
                responseDiv.style.color = '#dc3545';
                responseDiv.style.display = 'block';
            }
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
  <script>
    // Add active class to nav links on click
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', function () {
        document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
        this.classList.add('active');
      });
    });
  </script>
  <script>
    const courses = [
  "Business Studies",
  "Management Studies",
  "Financial Markets and Product",
  "Business Intelligence",
  "Aviation",
  "Tourism and Hospitality",
  "Journalism and Mass Communication",
  "Life Skills",
  "General Studies",
  "Web Technology",
  "Python Data Structures",
  "Introduction to Robotic Programming",
  "AI in Human Resource Management",
  "Advance Digital Transformation",
  "Advance Data Analytics",
  "IT Automation",
  "IT Support",
  "Design Study",
  "Cosmetology Studies",
  "3D Painting and Design for Educators",
  "UX Design",
  "Digital Marketing Strategy",
  "Digital Marketing & E-commerce"
];

const courseHeading = document.getElementById('course-heading');
let courseIndex = 0;
let charIndex = 0;
let isDeleting = false;

function typeCourse() {
  const currentCourse = courses[courseIndex];
  const speed = isDeleting ? 50 : 100; // Faster deletion, slower typing

  if (!isDeleting && charIndex <= currentCourse.length) {
    // Typing
    courseHeading.textContent = currentCourse.substring(0, charIndex);
    charIndex++;
  } else if (isDeleting && charIndex >= 0) {
    // Deleting
    courseHeading.textContent = currentCourse.substring(0, charIndex);
    charIndex--;
  }

  if (!isDeleting && charIndex > currentCourse.length) {
    // Pause after typing
    setTimeout(() => {
      isDeleting = true;
    }, 1000); // 1-second pause before deleting
  } else if (isDeleting && charIndex < 0) {
    // Move to next course
    isDeleting = false;
    courseIndex = (courseIndex + 1) % courses.length; // Loop back to first course
  }

  setTimeout(typeCourse, speed);
}

// Start the animation
setTimeout(typeCourse, 500); // Initial delay
  </script>

</body>

</html>