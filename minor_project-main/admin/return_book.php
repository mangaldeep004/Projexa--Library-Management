<?php
/**
 * Admin — Return Book & Calculate Fine
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdminLogin();

$error   = '';
$success = '';

// Get all currently issued/overdue books
$issued = $pdo->query(
  "SELECT ib.*, b.title, b.author, u.name as student_name, u.reg_no
   FROM issued_books ib
   JOIN books b ON ib.book_id = b.id
   JOIN users u ON ib.user_id = u.id
   WHERE ib.status IN ('issued','overdue')
   ORDER BY ib.due_date ASC"
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $issued_id  = (int)($_POST['issued_id'] ?? 0);
  $return_date = sanitize($_POST['return_date'] ?? date('Y-m-d'));

  if (!$issued_id) {
    $error = 'Please select an issued book record.';
  } else {
    // Get the record
    $rec = $pdo->prepare("SELECT * FROM issued_books WHERE id = ? AND status IN ('issued','overdue')");
    $rec->execute([$issued_id]);
    $record = $rec->fetch();

    if (!$record) {
      $error = 'Record not found or book already returned.';
    } else {
      // Calculate fine
      $fine = calculateFine($record['due_date'], $return_date);

      // Update issued_books
      $pdo->prepare(
        "UPDATE issued_books SET status='returned', return_date=? WHERE id=?"
      )->execute([$return_date, $issued_id]);

      // Increment available copies
      $pdo->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE id = ?")->execute([$record['book_id']]);

      // Record fine if any
      if ($fine > 0) {
        $pdo->prepare(
          "INSERT INTO fines (issued_book_id, user_id, amount, paid_status) VALUES (?,?,?,'unpaid')"
        )->execute([$issued_id, $record['user_id'], $fine]);
        $success = "Book returned successfully! Fine imposed: ₹$fine";
      } else {
        $success = 'Book returned successfully! No fine.';
      }

      // Re-fetch
      $issued = $pdo->query(
        "SELECT ib.*, b.title, b.author, u.name as student_name, u.reg_no
         FROM issued_books ib JOIN books b ON ib.book_id=b.id JOIN users u ON ib.user_id=u.id
         WHERE ib.status IN ('issued','overdue') ORDER BY ib.due_date ASC"
      )->fetchAll();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Return Book — SmartLib</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="../assets/css/dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
<div class="dashboard-layout">
  <div class="sidebar-overlay"></div>
  <?php include '_sidebar.php'; ?>
  <div class="main-content">
    <header class="topbar">
      <div class="topbar-left">
        <button id="toggle-sidebar"><i class="fa-solid fa-bars"></i></button>
        <div><div class="page-title">Return Book</div><div class="breadcrumb">Admin / Return Book</div></div>
      </div>
      <div class="topbar-right">
        <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </header>
    <div class="content-area">
      <?php if($error):   ?><div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div><?php endif; ?>
      <?php if($success): ?><div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= $success ?></div><?php endif; ?>

      <div class="page-header">
        <h2><i class="fa-solid fa-rotate-left" style="color:var(--warning)"></i> Return Book</h2>
      </div>

      <div class="grid-2" style="align-items:start;">
        <div class="card">
          <div class="card-header"><span class="card-title">📤 Return Form</span></div>
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Select Issued Book *</label>
              <select name="issued_id" class="form-control" required onchange="updateFinePreview(this)">
                <option value="">-- Select Record --</option>
                <?php foreach($issued as $ib): 
                  $days = getDaysRemaining($ib['due_date']);
                  $status = $days < 0 ? ' ⚠️ OVERDUE' : '';
                ?>
                  <option value="<?= $ib['id'] ?>"
                          data-due="<?= $ib['due_date'] ?>"
                          data-days="<?= $days ?>"
                          <?= (($_POST['issued_id'] ?? '') == $ib['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ib['student_name']) ?> — "<?= htmlspecialchars($ib['title']) ?>" (Due: <?= formatDate($ib['due_date']) ?><?= $status ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Return Date</label>
              <input type="date" name="return_date" id="return_date" class="form-control"
                     value="<?= $_POST['return_date'] ?? date('Y-m-d') ?>"
                     oninput="updateFinePreview(document.querySelector('select[name=issued_id]'))" />
            </div>

            <!-- Fine Preview -->
            <div id="fine-preview" style="display:none;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:var(--radius-sm);padding:1rem;margin-bottom:1rem;">
              <div style="font-weight:700;color:var(--danger);">⚠️ Fine Preview</div>
              <div id="fine-amount" style="font-size:1.5rem;font-weight:800;color:var(--danger);"></div>
              <div id="fine-detail" style="font-size:.85rem;color:var(--text-muted);"></div>
            </div>

            <button type="submit" class="btn btn-warning" style="width:100%;">
              <i class="fa-solid fa-rotate-left"></i> Process Return
            </button>
          </form>
        </div>

        <!-- Currently Issued Table -->
        <div class="card" style="padding:0;">
          <div class="card-header" style="padding:1.25rem 1.5rem;">
            <span class="card-title">📋 Currently Issued (<?= count($issued) ?>)</span>
          </div>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr><th>Student</th><th>Book</th><th>Due Date</th><th>Status</th></tr>
              </thead>
              <tbody>
                <?php if(empty($issued)): ?>
                  <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text-muted);">No books currently issued.</td></tr>
                <?php else: ?>
                  <?php foreach($issued as $ib): 
                    $days = getDaysRemaining($ib['due_date']);
                  ?>
                    <tr>
                      <td><?= htmlspecialchars($ib['student_name']) ?></td>
                      <td><?= htmlspecialchars(substr($ib['title'], 0, 25)) ?>...</td>
                      <td><?= formatDate($ib['due_date']) ?></td>
                      <td>
                        <?php if($days < 0): ?>
                          <span class="badge badge-danger">Overdue <?= abs($days) ?>d</span>
                        <?php elseif($days <= 3): ?>
                          <span class="badge badge-warning">Due soon</span>
                        <?php else: ?>
                          <span class="badge badge-success"><?= $days ?>d left</span>
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
</div>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/dashboard.js"></script>
<script>
function updateFinePreview(sel) {
  const opt = sel.options[sel.selectedIndex];
  const dueDate = opt.dataset.due;
  const returnDate = document.getElementById('return_date').value;
  const preview = document.getElementById('fine-preview');

  if (!dueDate || !returnDate) { preview.style.display='none'; return; }

  const due    = new Date(dueDate);
  const ret    = new Date(returnDate);
  const diffMs = ret - due;
  const days   = Math.floor(diffMs / (1000*60*60*24));

  if (days > 0) {
    const fine = days * 2;
    document.getElementById('fine-amount').textContent = '₹' + fine;
    document.getElementById('fine-detail').textContent = days + ' overdue days × ₹2/day';
    preview.style.display = 'block';
  } else {
    preview.style.display = 'none';
  }
}
</script>
</body>
</html>
