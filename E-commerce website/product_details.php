<?php
session_start();
include 'db_connect.php';

$product_id = $_GET['product_id'];

// Fetch product details
$product_query = "SELECT * FROM products WHERE id = ?";
$product_stmt = $conn->prepare($product_query);
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product = $product_stmt->get_result()->fetch_assoc();
$product_stmt->close();

// Check if the user has purchased this product
$purchased = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $purchase_query = "SELECT * FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.product_id = ? AND o.user_id = ?";
    $purchase_stmt = $conn->prepare($purchase_query);
    $purchase_stmt->bind_param("ii", $product_id, $user_id);
    $purchase_stmt->execute();
    $purchased = $purchase_stmt->get_result()->num_rows > 0;
    $purchase_stmt->close();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $purchased) {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Foul language filter using regex
    $foul_words = ['badword1', 'badword2', 'badword3'];
    $filtered_comment = preg_replace("/\b(" . implode('|', $foul_words) . ")\b/i", '****', $comment);

    $review_query = "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)";
    $review_stmt = $conn->prepare($review_query);
    $review_stmt->bind_param("iiis", $product_id, $user_id, $rating, $filtered_comment);
    $review_stmt->execute();
    $review_stmt->close();

    echo "Review added successfully!";
}
?>

<h1><?php echo htmlspecialchars($product['name']); ?></h1>

<!-- Review Form -->
<?php if ($purchased): ?>
<form action="product_details.php?product_id=<?php echo $product_id; ?>" method="post">
    Rating: 
    <select name="rating" required>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php endfor; ?>
    </select><br>
    Comment: <textarea name="comment" required></textarea><br>
    <button type="submit">Submit Review</button>
</form>
<?php else: ?>
<p>You must purchase this product to leave a review.</p>
<?php endif; ?>

<!-- Display existing reviews -->
<h2>Reviews</h2>
<?php
$reviews_query = "SELECT r.rating, r.comment, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ?";
$reviews_stmt = $conn->prepare($reviews_query);
$reviews_stmt->bind_param("i", $product_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();

while ($review = $reviews_result->fetch_assoc()): ?>
    <p><?php echo htmlspecialchars($review['username']); ?>: 
       Rating: <?php echo $review['rating']; ?>/5<br>
       <?php echo htmlspecialchars($review['comment']); ?>
    </p>
<?php endwhile;

$reviews_stmt->close();
$conn->close();
?>
