<?php
/**
 * Smart Library Management System
 * Student Registration Page
 */
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) { header('Location: index.php'); exit(); }

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name     = sanitize($_POST['name']     ?? '');
  $email    = sanitize($_POST['email']    ?? '');
  $phone    = sanitize($_POST['phone']    ?? '');
  $reg_no   = sanitize($_POST['reg_no']   ?? '');
  $course   = sanitize($_POST['course']   ?? 'BCA');
  $semester = sanitize($_POST['semester'] ?? '');
  $address  = sanitize($_POST['address']  ?? '');
  $password = $_POST['password'] ?? '';
  $confirm  = $_POST['confirm_password'] ?? '';

  // Validation
  if (empty($name) || empty($email) || empty($password)) {
    $error = 'Name, email, and password are required.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } elseif (strlen($password) < 6) {
    $error = 'Password must be at least 6 characters.';
  } elseif ($password !== $confirm) {
    $error = 'Passwords do not match.';
  } else {
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
      $error = 'Email already registered. Please login.';
    } else {
      // Check reg_no uniqueness
      if ($reg_no) {
        $stmt2 = $pdo->prepare("SELECT id FROM users WHERE reg_no = ?");
        $stmt2->execute([$reg_no]);
        if ($stmt2->fetch()) {
          $error = 'Registration number already in use.';
        }
      }
      if (!$error) {
        $hashed = hashPassword($password);
        $stmt3  = $pdo->prepare(
          "INSERT INTO users (name, email, password, phone, reg_no, course, semester, address) 
           VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt3->execute([$name, $email, $hashed, $phone, $reg_no, $course, $semester, $address]);
        header('Location: login.php?msg=registered');
        exit();
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register — SmartLib</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body { min-height:100vh; display:flex; align-items:center; justify-content:center; background:var(--gradient); padding:1rem; }
    .auth-card { background:var(--surface); border-radius:var(--radius-lg); padding:2.5rem; width:100%; max-width:520px; box-shadow:var(--shadow-lg); }
    .auth-logo { text-align:center; margin-bottom:1.75rem; }
    .auth-logo .icon { width:64px;height:64px;border-radius:16px;background:var(--gradient);color:white;font-size:1.75rem;display:flex;align-items:center;justify-content:center;margin:0 auto .75rem; }
    .dark-toggle-top { position:fixed; top:1rem; right:1rem; }
    .pass-strength { height:4px; border-radius:4px; margin-top:.4rem; transition:all .3s; }
    .strength-text { font-size:.75rem; margin-top:.3rem; }
  </style>
</head>
<body>
  <button class="btn-icon dark-toggle-top" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>

  <div class="auth-card animate-slide">
    <div class="auth-logo">
      <div class="icon"><i class="fa-solid fa-user-plus"></i></div>
      <h2>Create Account</h2>
      <p>Register as a student to access the library</p>
    </div>

    <?php if ($error): ?><div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div><?php endif; ?>

    <form method="POST" action="register.php" id="reg-form">
      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Full Name *</label>
          <div class="input-group">
            <i class="fa-solid fa-user input-icon"></i>
            <input type="text" name="name" class="form-control" placeholder="Your full name"
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

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <div class="input-group">
            <i class="fa-solid fa-phone input-icon"></i>
            <input type="tel" name="phone" class="form-control" placeholder="10-digit number"
                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" />
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Enrollment / Reg. No.</label>
          <div class="input-group">
            <i class="fa-solid fa-id-card input-icon"></i>
            <input type="text" name="reg_no" class="form-control" placeholder="BCA/2024/001"
                   value="<?= htmlspecialchars($_POST['reg_no'] ?? '') ?>" />
          </div>
        </div>
      </div>

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Course</label>
          <select name="course" class="form-control">
            <?php foreach(['BCA','MCA','BSc CS','BSc IT','BTech','Other'] as $c): ?>
              <option value="<?= $c ?>" <?= (($_POST['course'] ?? 'BCA') === $c) ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Semester</label>
          <select name="semester" class="form-control">
            <?php foreach(['1st','2nd','3rd','4th','5th','6th'] as $s): ?>
              <option value="<?= $s ?>" <?= (($_POST['semester'] ?? '') === $s) ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="2" placeholder="Your address (optional)"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
      </div>

      <div class="grid-2">
        <div class="form-group">
          <label class="form-label">Password *</label>
          <div class="input-group">
            <i class="fa-solid fa-lock input-icon"></i>
            <input type="password" name="password" id="password" class="form-control" placeholder="Min 6 chars" required />
          </div>
          <div class="pass-strength" id="strength-bar" style="background:var(--border);"></div>
          <div class="strength-text" id="strength-text"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Confirm Password *</label>
          <div class="input-group">
            <i class="fa-solid fa-lock input-icon"></i>
            <input type="password" name="confirm_password" id="confirm" class="form-control" placeholder="Repeat password" required />
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;">
        <i class="fa-solid fa-user-plus"></i> Create Account
      </button>
    </form>

    <p style="text-align:center;margin-top:1.25rem;font-size:.9rem;color:var(--text-muted);">
      Already have an account? <a href="login.php"><strong>Sign In</strong></a>
    </p>
    <p style="text-align:center;margin-top:.5rem;font-size:.9rem;">
      <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
    </p>
  </div>

  <script src="assets/js/main.js"></script>
  <script>
    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
      const val = this.value;
      const bar = document.getElementById('strength-bar');
      const txt = document.getElementById('strength-text');
      let strength = 0;
      if (val.length >= 6)  strength++;
      if (val.length >= 10) strength++;
      if (/[A-Z]/.test(val)) strength++;
      if (/[0-9]/.test(val)) strength++;
      if (/[^A-Za-z0-9]/.test(val)) strength++;

      const levels = ['', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
      const colors = ['', '#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#059669'];
      bar.style.background = colors[strength] || 'var(--border)';
      bar.style.width = (strength * 20) + '%';
      txt.textContent = levels[strength] || '';
      txt.style.color = colors[strength] || '';
    });

    // Confirm password match
    document.getElementById('confirm').addEventListener('input', function() {
      const pass = document.getElementById('password').value;
      this.style.borderColor = (this.value === pass) ? 'var(--success)' : 'var(--danger)';
    });
  </script>
</body>
</html>
