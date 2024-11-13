<?php
session_start();
include 'db_connect.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items (replace with actual cart logic)
$cart_items = [
    // Example items: ['product_id' => 1, 'name' => 'Product 1', 'quantity' => 2, 'price' => 20.00]
];

$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['quantity'] * $item['price'];
}

?>

<h2>Checkout Confirmation</h2>
<table>
    <tr>
        <th>Product</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Subtotal</th>
    </tr>
    <?php foreach ($cart_items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td>$<?php echo number_format($item['price'], 2); ?></td>
            <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3" align="right"><strong>Total Amount:</strong></td>
        <td>$<?php echo number_format($total_amount, 2); ?></td>
    </tr>
</table>

<form action="place_order.php" method="post">
    <button type="submit">Place Order</button>
</form>
