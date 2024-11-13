<?php include 'navbar.php';
session_start();
include 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error'] = "Access denied. Please log in as an admin.";
    header("Location: login.php");
    exit;
}

$product_id = $_GET['id'];

// Delete associated photos from file system
$photo_query = "SELECT photo_path FROM product_photos WHERE product_id = ?";
$photo_stmt = $conn->prepare($photo_query);
$photo_stmt->bind_param("i", $product_id);
$photo_stmt->execute();
$photo_result = $photo_stmt->get_result();

while ($photo = $photo_result->fetch_assoc()) {
    if (file_exists($photo['photo_path'])) {
        unlink($photo['photo_path']);  // Delete photo file
    }
}
$photo_stmt->close();

// Delete product and associated photos from database
$delete_photos_query = "DELETE FROM product_photos WHERE product_id = ?";
$delete_photos_stmt = $conn->prepare($delete_photos_query);
$delete_photos_stmt->bind_param("i", $product_id);
$delete_photos_stmt->execute();

$delete_product_query = "DELETE FROM products WHERE id = ?";
$delete_product_stmt = $conn->prepare($delete_product_query);
$delete_product_stmt->bind_param("i", $product_id);
$delete_product_stmt->execute();

header("Location: view_products.php");
exit;
?>
