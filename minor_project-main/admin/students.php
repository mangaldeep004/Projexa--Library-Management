<?php
/**
 * Admin — Students Management
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdminLogin();

$flash = getFlash();

// Toggle block/unblock
if (isset($_GET['toggle']) && isset($_GET['id'])) {
  $sid    = (int)$_GET['id'];
  $action = sanitize($_GET['toggle']);
  if (in_array($action, ['block','unblock'])) {
    $newStatus = ($action === 'block') ? 'blocked' : 'active';
    $pdo->prepare("UPDATE users SET status=? WHERE id=?")->execute([$newStatus, $sid]);
    setFlash('success', "Student has been $newStatus.");
    header('Location: students.php');
    exit();
  }
}

$search = sanitize($_GET['search'] ?? '');
$sql = "SELECT u.*, 
        (SELECT COUNT(*) FROM issued_books WHERE user_id=u.id AND status='issued') as books_issued,
        (SELECT COUNT(*) FROM fines WHERE user_id=u.id AND paid_status='unpaid') as unpaid_fines
        FROM users u WHERE 1=1";
$params = [];
if ($search) {
  $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.reg_no LIKE ?)";
  $params = ["%$search%", "%$search%", "%$search%"];
}
$sql .= " ORDER BY u.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Students — SmartLib</title>
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
        <div><div class="page-title">Students</div><div class="breadcrumb">Admin / Students</div></div>
      </div>
      <div class="topbar-right">
        <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </header>
    <div class="content-area">
      <?php if($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><i class="fa-solid fa-circle-check"></i> <?= $flash['message'] ?></div><?php endif; ?>
      <div class="page-header">
        <h2><i class="fa-solid fa-users" style="color:var(--primary)"></i> Students (<?= count($students) ?>)</h2>
      </div>
      <form method="GET">
        <div class="filter-bar">
          <div class="input-group" style="flex:1">
            <i class="fa-solid fa-magnifying-glass input-icon"></i>
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, or reg no..." value="<?= htmlspecialchars($search) ?>"/>
          </div>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Search</button>
          <a href="students.php" class="btn btn-secondary"><i class="fa-solid fa-rotate"></i> Reset</a>
        </div>
      </form>
      <div class="card" style="padding:0;">
        <div class="table-wrapper">
          <table>
            <thead>
              <tr><th>#</th><th>Name</th><th>Email</th><th>Reg. No.</th><th>Course</th><th>Books Issued</th><th>Unpaid Fines</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php if(empty($students)): ?>
                <tr><td colspan="9" style="text-align:center;padding:2.5rem;color:var(--text-muted);">No students found.</td></tr>
              <?php else: ?>
                <?php foreach($students as $i => $s): ?>
                <tr>
                  <td><?= $i+1 ?></td>
                  <td>
                    <div style="display:flex;align-items:center;gap:.6rem;">
                      <div style="width:32px;height:32px;border-radius:50%;background:var(--gradient);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;">
                        <?= strtoupper(substr($s['name'],0,1)) ?>
                      </div>
                      <strong><?= htmlspecialchars($s['name']) ?></strong>
                    </div>
                  </td>
                  <td><small><?= htmlspecialchars($s['email']) ?></small></td>
                  <td><?= htmlspecialchars($s['reg_no'] ?? '—') ?></td>
                  <td><?= htmlspecialchars($s['course'] ?? '—') ?> <?= htmlspecialchars($s['semester'] ?? '') ?></td>
                  <td><span class="badge badge-primary"><?= $s['books_issued'] ?></span></td>
                  <td><?= $s['unpaid_fines'] > 0 ? '<span class="badge badge-danger">'.$s['unpaid_fines'].'</span>' : '—' ?></td>
                  <td>
                    <?php if($s['status'] === 'active'): ?>
                      <span class="badge badge-success">Active</span>
                    <?php elseif($s['status'] === 'blocked'): ?>
                      <span class="badge badge-danger">Blocked</span>
                    <?php else: ?>
                      <span class="badge badge-secondary"><?= ucfirst($s['status']) ?></span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div style="display:flex;gap:.4rem;">
                      <?php if($s['status'] === 'active'): ?>
                        <a href="?toggle=block&id=<?= $s['id'] ?>" class="btn btn-sm btn-danger btn-delete-confirm" onclick="return confirm('Block this student?')">
                          <i class="fa-solid fa-ban"></i>
                        </a>
                      <?php else: ?>
                        <a href="?toggle=unblock&id=<?= $s['id'] ?>" class="btn btn-sm btn-success">
                          <i class="fa-solid fa-check"></i>
                        </a>
                      <?php endif; ?>
                    </div>
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
