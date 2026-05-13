<?php
/**
 * AJAX API — Real-time Book Search
 * Called by main.js via fetch()
 */
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$q   = sanitize($_GET['q']   ?? '');
$cat = sanitize($_GET['cat'] ?? '');

$books = getAllBooks($pdo, $q, $cat, 24, 0);

echo json_encode($books);
?>
