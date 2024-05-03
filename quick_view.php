   <?php
   include 'components/connect.php';

   session_start();

   if(isset($_SESSION['user_id'])){
      $user_id = $_SESSION['user_id'];
   }else{
      $user_id = '';
   };

   include 'components/wishlist_cart.php';

   ?>

   <!DOCTYPE html>
   <html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>quick view</title>
      
      <!-- font awesome cdn link  -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

      <!-- custom css file link  -->
      <link rel="stylesheet" href="css/style.css">

      <!-- SweetAlert2 CSS -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">

   </head>
   <body>
      
   <?php include 'components/user_header.php'; ?>

   <section class="quick-view">

      <h1 class="heading">quick view</h1>

      <?php
      $pid = $_GET['pid'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?"); 
      $select_products->execute([$pid]);
      if($select_products->rowCount() > 0){
         while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
      ?>
      <form action="" method="post" class="box" id="quick-view-form">
         <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
         <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
         <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
         <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
         <?php
         // Get the available quantity of the product
         $available_quantity = $fetch_product['quantity'];
         ?>
         <div class="row">
            <div class="image-container">
               <div class="main-image">
                  <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
               </div>
               <div class="sub-image">
                  <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
                  <img src="uploaded_img/<?= $fetch_product['image_02']; ?>" alt="">
                  <img src="uploaded_img/<?= $fetch_product['image_03']; ?>" alt="">
               </div>
            </div>
            <div class="content">
               <div class="name"><?= $fetch_product['name']; ?></div>
               <div class="quantity"><h3>Stocks: <span><?= $fetch_product['quantity']; ?></span></h3></div>
               <div class="flex">
                  <div class="price"><span>₱</span><?= $fetch_product['price']; ?><span></span></div>
                  <!-- Set the max attribute of the quantity input to the available quantity -->
                  <input type="number" name="qty" class="qty" id="qty" min="1" max="<?= $available_quantity ?>" onkeypress="if(this.value.length == 2) return false;" value="1">
               </div>
               <div class="details"><?= $fetch_product['details']; ?></div>
               <div class="flex-btn">
                  <button type="button" class="btn" id="add-to-cart-btn">add to cart</button>
                  <button type="submit" class="option-btn" name="add_to_wishlist">add to wishlist</button>
               </div>
            </div>
         </div>
      </form>
      <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
      ?>

   </section>

   <?php include 'components/footer.php'; ?>

   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
   <script src="js/script.js"></script>
   <script>
   document.getElementById('add-to-cart-btn').addEventListener('click', function() {
      var qtyInput = document.getElementById('qty');
      var maxQuantity = parseInt(qtyInput.getAttribute('max'));
      var selectedQuantity = parseInt(qtyInput.value);
      if (selectedQuantity > maxQuantity) {
         Swal.fire({
               icon: 'error',
               title: 'Oops...',
               text: 'The quantity exceeds the available product quantity!',
         });
      } else {
         document.getElementById('quick-view-form').submit();
      }
   });
   </script>

   </body>
   </html>

