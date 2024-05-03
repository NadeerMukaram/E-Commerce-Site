<?php
include 'components/connect.php'; // Include database connection

// Check if order ID is provided
if(isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];

    // Prepare and execute the query to delete the order
    $deleteOrder = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $deleteOrder->execute([$orderId]);

    // Check if the order was successfully deleted
    if($deleteOrder->rowCount() > 0) {
        // Return a success message (optional)
        echo "Order successfully canceled.";
    } else {
        // Return an error message if the order wasn't found or couldn't be deleted
        echo "Error canceling order.";
    }
} else {
    // Return an error message if order ID is not provided
    echo "Order ID not provided.";
}
?>
