<!-- Student Sidebar Include -->
<?php requireStudentLogin(); ?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo"><i class="fa-solid fa-graduation-cap"></i></div>
    <span class="sidebar-brand">Student Portal</span>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-title">My Library</div>
    <a href="dashboard.php"    class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'dashboard.php')    ? 'active' : '' ?>">
      <i class="fa-solid fa-gauge nav-icon"></i><span class="nav-label">Dashboard</span>
    </a>
    <a href="browse_books.php" class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'browse_books.php') ? 'active' : '' ?>">
      <i class="fa-solid fa-book nav-icon"></i><span class="nav-label">Browse Books</span>
    </a>
    <a href="my_books.php"     class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'my_books.php')     ? 'active' : '' ?>">
      <i class="fa-solid fa-list-check nav-icon"></i><span class="nav-label">My Books</span>
    </a>
    <div class="nav-section-title">Account</div>
    <a href="profile.php"      class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'profile.php')      ? 'active' : '' ?>">
      <i class="fa-solid fa-user nav-icon"></i><span class="nav-label">My Profile</span>
    </a>
  </nav>
  <div class="sidebar-footer">
    <div class="user-info">
      <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'S', 0, 1)) ?></div>
      <div>
        <div class="user-name"><?= htmlspecialchars(explode(' ', $_SESSION['user_name'] ?? 'Student')[0]) ?></div>
        <div class="user-role">Student</div>
      </div>
    </div>
    <a href="../logout.php" class="nav-item" style="margin-top:.5rem;color:#fca5a5;">
      <i class="fa-solid fa-right-from-bracket nav-icon"></i><span class="nav-label">Logout</span>
    </a>
  </div>
</aside>
