<?php
/**
 * Admin — Fine Management
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdminLogin();

$flash = getFlash();

// Mark as paid
if (isset($_GET['pay'])) {
  $fid = (int)$_GET['pay'];
  $pdo->prepare("UPDATE fines SET paid_status='paid', paid_date=CURDATE() WHERE id=?")->execute([$fid]);
  setFlash('success', 'Fine marked as paid.');
  header('Location: fines.php');
  exit();
}

$fines = $pdo->query(
  "SELECT f.*, u.name as student_name, u.reg_no, b.title as book_title, ib.due_date, ib.return_date
   FROM fines f
   JOIN users u ON f.user_id = u.id
   JOIN issued_books ib ON f.issued_book_id = ib.id
   JOIN books b ON ib.book_id = b.id
   ORDER BY f.created_at DESC"
)->fetchAll();

$totalUnpaid = array_sum(array_column(array_filter($fines, fn($f) => $f['paid_status'] === 'unpaid'), 'amount'));
$totalPaid   = array_sum(array_column(array_filter($fines, fn($f) => $f['paid_status'] === 'paid'),   'amount'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Fines — SmartLib</title>
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
        <div><div class="page-title">Fine Management</div><div class="breadcrumb">Admin / Fines</div></div>
      </div>
      <div class="topbar-right">
        <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </header>
    <div class="content-area">
      <?php if($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><i class="fa-solid fa-circle-check"></i> <?= $flash['message'] ?></div><?php endif; ?>
      <div class="page-header">
        <h2><i class="fa-solid fa-indian-rupee-sign" style="color:var(--danger)"></i> Fine Management</h2>
      </div>

      <!-- Summary Cards -->
      <div class="stats-row" style="grid-template-columns:repeat(3,1fr);margin-bottom:2rem;">
        <div class="stat-card">
          <div class="stat-icon red"><i class="fa-solid fa-indian-rupee-sign"></i></div>
          <div>
            <div class="stat-number">₹<?= number_format($totalUnpaid,0) ?></div>
            <div class="stat-label">Total Unpaid</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i class="fa-solid fa-check-circle"></i></div>
          <div>
            <div class="stat-number">₹<?= number_format($totalPaid,0) ?></div>
            <div class="stat-label">Total Collected</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon orange"><i class="fa-solid fa-list"></i></div>
          <div>
            <div class="stat-number"><?= count($fines) ?></div>
            <div class="stat-label">Total Fine Records</div>
          </div>
        </div>
      </div>

      <div class="card" style="padding:0;">
        <div class="table-wrapper">
          <table>
            <thead>
              <tr><th>#</th><th>Student</th><th>Book</th><th>Amount</th><th>Due Date</th><th>Return Date</th><th>Status</th><th>Paid On</th><th>Action</th></tr>
            </thead>
            <tbody>
              <?php if(empty($fines)): ?>
                <tr><td colspan="9" style="text-align:center;padding:2.5rem;color:var(--text-muted);">No fines recorded yet.</td></tr>
              <?php else: ?>
                <?php foreach($fines as $i => $f): ?>
                <tr>
                  <td><?= $i+1 ?></td>
                  <td><strong><?= htmlspecialchars($f['student_name']) ?></strong><br/><small><?= htmlspecialchars($f['reg_no'] ?? '') ?></small></td>
                  <td><?= htmlspecialchars($f['book_title']) ?></td>
                  <td><span class="fw-bold text-danger">₹<?= number_format($f['amount'],2) ?></span></td>
                  <td><?= formatDate($f['due_date']) ?></td>
                  <td><?= $f['return_date'] ? formatDate($f['return_date']) : '—' ?></td>
                  <td><?= $f['paid_status'] === 'paid' ? '<span class="badge badge-success">Paid</span>' : '<span class="badge badge-danger">Unpaid</span>' ?></td>
                  <td><?= $f['paid_date'] ? formatDate($f['paid_date']) : '—' ?></td>
                  <td>
                    <?php if($f['paid_status'] === 'unpaid'): ?>
                      <a href="?pay=<?= $f['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Mark this fine as paid?')">
                        <i class="fa-solid fa-check"></i> Mark Paid
                      </a>
                    <?php else: ?>
                      <span class="text-success"><i class="fa-solid fa-check-circle"></i> Done</span>
                    <?php endif; ?>
                  </td>
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
