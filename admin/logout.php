<?php
/**
 * Dızo Wear - Admin Logout
 */

session_start();
unset($_SESSION['admin']);
session_destroy();
header('Location: login.php');
exit;
