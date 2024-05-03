<?php
include 'components/connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
   header('location:user_login.php');
   exit; // Stop further execution
}

$user_id = $_SESSION['user_id'];
$message = [];

// Fetch user details from 'users' table
$select_user = $conn->prepare("SELECT fullname, number, email, drive, landmarks, city, country, zip_code FROM users WHERE id = ?");
$select_user->execute([$user_id]);
$user_details = $select_user->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['order'])) {
   // Sanitize input data
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
   $flat = filter_var($_POST['flat'], FILTER_SANITIZE_STRING);
   $street = filter_var($_POST['street'], FILTER_SANITIZE_STRING);
   $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
   $country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
   $pin_code = filter_var($_POST['pin_code'], FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   // Generate unique random order ID
   do {
       $order_id = mt_rand(100000, 999999);
       $check_order_id = $conn->prepare("SELECT * FROM `orders` WHERE order_id = ?");
       $check_order_id->execute([$order_id]);
   } while ($check_order_id->rowCount() > 0);

   // Fetch cart items and details
   $cart_items = [];
   $cart_details = [];
   $total_products = 0;

   $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $select_cart->execute([$user_id]);

   if ($select_cart->rowCount() > 0) {
      while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
         $cart_items[] = $fetch_cart['name'];
         $cart_details[] = $fetch_cart['details'];
         $total_products += $fetch_cart['quantity'];
         
         // Reduce product quantity in the 'products' table
         $reduce_quantity = $conn->prepare("UPDATE `products` SET quantity = quantity - ? WHERE name = ?");
         $reduce_quantity->execute([$fetch_cart['quantity'], $fetch_cart['name']]);
      }
   }

   // Check if user wants to use a second address
   if (isset($_POST['use_second_address'])) {
      $drive = filter_var($_POST['drive'], FILTER_SANITIZE_STRING);
      $landmark = filter_var($_POST['landmark'], FILTER_SANITIZE_STRING);
      $city2 = filter_var($_POST['city2'], FILTER_SANITIZE_STRING);
      $country2 = filter_var($_POST['country2'], FILTER_SANITIZE_STRING);
      $pin_code2 = filter_var($_POST['pin_code2'], FILTER_SANITIZE_STRING);

      // Create second address string
      $address2 = 'Flat No. ' . $drive . ', ' . $landmark . ', ' . $city2 . ', ' . $country2 . ' ' . $pin_code2;
      $address2 = filter_var($address2, FILTER_SANITIZE_STRING);
   } else {
      $address2 = ''; // Empty second address if not used
   }

   // Primary address string
   $address = 'Flat No. ' . $flat . ', ' . $street . ', ' . $city . ', ' . $country . ' ' . $pin_code;
   $address = filter_var($address, FILTER_SANITIZE_STRING);

   // Insert order details into orders table
   date_default_timezone_set('Asia/Manila');
   $order_timestamp = (new DateTime())->format('h:i A'); 

   $insert_order = $conn->prepare("INSERT INTO `orders` (order_id, user_id, product_name, product_details, number, email, method, address, address_2, total_products, total_price, name, order_timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
   $insert_order->execute([$order_id, $user_id, implode(', ', $cart_items), implode(', ', $cart_details), $number, $email, $method, $address, $address2, $total_products, $total_price, $name, $order_timestamp]);

   // Clear user's cart after placing order
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart->execute([$user_id]);

   $message[] = 'Order placed successfully!';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>
   
   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

   <!-- JavaScript for show/hide second address fields -->
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         var useSecondAddress = document.getElementById('use_second_address');
         var secondAddressFields = document.getElementById('second_address_fields');

         useSecondAddress.addEventListener('change', function() {
            secondAddressFields.style.display = this.checked ? 'block' : 'none';
         });
      });
   </script>

<script>
document.addEventListener('DOMContentLoaded', function() {
   var paymentMethodSelect = document.querySelector('select[name="method"]');
   var paypalContainer = document.getElementById('paypal-button-container');
   var placeOrderButton = document.querySelector('input[name="order"]');
   
   // Hide PayPal container initially
   paypalContainer.style.display = 'none';

   // Event listener for payment method selection
   paymentMethodSelect.addEventListener('change', function() {
      var selectedPaymentMethod = this.value;

      if (selectedPaymentMethod === 'PayPal') {
         // Show PayPal container and hide Place Order button
         paypalContainer.style.display = 'block';
         placeOrderButton.style.display = 'none';
      } else {
         // Hide PayPal container and show Place Order button
         paypalContainer.style.display = 'none';
         placeOrderButton.style.display = 'inline-block'; // Show Place Order button
      }
   });

   // Event listener for hovering over payment method options
   paymentMethodSelect.addEventListener('mouseover', function() {
      var selectedPaymentMethod = this.value;

      if (selectedPaymentMethod === 'PayPal') {
         // Show PayPal container when hovering over PayPal option
         paypalContainer.style.display = 'block';
         placeOrderButton.style.display = 'none'; // Hide Place Order button
      }
   });

   // Event listener for hovering out of payment method options
   paymentMethodSelect.addEventListener('mouseout', function() {
      var selectedPaymentMethod = this.value;

      if (selectedPaymentMethod !== 'PayPal') {
         // Hide PayPal container if not hovering over PayPal option
         paypalContainer.style.display = 'none';
         placeOrderButton.style.display = 'inline-block'; // Show Place Order button
      }
   });
});

   </script>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-orders">
   <form action="" method="POST">
      <h3>Your Orders</h3>
      <div class="display-orders">
         <?php
            // Display cart items and calculate grand total
            $grand_total = 0;
            $cart_items = [];

            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);

            if ($select_cart->rowCount() > 0) {
               while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                  $cart_items[] = $fetch_cart['name'] . ' (' . $fetch_cart['quantity'] . ')';
                  $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
                  echo '<p>' . $fetch_cart['name'] . ' <span>(' . $fetch_cart['quantity'] . ')</span></p><br>';
               }
            } else {
               echo '<p class="empty">Your cart is empty!</p>';
            }
         ?>
         <input type="hidden" name="total_products" value="<?= count($cart_items); ?>">
         <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
         <div class="grand-total">Grand Total: <span>₱<?= $grand_total; ?></span></div>
      </div>

      <h3>Place Your Orders</h3>

      <div class="flex">
         <div class="inputBox">
            <span>Your Name:</span>
            <input type="text" name="name" placeholder="Enter your name" value="<?= htmlspecialchars($user_details['fullname'] ?? '') ?>" class="box" maxlength="20" required>
         </div>
         <div class="inputBox">
            <span>Your Number:</span>
            <input type="number" name="number" placeholder="Enter your number" value="<?= htmlspecialchars($user_details['number'] ?? '') ?>" class="box" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;" required>
         </div>
         <div class="inputBox">
            <span>Your Email:</span>
            <input type="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($user_details['email'] ?? '') ?>" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Payment Method:</span>
            <select name="method" class="box" required>
               <option value="Cash on Delivery">Cash on Delivery</option>
               <!-- <option value="credit card">Credit Card</option> -->
               <option value="PayPal">PayPal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Drive:</span>
            <input type="text" name="flat" placeholder="e.g. street" value="<?= htmlspecialchars($user_details['drive'] ?? '') ?>" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Landmarks:</span>
            <input type="text" name="street" placeholder="e.g. near Maria Sari-sari store" value="<?= htmlspecialchars($user_details['landmarks'] ?? '') ?>" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>City:</span>
            <input type="text" name="city" placeholder="e.g. Manila" value="<?= htmlspecialchars($user_details['city'] ?? '') ?>" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Country:</span>
            <input type="text" name="country" placeholder="e.g. Philippines" value="<?= htmlspecialchars($user_details['country'] ?? '') ?>" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Zip Code:</span>
            <input type="number" name="pin_code" placeholder="e.g. 123456" value="<?= htmlspecialchars($user_details['zip_code'] ?? '') ?>" class="box" min="0" max="999999" required>
         </div>
      </div>

      
      <h1 style="margin-top:2rem">
      <div class="inputBox">   
         <input type="checkbox" id="use_second_address" name="use_second_address">
         <label for="use_second_address">Add 2nd Address</label>      
      </div>
      </h1>
      
      <section class="checkout-orders">
      <div class="flex">
      <!-- Second Address Fields (Initially Hidden) -->
      <div id="second_address_fields" style="display: none;">
         <div class="inputBox">
         <div>
            <span>Drive:</span>
            <input style="width:500%" type="text" name="drive" placeholder="e.g. street" class="box" maxlength="50">
         </div>
         </div>
         <div class="inputBox">
            <span>Landmarks:</span>
            <input style="width:500%" type="text" name="landmark" placeholder="e.g. near Maria Sari-sari store" class="box" maxlength="50">
         </div>
         <div class="inputBox">
            <span>City:</span>
            <input style="width:500%" type="text" name="city2" placeholder="e.g. Manila" class="box" maxlength="50">
         </div>
         <div class="inputBox">
            <span>Country:</span>
            <input style="width:500%" type="text" name="country2" placeholder="e.g. Philippines" class="box" maxlength="50">
         </div>
         <div class="inputBox">
            <span>Zip Code:</span>
            <input style="width:350%" type="number" name="pin_code2" placeholder="e.g. 123456" class="box" min="0" max="999999">
         </div>
      </div>
      </div>
      </section>

      <div style="margin-left: 17.5%;" id="paypal-button-container">
      <!-- PayPal button will be rendered here -->
      </div>

      <input type="submit" id="place-order-button" name="order" class="btn <?= ($grand_total > 0) ? '' : 'disabled'; ?>" value="Place Order">

   </form>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

<script src="https://www.paypal.com/sdk/js?client-id=AW5kenkfkuxUkHBX4HHuA5JJp9PQBQ8zlQ808yb2U3s8RLu3WbEU2nhL3Z7QsZFxaGIlcj2wwz7MjokH&currency=PHP"></script>
<!-- <script src="https://www.paypal.com/sdk/js?client-id=YOUR_PAYPAL_CLIENT_ID&currency=PHP"></script> -->
<script>
   paypal.Buttons({
      createOrder: function(data, actions) {
         return actions.order.create({
            purchase_units: [{
               amount: {
                  value: '<?= $grand_total ?>' // Total amount for the transaction
               }
            }]
         });
      },
      onApprove: function(data, actions) {
         return actions.order.capture().then(function(details) {
            alert('Transaction completed by ' + details.payer.name.given_name);
            // Trigger the click event on the 'Place Order' button
            document.getElementById('place-order-button').click();
         });
      }
   }).render('#paypal-button-container');
</script>



</body>
</html>
