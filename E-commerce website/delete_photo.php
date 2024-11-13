<?php include 'navbar.php';
session_start();
include 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error'] = "Access denied. Please log in as an admin.";
    header("Location: login.php");
    exit;
}

$photo_id = $_GET['photo_id'];
$product_id = $_GET['product_id'];

// Get the photo path to delete from the file system
$query = "SELECT photo_path FROM product_photos WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $photo_id);
$stmt->execute();
$stmt->bind_result($photo_path);
$stmt->fetch();
$stmt->close();

if (file_exists($photo_path)) {
    unlink($photo_path);  // Delete photo file
}

// Delete photo entry from database
$delete_query = "DELETE FROM product_photos WHERE id = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param("i", $photo_id);
$delete_stmt->execute();
$delete_stmt->close();

header("Location: edit_product.php?id=" . $product_id);
exit;
?>
