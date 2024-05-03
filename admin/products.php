<?php
// Include database connection
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit(); 
}

// Category 
$categories = ["Shirt", "Pants", "Laptop", "TV", "Camera", "Mouse", "Fridge", "Washing Machine", "Smartphone", "Watch"];
$sizes = ["XS", "S", "M", "L", "XL"];


if (isset($_POST['add_product'])) {
   $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
   $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
   $details = filter_input(INPUT_POST, 'details', FILTER_SANITIZE_STRING);
   $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
   $category = $_POST['category'];

   if ($quantity === false || $quantity <= 0) {
      $message[] = 'Quantity must be a positive integer.';
   }

   $image_01 = $_FILES['image_01']['name'];
   $image_02 = $_FILES['image_02']['name'];
   $image_03 = $_FILES['image_03']['name'];

   $image_01 = filter_var($image_01, FILTER_SANITIZE_STRING);
   $image_02 = filter_var($image_02, FILTER_SANITIZE_STRING);
   $image_03 = filter_var($image_03, FILTER_SANITIZE_STRING);

   $image_folder_01 = '../uploaded_img/' . $image_01;
   $image_folder_02 = '../uploaded_img/' . $image_02;
   $image_folder_03 = '../uploaded_img/' . $image_03;

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if ($select_products->rowCount() > 0) {
      $message[] = 'Product name already exists!';
   } else {
      
      $insert_product = $conn->prepare("INSERT INTO products (name, details, quantity, price, image_01, image_02, image_03, category, product_size) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

      if ($category === 'Shirt' || $category === 'Pants') {
         $size = $_POST['size'];
      } else {
         $size = null; 
      }

      $insert_product->execute([$name, $details, $quantity, $price, $image_01, $image_02, $image_03, $category, $size]);

      if ($insert_product) {
         
         if ($_FILES['image_01']['size'] > 2000000 || $_FILES['image_02']['size'] > 2000000 || $_FILES['image_03']['size'] > 2000000) {
            $message[] = 'Image size is too large!';
         } else {
            
            move_uploaded_file($_FILES['image_01']['tmp_name'], $image_folder_01);
            move_uploaded_file($_FILES['image_02']['tmp_name'], $image_folder_02);
            move_uploaded_file($_FILES['image_03']['tmp_name'], $image_folder_03);
            $message[] = 'New product added successfully!';
         }
      }
   }
}


if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

  
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);

  
   unlink('../uploaded_img/' . $fetch_delete_image['image_01']);
   unlink('../uploaded_img/' . $fetch_delete_image['image_02']);
   unlink('../uploaded_img/' . $fetch_delete_image['image_03']);

   
   $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_product->execute([$delete_id]);

   
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);

   header('location:products.php');
   exit(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <script>
      // JavaScript function to show/hide size dropdown based on category
      function toggleSizeDropdown() {
         var category = document.getElementById('category').value;
         var sizeDropdown = document.getElementById('sizeDropdown');

         if (category === 'Shirt' || category === 'Pants') {
            sizeDropdown.style.display = 'block';
         } else {
            sizeDropdown.style.display = 'none';
         }
      }
   </script>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="add-products">
   <h1 class="heading">Add Product</h1>
   <form action="" method="post" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
            <span>Product Name (required)</span>
            <input type="text" class="box" required maxlength="100" placeholder="Enter product name" name="name">
         </div>
         <div class="inputBox">
            <span>Product Price (required)</span>
            <input type="number" min="0" class="box" required max="9999999999" placeholder="Enter product price" name="price">
         </div>
         <div class="inputBox">
            <span>Quantity (required)</span>
            <input type="number" min="1" class="box" required placeholder="Enter quantity" name="quantity">
         </div>
         <div class="inputBox">
            <span>Image 01 (required)</span>
            <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
         </div>
         <div class="inputBox">
            <span>Image 02 (required)</span>
            <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
         </div>
         <div class="inputBox">
            <span>Image 03 (required)</span>
            <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
         </div>
         <div class="inputBox">
            <span>Category (required)</span>
            <select name="category" id="category" class="box" required onchange="toggleSizeDropdown()">
               <option value="">Select Category</option>
               <?php foreach ($categories as $category): ?>
                  <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
               <?php endforeach; ?>
            </select>
         </div>
         <div class="inputBox" id="sizeDropdown" style="display: none;">
            <span>Size (required for Shirt/Pants)</span>
            <select name="size" class="box">
               <option value="">Select Size</option>
               <?php foreach ($sizes as $size): ?>
                  <option value="<?php echo $size; ?>"><?php echo $size; ?></option>
               <?php endforeach; ?>
            </select>
         </div>
         <div class="inputBox">
            <span>Product Details (required)</span>
            <textarea name="details" placeholder="Enter product details" class="box" required maxlength="500" cols="30" rows="10"></textarea>
         </div>
      </div>
      <input type="submit" value="Add Product" class="btn" name="add_product">
   </form>
</section>

<section class="show-products">
   <h1 class="heading">Products Added</h1>
   <div class="box-container">
   <?php
$select_products = $conn->prepare("SELECT * FROM `products`");
$select_products->execute();
if ($select_products->rowCount() > 0) {
   while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <div class="box">
         <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
         <div class="name"><?= $fetch_products['name']; ?></div>
         <div class="category"><h3>Category: <?= $fetch_products['category']; ?></h3></div>
         <?php if ($fetch_products['category'] === 'Shirt' || $fetch_products['category'] === 'Pants'): ?>
            <div class="size"><span><h3>Size: <?= $fetch_products['product_size']; ?></h3></span></div>
         <?php endif; ?>
         <div class="quantity"><h3>Stocks: <span><?= $fetch_products['quantity']; ?></span></h3></div>
         <div class="price">₱<span><?= $fetch_products['price']; ?></span></div>       
         <div class="details"><span><?= $fetch_products['details']; ?></span></div>
         <div class="flex-btn">
            <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">Update</a>
            <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
         </div>
      </div>
      <?php
   }
} else {
   echo '<p class="empty">No products added yet!</p>';
}
?>

   </div>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
