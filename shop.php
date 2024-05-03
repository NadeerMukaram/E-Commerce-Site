<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';

// if(isset($_POST['add_to_cart'])){
//     $pid = $_POST['pid'];
//     $name = $_POST['name'];
//     $price = $_POST['price'];
//     $image = $_POST['image'];
//     $qty = $_POST['qty'];

//     // Retrieve available quantity of the product
//     $select_product = $conn->prepare("SELECT quantity FROM `products` WHERE id = ?");
//     $select_product->execute([$pid]);
//     $available_quantity = $select_product->fetchColumn();

//     // Ensure the quantity selected by the user does not exceed available quantity
//     if($qty <= $available_quantity) {
//         // Add the product to the cart
//         $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, pid, name, price, image, quantity) VALUES (?, ?, ?, ?, ?, ?)");
//         $insert_cart->execute([$user_id, $pid, $name, $price, $image, $qty]);
//         $message[] = 'Product added to cart';
//     } else {
//         $message[] = 'Selected quantity exceeds available quantity';
//     }
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shop</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="products">

   <h1 class="heading">latest products</h1>

   <div class="box-container">

   <?php
     $select_products = $conn->prepare("SELECT * FROM `products`"); 
     $select_products->execute();
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="details" value="<?= $fetch_product['details']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="name"><h6>Stocks: <?= $fetch_product['quantity']; ?></h6></div>
      <div class="flex">
         <div class="price"><span>₱</span><?= $fetch_product['price']; ?><span></span></div>
         <input type="number" name="qty" class="qty" min="1" max="<?= $fetch_product['quantity']; ?>" onkeypress="if(this.value.length == 2) return false;" value="1">
      </div>
      <input type="submit" value="add to cart" class="btn" name="add_to_cart">
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">no products found!</p>';
   }
   ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
