<?php
/**
 * Admin — All Issued Books List
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdminLogin();
getTotalOverdue($pdo); // refresh overdue statuses

$status_filter = sanitize($_GET['status'] ?? '');
$books = getAllIssuedBooks($pdo, $status_filter);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Issued Books — SmartLib</title>
  <link rel="stylesheet" href="../assets/css/style.css"/>
  <link rel="stylesheet" href="../assets/css/dashboard.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
<div class="dashboard-layout">
  <div class="sidebar-overlay"></div>
  <?php include '_sidebar.php'; ?>
  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <button id="toggle-sidebar"><i class="fa-solid fa-bars"></i></button>
        <div><div class="page-title">Issued Books</div><div class="breadcrumb">Admin / Issued Books</div></div>
      </div>
      <div class="topbar-right">
        <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </header>
    <div class="content-area">
      <div class="page-header">
        <h2><i class="fa-solid fa-list-check" style="color:var(--primary)"></i> Issued Books (<?= count($books) ?>)</h2>
        <div style="display:flex;gap:.75rem;">
          <a href="return_book.php" class="btn btn-warning btn-sm"><i class="fa-solid fa-rotate-left"></i> Return</a>
          <a href="issue_book.php"  class="btn btn-success btn-sm"><i class="fa-solid fa-arrow-right-from-bracket"></i> Issue</a>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div style="display:flex;gap:.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
        <?php foreach([''=>'All','issued'=>'Issued','returned'=>'Returned','overdue'=>'Overdue'] as $val => $lbl): ?>
          <a href="?status=<?= $val ?>"
             class="btn btn-sm <?= ($status_filter === $val) ? 'btn-primary' : 'btn-secondary' ?>">
            <?= $lbl ?>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="card" style="padding:0;">
        <div class="table-wrapper">
          <table>
            <thead>
              <tr><th>#</th><th>Student</th><th>Reg No.</th><th>Book</th><th>Issue Date</th><th>Due Date</th><th>Return Date</th><th>Status</th><th>Fine</th></tr>
            </thead>
            <tbody>
              <?php if(empty($books)): ?>
                <tr><td colspan="9" style="text-align:center;padding:2.5rem;color:var(--text-muted);">No records found.</td></tr>
              <?php else: ?>
                <?php foreach($books as $i => $b):
                  $fine = ($b['status'] === 'overdue') ? calculateFine($b['due_date'], $b['return_date']) : 0;
                ?>
                <tr>
                  <td><?= $i+1 ?></td>
                  <td><strong><?= htmlspecialchars($b['student_name']) ?></strong></td>
                  <td><small><?= htmlspecialchars($b['reg_no'] ?? '—') ?></small></td>
                  <td><?= htmlspecialchars($b['title']) ?></td>
                  <td><?= formatDate($b['issue_date']) ?></td>
                  <td><?= formatDate($b['due_date']) ?></td>
                  <td><?= $b['return_date'] ? formatDate($b['return_date']) : '—' ?></td>
                  <td><?= getStatusBadge($b['status']) ?></td>
                  <td><?= $fine > 0 ? '<span class="text-danger fw-bold">₹'.$fine.'</span>' : '—' ?></td>
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
<script src="../assets/js/main.js"></script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>
