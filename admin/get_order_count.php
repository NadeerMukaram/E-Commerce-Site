<?php
// Include your database connection file
include '../components/connect.php';

// Prepare and execute query to get the number of orders
$select_orders = $conn->prepare("SELECT COUNT(*) as order_count FROM `orders`");
$select_orders->execute();

// Fetch the result
$result = $select_orders->fetch(PDO::FETCH_ASSOC);

// Return the number of orders
echo $result['order_count'];
?>
