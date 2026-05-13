<?php
/**
 * Admin Dashboard
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdminLogin();

// Update overdue statuses
getTotalOverdue($pdo);

// Statistics
$totalBooks    = getTotalBooks($pdo);
$totalTitles   = getTotalTitles($pdo);
$availBooks    = getAvailableBooks($pdo);
$totalStudents = getTotalStudents($pdo);
$totalIssued   = getTotalIssued($pdo);
$totalOverdue  = getTotalOverdue($pdo);
$totalFines    = getTotalFines($pdo);

// Recent issued books
$recentIssued  = getAllIssuedBooks($pdo);
$recentIssued  = array_slice($recentIssued, 0, 8);

// Chart data
$monthlyData   = getMonthlyIssueData($pdo);
$catData       = getBooksPerCategory($pdo);

$chartMonthLabels = json_encode(array_column($monthlyData, 'month'));
$chartMonthValues = json_encode(array_column($monthlyData, 'count'));
$chartCatLabels   = json_encode(array_column($catData, 'name'));
$chartCatValues   = json_encode(array_column($catData, 'count'));

// Notifications
$notifications = getUnreadNotifications($pdo, $_SESSION['admin_id'], 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard — SmartLib</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="../assets/css/dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="dashboard-layout">
  <div class="sidebar-overlay"></div>

  <!-- ===== SIDEBAR ===== -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo"><i class="fa-solid fa-book-open"></i></div>
      <span class="sidebar-brand">SmartLib Admin</span>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-title">Main</div>
      <a href="dashboard.php" class="nav-item active">
        <i class="fa-solid fa-gauge nav-icon"></i>
        <span class="nav-label">Dashboard</span>
      </a>
      <a href="books.php" class="nav-item">
        <i class="fa-solid fa-book nav-icon"></i>
        <span class="nav-label">Books</span>
      </a>
      <a href="categories.php" class="nav-item">
        <i class="fa-solid fa-tags nav-icon"></i>
        <span class="nav-label">Categories</span>
      </a>

      <div class="nav-section-title">Library</div>
      <a href="issue_book.php" class="nav-item">
        <i class="fa-solid fa-arrow-right-from-bracket nav-icon"></i>
        <span class="nav-label">Issue Book</span>
      </a>
      <a href="return_book.php" class="nav-item">
        <i class="fa-solid fa-rotate-left nav-icon"></i>
        <span class="nav-label">Return Book</span>
      </a>
      <a href="issued_books.php" class="nav-item">
        <i class="fa-solid fa-list-check nav-icon"></i>
        <span class="nav-label">Issued Books</span>
        <?php if($totalOverdue > 0): ?><span class="badge-count"><?= $totalOverdue ?></span><?php endif; ?>
      </a>
      <a href="fines.php" class="nav-item">
        <i class="fa-solid fa-indian-rupee-sign nav-icon"></i>
        <span class="nav-label">Fines</span>
      </a>

      <div class="nav-section-title">Users</div>
      <a href="students.php" class="nav-item">
        <i class="fa-solid fa-users nav-icon"></i>
        <span class="nav-label">Students</span>
      </a>
      <a href="reports.php" class="nav-item">
        <i class="fa-solid fa-chart-bar nav-icon"></i>
        <span class="nav-label">Reports</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="user-info">
        <div class="user-avatar"><?= strtoupper(substr($_SESSION['admin_name'], 0, 1)) ?></div>
        <div>
          <div class="user-name"><?= htmlspecialchars($_SESSION['admin_name']) ?></div>
          <div class="user-role">Administrator</div>
        </div>
      </div>
      <a href="../logout.php" class="nav-item" style="margin-top:.5rem;color:#fca5a5;">
        <i class="fa-solid fa-right-from-bracket nav-icon"></i>
        <span class="nav-label">Logout</span>
      </a>
    </div>
  </aside>

  <!-- ===== MAIN CONTENT ===== -->
  <div class="main-content">
    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar-left">
        <button id="toggle-sidebar"><i class="fa-solid fa-bars"></i></button>
        <div>
          <div class="page-title">Dashboard</div>
          <div class="breadcrumb">Home / Admin / Dashboard</div>
        </div>
      </div>
      <div class="topbar-right">
        <!-- Notification Bell -->
        <div style="position:relative;">
          <button class="notif-btn">
            <i class="fa-solid fa-bell"></i>
            <?php if(count($notifications)): ?><span class="notif-badge"><?= count($notifications) ?></span><?php endif; ?>
          </button>
          <div class="notif-dropdown">
            <div class="notif-header">
              <h4>Notifications</h4>
              <span class="badge badge-primary"><?= count($notifications) ?> new</span>
            </div>
            <div class="notif-list">
              <?php if(empty($notifications)): ?>
                <div class="notif-item"><p style="color:var(--text-muted);font-size:.85rem;padding:.5rem">No new notifications</p></div>
              <?php else: ?>
                <?php foreach($notifications as $n): ?>
                  <div class="notif-item">
                    <div class="notif-dot"></div>
                    <div>
                      <div class="notif-msg"><?= htmlspecialchars($n['message']) ?></div>
                      <div class="notif-time"><?= formatDate($n['created_at']) ?></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <button class="btn-icon" id="dark-mode-toggle" data-tooltip="Toggle Dark Mode">
          <i class="fa-solid fa-moon"></i>
        </button>
        <a href="../logout.php" class="btn btn-danger btn-sm">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
      </div>
    </header>

    <!-- Content -->
    <div class="content-area">
      <!-- Welcome Banner -->
      <div style="background:var(--gradient);border-radius:var(--radius);padding:1.75rem 2rem;margin-bottom:2rem;color:white;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
          <h2 style="color:white;font-size:1.5rem;">Good <?= (date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening')) ?>, <?= htmlspecialchars(explode(' ', $_SESSION['admin_name'])[0]) ?>! 👋</h2>
          <p style="color:rgba(255,255,255,.85);margin-top:.25rem;"><?= date('l, d F Y') ?> — Manage your library efficiently.</p>
        </div>
        <div style="display:flex;gap:.75rem;">
          <a href="books.php" class="btn btn-white btn-sm"><i class="fa-solid fa-plus"></i> Add Book</a>
          <a href="issue_book.php" class="btn btn-ghost btn-sm"><i class="fa-solid fa-arrow-right-from-bracket"></i> Issue Book</a>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon blue"><i class="fa-solid fa-book"></i></div>
          <div>
            <div class="stat-number counter" data-target="<?= $totalBooks ?>"><?= $totalBooks ?></div>
            <div class="stat-label">Total Books</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i class="fa-solid fa-book-open"></i></div>
          <div>
            <div class="stat-number counter" data-target="<?= $availBooks ?>"><?= $availBooks ?></div>
            <div class="stat-label">Available Books</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple"><i class="fa-solid fa-users"></i></div>
          <div>
            <div class="stat-number counter" data-target="<?= $totalStudents ?>"><?= $totalStudents ?></div>
            <div class="stat-label">Students</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon orange"><i class="fa-solid fa-list-check"></i></div>
          <div>
            <div class="stat-number counter" data-target="<?= $totalIssued ?>"><?= $totalIssued ?></div>
            <div class="stat-label">Books Issued</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon red"><i class="fa-solid fa-triangle-exclamation"></i></div>
          <div>
            <div class="stat-number counter" data-target="<?= $totalOverdue ?>"><?= $totalOverdue ?></div>
            <div class="stat-label">Overdue Books</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon orange"><i class="fa-solid fa-indian-rupee-sign"></i></div>
          <div>
            <div class="stat-number">₹<?= number_format($totalFines, 0) ?></div>
            <div class="stat-label">Pending Fines</div>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="charts-row">
        <div class="chart-card">
          <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-chart-line" style="color:var(--primary);margin-right:.5rem"></i>Monthly Book Issues</span>
          </div>
          <div style="height:250px;">
            <canvas id="issueChart"
              data-labels='<?= $chartMonthLabels ?>'
              data-values='<?= $chartMonthValues ?>'>
            </canvas>
          </div>
        </div>
        <div class="chart-card">
          <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-chart-pie" style="color:var(--secondary);margin-right:.5rem"></i>Books by Category</span>
          </div>
          <div style="height:250px;">
            <canvas id="categoryChart"
              data-labels='<?= $chartCatLabels ?>'
              data-values='<?= $chartCatValues ?>'>
            </canvas>
          </div>
        </div>
      </div>

      <!-- Availability Chart + Quick Actions -->
      <div class="charts-row">
        <div class="chart-card">
          <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-chart-bar" style="color:var(--success);margin-right:.5rem"></i>Library Overview</span>
          </div>
          <div style="height:250px;">
            <canvas id="availabilityChart"
              data-total="<?= $totalBooks ?>"
              data-avail="<?= $availBooks ?>"
              data-issued="<?= $totalIssued ?>"
              data-overdue="<?= $totalOverdue ?>">
            </canvas>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <span class="card-title">⚡ Quick Actions</span>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
            <a href="add_book.php" class="btn btn-primary" style="justify-content:center;">
              <i class="fa-solid fa-plus"></i> Add Book
            </a>
            <a href="issue_book.php" class="btn btn-success" style="justify-content:center;">
              <i class="fa-solid fa-arrow-right-from-bracket"></i> Issue
            </a>
            <a href="return_book.php" class="btn btn-warning" style="justify-content:center;">
              <i class="fa-solid fa-rotate-left"></i> Return
            </a>
            <a href="students.php" class="btn btn-secondary" style="justify-content:center;">
              <i class="fa-solid fa-users"></i> Students
            </a>
            <a href="fines.php" class="btn btn-danger" style="justify-content:center;">
              <i class="fa-solid fa-indian-rupee-sign"></i> Fines
            </a>
            <a href="reports.php" class="btn btn-secondary" style="justify-content:center;">
              <i class="fa-solid fa-file-export"></i> Reports
            </a>
          </div>
        </div>
      </div>

      <!-- Recent Issued Books Table -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">📋 Recent Issued Books</span>
          <a href="issued_books.php" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Book</th>
                <th>Issue Date</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Fine</th>
              </tr>
            </thead>
            <tbody>
              <?php if(empty($recentIssued)): ?>
                <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted);">No issued books yet.</td></tr>
              <?php else: ?>
                <?php foreach($recentIssued as $i => $book): 
                  $fine = ($book['status'] === 'overdue') ? calculateFine($book['due_date'], $book['return_date']) : 0;
                ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                      <strong><?= htmlspecialchars($book['student_name']) ?></strong><br/>
                      <small style="color:var(--text-muted)"><?= htmlspecialchars($book['reg_no'] ?? '') ?></small>
                    </td>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= formatDate($book['issue_date']) ?></td>
                    <td><?= formatDate($book['due_date']) ?></td>
                    <td><?= getStatusBadge($book['status']) ?></td>
                    <td><?= $fine > 0 ? '<span class="text-danger fw-bold">₹' . $fine . '</span>' : '<span class="text-muted">—</span>' ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div><!-- /content-area -->
  </div><!-- /main-content -->
</div><!-- /dashboard-layout -->

<script src="../assets/js/main.js"></script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>
