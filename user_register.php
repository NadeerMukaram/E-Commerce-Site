<?php
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

if(isset($_POST['submit'])){
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']); // Consider using password_hash instead
   $number = filter_var($_POST['number'], FILTER_SANITIZE_NUMBER_INT);
   $drive = filter_var($_POST['drive'], FILTER_SANITIZE_STRING);
   $landmark = filter_var($_POST['landmark'], FILTER_SANITIZE_STRING);
   $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
   $country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
   $zip_code = filter_var($_POST['zip_code'], FILTER_SANITIZE_STRING);
   $fullname = filter_var($_POST['fullname'], FILTER_SANITIZE_STRING); // New input for full name

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){
      $message[] = 'Email already exists!';
   } else {
      $insert_user = $conn->prepare("INSERT INTO `users` (name, email, password, number, drive, landmarks, city, country, zip_code, fullname) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $insert_user->execute([$name, $email, $pass, $number, $drive, $landmark, $city, $country, $zip_code, $fullname]);

      if($insert_user->rowCount() > 0){
         $message[] = 'Registered successfully. Please login!';
      } else {
         $message[] = 'Registration failed. Please try again.';
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   
   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <form action="" method="post">
      <h3>Register Now</h3>
      <input type="text" name="name" required placeholder="Enter your username" maxlength="20" class="box">
      <input type="text" name="fullname" required placeholder="Enter your full name" class="box">
      <input type="email" name="email" required placeholder="Enter your email" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Enter your password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="Confirm your password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="number" name="number" required placeholder="Enter your number" maxlength="20" class="box">
      <input type="text" name="drive" required placeholder="Enter your drive" class="box">
      <input type="text" name="landmark" required placeholder="Enter your landmark" class="box">
      <input type="text" name="city" required placeholder="Enter your city" class="box">
      <input type="text" name="country" required placeholder="Enter your country" class="box">
      <input type="text" name="zip_code" required placeholder="Enter your ZIP code" class="box">
      <input type="submit" value="Register Now" class="btn" name="submit">
      <p>Already have an account?</p>
      <a href="user_login.php" class="option-btn">Login Now</a>
   </form>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
