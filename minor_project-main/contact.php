<?php
/**
 * Contact Page
 */
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name    = sanitize($_POST['name']    ?? '');
  $email   = sanitize($_POST['email']   ?? '');
  $subject = sanitize($_POST['subject'] ?? '');
  $message = sanitize($_POST['message'] ?? '');

  if (empty($name) || empty($email) || empty($message)) {
    $error = 'Please fill in all required fields.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } else {
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)");
    $stmt->execute([$name, $email, $subject, $message]);
    $success = "Thank you, $name! Your message has been sent. We'll get back to you soon.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Contact — SmartLib</title>
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
    <a href="about.php">About</a>
    <a href="contact.php" class="active">Contact</a>
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

<section style="padding:8rem 2rem 5rem;background:var(--gradient);text-align:center;">
  <h1 style="color:white;">Contact Us</h1>
  <p style="color:rgba(255,255,255,.85);font-size:1.1rem;margin-top:.75rem;">Have questions? We're here to help!</p>
</section>

<section>
  <div class="container">
    <div class="grid-2" style="gap:3rem;align-items:start;">
      <!-- Contact Form -->
      <div class="card">
        <div class="card-header"><span class="card-title">📩 Send us a Message</span></div>

        <?php if($error):   ?><div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= $success ?></div><?php endif; ?>

        <form method="POST">
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">Your Name *</label>
              <div class="input-group">
                <i class="fa-solid fa-user input-icon"></i>
                <input type="text" name="name" class="form-control" placeholder="Full name"
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Email Address *</label>
              <div class="input-group">
                <i class="fa-solid fa-envelope input-icon"></i>
                <input type="email" name="email" class="form-control" placeholder="you@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required />
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" placeholder="What is this about?"
                   value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" />
          </div>
          <div class="form-group">
            <label class="form-label">Message *</label>
            <textarea name="message" class="form-control" rows="5" placeholder="Type your message here..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;">
            <i class="fa-solid fa-paper-plane"></i> Send Message
          </button>
        </form>
      </div>

      <!-- Contact Info -->
      <div>
        <div class="card" style="margin-bottom:1.25rem;">
          <div class="card-header"><span class="card-title">📍 Library Information</span></div>
          <div style="display:flex;flex-direction:column;gap:1.1rem;">
            <div style="display:flex;align-items:flex-start;gap:1rem;">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(102,126,234,.15);color:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.1rem;">
                <i class="fa-solid fa-location-dot"></i>
              </div>
              <div>
                <div style="font-weight:600;margin-bottom:.2rem;">Address</div>
                <div style="color:var(--text-muted);font-size:.9rem;">Library Block, Room 101<br/>College Campus, City - 110001</div>
              </div>
            </div>
            <div style="display:flex;align-items:flex-start;gap:1rem;">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(16,185,129,.15);color:var(--success);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.1rem;">
                <i class="fa-solid fa-phone"></i>
              </div>
              <div>
                <div style="font-weight:600;margin-bottom:.2rem;">Phone</div>
                <div style="color:var(--text-muted);font-size:.9rem;">+91 98765 43210<br/>+91 98765 43211</div>
              </div>
            </div>
            <div style="display:flex;align-items:flex-start;gap:1rem;">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(245,158,11,.15);color:var(--warning);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.1rem;">
                <i class="fa-solid fa-envelope"></i>
              </div>
              <div>
                <div style="font-weight:600;margin-bottom:.2rem;">Email</div>
                <div style="color:var(--text-muted);font-size:.9rem;">library@college.edu<br/>admin@library.com</div>
              </div>
            </div>
            <div style="display:flex;align-items:flex-start;gap:1rem;">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(118,75,162,.15);color:var(--secondary);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.1rem;">
                <i class="fa-regular fa-clock"></i>
              </div>
              <div>
                <div style="font-weight:600;margin-bottom:.2rem;">Library Hours</div>
                <div style="color:var(--text-muted);font-size:.9rem;">Monday – Saturday: 9 AM – 6 PM<br/>Sunday: Closed</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><span class="card-title">🔗 Quick Links</span></div>
          <div style="display:flex;flex-direction:column;gap:.5rem;">
            <a href="login.php" class="btn btn-primary"><i class="fa-solid fa-right-to-bracket"></i> Student Login</a>
            <a href="register.php" class="btn btn-secondary"><i class="fa-solid fa-user-plus"></i> Student Registration</a>
            <a href="about.php" class="btn btn-outline"><i class="fa-solid fa-info-circle"></i> About the Project</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<footer style="background:#1e1b4b;color:#c7d2fe;padding:2rem;text-align:center;">
  <p>© 2024 SmartLib — BCA Minor Project.</p>
  <p style="margin-top:.5rem;"><a href="index.php" style="color:#a5b4fc;">← Back to Home</a></p>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
