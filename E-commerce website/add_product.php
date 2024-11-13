<?php include 'navbar.php';
session_start();
include 'db_connect.php';
include 'navbar.php';

// Check if the user is an admin (assuming role check is required here)
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error'] = "Access denied. Please log in as an admin.";
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Insert product details into products table
    $query = "INSERT INTO products (name, description, price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssd", $name, $description, $price);
    
    if ($stmt->execute()) {
        $product_id = $stmt->insert_id;
        
        // Handle multiple photo uploads
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
        echo "Product added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML form for adding product -->
<form action="add_product.php" method="post" enctype="multipart/form-data">
    Product Name: <input type="text" name="name" required><br>
    Description: <textarea name="description"></textarea><br>
    Price: <input type="number" step="0.01" name="price" required><br>
    Photos: <input type="file" name="photos[]" multiple required><br>
    <button type="submit">Add Product</button>
</form>
