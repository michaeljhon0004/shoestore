<?php include 'navbar.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error'] = "Access denied. Please log in as an admin.";
    header("Location: login.php");
    exit;
}
?>
