<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];

// List of foul words to filter
$foul_words = ['badword1', 'badword2']; // Add actual words to filter

// Filter foul language
$filtered_comment = preg_replace("/\b(" . implode("|", $foul_words) . ")\b/i", "***", $comment);

// Insert review into the database
$review_query = "INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)";
$review_stmt = $conn->prepare($review_query);
$review_stmt->bind_param("iiis", $user_id, $product_id, $rating, $filtered_comment);
$review_stmt->execute();

if ($review_stmt->affected_rows > 0) {
    echo "Review submitted successfully!";
} else {
    echo "Error submitting review.";
}

$review_stmt->close();
header("Location: product_details.php?id=" . $product_id);
exit;
?>
