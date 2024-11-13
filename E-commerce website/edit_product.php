<?php include 'navbar.php';
session_start();
include 'db_connect.php';
include 'navbar.php';

// Check if the user is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error'] = "Access denied. Please log in as an admin.";
    header("Location: login.php");
    exit;
}

$product_id = $_GET['id'];
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update product details
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $update_query = "UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssdi", $name, $description, $price, $product_id);
    $update_stmt->execute();

    // Handle new photo uploads
    foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
        $photo_path = 'uploads/' . basename($_FILES['photos']['name'][$key]);
        if (move_uploaded_file($tmp_name, $photo_path)) {
            $photo_query = "INSERT INTO product_photos (product_id, photo_path) VALUES (?, ?)";
            $photo_stmt = $conn->prepare($photo_query);
            $photo_stmt->bind_param("is", $product_id, $photo_path);
            $photo_stmt->execute();
            $photo_stmt->close();
        }
    }

    echo "Product updated successfully.";
}

// Fetch existing photos for the product
$photo_query = "SELECT * FROM product_photos WHERE product_id = ?";
$photo_stmt = $conn->prepare($photo_query);
$photo_stmt->bind_param("i", $product_id);
$photo_stmt->execute();
$photo_result = $photo_stmt->get_result();
?>

<!-- HTML form to edit product details -->
<form action="edit_product.php?id=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data">
    Product Name: <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br>
    Description: <textarea name="description"><?php echo htmlspecialchars($product['description']); ?></textarea><br>
    Price: <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required><br>
    New Photos: <input type="file" name="photos[]" multiple><br>
    <button type="submit">Update Product</button>
</form>

<!-- Display existing photos with delete option -->
<h3>Current Photos</h3>
<?php while ($photo = $photo_result->fetch_assoc()): ?>
    <div>
        <img src="<?php echo htmlspecialchars($photo['photo_path']); ?>" width="100" height="100">
        <a href="delete_photo.php?photo_id=<?php echo $photo['id']; ?>&product_id=<?php echo $product_id; ?>">Delete</a>
    </div>
<?php endwhile; ?>

<?php
$photo_stmt->close();
$stmt->close();
$conn->close();
?>
