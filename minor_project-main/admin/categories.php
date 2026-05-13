<?php
/**
 * Admin — Categories Management
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdminLogin();

$flash = getFlash();

// Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
  $name = sanitize($_POST['name'] ?? '');
  $desc = sanitize($_POST['description'] ?? '');
  if ($name) {
    $pdo->prepare("INSERT INTO categories (name, description) VALUES (?,?)")->execute([$name, $desc]);
    setFlash('success', "Category '$name' added successfully.");
  }
  header('Location: categories.php');
  exit();
}

// Delete
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  // Check if books exist in this category
  $count = $pdo->prepare("SELECT COUNT(*) FROM books WHERE category_id=?");
  $count->execute([$id]);
  if ($count->fetchColumn() > 0) {
    setFlash('danger', 'Cannot delete category — it has books assigned to it.');
  } else {
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    setFlash('success', 'Category deleted.');
  }
  header('Location: categories.php');
  exit();
}

$categories = $pdo->query("SELECT c.*, COUNT(b.id) as book_count FROM categories c LEFT JOIN books b ON b.category_id=c.id GROUP BY c.id ORDER BY c.name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Categories — SmartLib</title>
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
        <div><div class="page-title">Categories</div><div class="breadcrumb">Admin / Categories</div></div>
      </div>
      <div class="topbar-right">
        <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </header>
    <div class="content-area">
      <?php if($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><i class="fa-solid fa-circle-check"></i> <?= $flash['message'] ?></div><?php endif; ?>

      <div class="grid-2" style="align-items:start;">
        <!-- Add Category Form -->
        <div class="card">
          <div class="card-header"><span class="card-title">➕ Add New Category</span></div>
          <form method="POST">
            <input type="hidden" name="action" value="add"/>
            <div class="form-group">
              <label class="form-label">Category Name *</label>
              <input type="text" name="name" class="form-control" placeholder="e.g. Computer Science" required/>
            </div>
            <div class="form-group">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Brief description..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Category</button>
          </form>
        </div>

        <!-- Categories List -->
        <div class="card" style="padding:0;">
          <div class="card-header" style="padding:1.25rem 1.5rem;">
            <span class="card-title">📋 All Categories (<?= count($categories) ?>)</span>
          </div>
          <div class="table-wrapper">
            <table>
              <thead><tr><th>#</th><th>Name</th><th>Books</th><th>Description</th><th>Action</th></tr></thead>
              <tbody>
                <?php if(empty($categories)): ?>
                  <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted);">No categories yet.</td></tr>
                <?php else: ?>
                  <?php foreach($categories as $i => $cat): ?>
                  <tr>
                    <td><?= $i+1 ?></td>
                    <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                    <td><span class="badge badge-primary"><?= $cat['book_count'] ?></span></td>
                    <td><small style="color:var(--text-muted)"><?= htmlspecialchars(substr($cat['description'] ?? '', 0, 50)) ?>...</small></td>
                    <td>
                      <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger btn-delete-confirm">
                        <i class="fa-solid fa-trash"></i>
                      </a>
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
</body>
</html>
