<?php
/**
 * Smart Library Management System
 * Home / Landing Page
 */
require_once 'includes/auth.php';
require_once 'includes/functions.php';
$page_title = "Smart Library Management System";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Smart Library Management System — Manage books, students, and library resources efficiently." />
  <title><?= $page_title ?></title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>

<!-- ========== LOADING OVERLAY ========== -->
<div id="loading-overlay">
  <div class="loader-logo"><i class="fa-solid fa-book-open"></i> SmartLib</div>
  <div class="loader-bar"><div class="loader-fill"></div></div>
</div>

<!-- ========== NAVBAR ========== -->
<nav class="navbar">
  <a href="index.php" class="navbar-brand">
    <div class="logo-icon"><i class="fa-solid fa-book-open"></i></div>
    <span>SmartLib</span>
  </a>
  <div class="nav-links" id="main-nav">
    <a href="index.php" class="active">Home</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
    <?php if(isAdmin()): ?>
      <a href="admin/dashboard.php">Admin Panel</a>
    <?php elseif(isStudent()): ?>
      <a href="student/dashboard.php">Dashboard</a>
    <?php endif; ?>
  </div>
  <div class="nav-actions">
    <button class="btn-icon" id="dark-mode-toggle" data-tooltip="Toggle Dark Mode">
      <i class="fa-solid fa-moon"></i>
    </button>
    <?php if(isLoggedIn()): ?>
      <a href="logout.php" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    <?php else: ?>
      <a href="login.php" class="btn btn-secondary btn-sm">Login</a>
      <a href="register.php" class="btn btn-primary btn-sm">Register</a>
    <?php endif; ?>
    <div class="hamburger" id="hamburger" onclick="document.getElementById('main-nav').classList.toggle('open')">
      <span></span><span></span><span></span>
    </div>
  </div>
</nav>

<!-- ========== HERO ========== -->
<section class="hero">
  <div class="container hero-content" style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:center;">
    <div>
      <div class="hero-badge animate-fade">
        <i class="fa-solid fa-star"></i> BCA Minor Project 2024
      </div>
      <h1 class="animate-slide animate-delay-1">
        Smart Library<br/>Management System
      </h1>
      <p class="animate-slide animate-delay-2">
        A modern, intelligent platform to manage books, students, and library resources with ease. Designed for colleges and educational institutions.
      </p>
      <div class="hero-actions animate-slide animate-delay-3">
        <a href="register.php" class="btn btn-white btn-lg">
          <i class="fa-solid fa-user-plus"></i> Get Started
        </a>
        <a href="login.php" class="btn btn-ghost btn-lg">
          <i class="fa-solid fa-right-to-bracket"></i> Login
        </a>
      </div>
    </div>
    <div class="hero-visual">
      <div class="hero-stats-float">
        <div class="float-card">
          <div class="number counter" data-target="500">0</div>
          <div class="label">Books Available</div>
        </div>
        <div class="float-card">
          <div class="number counter" data-target="250">0</div>
          <div class="label">Students Enrolled</div>
        </div>
        <div class="float-card">
          <div class="number counter" data-target="1200">0</div>
          <div class="label">Books Issued</div>
        </div>
        <div class="float-card">
          <div class="number counter" data-target="98">0</div>
          <div class="label">% Satisfaction</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========== FEATURES ========== -->
<section style="background: var(--bg);">
  <div class="container">
    <div class="section-header">
      <div class="overline">What We Offer</div>
      <h2>Powerful Features</h2>
      <p>Everything you need to manage a modern library efficiently and effectively.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-book"></i></div>
        <h3>Book Management</h3>
        <p>Add, edit, delete, and search books with full CRUD operations and category management.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-users"></i></div>
        <h3>Student Management</h3>
        <p>Register students, manage profiles, track issued books, and view borrowing history.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-rotate"></i></div>
        <h3>Issue & Return</h3>
        <p>Seamlessly issue and return books with automated due date tracking and fine calculation.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-chart-bar"></i></div>
        <h3>Analytics Dashboard</h3>
        <p>Visual charts and statistics showing issued books, returns, overdue items, and fines.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
        <h3>Real-time Search</h3>
        <p>Instantly search books by title, author, or category using AJAX-powered live search.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-moon"></i></div>
        <h3>Dark Mode</h3>
        <p>Eye-friendly dark mode with persistent preference — perfect for late-night studying.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h3>Fine Management</h3>
        <p>Automatic fine calculation at ₹2/day for overdue books, with payment tracking.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-bell"></i></div>
        <h3>Notifications</h3>
        <p>Automated alerts for due dates, overdue books, and library announcements.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"><i class="fa-solid fa-mobile-screen"></i></div>
        <h3>Responsive Design</h3>
        <p>Fully responsive UI — works perfectly on mobile, tablet, and desktop screens.</p>
      </div>
    </div>
  </div>
</section>

<!-- ========== STATS SECTION ========== -->
<section class="stats-section">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item">
        <span class="num counter" data-target="500">0</span>
        <span class="lbl">Books in Library</span>
      </div>
      <div class="stat-item">
        <span class="num counter" data-target="8">0</span>
        <span class="lbl">Book Categories</span>
      </div>
      <div class="stat-item">
        <span class="num counter" data-target="250">0</span>
        <span class="lbl">Registered Students</span>
      </div>
      <div class="stat-item">
        <span class="num counter" data-target="1200">0</span>
        <span class="lbl">Books Issued Till Date</span>
      </div>
    </div>
  </div>
</section>

<!-- ========== HOW IT WORKS ========== -->
<section>
  <div class="container">
    <div class="section-header">
      <div class="overline">Simple Process</div>
      <h2>How It Works</h2>
      <p>Get started with SmartLib in just a few simple steps.</p>
    </div>
    <div class="grid-3" style="text-align:center;">
      <div class="card" style="text-align:center;padding:2.5rem;">
        <div style="width:60px;height:60px;border-radius:50%;background:var(--gradient);color:white;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:800;margin:0 auto 1.25rem;">1</div>
        <h3 style="margin-bottom:.5rem;">Register</h3>
        <p>Create your student account in seconds with your basic details.</p>
      </div>
      <div class="card" style="text-align:center;padding:2.5rem;">
        <div style="width:60px;height:60px;border-radius:50%;background:var(--gradient);color:white;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:800;margin:0 auto 1.25rem;">2</div>
        <h3 style="margin-bottom:.5rem;">Browse & Request</h3>
        <p>Search thousands of books and request issue from the library.</p>
      </div>
      <div class="card" style="text-align:center;padding:2.5rem;">
        <div style="width:60px;height:60px;border-radius:50%;background:var(--gradient);color:white;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:800;margin:0 auto 1.25rem;">3</div>
        <h3 style="margin-bottom:.5rem;">Read & Return</h3>
        <p>Pick up your book, read it, and return on time to avoid fines.</p>
      </div>
    </div>
  </div>
</section>

<!-- ========== TESTIMONIALS ========== -->
<section style="background:var(--surface2);">
  <div class="container">
    <div class="section-header">
      <div class="overline">What Students Say</div>
      <h2>Testimonials</h2>
    </div>
    <div class="testimonials-grid">
      <div class="testimonial-card">
        <div class="testimonial-text">SmartLib made managing my borrowed books so easy! I never miss a due date now thanks to the notification system.</div>
        <div class="testimonial-author">
          <div class="author-avatar">R</div>
          <div>
            <div class="author-name">Rahul Sharma</div>
            <div class="author-role">BCA 2nd Semester</div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="testimonial-text">The admin dashboard is incredibly powerful. Managing books, students, and fines is now a breeze for our library staff.</div>
        <div class="testimonial-author">
          <div class="author-avatar">D</div>
          <div>
            <div class="author-name">Dr. Priya Verma</div>
            <div class="author-role">Library Administrator</div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="testimonial-text">The dark mode and real-time search are my favorite features. This system feels as modern as any commercial software!</div>
        <div class="testimonial-author">
          <div class="author-avatar">A</div>
          <div>
            <div class="author-name">Amit Kumar</div>
            <div class="author-role">MCA Student</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========== FAQ ========== -->
<section>
  <div class="container">
    <div class="section-header">
      <div class="overline">Questions & Answers</div>
      <h2>Frequently Asked Questions</h2>
    </div>
    <div class="faq-list">
      <div class="faq-item">
        <div class="faq-question">How do I register as a student? <span class="faq-icon">+</span></div>
        <div class="faq-answer">Click the "Register" button on the homepage, fill in your details including your enrollment number, and submit. Your account will be created immediately.</div>
      </div>
      <div class="faq-item">
        <div class="faq-question">How is the fine calculated? <span class="faq-icon">+</span></div>
        <div class="faq-answer">The fine is ₹2 per day for every day the book is kept past its due date. The due date is typically 14 days from the issue date.</div>
      </div>
      <div class="faq-item">
        <div class="faq-question">How many books can I borrow at once? <span class="faq-icon">+</span></div>
        <div class="faq-answer">Each student can borrow up to 3 books at a time. The librarian can modify this limit from the admin panel.</div>
      </div>
      <div class="faq-item">
        <div class="faq-question">Can I renew a book? <span class="faq-icon">+</span></div>
        <div class="faq-answer">Yes, contact the library admin through the dashboard or in person to request a renewal, provided no other student has reserved the book.</div>
      </div>
      <div class="faq-item">
        <div class="faq-question">Is my data secure? <span class="faq-icon">+</span></div>
        <div class="faq-answer">Yes. All passwords are hashed using bcrypt, sessions are managed securely, and all user inputs are sanitized to prevent injection attacks.</div>
      </div>
    </div>
  </div>
</section>

<!-- ========== FOOTER ========== -->
<footer>
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-brand"><i class="fa-solid fa-book-open"></i> SmartLib</div>
        <p class="footer-desc">A modern Smart Library Management System developed as a Minor Project for BCA 2nd Semester. Manage books, students, and library resources effortlessly.</p>
        <div class="social-links">
          <a href="#" class="social-link"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" class="social-link"><i class="fa-brands fa-twitter"></i></a>
          <a href="#" class="social-link"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" class="social-link"><i class="fa-brands fa-github"></i></a>
        </div>
      </div>
      <div>
        <div class="footer-heading">Quick Links</div>
        <div class="footer-links">
          <a href="index.php">Home</a>
          <a href="about.php">About</a>
          <a href="contact.php">Contact</a>
          <a href="login.php">Login</a>
          <a href="register.php">Register</a>
        </div>
      </div>
      <div>
        <div class="footer-heading">Library</div>
        <div class="footer-links">
          <a href="student/browse_books.php">Browse Books</a>
          <a href="student/my_books.php">My Books</a>
          <a href="admin/dashboard.php">Admin Panel</a>
          <a href="admin/books.php">Manage Books</a>
        </div>
      </div>
      <div>
        <div class="footer-heading">Contact Info</div>
        <div class="footer-links">
          <a href="#"><i class="fa-solid fa-location-dot"></i> College Library, Block A</a>
          <a href="#"><i class="fa-solid fa-phone"></i> +91 98765 43210</a>
          <a href="#"><i class="fa-solid fa-envelope"></i> library@college.edu</a>
          <a href="#"><i class="fa-regular fa-clock"></i> Mon–Sat: 9AM – 6PM</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2024 SmartLib. Developed by BCA 2nd Semester Students. All rights reserved.</p>
    </div>
  </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
