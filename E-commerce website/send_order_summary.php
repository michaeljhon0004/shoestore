<?php
function sendOrderSummary($user_email, $order_id) {
    include 'db_connect.php';

    // Fetch order details
    $order_query = "SELECT * FROM orders WHERE id = ?";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("i", $order_id);
    $order_stmt->execute();
    $order = $order_stmt->get_result()->fetch_assoc();

    // Fetch order items
    $items_query = "SELECT p.name, oi.quantity, oi.subtotal FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
    $items_stmt = $conn->prepare($items_query);
    $items_stmt->bind_param("i", $order_id);
    $items_stmt->execute();
    $items = $items_stmt->get_result();

    $message = "Thank you for your order!\n\nOrder ID: " . $order['id'] . "\nTotal Amount: $" . $order['total_amount'] . "\n\nItems:\n";

    while ($item = $items->fetch_assoc()) {
        $message .= $item['name'] . " - Quantity: " . $item['quantity'] . " - Subtotal: $" . $item['subtotal'] . "\n";
    }

    $message .= "\nThank you for shopping with us!";

    // Send the email
    mail($user_email, "Order Summary for Order #" . $order['id'], $message);

    $items_stmt->close();
    $order_stmt->close();
    $conn->close();
}
