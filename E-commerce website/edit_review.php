<?php
session_start();
include 'db_connect.php';
include 'navbar.php';

$review_id = $_GET['review_id'];
$user_id = $_SESSION['user_id'];

// Fetch existing review text
$review_query = "SELECT review_text FROM product_reviews WHERE id = ? AND user_id = ?";
$review_stmt = $conn->prepare($review_query);
$review_stmt->bind_param("ii", $review_id, $user_id);
$review_stmt->execute();
$review_result = $review_stmt->get_result();
$review = $review_result->fetch_assoc();

if (!$review) {
    echo "Review not found or access denied.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_review_text = $_POST['review_text'];

    // Apply foul language filter
    $bad_words = "/(badword1|badword2|badword3)/i";
    $filtered_text = preg_replace($bad_words, "***", $new_review_text);

    // Update review
    $update_query = "UPDATE product_reviews SET review_text = ? WHERE id = ? AND user_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sii", $filtered_text, $review_id, $user_id);
    $update_stmt->execute();

    echo "Review updated successfully.";
    header("Location: product_details.php?id=" . $_GET['product_id']);
    exit;
}
?>

<!-- Edit review form -->
<form action="edit_review.php?review_id=<?php echo $review_id; ?>&product_id=<?php echo $_GET['product_id']; ?>" method="post">
    <textarea name="review_text" required><?php echo htmlspecialchars($review['review_text']); ?></textarea><br>
    <button type="submit">Update Review</button>
</form>
