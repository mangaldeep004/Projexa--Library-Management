<?php
/**
 * Student — My Issued Books History
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireStudentLogin();

$uid    = $_SESSION['user_id'];
$status = sanitize($_GET['status'] ?? '');
$books  = getStudentIssuedBooks($pdo, $uid);

if ($status) {
  $books = array_filter($books, fn($b) => $b['status'] === $status);
}

// Unpaid fines
$fineStmt = $pdo->prepare("SELECT SUM(amount) FROM fines WHERE user_id=? AND paid_status='unpaid'");
$fineStmt->execute([$uid]);
$myFines = $fineStmt->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>My Books — SmartLib</title>
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
        <div><div class="page-title">My Books</div><div class="breadcrumb">Student / My Books</div></div>
      </div>
      <div class="topbar-right">
        <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </header>
    <div class="content-area">
      <?php if($myFines > 0): ?>
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <div><strong>Pending Fine: ₹<?= number_format($myFines,2) ?></strong> — Please pay at the library counter.</div>
        </div>
      <?php endif; ?>

      <div class="page-header">
        <h2><i class="fa-solid fa-list-check" style="color:var(--primary)"></i> My Book History</h2>
        <a href="browse_books.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-book"></i> Browse More</a>
      </div>

      <!-- Filter Tabs -->
      <div style="display:flex;gap:.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
        <?php foreach([''=>'All','issued'=>'Currently Issued','returned'=>'Returned','overdue'=>'Overdue'] as $val => $lbl): ?>
          <a href="?status=<?= $val ?>"
             class="btn btn-sm <?= ($status === $val) ? 'btn-primary' : 'btn-secondary' ?>">
            <?= $lbl ?>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="card" style="padding:0;">
        <div class="table-wrapper">
          <table>
            <thead>
              <tr><th>#</th><th>Book Title</th><th>Author</th><th>Issue Date</th><th>Due Date</th><th>Return Date</th><th>Status</th><th>Fine</th></tr>
            </thead>
            <tbody>
              <?php if(empty($books)): ?>
                <tr><td colspan="8" style="text-align:center;padding:2.5rem;color:var(--text-muted);">
                  <i class="fa-solid fa-book" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:.75rem"></i>
                  No books found in this category.
                </td></tr>
              <?php else: ?>
                <?php $i=1; foreach($books as $b):
                  $fine = ($b['status'] === 'overdue') ? calculateFine($b['due_date'], $b['return_date']) : 0;
                  $days = getDaysRemaining($b['due_date']);
                ?>
                <tr>
                  <td><?= $i++ ?></td>
                  <td><strong><?= htmlspecialchars($b['title']) ?></strong></td>
                  <td><?= htmlspecialchars($b['author']) ?></td>
                  <td><?= formatDate($b['issue_date']) ?></td>
                  <td>
                    <?= formatDate($b['due_date']) ?>
                    <?php if($b['status'] === 'issued'): ?>
                      <?php if($days < 0): ?>
                        <br/><small class="text-danger">Overdue by <?= abs($days) ?> days</small>
                      <?php elseif($days <= 3): ?>
                        <br/><small class="text-warning">Due in <?= $days ?> days!</small>
                      <?php endif; ?>
                    <?php endif; ?>
                  </td>
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
