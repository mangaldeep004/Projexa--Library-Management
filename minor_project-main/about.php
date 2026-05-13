<?php
/**
 * About Page
 */
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <meta name="description" content="About Smart Library Management System — BCA Minor Project"/>
  <title>About — SmartLib</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
<nav class="navbar">
  <a href="index.php" class="navbar-brand">
    <div class="logo-icon"><i class="fa-solid fa-book-open"></i></div><span>SmartLib</span>
  </a>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="about.php" class="active">About</a>
    <a href="contact.php">Contact</a>
  </div>
  <div class="nav-actions">
    <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
    <?php if(isLoggedIn()): ?>
      <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
    <?php else: ?>
      <a href="login.php" class="btn btn-secondary btn-sm">Login</a>
      <a href="register.php" class="btn btn-primary btn-sm">Register</a>
    <?php endif; ?>
    <div class="hamburger" onclick="document.querySelector('.nav-links').classList.toggle('open')"><span></span><span></span><span></span></div>
  </div>
</nav>

<!-- About Hero -->
<section style="padding:8rem 2rem 5rem;background:var(--gradient);text-align:center;">
  <h1 style="color:white;margin-bottom:1rem;">About SmartLib</h1>
  <p style="color:rgba(255,255,255,.85);font-size:1.1rem;max-width:600px;margin:0 auto;">
    A modern Library Management System developed as a Minor Project for BCA 2nd Semester students.
  </p>
</section>

<!-- Project Info -->
<section>
  <div class="container">
    <div class="grid-2" style="align-items:center;gap:3rem;">
      <div>
        <div class="overline" style="color:var(--primary);font-size:.85rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;">The Project</div>
        <h2 style="margin:.5rem 0 1rem;">Smart Library Management System</h2>
        <p style="margin-bottom:1rem;">This project is developed as part of the BCA 2nd Semester Minor Project curriculum. It demonstrates practical application of web technologies including PHP, MySQL, HTML, CSS, and JavaScript.</p>
        <p style="margin-bottom:1.5rem;">The system is designed to digitize and streamline the operations of a college library, replacing traditional manual record-keeping with a modern, efficient, and user-friendly web application.</p>
        <div style="display:flex;gap:1rem;flex-wrap:wrap;">
          <div class="badge badge-primary" style="padding:.5rem 1rem;font-size:.9rem;"><i class="fa-solid fa-code"></i> PHP + MySQL</div>
          <div class="badge badge-success" style="padding:.5rem 1rem;font-size:.9rem;"><i class="fa-brands fa-html5"></i> HTML/CSS/JS</div>
          <div class="badge badge-warning" style="padding:.5rem 1rem;font-size:.9rem;"><i class="fa-solid fa-graduation-cap"></i> BCA Project</div>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="stat-card"><div class="stat-icon blue"><i class="fa-solid fa-file-code"></i></div><div><div class="stat-number">15+</div><div class="stat-label">PHP Files</div></div></div>
        <div class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-database"></i></div><div><div class="stat-number">7</div><div class="stat-label">DB Tables</div></div></div>
        <div class="stat-card"><div class="stat-icon purple"><i class="fa-solid fa-layer-group"></i></div><div><div class="stat-number">10+</div><div class="stat-label">Pages</div></div></div>
        <div class="stat-card"><div class="stat-icon orange"><i class="fa-solid fa-star"></i></div><div><div class="stat-number">20+</div><div class="stat-label">Features</div></div></div>
      </div>
    </div>
  </div>
</section>

<!-- Tech Stack -->
<section style="background:var(--surface2);">
  <div class="container">
    <div class="section-header">
      <div class="overline">Technology Stack</div>
      <h2>Built With Modern Tools</h2>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon" style="background:linear-gradient(135deg,#e44d26,#f16529)"><i class="fa-brands fa-html5"></i></div>
        <h3>HTML5</h3>
        <p>Semantic HTML5 markup for structured, accessible content and forms.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="background:linear-gradient(135deg,#264de4,#2965f1)"><i class="fa-brands fa-css3-alt"></i></div>
        <h3>CSS3</h3>
        <p>Modern CSS with custom properties, flexbox, grid, animations, and dark mode.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="background:linear-gradient(135deg,#f7df1e,#e5c00e)"><i class="fa-brands fa-js"></i></div>
        <h3>JavaScript</h3>
        <p>Vanilla JS for interactivity, AJAX search, dark mode, and Chart.js charts.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="background:linear-gradient(135deg,#777bb4,#5c5fa8)"><i class="fa-solid fa-server"></i></div>
        <h3>PHP 8</h3>
        <p>Server-side scripting with PDO for secure database operations and sessions.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="background:linear-gradient(135deg,#00758f,#f29111)"><i class="fa-solid fa-database"></i></div>
        <h3>MySQL</h3>
        <p>Relational database with 7 tables for books, users, issued books, fines and more.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="background:linear-gradient(135deg,#667eea,#764ba2)"><i class="fa-solid fa-chart-line"></i></div>
        <h3>Chart.js</h3>
        <p>Interactive line, doughnut, and bar charts for visual analytics.</p>
      </div>
    </div>
  </div>
</section>

<!-- Team / Developer -->
<section>
  <div class="container">
    <div class="section-header">
      <div class="overline">Development</div>
      <h2>Project Team</h2>
    </div>
    <div style="max-width:600px;margin:0 auto;text-align:center;">
      <div class="card" style="padding:2.5rem;">
        <div style="width:80px;height:80px;border-radius:50%;background:var(--gradient);color:white;font-size:2rem;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">S</div>
        <h3>BCA 2nd Semester Students</h3>
        <p style="margin:.5rem 0 1rem;">College of Computer Science</p>
        <div style="display:flex;justify-content:center;gap:.75rem;flex-wrap:wrap;">
          <span class="badge badge-primary">Minor Project 2024</span>
          <span class="badge badge-success">Web Development</span>
          <span class="badge badge-warning">Database Design</span>
        </div>
        <p style="margin-top:1.25rem;color:var(--text-muted);font-size:.9rem;">
          This project showcases full-stack web development skills including database design, server-side programming, UI/UX design, and user authentication systems.
        </p>
      </div>
    </div>
  </div>
</section>

<footer style="background:#1e1b4b;color:#c7d2fe;padding:2rem;text-align:center;">
  <p>© 2024 SmartLib — BCA Minor Project. All rights reserved.</p>
  <p style="margin-top:.5rem;"><a href="index.php" style="color:#a5b4fc;">← Back to Home</a></p>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
