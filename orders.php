<?php
// Include database connection file
include 'components/connect.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // Redirect to login page or display message
    header("Location: login.php");
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Check if "Cancel Order" button is clicked
if(isset($_POST['cancel_order_btn'])) {
    $cancelOrderId = $_POST['cancel_order_id'];

    try {
        // Fetch order details from orders table
        $selectOrder = $conn->prepare("SELECT * FROM orders WHERE id = ?");
        $selectOrder->execute([$cancelOrderId]);

        if ($selectOrder->rowCount() > 0) {
            // Fetch order details
            $orderDetails = $selectOrder->fetch(PDO::FETCH_ASSOC);

            // Insert cancelled order details into cancelled_orders table
            $insertCancelledOrder = $conn->prepare("
                INSERT INTO cancelled_orders (id, user_id, name, number, email, method, address, total_products, total_price, placed_on, payment_status, order_id, order_received, product_name, product_details, order_timestamp, address_2)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $insertCancelledOrder->execute([
                $orderDetails['id'],
                $orderDetails['user_id'],
                $orderDetails['name'],
                $orderDetails['number'],
                $orderDetails['email'],
                $orderDetails['method'],
                $orderDetails['address'],
                $orderDetails['total_products'],
                $orderDetails['total_price'],
                $orderDetails['placed_on'],
                'cancelled', // Set payment_status to 'cancelled'
                $orderDetails['order_id'],
                $orderDetails['order_received'],
                $orderDetails['product_name'],
                $orderDetails['product_details'],
                $orderDetails['order_timestamp'],
                $orderDetails['address_2']
            ]);

            // Check if insert was successful
            if ($insertCancelledOrder->rowCount() > 0) {
                // Delete the order from orders table
                $deleteOrder = $conn->prepare("DELETE FROM orders WHERE id = ?");
                $deleteOrder->execute([$cancelOrderId]);

                // Check if order was deleted
                if ($deleteOrder->rowCount() > 0) {
                    // Display success message and redirect
                    echo '<script>alert("Order cancelled successfully.");</script>';
                    echo '<script>window.location.href = "orders.php";</script>';
                    exit;
                }
            }
        }
    } catch (PDOException $e) {
        // Display error message
        echo '<script>alert("Error cancelling order. Please try again.");</script>';
        echo '<script>window.location.href = "orders.php";</script>';
        exit;
    }

    // If execution reaches here, it means something went wrong
    echo '<script>alert("Error cancelling order. Please try again.");</script>';
    echo '<script>window.location.href = "orders.php";</script>';
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>
   
   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="css/bootstrap.min.css">

   <style>
      /* Custom CSS styles for table */
      .table {
          width: 100%;
          margin-bottom: 1rem;
          color: #212529;
          border: 1px solid #dee2e6; /* Add border around the table */
      }

      .table th,
      .table td {
          padding: 0.75rem;
          vertical-align: top;
          border-top: 1px solid #dee2e6; /* Add top border to table cells */
          border-bottom: 1px solid #dee2e6; /* Add bottom border to table cells */
      }

      .table thead th {
          vertical-align: bottom;
          border-bottom: 2px solid #dee2e6;
      }

      .align-middle {
          vertical-align: middle !important;
      }

      .text-center {
          text-align: center;
      }
   </style>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="orders">
   <h1 class="heading">Placed Orders</h1>

   <div class="box-container">
      <?php
      // Check if user is logged in
      if ($user_id == '') {
          echo '<p class="empty">Please login to see your orders.</p>';
      } else {
          // Fetch user's orders
          $select_orders = $conn->prepare("
              SELECT o.*, GROUP_CONCAT(p.image_01) AS product_images
              FROM `orders` AS o 
              INNER JOIN `products` AS p 
              ON FIND_IN_SET(p.name, REPLACE(o.product_name, ', ', ',')) > 0
              WHERE user_id = ?
              GROUP BY o.id
              ORDER BY o.id ASC
          ");
          $select_orders->execute([$user_id]);

          if ($select_orders->rowCount() > 0) {
              echo '<div class="table-responsive">';
              echo '<table class="table table-striped">';
              echo '<thead>';
              echo '<tr>';
              echo '<th><h3 class="text-center">Product Images</h3></th>'; // Centered text
              echo '<th><h3 class="text-center">Order ID</h3></th>';
              echo '<th><h3 class="text-center">Product Name(s)</h3></th>';
              echo '<th><h3 class="text-center">Product Detail(s)</h3></th>';
              echo '<th><h3 class="text-center">Placed On</h3></th>';
              echo '<th><h3 class="text-center">Order Timestamp</h3></th>';
              echo '<th><h3 class="text-center">Customer Name</h3></th>';
              echo '<th><h3 class="text-center">Email</h3></th>';
              echo '<th><h3 class="text-center">Number</h3></th>';
              echo '<th><h3 class="text-center">Address</h3></th>';
              echo '<th><h3 class="text-center">Payment Method</h3></th>';
              echo '<th><h3 class="text-center">Total Products</h3></th>';
              echo '<th><h3 class="text-center">Total Price</h3></th>';
              echo '<th><h3 class="text-center">Delivery Status</h3></th>';
              echo '</tr>';
              echo '</thead>';
              echo '<tbody>';

              while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                  echo '<tr>';
                  echo '<td class="align-middle text-center">';
                  // Display combined product images
                  $product_images = explode(',', $fetch_orders['product_images']);
                  foreach ($product_images as $image) {
                      echo '<img src="uploaded_img/' . $image . '" style="border-radius:1rem" width="100" height="100" alt="Product Image"><br>';
                  }
                  echo '</td>';
                  echo '<td class="align-middle text-center"><b>' . $fetch_orders['order_id'] . '</b></td>';
                  echo '<td class="align-middle text-center">' . $fetch_orders['product_name'] . '</td>';
                  echo '<td class="align-middle text-center">' . $fetch_orders['product_details'] . '</td>';
                  echo '<td class="align-middle text-center">' . $fetch_orders['placed_on'] . '</td>';
                  echo '<td class="align-middle text-center">' . $fetch_orders['order_timestamp'] . '</td>';
                  echo '<td class="align-middle text-center">' . $fetch_orders['name'] . '</td>';
                  echo '<td class="align-middle text-center">' . $fetch_orders['email'] . '</td>';
                  echo '<td class="align-middle text-center">' . $fetch_orders['number'] . '</td>';
                  echo '<td class="align-middle text-center">' . $fetch_orders['address'];
                  if ($fetch_orders['address_2'] != null) {
                    echo '<br><br><b>2nd Address:</b><br>' . $fetch_orders['address_2'];
                  }
                  echo '</td>';
                  echo '<td class="align-middle text-center">' . $fetch_orders['method'] . '</td>';
                  echo '<td class="align-middle text-center">' . $fetch_orders['total_products'] . '</td>';
                  echo '<td class="align-middle text-center">₱' . $fetch_orders['total_price'] . '</td>';
                  echo '<td class="align-middle text-center">';
                  if ($fetch_orders['payment_status'] == 'completed') {
                      echo '<span style="color: green;"><h3>Completed</h3></span>';
                  } 
                  else if ($fetch_orders['payment_status'] == 'delivering') {
                    echo '<span style="color: green;"><h3>Delivering</h3></span>';
                    echo '<form method="post">';
                    echo '<input type="hidden" name="cancel_order_id" value="' . $fetch_orders['id'] . '">';
                    echo '</form>';
                  }
                  else if ($fetch_orders['payment_status'] == 'pending') {
                    echo '<span style="color: green;"><h3>Pending</h3></span>';
                    echo '<form method="post">';
                    echo '<input type="hidden" name="cancel_order_id" value="' . $fetch_orders['id'] . '">';
                    echo '<button type="submit" name="cancel_order_btn" class="btn btn-danger"><h6>Cancel Order</h6></button>';
                    echo '</form>';
                  }
                  else {
                      // Display "Cancel Order" button only if payment status is not completed
                      echo '<form method="post">';
                      echo '<input type="hidden" name="cancel_order_id" value="' . $fetch_orders['id'] . '">';
                      echo '<button type="submit" name="cancel_order_btn" class="btn btn-danger"><h6>Cancel Order</h6></button>';
                      echo '</form>';
                  }

                  // Display "Item Received" button for completed orders that haven't been received
                  if ($fetch_orders['payment_status'] == 'completed' && $fetch_orders['order_received'] != 'yes') {
                      echo '<form method="post">';
                      echo '<input type="hidden" name="order_received_id" value="' . $fetch_orders['id'] . '">';
                      echo '<button type="submit" name="order_received_btn" class="btn btn-success"><h6>Item Received</h6></button>';
                      echo '</form>';
                  }
                  echo '</td>';
                  echo '</tr>';
              }

              echo '</tbody>';
              echo '</table>';
              echo '</div>';
          } else {
              echo '<p class="empty">No orders placed yet!</p>';
          }
      }
      ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<?php
// Check if "Item Received" button is clicked
if(isset($_POST['order_received_btn'])) {
    $orderReceivedId = $_POST['order_received_id'];

    // Update order_received column to 'yes' for the order
    $updateOrderReceived = $conn->prepare("UPDATE orders SET order_received = 'yes' WHERE id = ?");
    $updateOrderReceived->execute([$orderReceivedId]);

    // Check if the order_received was successfully updated
    if ($updateOrderReceived->rowCount() > 0) {
        // Optionally, provide a confirmation message
        echo '<script>alert("Item received successfully.");</script>';
        // You may want to redirect or reload the page after successful update
        echo '<script>window.location.href = "orders.php";</script>';
    } else {
        // Optionally, provide an error message
        echo '<script>alert("Error updating order status.");</script>';
    }
}

// Check if "Cancel Order" button is clicked
if(isset($_POST['cancel_order_btn'])) {
    $cancelOrderId = $_POST['cancel_order_id'];

    // Prepare and execute the query to delete the order
    $deleteOrder = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $deleteOrder->execute([$cancelOrderId]);

    // Check if the order was successfully deleted
    if($deleteOrder->rowCount() > 0) {
        // Optionally, provide a confirmation message
        echo '<script>alert("Order canceled successfully.");</script>';
        // You may want to redirect or reload the page after successful deletion
        echo '<script>window.location.href = "orders.php";</script>';
    } else {
        // Optionally, provide an error message
        echo '<script>alert("Error canceling order.");</script>';
    }
}
?>

</body>
</html>
