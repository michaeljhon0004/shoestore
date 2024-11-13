<?php
session_start();
include 'db_connect.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = $_GET['order_id'];

// Fetch order details
$order_query = "SELECT o.id, o.total_amount, o.created_at, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?";
$order_stmt = $conn->prepare($order_query);
$order_stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$order_stmt->execute();
$order = $order_stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit;
}

?>

<h2>Order Confirmation</h2>
<p>Thank you, <?php echo htmlspecialchars($order['username']); ?>! Your order has been successfully placed.</p>
<p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
<p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
<p><strong>Order Date:</strong> <?php echo $order['created_at']; ?></p>
<p>An email confirmation with your order details has been sent to your email address.</p>

<a href="index.php">Return to Home</a>
