<?php
/**
 * Admin — Issue Book to Student
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdminLogin();

$error   = '';
$success = '';
$students   = $pdo->query("SELECT id, name, reg_no FROM users WHERE status='active' ORDER BY name")->fetchAll();
$books_list = $pdo->query("SELECT id, title, author, available_copies FROM books WHERE available_copies > 0 ORDER BY title")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id  = (int)($_POST['user_id']  ?? 0);
  $book_id  = (int)($_POST['book_id']  ?? 0);
  $issue_dt = sanitize($_POST['issue_date'] ?? date('Y-m-d'));
  $due_dt   = sanitize($_POST['due_date']   ?? date('Y-m-d', strtotime('+14 days')));

  if (!$user_id || !$book_id) {
    $error = 'Please select both a student and a book.';
  } else {
    // Check availability
    $avail = $pdo->prepare("SELECT available_copies FROM books WHERE id = ?");
    $avail->execute([$book_id]);
    $bk = $avail->fetch();

    if (!$bk || $bk['available_copies'] < 1) {
      $error = 'This book is currently not available.';
    } else {
      // Check student hasn't already issued same book
      $dup = $pdo->prepare("SELECT id FROM issued_books WHERE user_id=? AND book_id=? AND status='issued'");
      $dup->execute([$user_id, $book_id]);
      if ($dup->fetch()) {
        $error = 'This student already has this book issued.';
      } else {
        $ins = $pdo->prepare(
          "INSERT INTO issued_books (user_id, book_id, issue_date, due_date, status, issued_by) VALUES (?,?,?,?,'issued',?)"
        );
        $ins->execute([$user_id, $book_id, $issue_dt, $due_dt, $_SESSION['admin_id']]);
        // Decrement available copies
        $pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id = ?")->execute([$book_id]);
        $success = 'Book issued successfully!';
        // Re-fetch available books
        $books_list = $pdo->query("SELECT id, title, author, available_copies FROM books WHERE available_copies > 0 ORDER BY title")->fetchAll();
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Issue Book — SmartLib</title>
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
        <div><div class="page-title">Issue Book</div><div class="breadcrumb">Admin / Issue Book</div></div>
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
        <h2><i class="fa-solid fa-arrow-right-from-bracket" style="color:var(--success)"></i> Issue Book to Student</h2>
        <a href="issued_books.php" class="btn btn-secondary"><i class="fa-solid fa-list-check"></i> View Issued Books</a>
      </div>

      <div class="grid-2" style="align-items:start;">
        <div class="card">
          <div class="card-header"><span class="card-title">📋 Issue Form</span></div>
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Select Student *</label>
              <select name="user_id" class="form-control" required>
                <option value="">-- Choose Student --</option>
                <?php foreach($students as $s): ?>
                  <option value="<?= $s['id'] ?>" <?= (($_POST['user_id'] ?? '') == $s['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['reg_no'] ?? 'N/A') ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Select Book *</label>
              <select name="book_id" class="form-control" required>
                <option value="">-- Choose Book --</option>
                <?php foreach($books_list as $b): ?>
                  <option value="<?= $b['id'] ?>" <?= (($_POST['book_id'] ?? '') == $b['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($b['title']) ?> — <?= htmlspecialchars($b['author']) ?> (<?= $b['available_copies'] ?> available)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="grid-2">
              <div class="form-group">
                <label class="form-label">Issue Date</label>
                <input type="date" name="issue_date" class="form-control"
                       value="<?= $_POST['issue_date'] ?? date('Y-m-d') ?>" required />
              </div>
              <div class="form-group">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" class="form-control"
                       value="<?= $_POST['due_date'] ?? date('Y-m-d', strtotime('+14 days')) ?>" required />
              </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">
              <i class="fa-solid fa-arrow-right-from-bracket"></i> Issue Book
            </button>
          </form>
        </div>

        <!-- Info Card -->
        <div>
          <div class="card" style="margin-bottom:1.25rem;">
            <div class="card-header"><span class="card-title">ℹ️ Issuing Rules</span></div>
            <ul style="color:var(--text-muted);font-size:.9rem;line-height:2;padding-left:1rem;list-style:disc;">
              <li>Default loan period: <strong>14 days</strong></li>
              <li>Fine for overdue: <strong>₹2 per day</strong></li>
              <li>Max books per student: <strong>3 books</strong></li>
              <li>Student must be <strong>active</strong> (not blocked)</li>
            </ul>
          </div>
          <div class="card">
            <div class="card-header"><span class="card-title">📚 Available Books Count</span></div>
            <div class="stat-number" style="font-size:3rem;text-align:center;padding:1rem 0;background:var(--gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
              <?= count($books_list) ?>
            </div>
            <p style="text-align:center;color:var(--text-muted);">Books currently available</p>
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
