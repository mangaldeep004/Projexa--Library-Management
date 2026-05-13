<?php
/**
 * Smart Library Management System
 * Utility / Helper Functions
 */

require_once __DIR__ . '/db.php';

// ============================================================
// Security Functions
// ============================================================

/**
 * Sanitize user input to prevent XSS
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Hash a password securely
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify a password against its hash
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// ============================================================
// Book Functions
// ============================================================

/**
 * Get total number of books
 */
function getTotalBooks($pdo) {
    $stmt = $pdo->query("SELECT SUM(total_copies) as total FROM books");
    return $stmt->fetchColumn() ?? 0;
}

/**
 * Get number of available books
 */
function getAvailableBooks($pdo) {
    $stmt = $pdo->query("SELECT SUM(available_copies) as total FROM books WHERE available_copies > 0");
    return $stmt->fetchColumn() ?? 0;
}

/**
 * Get total unique book titles
 */
function getTotalTitles($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM books");
    return $stmt->fetchColumn() ?? 0;
}

/**
 * Get all books with category name
 */
function getAllBooks($pdo, $search = '', $category = '', $limit = 50, $offset = 0) {
    $sql = "SELECT b.*, c.name as category_name FROM books b 
            LEFT JOIN categories c ON b.category_id = c.id 
            WHERE 1=1";
    $params = [];
    
    if ($search) {
        $sql .= " AND (b.title LIKE :search OR b.author LIKE :search OR b.isbn LIKE :search)";
        $params[':search'] = "%$search%";
    }
    if ($category) {
        $sql .= " AND b.category_id = :category";
        $params[':category'] = $category;
    }
    
    $sql .= " ORDER BY b.created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Get a single book by ID
 */
function getBookById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT b.*, c.name as category_name FROM books b 
                           LEFT JOIN categories c ON b.category_id = c.id 
                           WHERE b.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ============================================================
// User Functions
// ============================================================

/**
 * Get total registered students
 */
function getTotalStudents($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    return $stmt->fetchColumn() ?? 0;
}

/**
 * Get student by ID
 */
function getStudentById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ============================================================
// Issued Books Functions
// ============================================================

/**
 * Get total currently issued books
 */
function getTotalIssued($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM issued_books WHERE status = 'issued'");
    return $stmt->fetchColumn() ?? 0;
}

/**
 * Get total overdue books
 */
function getTotalOverdue($pdo) {
    // Update status first
    $pdo->exec("UPDATE issued_books SET status = 'overdue' WHERE due_date < CURDATE() AND status = 'issued'");
    $stmt = $pdo->query("SELECT COUNT(*) FROM issued_books WHERE status = 'overdue'");
    return $stmt->fetchColumn() ?? 0;
}

/**
 * Get total unpaid fines
 */
function getTotalFines($pdo) {
    $stmt = $pdo->query("SELECT SUM(amount) FROM fines WHERE paid_status = 'unpaid'");
    return $stmt->fetchColumn() ?? 0;
}

/**
 * Calculate fine for an overdue book (₹2 per day)
 */
function calculateFine($due_date, $return_date = null) {
    $due = new DateTime($due_date);
    $today = $return_date ? new DateTime($return_date) : new DateTime();
    $diff = $today->diff($due);
    
    if ($today > $due) {
        return $diff->days * 2; // ₹2 per day
    }
    return 0;
}

/**
 * Get issued books for a student
 */
function getStudentIssuedBooks($pdo, $user_id) {
    $stmt = $pdo->prepare(
        "SELECT ib.*, b.title, b.author, b.isbn, c.name as category_name
         FROM issued_books ib
         JOIN books b ON ib.book_id = b.id
         LEFT JOIN categories c ON b.category_id = c.id
         WHERE ib.user_id = ?
         ORDER BY ib.issue_date DESC"
    );
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

/**
 * Get all issued books (admin view)
 */
function getAllIssuedBooks($pdo, $status = '') {
    $sql = "SELECT ib.*, b.title, b.author, u.name as student_name, u.reg_no
            FROM issued_books ib
            JOIN books b ON ib.book_id = b.id
            JOIN users u ON ib.user_id = u.id
            WHERE 1=1";
    $params = [];
    
    if ($status) {
        $sql .= " AND ib.status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY ib.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// ============================================================
// Category Functions
// ============================================================

/**
 * Get all categories
 */
function getAllCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

// ============================================================
// Notification Functions
// ============================================================

/**
 * Get unread notifications for a user
 */
function getUnreadNotifications($pdo, $user_id, $type = 'user') {
    if ($type === 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE admin_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 10");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 10");
    }
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

/**
 * Format a date nicely
 */
function formatDate($date) {
    if (!$date) return 'N/A';
    return date('d M Y', strtotime($date));
}

/**
 * Get days remaining till due date
 */
function getDaysRemaining($due_date) {
    $due = new DateTime($due_date);
    $today = new DateTime();
    $diff = $today->diff($due);
    
    if ($today > $due) {
        return -$diff->days; // negative means overdue
    }
    return $diff->days;
}

/**
 * Get badge HTML for book status
 */
function getStatusBadge($status) {
    $badges = [
        'issued'   => '<span class="badge badge-primary">Issued</span>',
        'returned' => '<span class="badge badge-success">Returned</span>',
        'overdue'  => '<span class="badge badge-danger">Overdue</span>',
    ];
    return $badges[$status] ?? '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
}

/**
 * Check if a book is available
 */
function isBookAvailable($pdo, $book_id) {
    $stmt = $pdo->prepare("SELECT available_copies FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    return $book && $book['available_copies'] > 0;
}

/**
 * Get monthly issued books data for chart (last 6 months)
 */
function getMonthlyIssueData($pdo) {
    $stmt = $pdo->query(
        "SELECT DATE_FORMAT(issue_date, '%b %Y') as month,
                COUNT(*) as count
         FROM issued_books
         WHERE issue_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
         GROUP BY DATE_FORMAT(issue_date, '%Y-%m')
         ORDER BY issue_date ASC"
    );
    return $stmt->fetchAll();
}

/**
 * Get books per category for chart
 */
function getBooksPerCategory($pdo) {
    $stmt = $pdo->query(
        "SELECT c.name, COUNT(b.id) as count
         FROM categories c
         LEFT JOIN books b ON b.category_id = c.id
         GROUP BY c.id
         ORDER BY count DESC"
    );
    return $stmt->fetchAll();
}
?>
