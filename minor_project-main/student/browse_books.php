<?php
/**
 * Student — Browse Books
 */
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireStudentLogin();

$search   = sanitize($_GET['search']   ?? '');
$category = sanitize($_GET['category'] ?? '');
$categories = getAllCategories($pdo);
$books      = getAllBooks($pdo, $search, $category, 50, 0);
$colors     = ['c1','c2','c3','c4','c5','c6'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Browse Books — SmartLib</title>
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
        <div><div class="page-title">Browse Books</div><div class="breadcrumb">Student / Browse Books</div></div>
      </div>
      <div class="topbar-right">
        <button class="btn-icon" id="dark-mode-toggle"><i class="fa-solid fa-moon"></i></button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </div>
    </header>
    <div class="content-area">
      <div class="page-header">
        <h2><i class="fa-solid fa-book" style="color:var(--primary)"></i> Browse Books (<?= count($books) ?>)</h2>
      </div>

      <!-- Real-time Search Bar -->
      <form method="GET">
        <div class="filter-bar">
          <div class="input-group" style="flex:1">
            <i class="fa-solid fa-magnifying-glass input-icon"></i>
            <input type="text" name="search" id="live-search" class="form-control"
                   placeholder="Search by title, author, or ISBN..."
                   value="<?= htmlspecialchars($search) ?>" />
          </div>
          <select name="category" id="cat-filter" class="form-control" style="max-width:180px;">
            <option value="">All Categories</option>
            <?php foreach($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Search</button>
          <a href="browse_books.php" class="btn btn-secondary"><i class="fa-solid fa-rotate"></i> Reset</a>
        </div>
      </form>

      <!-- Books Grid -->
      <div class="books-grid" id="books-container">
        <?php if(empty($books)): ?>
          <p class="text-center text-muted" style="grid-column:1/-1;padding:3rem;">
            <i class="fa-solid fa-book" style="font-size:3rem;opacity:.3;display:block;margin-bottom:1rem"></i>
            No books found. Try a different search.
          </p>
        <?php else: ?>
          <?php foreach($books as $i => $book): ?>
          <div class="book-card">
            <div class="book-cover <?= $colors[$i % count($colors)] ?>">
              <i class="fa-solid fa-book"></i>
              <span class="book-avail <?= $book['available_copies'] > 0 ? '' : 'unavailable' ?>">
                <?= $book['available_copies'] > 0 ? 'Available' : 'Unavailable' ?>
              </span>
            </div>
            <div class="book-info">
              <div class="book-title"><?= htmlspecialchars(substr($book['title'],0,35)) ?><?= strlen($book['title']) > 35 ? '...' : '' ?></div>
              <div class="book-author"><?= htmlspecialchars($book['author']) ?></div>
              <?php if($book['category_name']): ?>
                <div style="margin-bottom:.5rem;"><span class="badge badge-primary" style="font-size:.7rem;"><?= htmlspecialchars($book['category_name']) ?></span></div>
              <?php endif; ?>
              <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:.75rem;">
                <i class="fa-solid fa-copy"></i> <?= $book['available_copies'] ?>/<?= $book['total_copies'] ?> copies
              </div>
              <div class="book-actions">
                <button class="btn btn-sm btn-primary" onclick="showBookDetail(<?= htmlspecialchars(json_encode($book)) ?>)">
                  <i class="fa-solid fa-eye"></i> Details
                </button>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Book Detail Modal -->
<div class="modal-overlay hidden" id="book-modal">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="modal-title">Book Details</h3>
      <button class="btn-close" onclick="document.getElementById('book-modal').classList.add('hidden')">×</button>
    </div>
    <div class="modal-body">
      <div style="display:flex;gap:1.5rem;flex-wrap:wrap;">
        <div id="modal-cover" style="width:80px;height:110px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:white;flex-shrink:0;"></div>
        <div style="flex:1;min-width:200px;">
          <h3 id="modal-book-title" style="margin-bottom:.4rem;"></h3>
          <p id="modal-author" style="color:var(--text-muted);margin-bottom:.75rem;"></p>
          <div id="modal-details" style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;font-size:.88rem;"></div>
        </div>
      </div>
      <div id="modal-desc" style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border);color:var(--text-muted);font-size:.9rem;"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="document.getElementById('book-modal').classList.add('hidden')">Close</button>
      <div id="modal-availability" style="font-size:.9rem;font-weight:600;align-self:center;"></div>
    </div>
  </div>
</div>

<script src="../assets/js/main.js"></script>
<script src="../assets/js/dashboard.js"></script>
<script>
const colors = ['c1','c2','c3','c4','c5','c6'];
const gradients = {
  c1:'linear-gradient(135deg,#667eea,#764ba2)',
  c2:'linear-gradient(135deg,#f093fb,#f5576c)',
  c3:'linear-gradient(135deg,#4facfe,#00f2fe)',
  c4:'linear-gradient(135deg,#43e97b,#38f9d7)',
  c5:'linear-gradient(135deg,#fa709a,#fee140)',
  c6:'linear-gradient(135deg,#a18cd1,#fbc2eb)'
};

let bookColorIndex = 0;

function showBookDetail(book) {
  const colorKey = colors[bookColorIndex % colors.length];
  bookColorIndex++;

  document.getElementById('modal-title').textContent = 'Book Details';
  document.getElementById('modal-book-title').textContent = book.title;
  document.getElementById('modal-author').textContent = 'by ' + book.author;
  document.getElementById('modal-cover').style.background = gradients[colorKey];
  document.getElementById('modal-cover').innerHTML = '<i class="fa-solid fa-book"></i>';
  document.getElementById('modal-desc').textContent = book.description || 'No description available.';

  document.getElementById('modal-details').innerHTML = `
    <div><b>ISBN:</b> ${book.isbn || '—'}</div>
    <div><b>Publisher:</b> ${book.publisher || '—'}</div>
    <div><b>Year:</b> ${book.year || '—'}</div>
    <div><b>Location:</b> ${book.location || '—'}</div>
    <div><b>Total Copies:</b> ${book.total_copies}</div>
    <div><b>Available:</b> ${book.available_copies}</div>
  `;

  const avail = document.getElementById('modal-availability');
  if (parseInt(book.available_copies) > 0) {
    avail.innerHTML = '<span class="badge badge-success"><i class="fa-solid fa-check"></i> Available — Visit library to issue</span>';
  } else {
    avail.innerHTML = '<span class="badge badge-danger"><i class="fa-solid fa-times"></i> Currently Unavailable</span>';
  }

  document.getElementById('book-modal').classList.remove('hidden');
}
</script>
</body>
</html>
