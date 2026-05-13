<?php
/**
 * Smart Library Management System
 * Login Page — Admin & Student
 */
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isAdmin())   { header('Location: admin/dashboard.php');   exit(); }
if (isStudent()) { header('Location: student/dashboard.php'); exit(); }

$error   = '';
$success = '';

// Message from URL
if (isset($_GET['msg'])) {
  if ($_GET['msg'] === 'login_required') $error = 'Please log in to continue.';
  if ($_GET['msg'] === 'admin_required') $error = 'Admin access required.';
  if ($_GET['msg'] === 'registered')     $success = 'Account created! Please login.';
  if ($_GET['msg'] === 'logged_out')     $success = 'You have been logged out.';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email    = sanitize($_POST['email']    ?? '');
  $password = $_POST['password'] ?? '';
  $role     = $_POST['role']     ?? 'student';

  if (empty($email) || empty($password)) {
    $error = 'Email and password are required.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } else {
    if ($role === 'admin') {
      $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
      $stmt->execute([$email]);
      $admin = $stmt->fetch();

      if ($admin && verifyPassword($password, $admin['password'])) {
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['user_type']  = 'admin';
        header('Location: admin/dashboard.php');
        exit();
      } else {
        $error = 'Invalid admin email or password.';
      }
    } else {
      $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
      $stmt->execute([$email]);
      $user = $stmt->fetch();

      if ($user && verifyPassword($password, $user['password'])) {
        if ($user['status'] === 'blocked') {
          $error = 'Your account has been blocked. Contact the library admin.';
        } else {
          $_SESSION['user_id']   = $user['id'];
          $_SESSION['user_name'] = $user['name'];
          $_SESSION['user_type'] = 'student';
          header('Location: student/dashboard.php');
          exit();
        }
      } else {
        $error = 'Invalid email or password.';
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
  <title>Login — SmartLib</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body { min-height: 100vh; display:flex; align-items:center; justify-content:center; background: var(--gradient); padding: 1rem; }
    .auth-card { background: var(--surface); border-radius: var(--radius-lg); padding: 2.5rem; width: 100%; max-width: 440px; box-shadow: var(--shadow-lg); }
    .auth-logo { text-align:center; margin-bottom:1.75rem; }
    .auth-logo .icon { width:64px;height:64px;border-radius:16px;background:var(--gradient);color:white;font-size:1.75rem;display:flex;align-items:center;justify-content:center;margin:0 auto .75rem; }
    .auth-logo h2 { margin-bottom:.25rem; }
    .auth-logo p { font-size:.9rem; }
    .role-tabs { display:flex; background:var(--surface2); border-radius:var(--radius-sm); padding:4px; margin-bottom:1.5rem; }
    .role-tab { flex:1; padding:.6rem; text-align:center; border-radius:var(--radius-sm); cursor:pointer; font-weight:600; font-size:.9rem; color:var(--text-muted); transition:var(--transition); border:none; background:transparent; }
    .role-tab.active { background:var(--gradient); color:white; box-shadow: 0 2px 8px rgba(102,126,234,.4); }
    .forgot-link { text-align:right; margin-top:-.75rem; margin-bottom:1rem; }
    .forgot-link a { font-size:.85rem; }
    .divider { text-align:center; position:relative; margin: 1.25rem 0; color:var(--text-muted); font-size:.85rem; }
    .divider::before, .divider::after { content:''; position:absolute; top:50%; width:42%; height:1px; background:var(--border); }
    .divider::before { left:0; } .divider::after { right:0; }
    .demo-creds { background:var(--surface2); border-radius:var(--radius-sm); padding:1rem; font-size:.83rem; color:var(--text-muted); }
    .demo-creds strong { color:var(--primary); }
    .auth-footer { text-align:center; margin-top:1.5rem; font-size:.9rem; color:var(--text-muted); }
    .dark-toggle-top { position:fixed; top:1rem; right:1rem; }
  </style>
</head>
<body>
  <button class="btn-icon dark-toggle-top" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>

  <div class="auth-card animate-slide">
    <div class="auth-logo">
      <div class="icon"><i class="fa-solid fa-book-open"></i></div>
      <h2>Welcome Back!</h2>
      <p>Sign in to your SmartLib account</p>
    </div>

    <?php if ($error):   ?><div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= $success ?></div><?php endif; ?>

    <!-- Role Tabs -->
    <div class="role-tabs">
      <button class="role-tab active" id="tab-student" onclick="setRole('student')">
        <i class="fa-solid fa-user-graduate"></i> Student
      </button>
      <button class="role-tab" id="tab-admin" onclick="setRole('admin')">
        <i class="fa-solid fa-user-shield"></i> Admin
      </button>
    </div>

    <form method="POST" action="login.php">
      <input type="hidden" name="role" id="role-input" value="student" />

      <div class="form-group">
        <label class="form-label">Email Address</label>
        <div class="input-group">
          <i class="fa-solid fa-envelope input-icon"></i>
          <input type="email" name="email" id="email" class="form-control"
                 placeholder="Enter your email"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-group">
          <i class="fa-solid fa-lock input-icon"></i>
          <input type="password" name="password" id="password" class="form-control"
                 placeholder="Enter your password" required />
        </div>
      </div>

      <div class="forgot-link">
        <a href="#">Forgot password?</a>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;">
        <i class="fa-solid fa-right-to-bracket"></i> Sign In
      </button>
    </form>

    <div class="divider">Demo Credentials</div>
    <div class="demo-creds">
      <p><strong>Admin:</strong> admin@library.com / password</p>
      <p><strong>Student:</strong> rahul@student.com / password</p>
    </div>

    <div class="auth-footer">
      Don't have an account? <a href="register.php"><strong>Register here</strong></a>
    </div>
    <div class="auth-footer" style="margin-top:.5rem;">
      <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
    </div>
  </div>

  <script src="assets/js/main.js"></script>
  <script>
    function setRole(role) {
      document.getElementById('role-input').value = role;
      document.getElementById('tab-student').classList.toggle('active', role === 'student');
      document.getElementById('tab-admin').classList.toggle('active', role === 'admin');
      // Pre-fill demo credentials
      if (role === 'admin') {
        document.getElementById('email').value = 'admin@library.com';
      } else {
        document.getElementById('email').value = 'rahul@student.com';
      }
    }
  </script>
</body>
</html>
