<?php
/**
 * Logout Handler
 */
require_once 'includes/auth.php';
session_destroy();
header('Location: login.php?msg=logged_out');
exit();
?>
