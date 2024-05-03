<?php
include 'components/connect.php';
session_start();

// Check if user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

if (isset($_POST['submit'])) {
   // Sanitize and retrieve form inputs
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $number = $_POST['number']; // Assuming the number is already sanitized as needed
   $old_pass = $_POST['old_pass'];
   $new_pass = $_POST['new_pass'];
   $cpass = $_POST['cpass'];

   // Fetch user's current profile information including the hashed password
   $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
   $select_profile->execute([$user_id]);
   $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

   if (!$fetch_profile) {
      $message[] = 'User not found!';
   } else {
      // Retrieve the hashed password stored in the database
      $stored_password = $fetch_profile['password'];

      // Hash the entered old password for comparison
      $hashed_entered_password = sha1($old_pass);

      // Check if the hashed entered password matches the stored hashed password
      if ($hashed_entered_password !== $stored_password) {
         $message[] = 'Old password does not match!';
      } elseif ($new_pass !== $cpass) {
         $message[] = 'New password and confirm password do not match!';
      } else {
         // Update user's profile information (name, email, number)
         $update_profile = $conn->prepare("UPDATE `users` SET name = ?, email = ?, number = ? WHERE id = ?");
         $update_profile->execute([$name, $email, $number, $user_id]);

         // Update password if a new password is provided
         if (!empty($new_pass)) {
            // Hash the new password before updating in the database
            $hashed_new_password = sha1($new_pass);
            $update_password = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_password->execute([$hashed_new_password, $user_id]);
         }

         $message[] = 'Profile updated successfully!';
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
   <title>Update Profile</title>
   
   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      .danger {
         background-color: #f44336;
         width: 20%;
         height: 10%;
         margin-left: 1%;
         font-size: 1.2rem;
      } /* Red */
      .danger:hover {
         background: #da190b;
      }

      /* CSS for the popup/modal */
      .popup-container {
         display: none;
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black overlay */
         z-index: 9999;
         font-size:1.5rem;
      }

      .popup-content {
         position: absolute;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%);
         background-color: white;
         padding: 20px;
         border-radius: 5px;
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
      }

      .close-btn {
         position: absolute;
         top: 10px;
         right: 10px;
         cursor: pointer;
      }
   </style>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <form action="" method="post">
      <h3>Update Profile</h3>
      <input type="text" name="name" required placeholder="Enter your username" maxlength="20" class="box" value="<?= $fetch_profile['name'] ?>">
      <input type="email" name="email" required placeholder="Enter your email" maxlength="50" class="box" value="<?= $fetch_profile['email'] ?>">
      
      <div style="display:flex">
         <input type="number" name="number" required placeholder="Enter your number" maxlength="20" class="box" value="<?= $fetch_profile['number'] ?>">
         <button type="button" class="btn danger" onclick="displayPopup()">
            Not Verified
         </button>
      </div>

      <input type="password" name="old_pass" placeholder="Enter your old password" maxlength="20" class="box">
      <input type="password" name="new_pass" placeholder="Enter your new password" maxlength="20" class="box">
      <input type="password" name="cpass" placeholder="Confirm your new password" maxlength="20" class="box">
      <input type="submit" value="Update Now" class="btn" name="submit">
   </form>

   <!-- Popup container -->
   <div class="popup-container" id="popupContainer">
      <div class="popup-content">
         <h2 style="margin-bottom:1rem; margin-top:1.5rem">Verification Panel</h2>
         <p style="margin-bottom:1rem">This user is not yet verified.</p>
         
         <input type="text" name="otp" placeholder="Enter OTP code" maxlength="20" class="box">
         
         <button class="box btn" onclick="">Send OTP Code</button>
         <br>
         <button class="box btn" onclick="proceed()">Verify</button>
         <span class="close-btn" onclick="closePopup()">Close</span>
      </div>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<script>
   // Function to display the popup
   function displayPopup() {
      document.getElementById('popupContainer').style.display = 'block';
   }

   // Function to close the popup
   function closePopup() {
      document.getElementById('popupContainer').style.display = 'none';
   }

   // Function to handle the proceed action
   function proceed() {
      // Implement your logic here when user chooses to proceed
      alert('Proceeding with verification...');
      closePopup(); // Close the popup after processing
   }
</script>

<script src="js/script.js"></script>
</body>
</html>
