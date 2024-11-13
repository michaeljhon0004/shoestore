<?php
session_start();
include 'db_connect.php';
include 'navbar.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Update order status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status']) && isset($_POST['order_id'])) {
    $status = $_POST['status'];
    $order_id = $_POST['order_id'];

    $update_query = "UPDATE orders SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $status, $order_id);
    $update_stmt->execute();
    $update_stmt->close();

    echo "Order status updated.";
}

// Fetch all orders
$order_query = "SELECT o.id, o.total_amount, o.status, o.created_at, u.username FROM orders o JOIN users u ON o.user_id = u.id";
$order_result = $conn->query($order_query);
?>

<!-- Display all orders -->
<h1>All Transactions</h1>
<table>
    <tr>
        <th>Order ID</th>
        <th>User</th>
        <th>Total Amount</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>
    <?php while ($order = $order_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $order['id']; ?></td>
            <td><?php echo htmlspecialchars($order['username']); ?></td>
            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($order['status']); ?></td>
            <td><?php echo $order['created_at']; ?></td>
            <td>
                <form method="post" action="view_transactions.php">
                    <select name="status">
                        <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Shipped" <?php if ($order['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                        <option value="Delivered" <?php if ($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                        <option value="Cancelled" <?php if ($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
$conn->close();
?>
