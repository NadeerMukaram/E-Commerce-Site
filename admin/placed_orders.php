<?php
// Include database connection and start session
include '../components/connect.php';
session_start();

// Check if admin is logged in
$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('location: admin_login.php');
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

// Process order deletion
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
    $delete_order->execute([$delete_id]);
    header('location: placed_orders.php');
    exit; // Stop further execution
}

// Retrieve and sort orders by "Placed on" date in descending order
$select_orders = $conn->prepare("SELECT o.*, p.image_01 
FROM `orders` o 
INNER JOIN `products` p ON o.product_name = p.name 
WHERE o.payment_status != 'completed' 
ORDER BY o.placed_on DESC");
$select_orders->execute();
$orders = $select_orders->fetchAll(PDO::FETCH_ASSOC);

$number_of_orders = count($orders);
if (isset($_SESSION['last_order_count']) && $_SESSION['last_order_count'] < $number_of_orders) {
    $new_orders_count = $number_of_orders - $_SESSION['last_order_count'];
    echo '
        <div id="floatingDiv" class="animate__animated">
            <span id="closeButton" onclick="closeFloatingDiv()">X</span>
            <div style="padding: 2rem 5rem;">
                <h1>' . $new_orders_count . ' new order(s) placed!</h1>
            </div>
        </div>
    ';
}

$_SESSION['last_order_count'] = $number_of_orders;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placed Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="../css/style.css">
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
        .option-btn, .delete-btn {
            padding: 5px 10px;
            text-decoration: none;
            cursor: pointer;
        }
        .option-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 3px;
            margin-left: 10px;
        }

        #floatingDiv {
            display: none;
            position: fixed;
            top: 9rem;
            right: 1rem;
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            animation-duration: 1s;
        }
        #closeButton {
            cursor: pointer;
            float: right;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="orders">

    <h1 class="heading">Placed Orders</h1>

    <div class="box-container">
        <?php if ($number_of_orders > 0): ?>
            <table>
            <thead>
    <tr>
        <th style="text-align: center;">Order ID</th>
        <th style="text-align: center;">Placed on</th>
        <th style="text-align: center;">Product Image</th>
        <th style="text-align: center;">Order Timestamp</th>
        <th style="text-align: center;">Customer Name</th>
        <th style="text-align: center;">Address</th>
        <th style="text-align: center;">Total Products</th>
        <th style="text-align: center;">Total Price</th>
        <th style="text-align: center;">Payment Method</th>
        <th style="text-align: center;">Payment Status</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td style="text-align: center;"><?= $order['order_id']; ?></td>
            <td style="text-align: center;"><?= $order['placed_on']; ?></td>
            <td style="text-align: center;"><img src="../uploaded_img/<?= $order['image_01']; ?>" width="20%" class="product-image" alt="Product Image"></td>
            <td style="text-align: center;"><?= $order['order_timestamp']; ?></td>
            <td style="text-align: center;"><?= $order['name']; ?></td>
            <td style="text-align: center;"><?= $order['address']; ?></td>
            <td style="text-align: center;"><?= $order['total_products']; ?></td>
            <td style="text-align: center;"><?= $order['total_price']; ?></td>
            <td style="text-align: center;"><?= $order['method']; ?></td>
            <td>
                <form action="" method="post">
                    <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                    <select name="payment_status" class="select">
                        <option <?= ($order['payment_status'] == 'pending') ? 'selected' : ''; ?>>pending</option>
                        <option <?= ($order['payment_status'] == 'delivering') ? 'selected' : ''; ?>>delivering</option>
                        <option <?= ($order['payment_status'] == 'completed') ? 'selected' : ''; ?>>completed</option>
                    </select>
                    <input type="submit" value="Update" class="option-btn" name="update_payment">
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
            </table>
        <?php else: ?>
            <p class="empty">No orders placed yet!</p>
        <?php endif; ?>
    </div>

</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var floatingDiv = document.getElementById('floatingDiv');
        if (floatingDiv) {
            setTimeout(function() {
                floatingDiv.style.display = 'block';
                floatingDiv.classList.add('animate__fadeIn');
            }, 1000);
        }
    });
</script>


<script>
    function closeFloatingDiv() {
        var floatingDiv = document.getElementById('floatingDiv');
        floatingDiv.style.display = 'none';
    }

    // Automatically show and animate the floating div on page load
    document.addEventListener('DOMContentLoaded', function() {
        var floatingDiv = document.getElementById('floatingDiv');
        if (floatingDiv) {
            // Use a timeout to delay showing and apply fade-in animation
            setTimeout(function() {
                floatingDiv.style.display = 'block';
                floatingDiv.classList.add('animate__fadeIn');
            }, 1000); // Delay in milliseconds (adjust as needed)
        }
    });
</script>

</body>
</html>
