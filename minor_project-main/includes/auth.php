<?php
/**
 * Smart Library Management System
 * Authentication & Session Helper
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// Authentication Check Functions
// ============================================================

/**
 * Check if a user (student) is logged in
 * Redirects to login page if not
 */
function requireStudentLogin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
        header('Location: ' . getBaseUrl() . '/login.php?msg=login_required');
        exit();
    }
}

/**
 * Check if an admin is logged in
 * Redirects to login page if not
 */
function requireAdminLogin() {
    if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
        header('Location: ' . getBaseUrl() . '/login.php?msg=admin_required');
        exit();
    }
}

/**
 * Check if anyone is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) || isset($_SESSION['admin_id']);
}

/**
 * Check if admin is logged in
 */
function isAdmin() {
    return isset($_SESSION['admin_id']) && $_SESSION['user_type'] === 'admin';
}

/**
 * Check if student is logged in
 */
function isStudent() {
    return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'student';
}

/**
 * Get current logged-in user's name
 */
function getCurrentUserName() {
    if (isAdmin()) return $_SESSION['admin_name'] ?? 'Admin';
    if (isStudent()) return $_SESSION['user_name'] ?? 'Student';
    return 'Guest';
}

/**
 * Get the base URL for the project
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname(dirname($_SERVER['SCRIPT_NAME']));
    if ($script === '/' || $script === '\\') $script = '';
    return $protocol . '://' . $host . '/Minor Project';
}

/**
 * Redirect to a URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

/**
 * Flash message system
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
