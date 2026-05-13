<?php
/**
 * Admin — Book Management (List + Delete)
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdminLogin();

$flash = getFlash();
$search   = sanitize($_GET['search']   ?? '');
$category = sanitize($_GET['category'] ?? '');

// Handle Delete
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
  $stmt->execute([$id]);
  setFlash('success', 'Book deleted successfully.');
  header('Location: books.php');
  exit();
}

$books      = getAllBooks($pdo, $search, $category, 100, 0);
$categories = getAllCategories($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Books — SmartLib</title>
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
          <div class="page-title">Book Management</div>
          <div class="breadcrumb">Admin / Books</div>
        </div>
      </div>
      <div class="topbar-right">
        <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </header>

    <div class="content-area">
      <?php if($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>"><i class="fa-solid fa-circle-check"></i> <?= $flash['message'] ?></div>
      <?php endif; ?>

      <div class="page-header">
        <h2><i class="fa-solid fa-book" style="color:var(--primary)"></i> All Books (<?= count($books) ?>)</h2>
        <a href="add_book.php" class="btn btn-primary">
          <i class="fa-solid fa-plus"></i> Add New Book
        </a>
      </div>

      <!-- Search & Filter -->
      <form method="GET" action="books.php">
        <div class="filter-bar">
          <div class="input-group" style="flex:1;min-width:220px;">
            <i class="fa-solid fa-magnifying-glass input-icon"></i>
            <input type="text" name="search" class="form-control" placeholder="Search by title, author, ISBN..." value="<?= htmlspecialchars($search) ?>" />
          </div>
          <select name="category" class="form-control" style="max-width:180px;">
            <option value="">All Categories</option>
            <?php foreach($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Search</button>
          <a href="books.php" class="btn btn-secondary"><i class="fa-solid fa-rotate"></i> Reset</a>
        </div>
      </form>

      <!-- Books Table -->
      <div class="card" style="padding:0;">
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>ISBN</th>
                <th>Copies</th>
                <th>Available</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if(empty($books)): ?>
                <tr><td colspan="9" style="text-align:center;padding:2.5rem;color:var(--text-muted);">
                  <i class="fa-solid fa-book-open" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.5rem"></i>
                  No books found.
                </td></tr>
              <?php else: ?>
                <?php foreach($books as $i => $book): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><strong><?= htmlspecialchars($book['title']) ?></strong></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><span class="badge badge-primary"><?= htmlspecialchars($book['category_name'] ?? 'N/A') ?></span></td>
                    <td><small><?= htmlspecialchars($book['isbn'] ?? '—') ?></small></td>
                    <td><?= $book['total_copies'] ?></td>
                    <td><?= $book['available_copies'] ?></td>
                    <td>
                      <?php if($book['available_copies'] > 0): ?>
                        <span class="badge badge-success">Available</span>
                      <?php else: ?>
                        <span class="badge badge-danger">Unavailable</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div style="display:flex;gap:.4rem;">
                        <a href="edit_book.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-warning" data-tooltip="Edit">
                          <i class="fa-solid fa-pen"></i>
                        </a>
                        <a href="books.php?delete=<?= $book['id'] ?>"
                           class="btn btn-sm btn-danger btn-delete-confirm" data-tooltip="Delete">
                          <i class="fa-solid fa-trash"></i>
                        </a>
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
