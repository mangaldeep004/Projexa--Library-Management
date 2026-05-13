<?php
/**
 * Admin — Add New Book
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdminLogin();

$error   = '';
$success = '';
$categories = getAllCategories($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title   = sanitize($_POST['title']   ?? '');
  $author  = sanitize($_POST['author']  ?? '');
  $isbn    = sanitize($_POST['isbn']    ?? '');
  $cat_id  = (int)($_POST['category_id'] ?? 0);
  $pub     = sanitize($_POST['publisher'] ?? '');
  $year    = (int)($_POST['year'] ?? date('Y'));
  $copies  = max(1, (int)($_POST['total_copies'] ?? 1));
  $desc    = sanitize($_POST['description'] ?? '');
  $loc     = sanitize($_POST['location'] ?? '');

  if (empty($title) || empty($author)) {
    $error = 'Title and Author are required.';
  } else {
    // Check ISBN uniqueness
    if ($isbn) {
      $chk = $pdo->prepare("SELECT id FROM books WHERE isbn = ?");
      $chk->execute([$isbn]);
      if ($chk->fetch()) { $error = 'A book with this ISBN already exists.'; }
    }
    if (!$error) {
      $stmt = $pdo->prepare(
        "INSERT INTO books (title, author, isbn, category_id, publisher, year, total_copies, available_copies, description, location)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
      );
      $stmt->execute([$title, $author, $isbn ?: null, $cat_id ?: null, $pub, $year, $copies, $copies, $desc, $loc]);
      setFlash('success', "Book '$title' added successfully!");
      header('Location: books.php');
      exit();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Book — SmartLib</title>
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
        <div>
          <div class="page-title">Add New Book</div>
          <div class="breadcrumb">Admin / Books / Add</div>
        </div>
      </div>
      <div class="topbar-right">
        <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </header>
    <div class="content-area">
      <?php if($error): ?><div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div><?php endif; ?>

      <div class="page-header">
        <h2><i class="fa-solid fa-plus" style="color:var(--primary)"></i> Add New Book</h2>
        <a href="books.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Books</a>
      </div>

      <div class="card" style="max-width:720px;">
        <form method="POST" action="add_book.php">
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">Book Title *</label>
              <input type="text" name="title" class="form-control" placeholder="e.g. Introduction to Algorithms"
                     value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required />
            </div>
            <div class="form-group">
              <label class="form-label">Author *</label>
              <input type="text" name="author" class="form-control" placeholder="e.g. Thomas H. Cormen"
                     value="<?= htmlspecialchars($_POST['author'] ?? '') ?>" required />
            </div>
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">ISBN</label>
              <input type="text" name="isbn" class="form-control" placeholder="978-xxxxxxxxxx"
                     value="<?= htmlspecialchars($_POST['isbn'] ?? '') ?>" />
            </div>
            <div class="form-group">
              <label class="form-label">Category</label>
              <select name="category_id" class="form-control">
                <option value="">Select Category</option>
                <?php foreach($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>" <?= (($_POST['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">Publisher</label>
              <input type="text" name="publisher" class="form-control" placeholder="Publisher name"
                     value="<?= htmlspecialchars($_POST['publisher'] ?? '') ?>" />
            </div>
            <div class="form-group">
              <label class="form-label">Year of Publication</label>
              <input type="number" name="year" class="form-control" min="1800" max="<?= date('Y') ?>"
                     value="<?= htmlspecialchars($_POST['year'] ?? date('Y')) ?>" />
            </div>
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">Total Copies</label>
              <input type="number" name="total_copies" class="form-control" min="1" value="<?= $_POST['total_copies'] ?? 1 ?>" />
            </div>
            <div class="form-group">
              <label class="form-label">Shelf Location</label>
              <input type="text" name="location" class="form-control" placeholder="e.g. CS-A1"
                     value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Short description of the book..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
          </div>
          <div style="display:flex;gap:1rem;">
            <button type="submit" class="btn btn-primary">
              <i class="fa-solid fa-plus"></i> Add Book
            </button>
            <a href="books.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>
