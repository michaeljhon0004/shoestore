<?php
session_start();
include 'db_connect.php';
include 'send_order_summary.php'; // Include the email summary function

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Assume cart items are passed here, or retrieve them based on session/cart logic
$cart_items = [
    // Example: ['product_id' => 1, 'quantity' => 2, 'price' => 20.00]
];

// Calculate total amount
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['quantity'] * $item['price'];
}

// Insert into orders table
$order_query = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
$order_stmt = $conn->prepare($order_query);
$order_stmt->bind_param("id", $user_id, $total_amount);
$order_stmt->execute();
$order_id = $order_stmt->insert_id;
$order_stmt->close();

// Insert into order_items table
foreach ($cart_items as $item) {
    $subtotal = $item['quantity'] * $item['price'];
    $order_item_query = "INSERT INTO order_items (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)";
    $order_item_stmt = $conn->prepare($order_item_query);
    $order_item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $subtotal);
    $order_item_stmt->execute();
    $order_item_stmt->close();
}

// Fetch user's email address
$user_query = "SELECT email FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_email = $user_stmt->get_result()->fetch_assoc()['email'];
$user_stmt->close();

// Send order summary email
sendOrderSummary($user_email, $order_id);

// Redirect to confirmation page
header("Location: order_confirmation.php?order_id=" . $order_id);
exit;
?>
