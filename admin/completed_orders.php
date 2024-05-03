<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

if (isset($_POST['update_payment'])) {
    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];
    $payment_status = filter_var($payment_status, FILTER_SANITIZE_STRING);
    $update_payment = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
    $update_payment->execute([$payment_status, $order_id]);
    $message[] = 'Payment status updated!';
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
    $delete_order->execute([$delete_id]);
    header('location:completed_orders.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        /* Additional styles for table */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .status {
            color: green;
            font-size: 20px;
            font-weight: bold;
        }
        .product-image {
            max-width: 100px;
            max-height: 100px;
        }
    </style>
</head>
<body>
<?php include '../components/admin_header.php'; ?>
<section class="orders">
    <h1 class="heading">Completed Orders</h1>
    <div class="box-container">
        <?php
        $select_orders = $conn->prepare("SELECT o.*, p.image_01 
                                         FROM `orders` o 
                                         INNER JOIN `products` p ON o.product_name = p.name 
                                         WHERE o.payment_status = 'completed' 
                                         ORDER BY o.placed_on DESC");
        $select_orders->execute();
        if ($select_orders->rowCount() > 0) {
            echo '<table>';
            echo '<thead>
                <tr>
                    <th>Placed on</th>
                    <th>Order Timestamp</th>
                    <th>Product Image</th>
                    <th>Customer Name</th>
                    <th>Product Name(s)</th>

                    <th>Number</th>
                    <th>Address</th>
                    <th>Total Price</th>
                    <th>Payment Method</th>
                    <th>Item Received</th>
                    <th>Status</th>
                </tr>
            </thead>';
            echo '<tbody>';
            while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>' . $fetch_orders['placed_on'] . '</td>';
                echo '<td>' . $fetch_orders['order_timestamp'] . '</td>';
                echo '<td><img src="../uploaded_img/' . $fetch_orders['image_01'] . '" class="product-image" alt="Product Image"></td>';
                echo '<td>' . $fetch_orders['name'] . '</td>';
                echo '<td>' . $fetch_orders['product_name'] . '</td>';
                
                echo '<td>' . $fetch_orders['number'] . '</td>';
                echo '<td>' . $fetch_orders['address'] . '</td>';
                echo '<td>₱' . $fetch_orders['total_price'] . '</td>';
                echo '<td>' . $fetch_orders['method'] . '</td>';
                echo '<td>' . ($fetch_orders['order_received'] == 'yes' ? '<span class="status" style="color: green;">Yes</span>' : '<span class="status" style="color: red;">No</span>') . '</td>';
                echo '<td><span class="status">Completed</span></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p class="empty">No completed orders yet!</p>';
        }
        ?>
    </div>
</section>
<script src="../js/admin_script.js"></script>
</body>
</html>
