<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>HardTech</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="dashboard">

   <h1 class="heading">HardTech</h1>

   <div class="box-container">



      <div class="box">
      <h3 style="margin-bottom:2rem" >ORDERS<h3>
      <div class="box"> 
         <?php
            $total_pendings = 0;
            $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
            $select_pendings->execute(['pending']);
            if($select_pendings->rowCount() > 0){
               while($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)){
                  $total_pendings += $fetch_pendings['total_price'];
               }
            }
         ?>
         <h3><span>₱</span><?= $total_pendings; ?><span></span></h3>
         <p>total pendings</p>
         </div>

         <div class="box">
         <?php
            $total_completes = 0;
            $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
            $select_completes->execute(['completed']);
            if($select_completes->rowCount() > 0){
               while($fetch_completes = $select_completes->fetch(PDO::FETCH_ASSOC)){
                  $total_completes += $fetch_completes['total_price'];
               }
            }
         ?>
         <h3><span>₱</span><?= $total_completes; ?><span></span></h3>
         <p>completed orders</p>
         </div>

         <div class="box">
         <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders`");
            $select_orders->execute();
            $number_of_orders = $select_orders->rowCount()
         ?>
         <h3><?= $number_of_orders; ?></h3>
         <p>orders placed</p>         
         </div>
         
         <div class="box">
            <?php
            // Select only completed orders from the orders table
            $select_completed_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = 'completed'");
            $select_completed_orders->execute();
            $number_of_completed_orders = $select_completed_orders->rowCount();
            ?>
            <h3><?= $number_of_completed_orders; ?></h3>
            <p>completed orders</p>

         </div>
         
         <a href="placed_orders.php" class="btn">see orders</a>
         <a href="completed_orders.php" class="btn">see completed orders</a>
      </div>





      <div  class="box">
      <h3 style="margin-bottom:5rem" >PRODUCTS<h3>
      <img src="../images/shop_cart.png" style="border-radius:1rem" width="100%" height="150%">
         <?php
            $select_products = $conn->prepare("SELECT * FROM `products`");
            $select_products->execute();
            $number_of_products = $select_products->rowCount()
         ?>
         <h3><?= $number_of_products; ?></h3>
         <p>products added</p>
         <a href="products.php" class="btn">see products</a>
      </div>




      <div class="box">
      <h3 style="margin-bottom:2rem" >USERS<h3>
         <div class="box">
         <?php
            $select_users = $conn->prepare("SELECT * FROM `users`");
            $select_users->execute();
            $number_of_users = $select_users->rowCount()
         ?>
         <h3><?= $number_of_users; ?></h3>
         <p>normal users</p>
         <a href="users_accounts.php" class="btn">see users</a>
         </div>

         <div class="box">
            <?php
               $select_admins = $conn->prepare("SELECT * FROM `admins`");
               $select_admins->execute();
               $number_of_admins = $select_admins->rowCount()
            ?>
            <h3><?= $number_of_admins; ?></h3>
            <p>admin users</p>
            <a href="admin_accounts.php" class="btn">see admins</a>
         </div>

         <div class="box">
         
         <p><?= $fetch_profile['name']; ?></p>
         <a href="update_profile.php" class="btn">update profile</a>
         </div>

      </div>



      

   </div>

</section>












<script src="../js/admin_script.js"></script>
   
</body>
</html>