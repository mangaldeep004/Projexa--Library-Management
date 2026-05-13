<!-- Shared Admin Sidebar Include -->
<?php requireAdminLogin(); ?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo"><i class="fa-solid fa-book-open"></i></div>
    <span class="sidebar-brand">SmartLib Admin</span>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-title">Main</div>
    <a href="dashboard.php"   class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'dashboard.php')   ? 'active' : '' ?>">
      <i class="fa-solid fa-gauge nav-icon"></i><span class="nav-label">Dashboard</span>
    </a>
    <a href="books.php"       class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'books.php' || basename($_SERVER['PHP_SELF']) === 'add_book.php' || basename($_SERVER['PHP_SELF']) === 'edit_book.php') ? 'active' : '' ?>">
      <i class="fa-solid fa-book nav-icon"></i><span class="nav-label">Books</span>
    </a>
    <a href="categories.php"  class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'categories.php')  ? 'active' : '' ?>">
      <i class="fa-solid fa-tags nav-icon"></i><span class="nav-label">Categories</span>
    </a>
    <div class="nav-section-title">Library</div>
    <a href="issue_book.php"  class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'issue_book.php')  ? 'active' : '' ?>">
      <i class="fa-solid fa-arrow-right-from-bracket nav-icon"></i><span class="nav-label">Issue Book</span>
    </a>
    <a href="return_book.php" class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'return_book.php') ? 'active' : '' ?>">
      <i class="fa-solid fa-rotate-left nav-icon"></i><span class="nav-label">Return Book</span>
    </a>
    <a href="issued_books.php" class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'issued_books.php') ? 'active' : '' ?>">
      <i class="fa-solid fa-list-check nav-icon"></i><span class="nav-label">Issued Books</span>
    </a>
    <a href="fines.php"       class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'fines.php')       ? 'active' : '' ?>">
      <i class="fa-solid fa-indian-rupee-sign nav-icon"></i><span class="nav-label">Fines</span>
    </a>
    <div class="nav-section-title">Users</div>
    <a href="students.php"    class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'students.php')    ? 'active' : '' ?>">
      <i class="fa-solid fa-users nav-icon"></i><span class="nav-label">Students</span>
    </a>
    <a href="reports.php"     class="nav-item <?= (basename($_SERVER['PHP_SELF']) === 'reports.php')     ? 'active' : '' ?>">
      <i class="fa-solid fa-chart-bar nav-icon"></i><span class="nav-label">Reports</span>
    </a>
  </nav>
  <div class="sidebar-footer">
    <div class="user-info">
      <div class="user-avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?></div>
      <div>
        <div class="user-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
        <div class="user-role">Administrator</div>
      </div>
    </div>
    <a href="../logout.php" class="nav-item" style="margin-top:.5rem;color:#fca5a5;">
      <i class="fa-solid fa-right-from-bracket nav-icon"></i>
      <span class="nav-label">Logout</span>
    </a>
  </div>
</aside>
