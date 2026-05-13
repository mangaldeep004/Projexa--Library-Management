<?php
/**
 * Admin — Edit Book
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdminLogin();

$id   = (int)($_GET['id'] ?? 0);
$book = getBookById($pdo, $id);
if (!$book) { setFlash('danger', 'Book not found.'); header('Location: books.php'); exit(); }

$error      = '';
$categories = getAllCategories($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title   = sanitize($_POST['title']   ?? '');
  $author  = sanitize($_POST['author']  ?? '');
  $isbn    = sanitize($_POST['isbn']    ?? '');
  $cat_id  = (int)($_POST['category_id'] ?? 0);
  $pub     = sanitize($_POST['publisher'] ?? '');
  $year    = (int)($_POST['year'] ?? date('Y'));
  $total   = max(1, (int)($_POST['total_copies'] ?? 1));
  $avail   = max(0, (int)($_POST['available_copies'] ?? 0));
  $desc    = sanitize($_POST['description'] ?? '');
  $loc     = sanitize($_POST['location'] ?? '');

  if (empty($title) || empty($author)) {
    $error = 'Title and Author are required.';
  } else {
    $stmt = $pdo->prepare(
      "UPDATE books SET title=?, author=?, isbn=?, category_id=?, publisher=?, year=?,
       total_copies=?, available_copies=?, description=?, location=? WHERE id=?"
    );
    $stmt->execute([$title, $author, $isbn ?: null, $cat_id ?: null, $pub, $year, $total, min($avail, $total), $desc, $loc, $id]);
    setFlash('success', "Book '$title' updated successfully!");
    header('Location: books.php');
    exit();
  }
}
$b = $book; // shorthand
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Book — SmartLib</title>
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
          <div class="page-title">Edit Book</div>
          <div class="breadcrumb">Admin / Books / Edit</div>
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
        <h2><i class="fa-solid fa-pen" style="color:var(--warning)"></i> Edit Book</h2>
        <a href="books.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Books</a>
      </div>
      <div class="card" style="max-width:720px;">
        <form method="POST">
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">Book Title *</label>
              <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($_POST['title'] ?? $b['title']) ?>" required />
            </div>
            <div class="form-group">
              <label class="form-label">Author *</label>
              <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($_POST['author'] ?? $b['author']) ?>" required />
            </div>
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">ISBN</label>
              <input type="text" name="isbn" class="form-control" value="<?= htmlspecialchars($_POST['isbn'] ?? $b['isbn']) ?>" />
            </div>
            <div class="form-group">
              <label class="form-label">Category</label>
              <select name="category_id" class="form-control">
                <option value="">Select Category</option>
                <?php foreach($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>" <?= (($_POST['category_id'] ?? $b['category_id']) == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">Publisher</label>
              <input type="text" name="publisher" class="form-control" value="<?= htmlspecialchars($_POST['publisher'] ?? $b['publisher']) ?>" />
            </div>
            <div class="form-group">
              <label class="form-label">Year</label>
              <input type="number" name="year" class="form-control" value="<?= $_POST['year'] ?? $b['year'] ?>" />
            </div>
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">Total Copies</label>
              <input type="number" name="total_copies" class="form-control" min="1" value="<?= $_POST['total_copies'] ?? $b['total_copies'] ?>" />
            </div>
            <div class="form-group">
              <label class="form-label">Available Copies</label>
              <input type="number" name="available_copies" class="form-control" min="0" value="<?= $_POST['available_copies'] ?? $b['available_copies'] ?>" />
            </div>
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">Shelf Location</label>
              <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($_POST['location'] ?? $b['location']) ?>" />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST['description'] ?? $b['description']) ?></textarea>
          </div>
          <div style="display:flex;gap:1rem;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
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
