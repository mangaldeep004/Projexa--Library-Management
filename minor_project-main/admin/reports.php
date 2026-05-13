<?php
/**
 * Admin — Reports & Analytics
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdminLogin();

$totalBooks    = getTotalBooks($pdo);
$totalTitles   = getTotalTitles($pdo);
$availBooks    = getAvailableBooks($pdo);
$totalStudents = getTotalStudents($pdo);
$totalIssued   = $pdo->query("SELECT COUNT(*) FROM issued_books")->fetchColumn();
$totalReturned = $pdo->query("SELECT COUNT(*) FROM issued_books WHERE status='returned'")->fetchColumn();
$totalOverdue  = getTotalOverdue($pdo);
$totalFines    = getTotalFines($pdo);
$totalPaid     = $pdo->query("SELECT SUM(amount) FROM fines WHERE paid_status='paid'")->fetchColumn() ?? 0;

// Top 5 most issued books
$topBooks = $pdo->query(
  "SELECT b.title, b.author, COUNT(ib.id) as issue_count
   FROM issued_books ib JOIN books b ON ib.book_id=b.id
   GROUP BY ib.book_id ORDER BY issue_count DESC LIMIT 5"
)->fetchAll();

// Top 5 active students
$topStudents = $pdo->query(
  "SELECT u.name, u.reg_no, COUNT(ib.id) as issue_count
   FROM issued_books ib JOIN users u ON ib.user_id=u.id
   GROUP BY ib.user_id ORDER BY issue_count DESC LIMIT 5"
)->fetchAll();

$monthlyData = getMonthlyIssueData($pdo);
$catData     = getBooksPerCategory($pdo);
$chartMonthLabels = json_encode(array_column($monthlyData, 'month'));
$chartMonthValues = json_encode(array_column($monthlyData, 'count'));
$chartCatLabels   = json_encode(array_column($catData, 'name'));
$chartCatValues   = json_encode(array_column($catData, 'count'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Reports — SmartLib</title>
  <link rel="stylesheet" href="../assets/css/style.css"/>
  <link rel="stylesheet" href="../assets/css/dashboard.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="dashboard-layout">
  <div class="sidebar-overlay"></div>
  <?php include '_sidebar.php'; ?>
  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <button id="toggle-sidebar"><i class="fa-solid fa-bars"></i></button>
        <div><div class="page-title">Reports & Analytics</div><div class="breadcrumb">Admin / Reports</div></div>
      </div>
      <div class="topbar-right">
        <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
        <button class="btn btn-secondary btn-sm" onclick="window.print()"><i class="fa-solid fa-print"></i> Print</button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </header>
    <div class="content-area">
      <div class="page-header">
        <h2><i class="fa-solid fa-chart-bar" style="color:var(--primary)"></i> Library Analytics Report</h2>
        <small class="text-muted">Generated on <?= date('d F Y, h:i A') ?></small>
      </div>

      <!-- Summary Cards -->
      <div class="stats-row">
        <div class="stat-card"><div class="stat-icon blue"><i class="fa-solid fa-book"></i></div><div><div class="stat-number"><?= $totalBooks ?></div><div class="stat-label">Total Book Copies</div></div></div>
        <div class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-book-open"></i></div><div><div class="stat-number"><?= $availBooks ?></div><div class="stat-label">Available Books</div></div></div>
        <div class="stat-card"><div class="stat-icon purple"><i class="fa-solid fa-users"></i></div><div><div class="stat-number"><?= $totalStudents ?></div><div class="stat-label">Students</div></div></div>
        <div class="stat-card"><div class="stat-icon orange"><i class="fa-solid fa-list-check"></i></div><div><div class="stat-number"><?= $totalIssued ?></div><div class="stat-label">Total Issues</div></div></div>
        <div class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-rotate-left"></i></div><div><div class="stat-number"><?= $totalReturned ?></div><div class="stat-label">Returned</div></div></div>
        <div class="stat-card"><div class="stat-icon red"><i class="fa-solid fa-triangle-exclamation"></i></div><div><div class="stat-number"><?= $totalOverdue ?></div><div class="stat-label">Overdue</div></div></div>
        <div class="stat-card"><div class="stat-icon red"><i class="fa-solid fa-indian-rupee-sign"></i></div><div><div class="stat-number">₹<?= number_format($totalFines,0) ?></div><div class="stat-label">Unpaid Fines</div></div></div>
        <div class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-check-circle"></i></div><div><div class="stat-number">₹<?= number_format($totalPaid,0) ?></div><div class="stat-label">Fines Collected</div></div></div>
      </div>

      <!-- Charts -->
      <div class="charts-row">
        <div class="chart-card">
          <div class="card-header"><span class="card-title">📈 Monthly Issues (Last 6 Months)</span></div>
          <div style="height:250px;">
            <canvas id="issueChart" data-labels='<?= $chartMonthLabels ?>' data-values='<?= $chartMonthValues ?>'></canvas>
          </div>
        </div>
        <div class="chart-card">
          <div class="card-header"><span class="card-title">🍩 Books by Category</span></div>
          <div style="height:250px;">
            <canvas id="categoryChart" data-labels='<?= $chartCatLabels ?>' data-values='<?= $chartCatValues ?>'></canvas>
          </div>
        </div>
      </div>

      <div class="grid-2" style="margin-top:1.5rem;">
        <!-- Top Books -->
        <div class="card" style="padding:0;">
          <div class="card-header" style="padding:1.25rem 1.5rem;">
            <span class="card-title">🏆 Most Issued Books</span>
          </div>
          <div class="table-wrapper">
            <table>
              <thead><tr><th>Rank</th><th>Book</th><th>Author</th><th>Times Issued</th></tr></thead>
              <tbody>
                <?php if(empty($topBooks)): ?>
                  <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text-muted);">No data yet.</td></tr>
                <?php else: ?>
                  <?php foreach($topBooks as $i => $b): ?>
                  <tr>
                    <td><span class="badge badge-primary">#<?= $i+1 ?></span></td>
                    <td><?= htmlspecialchars($b['title']) ?></td>
                    <td><?= htmlspecialchars($b['author']) ?></td>
                    <td><strong><?= $b['issue_count'] ?></strong></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Top Students -->
        <div class="card" style="padding:0;">
          <div class="card-header" style="padding:1.25rem 1.5rem;">
            <span class="card-title">🎓 Most Active Students</span>
          </div>
          <div class="table-wrapper">
            <table>
              <thead><tr><th>Rank</th><th>Student</th><th>Reg No.</th><th>Books Issued</th></tr></thead>
              <tbody>
                <?php if(empty($topStudents)): ?>
                  <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text-muted);">No data yet.</td></tr>
                <?php else: ?>
                  <?php foreach($topStudents as $i => $s): ?>
                  <tr>
                    <td><span class="badge badge-success">#<?= $i+1 ?></span></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['reg_no'] ?? '—') ?></td>
                    <td><strong><?= $s['issue_count'] ?></strong></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>
