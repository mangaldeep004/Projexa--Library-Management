<?php
/**
 * Smart Library Management System
 * Database Connection File
 * 
 * HOW TO USE:
 * Include this file in any PHP page that needs database access:
 * require_once '../includes/db.php';
 */

// ============================================================
// DATABASE CONFIGURATION - Change these settings!
// ============================================================
define('DB_HOST', 'localhost');        // Your MySQL host (usually localhost)
define('DB_USER', 'root');             // Your MySQL username
define('DB_PASS', '');                 // Your MySQL password (empty for XAMPP default)
define('DB_NAME', 'smart_library');    // The database name

// ============================================================
// Create Database Connection using PDO
// ============================================================
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Show a friendly error message
    die('<div style="font-family:sans-serif;padding:20px;background:#fee;border:1px solid red;border-radius:8px;margin:20px;">
        <h2 style="color:red;">🔴 Database Connection Failed</h2>
        <p>Could not connect to the database. Please check:</p>
        <ul>
            <li>Is XAMPP/WAMP running?</li>
            <li>Is MySQL service started?</li>
            <li>Did you import the <strong>library.sql</strong> file?</li>
            <li>Are DB credentials correct in <strong>includes/db.php</strong>?</li>
        </ul>
        <p><small>Error: ' . $e->getMessage() . '</small></p>
    </div>');
}
?>
