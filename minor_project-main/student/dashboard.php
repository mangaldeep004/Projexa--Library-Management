<?php
/**
 * Student Dashboard
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireStudentLogin();

$uid  = $_SESSION['user_id'];
$user = getStudentById($pdo, $uid);
if (!$user) { header('Location: ../logout.php'); exit(); }

// Student's issued books
$myBooks  = getStudentIssuedBooks($pdo, $uid);
$issued   = array_filter($myBooks, fn($b) => $b['status'] === 'issued');
$overdue  = array_filter($myBooks, fn($b) => $b['status'] === 'overdue');
$returned = array_filter($myBooks, fn($b) => $b['status'] === 'returned');

// Unpaid fines
$fineStmt = $pdo->prepare("SELECT SUM(amount) FROM fines WHERE user_id=? AND paid_status='unpaid'");
$fineStmt->execute([$uid]);
$myFines = $fineStmt->fetchColumn() ?? 0;

// Total available books
$totalAvail = getAvailableBooks($pdo);

// Notifications
$notifications = getUnreadNotifications($pdo, $uid, 'student');

// Recent books (for browse preview)
$recentBooks = getAllBooks($pdo, '', '', 6, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>My Dashboard — SmartLib</title>
  <link rel="stylesheet" href="../assets/css/style.css"/>
  <link rel="stylesheet" href="../assets/css/dashboard.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="dashboard-layout">
  <div class="sidebar-overlay"></div>

  <!-- Student Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo"><i class="fa-solid fa-graduation-cap"></i></div>
      <span class="sidebar-brand">Student Portal</span>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section-title">My Library</div>
      <a href="dashboard.php" class="nav-item active">
        <i class="fa-solid fa-gauge nav-icon"></i><span class="nav-label">Dashboard</span>
      </a>
      <a href="browse_books.php" class="nav-item">
        <i class="fa-solid fa-book nav-icon"></i><span class="nav-label">Browse Books</span>
      </a>
      <a href="my_books.php" class="nav-item">
        <i class="fa-solid fa-list-check nav-icon"></i><span class="nav-label">My Books</span>
        <?php if(count($overdue) > 0): ?><span class="badge-count"><?= count($overdue) ?></span><?php endif; ?>
      </a>
      <div class="nav-section-title">Account</div>
      <a href="profile.php" class="nav-item">
        <i class="fa-solid fa-user nav-icon"></i><span class="nav-label">My Profile</span>
      </a>
    </nav>
    <div class="sidebar-footer">
      <div class="user-info">
        <div class="user-avatar"><?= strtoupper(substr($user['name'],0,1)) ?></div>
        <div>
          <div class="user-name"><?= htmlspecialchars(explode(' ',$user['name'])[0]) ?></div>
          <div class="user-role"><?= htmlspecialchars($user['course'] ?? 'Student') ?> <?= htmlspecialchars($user['semester'] ?? '') ?></div>
        </div>
      </div>
      <a href="../logout.php" class="nav-item" style="margin-top:.5rem;color:#fca5a5;">
        <i class="fa-solid fa-right-from-bracket nav-icon"></i><span class="nav-label">Logout</span>
      </a>
    </div>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <button id="toggle-sidebar"><i class="fa-solid fa-bars"></i></button>
        <div>
          <div class="page-title">My Dashboard</div>
          <div class="breadcrumb">Student / Dashboard</div>
        </div>
      </div>
      <div class="topbar-right">
        <div style="position:relative;">
          <button class="notif-btn">
            <i class="fa-solid fa-bell"></i>
            <?php if(count($notifications)): ?><span class="notif-badge"><?= count($notifications) ?></span><?php endif; ?>
          </button>
          <div class="notif-dropdown">
            <div class="notif-header"><h4>Notifications</h4></div>
            <div class="notif-list">
              <?php if(empty($notifications)): ?>
                <div class="notif-item"><p style="color:var(--text-muted);font-size:.85rem">No new notifications</p></div>
              <?php else: ?>
                <?php foreach($notifications as $n): ?>
                <div class="notif-item">
                  <div class="notif-dot"></div>
                  <div><div class="notif-msg"><?= htmlspecialchars($n['message']) ?></div><div class="notif-time"><?= formatDate($n['created_at']) ?></div></div>
                </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </header>

    <div class="content-area">
      <!-- Welcome Banner -->
      <div style="background:var(--gradient);border-radius:var(--radius);padding:1.75rem 2rem;margin-bottom:2rem;color:white;">
        <h2 style="color:white;">Welcome back, <?= htmlspecialchars(explode(' ',$user['name'])[0]) ?>! 👋</h2>
        <p style="color:rgba(255,255,255,.85);margin:.25rem 0 1rem;">Reg. No: <?= htmlspecialchars($user['reg_no'] ?? 'N/A') ?> &nbsp;|&nbsp; <?= htmlspecialchars($user['course'] ?? '') ?> <?= htmlspecialchars($user['semester'] ?? '') ?></p>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
          <a href="browse_books.php" class="btn btn-white btn-sm"><i class="fa-solid fa-book"></i> Browse Books</a>
          <a href="my_books.php"     class="btn btn-ghost btn-sm"><i class="fa-solid fa-list-check"></i> My Books</a>
          <a href="profile.php"      class="btn btn-ghost btn-sm"><i class="fa-solid fa-user"></i> My Profile</a>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon blue"><i class="fa-solid fa-book-open"></i></div>
          <div>
            <div class="stat-number counter" data-target="<?= count($issued) ?>"><?= count($issued) ?></div>
            <div class="stat-label">Books Issued</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i class="fa-solid fa-rotate-left"></i></div>
          <div>
            <div class="stat-number counter" data-target="<?= count($returned) ?>"><?= count($returned) ?></div>
            <div class="stat-label">Books Returned</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red"><i class="fa-solid fa-triangle-exclamation"></i></div>
          <div>
            <div class="stat-number counter" data-target="<?= count($overdue) ?>"><?= count($overdue) ?></div>
            <div class="stat-label">Overdue Books</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon orange"><i class="fa-solid fa-indian-rupee-sign"></i></div>
          <div>
            <div class="stat-number">₹<?= number_format($myFines,0) ?></div>
            <div class="stat-label">Pending Fine</div>
          </div>
        </div>
      </div>

      <!-- Currently Issued Books -->
      <?php if(!empty($issued)): ?>
      <div class="card" style="margin-bottom:1.75rem;">
        <div class="card-header">
          <span class="card-title">📚 Currently Issued Books</span>
          <a href="my_books.php" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr><th>Book</th><th>Author</th><th>Issue Date</th><th>Due Date</th><th>Days Left</th><th>Status</th></tr>
            </thead>
            <tbody>
              <?php foreach($issued as $b):
                $days = getDaysRemaining($b['due_date']);
              ?>
              <tr>
                <td><strong><?= htmlspecialchars($b['title']) ?></strong></td>
                <td><?= htmlspecialchars($b['author']) ?></td>
                <td><?= formatDate($b['issue_date']) ?></td>
                <td><?= formatDate($b['due_date']) ?></td>
                <td>
                  <?php if($days < 0): ?>
                    <span class="badge badge-danger">Overdue <?= abs($days) ?>d</span>
                  <?php elseif($days <= 3): ?>
                    <span class="badge badge-warning"><?= $days ?> days</span>
                  <?php else: ?>
                    <span class="badge badge-success"><?= $days ?> days</span>
                  <?php endif; ?>
                </td>
                <td><?= getStatusBadge($b['status']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>

      <!-- Fine Alert -->
      <?php if($myFines > 0): ?>
      <div class="alert alert-danger" style="margin-bottom:1.75rem;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
          <strong>Pending Fine: ₹<?= number_format($myFines,2) ?></strong><br/>
          <small>Please pay your overdue fines at the library counter.</small>
        </div>
      </div>
      <?php endif; ?>

      <!-- Browse New Books -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">🔍 Available Books</span>
          <a href="browse_books.php" class="btn btn-sm btn-primary">Browse All</a>
        </div>
        <div class="books-grid">
          <?php $colors = ['c1','c2','c3','c4','c5','c6']; ?>
          <?php foreach($recentBooks as $i => $book): ?>
          <div class="book-card">
            <div class="book-cover <?= $colors[$i % count($colors)] ?>">
              <i class="fa-solid fa-book"></i>
              <span class="book-avail <?= $book['available_copies'] > 0 ? '' : 'unavailable' ?>">
                <?= $book['available_copies'] > 0 ? 'Available' : 'Unavailable' ?>
              </span>
            </div>
            <div class="book-info">
              <div class="book-title"><?= htmlspecialchars(substr($book['title'],0,30)) ?>...</div>
              <div class="book-author"><?= htmlspecialchars($book['author']) ?></div>
              <div class="book-actions">
                <a href="browse_books.php?book=<?= $book['id'] ?>" class="btn btn-sm btn-primary">
                  <i class="fa-solid fa-eye"></i> View
                </a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>
